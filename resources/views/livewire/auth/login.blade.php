<x-layouts::auth :title="__('Log in')">
  <style>
    /* Page background to match dashboard/menu pages */
    body {
      background: #f9fafb !important;
      color: #111827 !important
    }

    .login-card {
      max-width: 420px;
      margin: 2.5rem auto;
      background: #fff;
      padding: 2rem;
      border-radius: 14px;
      box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
      text-align: center
    }

    .login-icon {
      width: 72px;
      height: 72px;
      border-radius: 999px;
      background: #fff3ea;
      display: grid;
      place-items: center;
      margin: 0 auto 0.6rem
    }

    .login-title {
      font-size: 1.5rem;
      font-weight: 800;
      margin-bottom: 0.6rem
    }

    .login-input {
      width: 100%;
      padding: 0.9rem 1rem;
      border-radius: 10px;
      border: 1px solid #eee;
      margin-bottom: 0.75rem;
      font-size: 0.95rem;
      color: #111827 !important;
    }

    /* placeholder and typed text visibility */
    .login-input::placeholder {
      color: #9ca3af !important;
      opacity: 1
    }

    .login-input:-webkit-autofill,
    .login-input:-webkit-autofill:hover,
    .login-input:-webkit-autofill:focus {
      -webkit-text-fill-color: #111827 !important
    }

    .login-button {
      width: 100%;
      background: #fb7e00;
      color: #fff;
      padding: 0.9rem;
      border-radius: 10px;
      border: none;
      font-weight: 700;
      font-size: 1rem
    }

    .login-button:hover {
      background: #ea6800
    }

    .login-small {
      font-size: 0.9rem;
      color: #6b7280;
      margin-top: 0.6rem
    }

    .login-link {
      color: #fb7e00;
      font-weight: 700;
      text-decoration: none
    }
  </style>

  <div class="login-card">
    <div class="login-icon">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2C9.243 2 7 4.243 7 7C7 9.757 9.243 12 12 12C14.757 12 17 9.757 17 7C17 4.243 14.757 2 12 2Z" stroke="#fb7e00" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M4 22C4 17.581 7.581 14 12 14C16.419 14 20 17.581 20 22" stroke="#fb7e00" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </div>
    <div class="login-title">Login</div>

    @if(session('status'))
    <div style="margin-bottom:.75rem;color:#065f46;font-weight:600">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
      @csrf

      <label style="display:block;text-align:left;font-size:0.85rem;color:#374151;margin-bottom:.25rem">Username</label>
      <input name="phone" type="text" class="login-input" placeholder="Enter your username" value="{{ old('phone') }}" required autofocus />
      @error('phone')<div style="color:#ef4444;font-size:.85rem;margin-bottom:.5rem">{{ $message }}</div>@enderror

      <label style="display:block;text-align:left;font-size:0.85rem;color:#374151;margin-bottom:.25rem">Password</label>
      <input name="password" type="password" class="login-input" placeholder="Enter your password" required autocomplete="current-password" />
      @error('password')<div style="color:#ef4444;font-size:.85rem;margin-bottom:.5rem">{{ $message }}</div>@enderror

      <div style="margin-top:.5rem">
        <button type="submit" class="login-button">Login</button>
      </div>
    </form>

    <!-- registration disabled -->
  </div>
</x-layouts::auth>