<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;


Route::redirect('/', '/dashboard')->name('home');

Route::get('dashboard', function () {
  // load menus from database if the table exists; otherwise fall back to prototype
  if (Schema::hasTable('menus')) {
    $productMenu = \App\Models\Menu::orderBy('id')->get()->map(function ($m) {
      $imageUrl = null;
      if ($m->image_path && Storage::disk('public')->exists($m->image_path)) {
        $imageUrl = asset('storage/' . $m->image_path);
      }

      return [
        'id' => (string)$m->id,
        'title' => $m->name,
        'description' => $m->description,
        'badge' => $m->category,
        'category' => $m->category,
        'price' => ($m->price / 1),
        'stock' => (int)$m->stock,
        'icon' => '',
        'image_url' => $imageUrl,
        'action' => 'Tambahkan',
      ];
    })->toArray();

    $categories = array_merge(['All'], \App\Models\Menu::whereNotNull('category')->pluck('category')->unique()->filter()->values()->all());
  } else {
    $productMenu = [
      [
        'id' => 'classic-potato-chips',
        'title' => 'Classic Potato Chips',
        'description' => 'Crispy and salty potato chips.',
        'badge' => 'Chips',
        'category' => 'Chips',
        'price' => 1.50,
        'stock' => 50,
        'icon' => '🥔',
        'action' => 'Tambahkan',
      ],
      [
        'id' => 'chocolate-bar',
        'title' => 'Chocolate Bar',
        'description' => 'Smooth milk chocolate with a rich bite.',
        'badge' => 'Candy',
        'category' => 'Candy',
        'price' => 2.00,
        'stock' => 30,
        'icon' => '🍫',
        'action' => 'Tambahkan',
      ],
      [
        'id' => 'energy-drink',
        'title' => 'Energy Drink',
        'description' => 'Boost your energy with a refreshing sip.',
        'badge' => 'Beverages',
        'category' => 'Beverages',
        'price' => 3.00,
        'stock' => 25,
        'icon' => '🥤',
        'action' => 'Tambahkan',
      ],
      [
        'id' => 'cookies',
        'title' => 'Cookies',
        'description' => 'Freshly baked chocolate chip cookies.',
        'badge' => 'Baked Goods',
        'category' => 'Snacks',
        'price' => 2.50,
        'stock' => 40,
        'icon' => '🍪',
        'action' => 'Tambahkan',
      ],
      [
        'id' => 'pretzels',
        'title' => 'Pretzels',
        'description' => 'Salted twisted pretzels with crunchy texture.',
        'badge' => 'Snacks',
        'category' => 'Chips',
        'price' => 1.75,
        'stock' => 35,
        'icon' => '🥨',
        'action' => 'Tambahkan',
      ],
      [
        'id' => 'gummy-bears',
        'title' => 'Gummy Bears',
        'description' => 'Colorful fruit-flavored gummies.',
        'badge' => 'Candy',
        'category' => 'Candy',
        'price' => 1.25,
        'stock' => 60,
        'icon' => '🧸',
        'action' => 'Tambahkan',
      ],
      [
        'id' => 'bottled-water',
        'title' => 'Bottled Water',
        'description' => 'Pure mineral water to stay refreshed.',
        'badge' => 'Beverages',
        'category' => 'Beverages',
        'price' => 1.00,
        'stock' => 100,
        'icon' => '💧',
        'action' => 'Tambahkan',
      ],
      [
        'id' => 'popcorn',
        'title' => 'Popcorn',
        'description' => 'Buttery popcorn for crunchy snacks.',
        'badge' => 'Baked Goods',
        'category' => 'Snacks',
        'price' => 2.25,
        'stock' => 20,
        'icon' => '🍿',
        'action' => 'Tambahkan',
      ],
    ];
  }

  $salesMetrics = [
    'day' => [
      '08:00' => 2100000,
      '10:00' => 1450000,
      '12:00' => 3200000,
      '14:00' => 2500000,
      '16:00' => 1800000,
    ],
    'week' => [
      'Sen' => 3200000,
      'Sel' => 2850000,
      'Rab' => 3500000,
      'Kam' => 4100000,
      'Jum' => 3950000,
      'Sab' => 2750000,
      'Min' => 3050000,
    ],
    'month' => [
      'Minggu 1' => 12400000,
      'Minggu 2' => 13800000,
      'Minggu 3' => 15200000,
      'Minggu 4' => 14200000,
    ],
    'year' => [
      'Jan' => 48000000,
      'Feb' => 51500000,
      'Mar' => 53000000,
      'Apr' => 56000000,
    ],
  ];

  return view('dashboard', compact('productMenu', 'salesMetrics', 'categories'));
})->name('dashboard');

