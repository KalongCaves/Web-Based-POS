<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\Menu;

class OrderController extends Controller
{
  public function index()
  {
    $orders = Order::with('detailOrders.menu')->orderBy('created_at', 'desc')->get();

    // fallback to legacy `order_items` when detail_orders are not present
    if (Schema::hasTable('order_items')) {
      $legacyRows = DB::table('order_items')->get()->groupBy('order_id');
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

          $order->setRelation('detailOrders', $mapped);
          $order->setRelation('items', $mapped);
        }
      });
    }

    return response()->json($orders);
  }

  public function uploadQr(Request $request, $id)
  {
    $data = $request->validate([
      'qr_image' => 'required|image|max:4096',
    ]);

    $order = Order::findOrFail($id);

    if ($order->qris_image_path) {
      Storage::disk('public')->delete($order->qris_image_path);
    }

    $path = $request->file('qr_image')->store('orders/qris', 'public');
    $order->qris_image_path = $path;
    $order->save();

    return response()->json(['message' => 'QR uploaded', 'order' => $order]);
  }

  public function updateStatus(Request $request, $id)
  {
    $data = $request->validate([
      'status' => 'required|string|max:50'
    ]);

    DB::beginTransaction();
    try {
      $order = Order::with('detailOrders')->findOrFail($id);
      $previousStatus = $order->status;
      $order->status = $data['status'];
      $order->save();

      foreach ($order->detailOrders as $do) {
        $do->order_status = $data['status'];
        $do->save();

        if ($data['status'] === 'cancelled' && $previousStatus !== 'cancelled') {
          $qty = max(0, intval($do->order_item_quantity));
          if ($qty > 0) {
            $menu = Menu::find($do->menu_id);
            if ($menu) {
              $menu->stock = max(0, intval($menu->stock) + $qty);
              $menu->save();
            }
          }
        }
      }

      DB::commit();
      return response()->json(['message' => 'Status updated', 'order' => $order]);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Failed', 'error' => $e->getMessage()], 500);
    }
  }

  public function updatePaymentText(Request $request)
  {
    $data = $request->validate([
      'payment_text' => 'required|string|max:1000'
    ]);

    Setting::set('payment_text', $data['payment_text']);

    return response()->json(['message' => 'Payment text updated successfully']);
  }

  public function getPaymentText()
  {
    $text = Setting::get('payment_text', '');
    return response()->json(['payment_text' => $text]);
  }
}
