<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;

uses(RefreshDatabase::class);

test('login screen can be rendered', function () {
  $response = $this->get(route('login'));

  $response->assertOk();
});

test('users can authenticate using the login screen', function () {
  $user = User::factory()->create();

  $response = $this->post(route('login.store'), [
    'phone' => $user->phone,
    'password' => 'password',
  ]);

  $response
    ->assertSessionHasNoErrors()
    ->assertRedirect(route('dashboard', absolute: false));

  $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
  $user = User::factory()->create();

  $response = $this->post(route('login.store'), [
    'phone' => $user->phone,
    'password' => 'wrong-password',
  ]);

  $response->assertSessionHasErrorsIn('phone');

  $this->assertGuest();
});

test('admin can authenticate using admin phone and password', function () {
  $response = $this->post(route('login.store'), [
    'phone' => 'admin',
    'password' => 'admin123',
  ]);

  $response
    ->assertSessionHasNoErrors()
    ->assertRedirect(route('dashboard', absolute: false));

  $this->assertAuthenticated();
  $this->assertTrue(auth()->user()->isAdmin());
});

test('users with two factor enabled are redirected to two factor challenge', function () {
  $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

  $user = User::factory()->withTwoFactor()->create();

  $response = $this->post(route('login.store'), [
    'phone' => $user->phone,
    'password' => 'password',
  ]);

  $response->assertRedirect(route('two-factor.login'));
  $this->assertGuest();
});

test('users can logout', function () {
  $user = User::factory()->create();

  $response = $this->actingAs($user)->post(route('logout'));

  $response->assertRedirect(route('home'));

  $this->assertGuest();
});
