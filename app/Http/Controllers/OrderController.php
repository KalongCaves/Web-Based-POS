<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\BlacklIst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
  public function checkBlacklist(Request $request)
  {
    $data = $request->validate([
      'phone' => 'nullable|string|max:50',
    ]);

    $phoneNormalized = preg_replace('/[^0-9]/', '', $data['phone'] ?? '');
    $blacklisted = $phoneNormalized && Blacklist::where('phone', $phoneNormalized)
      ->orWhere('blacklistNoHp', $phoneNormalized)
      ->exists();

    if ($blacklisted) {
      return response()->json([
        'message' => 'Mohon Maaf, Nomor telepon yang anda masukan untuk saat ini tidak bisa digunakan. Mohon untuk menggunakan nomor telepon lain.',
        'blacklisted' => true,
      ], 422);
    }

    return response()->json(['blacklisted' => false]);
  }

  public function index()
  {
    $this->autoCancelExpiredPendingOrders();

    // Eager-load detailOrders (single source of truth) and attach it to `items` for compatibility
    $orders = Order::with('detailOrders.menu')->orderBy('created_at', 'desc')->get();

    // ensure `items` relation mirrors `detailOrders` so older code still works
    $orders->each(function ($order) {
      if ($order->relationLoaded('detailOrders')) {
        $order->setRelation('items', $order->getRelation('detailOrders'));
      }
    });

    // Fallback for legacy `order_items` table: if detailOrders are empty, map legacy rows
    if (Schema::hasTable('order_items')) {
      // preload all legacy rows grouped by order_id to minimize queries
      $legacyRows = DB::table('order_items')->get()->groupBy('order_id');

      // preload menu names for any referenced menu_ids
      $menuIds = $legacyRows->flatten(1)->pluck('menu_id')->unique()->filter()->values()->all();
      $menus = [];
      if (!empty($menuIds)) {
        $menus = Menu::whereIn('id', $menuIds)->get()->keyBy('id');
      }

      $orders->each(function ($order) use ($legacyRows, $menus) {
        $rows = $legacyRows->get($order->id, collect());
        if ($rows && $rows->isNotEmpty()) {
          $mapped = $rows->map(function ($r) use ($menus) {
            return (object) [
              'id' => $r->id,
              'menu_id' => $r->menu_id,
              'menu_name' => isset($menus[$r->menu_id]) ? $menus[$r->menu_id]->name : null,
              'quantity' => $r->quantity,
              'order_item_quantity' => $r->quantity,
              'subtotal' => $r->subtotal,
              'order_subtotal' => $r->subtotal,
              'created_at' => $r->created_at,
              'updated_at' => $r->updated_at,
            ];
          });

          // attach mapped collection to both relation names expected by views/controllers
          $order->setRelation('detailOrders', $mapped);
          $order->setRelation('items', $mapped);
        }
      });
    }

    $this->autoCancelExpiredPendingOrders();

    return view('orders.index', compact('orders'));
  }

  protected function autoCancelExpiredPendingOrders()
  {
    $expiredOrders = Order::with('detailOrders')
      ->where('status', 'pending_payment')
      ->where('created_at', '<', now()->subSeconds(30))
      ->get();

    foreach ($expiredOrders as $order) {
      $this->cancelPendingOrderInstance($order);
    }
  }

  protected function cancelPendingOrderInstance(Order $order)
  {
    if ($order->status !== 'pending_payment') {
      return;
    }

    DB::beginTransaction();
    try {
      foreach ($order->detailOrders as $detail) {
        $detail->order_status = 'cancelled';
        $detail->save();

        $menu = Menu::find($detail->menu_id);
        if ($menu) {
          $menu->stock = max(0, intval($menu->stock) + intval($detail->order_item_quantity));
          $menu->save();
        }
      }

      $order->status = 'cancelled';
      $order->save();
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
    }
  }

  public function store(Request $request)
  {
    $cartInput = $request->input('cart');
    if (is_string($cartInput)) {
      $cartInput = json_decode($cartInput, true);
    }

    $customerInput = $request->input('customer');
    if (is_string($customerInput)) {
      $customerInput = json_decode($customerInput, true);
    }

    $request->merge([
      'cart' => is_array($cartInput) ? $cartInput : [],
      'customer' => is_array($customerInput) ? $customerInput : [],
      'table_no' => $request->input('table_no'),
    ]);

    $hasTableNo = !empty($request->input('table_no'));

    $rules = [
      'cart' => 'required|array',
      'customer.name' => 'nullable|string|max:191',
      'customer.phone' => 'nullable|string|max:50',
      'customer.address' => 'nullable|string|max:500',
      'table_no' => 'nullable|integer',
      'payment_proof' => 'nullable|image|max:4096',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'The given data was invalid.',
        'errors' => $validator->errors(),
      ], 422);
    }

    $data = $validator->validated();

    $cart = $request->input('cart', []);

    if (empty($cart) || !is_array($cart)) {
      return response()->json(['message' => 'Cart is empty or invalid'], 422);
    }

    // normalize cart: ensure keys are integers (menu ids) and values are positive ints
    $normalized = [];
    foreach ($cart as $key => $val) {
      $menuId = intval($key);
      $qty = max(0, intval($val));
      if ($menuId > 0 && $qty > 0) {
        $normalized[$menuId] = ($normalized[$menuId] ?? 0) + $qty;
      }
    }

    if (empty($normalized)) {
      return response()->json(['message' => 'Cart contains no valid items'], 422);
    }

    DB::beginTransaction();
    try {
      $total = 0;
      $custName = data_get($data, 'customer.name');
      $custPhone = data_get($data, 'customer.phone');
      $custAddr = data_get($data, 'customer.address');
      $tableNo = $request->input('table_no');

      // Only write the canonical/alias columns you keep in the schema
      $orderStatus = empty($tableNo) ? 'pending_payment' : 'placed';
      $order = Order::create([
        'admin_id' => auth()->check() ? auth()->id() : null,
        'total_price' => 0,
        'status' => $orderStatus,
        'nama_customer' => $custName,
        'customer_no_hp' => preg_replace('/[^0-9]/', '', $custPhone),
        'customer_alamat' => $custAddr,
        'no_table' => $tableNo,
      ]);

      $detailOrderStatus = $orderStatus === 'pending_payment' ? 'pending_payment' : 'placed';
      foreach ($normalized as $menuId => $qty) {
        // lock the menu row for concurrency safety
        $menu = Menu::where('id', $menuId)->lockForUpdate()->first();
        if (!$menu) {
          DB::rollBack();
          return response()->json(['message' => "Menu item {$menuId} not found"], 404);
        }

        if ($qty <= 0) continue;
        if ($menu->stock < $qty) {
          DB::rollBack();
          return response()->json(['message' => "Not enough stock for {$menu->name}"], 422);
        }

        $unit = $menu->price;
        $sub = $unit * $qty;

        // create detail order via relation to ensure correct order_id
        $order->detailOrders()->create([
          'menu_id' => $menu->id,
          'order_item_quantity' => $qty,
          'order_subtotal' => $sub,
          'order_type' => empty($tableNo) ? 'dirumah' : 'ditempat',
          'order_status' => $detailOrderStatus,
          'order_date' => now(),
        ]);

        // reduce stock
        $menu->stock = max(0, $menu->stock - $qty);
        $menu->save();

        $total += $sub;
      }

      // update canonical total_price
      $order->total_price = $total;
      if ($request->hasFile('payment_proof')) {
        if ($order->payment_proof_path) {
          Storage::disk('public')->delete($order->payment_proof_path);
        }
        $proofPath = $request->file('payment_proof')->store('orders/payment_proofs', 'public');
        $order->payment_proof_path = $proofPath;
        if ($order->status === 'pending_payment') {
          $order->status = 'placed';
          foreach ($order->detailOrders as $detail) {
            $detail->order_status = 'placed';
            $detail->save();
          }
        }
      }
      $order->save();

      DB::commit();
      return response()->json(['message' => 'Order placed', 'order' => $order]);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Failed to place order', 'error' => $e->getMessage()], 500);
    }
  }

  public function uploadPaymentProof(Request $request, $id)
  {
    $request->validate([
      'payment_proof' => 'required|image|max:4096',
    ]);

    $order = Order::with('detailOrders')->findOrFail($id);
    if ($order->payment_proof_path) {
      Storage::disk('public')->delete($order->payment_proof_path);
    }

    $path = $request->file('payment_proof')->store('orders/payment_proofs', 'public');
    $order->payment_proof_path = $path;

    if ($order->status === 'pending_payment') {
      $order->status = 'placed';
      foreach ($order->detailOrders as $detail) {
        $detail->order_status = 'placed';
        $detail->save();
      }
    }

    $order->save();

    return response()->json(['message' => 'Payment proof uploaded', 'order' => $order]);
  }

  public function cancelPending(Request $request, $id)
  {
    $order = Order::with('detailOrders')->findOrFail($id);

    if ($order->status !== 'pending_payment') {
      return response()->json(['message' => 'Order cannot be cancelled'], 400);
    }

    $this->cancelPendingOrderInstance($order);

    return response()->json(['message' => 'Order cancelled', 'order' => $order]);
  }
  public function cancelPlaced(Request $request, $id)
  {
    $order = Order::with('detailOrders')->findOrFail($id);

    // Pelanggan hanya boleh membatalkan order yang masih placed.
    if ($order->status !== 'placed') {
      return response()->json([
        'message' => 'Order tidak dapat dibatalkan.'
      ], 400);
    }

    DB::beginTransaction();

    try {
      foreach ($order->detailOrders as $detail) {
        $detail->order_status = 'cancelled';
        $detail->save();

        $menu = Menu::find($detail->menu_id);

        if ($menu) {
          $menu->stock = max(
            0,
            intval($menu->stock) +
              intval($detail->order_item_quantity)
          );

          $menu->save();
        }
      }

      $order->status = 'cancelled';
      $order->save();

      DB::commit();

      return response()->json([
        'message' => 'Order berhasil dibatalkan.',
        'order' => $order
      ]);
    } catch (\Exception $e) {
      DB::rollBack();

      return response()->json([
        'message' => 'Gagal membatalkan order.'
      ], 500);
    }
  }
}