Route::get('admin', function () {
  // protect admin page: redirect to login when not authenticated or not admin
  if (!auth()->check() || !auth()->user()->isAdmin()) {
    return redirect()->route('login');
  }
  // reuse same data for admin view
  if (Schema::hasTable('menus')) {
    $productMenu = \App\Models\Menu::orderBy('id')->get()->map(function ($m) {
      return [
        'id' => (string)$m->id,
        'title' => $m->name,
        'description' => $m->description,
        'badge' => $m->category,
        'category' => $m->category,
        'price' => ($m->price / 1),
        'stock' => (int)$m->stock,
        'icon' => '',
        'action' => 'Tambahkan',
      ];
    })->toArray();
  } else {
    $productMenu = [];
  }

  $categories = array_merge(['All'], \App\Models\Menu::whereNotNull('category')->pluck('category')->unique()->filter()->values()->all());

  $salesMetrics = [
    'day' => [
      '08:00' => 2100000,
      '10:00' => 1450000,
      '12:00' => 3200000,
      '14:00' => 2500000,
      '16:00' => 1800000,
    ],
    'week' => [
      'Sen' => 3200000,
      'Sel' => 2850000,
      'Rab' => 3500000,
      'Kam' => 4100000,
      'Jum' => 3950000,
      'Sab' => 2750000,
      'Min' => 3050000,
    ],
    'month' => [
      'Minggu 1' => 12400000,
      'Minggu 2' => 13800000,
      'Minggu 3' => 15200000,
      'Minggu 4' => 14200000,
    ],
    'year' => [
      'Jan' => 48000000,
      'Feb' => 51500000,
      'Mar' => 53000000,
      'Apr' => 56000000,
    ],
  ];

  return view('admin_page', compact('productMenu', 'salesMetrics', 'categories'));
})->name('admin.dashboard');

use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\BlacklistController as AdminBlacklistController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders/check-blacklist', [OrderController::class, 'checkBlacklist'])->name('orders.blacklist-check');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::post('/orders/{id}/payment-proof', [OrderController::class, 'uploadPaymentProof']);
Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelPending']);
Route::post('/orders/{id}/cancel-placed', [OrderController::class, 'cancelPlaced']);

// admin API endpoints
Route::get('/admin/api/blacklists', [AdminBlacklistController::class, 'index'])->middleware('auth');
Route::post('/admin/api/blacklists', [AdminBlacklistController::class, 'store'])->middleware('auth');
Route::delete('/admin/api/blacklists/{id}', [AdminBlacklistController::class, 'destroy'])->middleware('auth');

Route::get('/admin/api/orders', [AdminOrderController::class, 'index'])->middleware('auth');
Route::post('/admin/api/orders/{id}/qr', [AdminOrderController::class, 'uploadQr'])->middleware('auth');
Route::patch('/admin/api/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->middleware('auth');
Route::patch('/admin/api/settings/payment-text', [AdminOrderController::class, 'updatePaymentText'])->middleware('auth');
Route::get('/admin/api/settings/payment-text', [AdminOrderController::class, 'getPaymentText']);

// finance endpoints
use App\Http\Controllers\Admin\FinanceController as AdminFinanceController;
use App\Http\Controllers\Admin\PengeluaranController as AdminPengeluaranController;

Route::get('/admin/api/finance', [AdminFinanceController::class, 'index'])->middleware('auth');
Route::post('/admin/api/pengeluarans', [AdminPengeluaranController::class, 'store'])->middleware('auth');
Route::delete('/admin/api/pengeluarans/{id}', [AdminPengeluaranController::class, 'destroy'])->middleware('auth');

// temporary debug route: show detail_orders rows for an order id (only when APP_DEBUG=true)
if (env('APP_DEBUG', false)) {
  Route::get('/debug/order-details/{id}', function ($id) {
    return response()->json(\Illuminate\Support\Facades\DB::table('detail_orders')->where('order_id', $id)->get());
  });
}

// admin menu management
Route::get('/admin/api/menus', [AdminMenuController::class, 'index'])->middleware('auth');
Route::post('/admin/api/menus', [AdminMenuController::class, 'store'])->middleware('auth');
Route::patch('/admin/api/menus/{id}', [AdminMenuController::class, 'update'])->middleware('auth');
Route::delete('/admin/api/menus/{id}', [AdminMenuController::class, 'destroy'])->middleware('auth');

// public menu listing for customer stock sync
Route::get('/api/menus', [AdminMenuController::class, 'index']);

require __DIR__ . '/settings.php';
