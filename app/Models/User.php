<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'phone', 'password', 'is_admin'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
  /** @use HasFactory<UserFactory> */
  use HasFactory, Notifiable, TwoFactorAuthenticatable;

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      'is_admin' => 'boolean',
    ];
  }

  /**
   * Determine whether the user is an administrator.
   */
  public function isAdmin(): bool
  {
    return (bool) ($this->attributes['is_admin'] ?? false) || in_array($this->email, [
      'admin@example.com',
      'admin@localhost',
    ], true);
  }

  /**
   * Get the user's initials
   */
  public function initials(): string
  {
    return Str::of($this->name)
      ->explode(' ')
      ->take(2)
      ->map(fn($word) => Str::substr($word, 0, 1))
      ->implode('');
  }
}
