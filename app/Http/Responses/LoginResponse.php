<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
  /**
   * Create an HTTP response that represents the object.
   */
  public function toResponse($request)
  {
    // read intended url from session (if any)
    $intended = session('url.intended');

    // if intended points to an admin API endpoint, replace with admin dashboard
    if ($intended && str_contains(parse_url($intended, PHP_URL_PATH) ?? '', '/admin/api')) {
      $intended = route('admin.dashboard');
    }

    // clear intended to avoid reuse
    session()->forget('url.intended');

    $default = Fortify::redirects('login') ?? '/';

    $target = $intended ?? $default;

    return redirect()->to($target);
  }
}
