<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests can access pelanggan dashboard', function () {
  $response = $this->get(route('dashboard'));
  $response->assertOk();
  $response->assertSee('Pilih Menu Favoritmu');
  $response->assertSee('Tambah ke keranjang');
});

test('authenticated users can visit the dashboard', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  $response = $this->get(route('dashboard'));
  $response->assertOk();
});

test('admin users see analytics sections', function () {
  $admin = User::factory()->create(['is_admin' => true]);
  $this->actingAs($admin);

  $response = $this->get(route('dashboard'));
  $response->assertOk();
  $response->assertSee('Analitik Penjualan');
  $response->assertSee('Grafik Penjualan');
  $response->assertSee('Model Pizza');
});

test('pelanggan users see menu selection cards', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  $response = $this->get(route('dashboard'));
  $response->assertOk();
  $response->assertSee('Pilih Menu Favoritmu');
  $response->assertSee('Tambah ke keranjang');
});
