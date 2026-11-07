<?php

use App\Models\DetailOrder;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('delivery orders with invalid name or phone are rejected', function () {
  $response = $this->withHeader('Accept', 'application/json')
    ->postJson(route('orders.store'), [
      'cart' => [1 => 1],
      'customer' => [
        'name' => '1234',
        'phone' => '12345',
        'address' => 'Jl. Merdeka',
      ],
    ]);

  $response->assertStatus(422);
  $response->assertJsonValidationErrors(['customer.name', 'customer.phone']);
});

test('delivery orders can be created without payment proof and remain pending', function () {
  Storage::fake('public');
  $menu = Menu::create([
    'name' => 'Test Menu',
    'category' => 'food',
    'price' => 10000,
    'stock' => 3,
    'description' => 'Test',
  ]);

  $response = $this->post(route('orders.store'), [
    'cart' => [$menu->id => 1],
    'customer' => [
      'name' => 'Test Customer',
      'phone' => '081234567890',
      'address' => 'Jl. Test',
    ],
  ]);

  $response->assertStatus(200);
  $this->assertDatabaseCount('orders', 1);
  $this->assertSame('pending_payment', Order::find($response->json('order.id'))->status);
});

test('pending payment orders can be cancelled and restore stock', function () {
  Storage::fake('public');
  $menu = Menu::create([
    'name' => 'Test Menu',
    'category' => 'food',
    'price' => 10000,
    'stock' => 3,
    'description' => 'Test',
  ]);

  $response = $this->post(route('orders.store'), [
    'cart' => [$menu->id => 2],
    'customer' => [
      'name' => 'Test Customer',
      'phone' => '081234567890',
      'address' => 'Jl. Test',
    ],
  ]);

  $response->assertStatus(200);
  $orderId = $response->json('order.id');
  $this->assertSame('pending_payment', Order::find($orderId)->status);

  $cancelResponse = $this->post('/orders/' . $orderId . '/cancel');
  $cancelResponse->assertOk();

  $this->assertSame('cancelled', Order::find($orderId)->status);
  $this->assertSame(3, Menu::find($menu->id)->stock);
});

test('cancelling an order restores stock to the menu', function () {
  $admin = User::factory()->create(['is_admin' => true]);
  $this->actingAs($admin);

  $menu = Menu::create([
    'name' => 'Test Menu',
    'category' => 'food',
    'price' => 10000,
    'stock' => 3,
    'description' => 'Test',
  ]);

  $order = Order::create([
    'admin_id' => $admin->id,
    'total_price' => 20000,
    'status' => 'placed',
    'nama_customer' => 'Test',
    'customer_no_hp' => '081234567890',
    'customer_alamat' => 'Jl. Test',
  ]);

  DetailOrder::create([
    'order_id' => $order->id,
    'menu_id' => $menu->id,
    'order_item_quantity' => 2,
    'order_subtotal' => 20000,
    'order_type' => 'dirumah',
    'order_status' => 'placed',
    'order_date' => now(),
  ]);

  $response = $this->withHeader('Accept', 'application/json')
    ->patchJson('/admin/api/orders/' . $order->id . '/status', [
      'status' => 'cancelled',
    ]);

  $response->assertOk();
  $menu->refresh();
  $this->assertSame(5, $menu->stock);
  $this->assertSame('cancelled', $order->fresh()->status);
});
