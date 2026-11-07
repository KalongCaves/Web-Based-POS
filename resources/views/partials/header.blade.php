<style>
  .container {
    width: 100%;
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 1.25rem
  }

  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 0;
    border-bottom: 1px solid #f1f1f1;
    background: #fff;
    position: relative;
    z-index: 20
  }

  .brand {
    display: flex;
    align-items: center;
    gap: .85rem;
    font-weight: 700;
    font-size: 1.15rem
  }

  .brand-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 1rem;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 24px 60px rgba(0, 0, 0, .12);
    border: 1px solid rgba(255, 211, 185, .6);
    color: #fb7e00
  }

  .nav {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    font-size: .95rem;
    font-weight: 500;
    color: rgba(17, 24, 39, .8)
  }

  .nav-link {
    color: rgba(17, 24, 39, .9);
    font-weight: 600;
    transition: color .15s ease
  }

  .nav-link:hover {
    color: #fb7e00
  }

  .nav-link.active {
    color: #111827;
    border-bottom: 2px solid #fb7e00;
    padding-bottom: .35rem
  }

  .nav .button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .85rem 1.5rem;
    border-radius: 9999px;
    background: #fb7e00;
    color: #fff;
    font-weight: 600
  }

  .nav .button:hover {
    background: #ea6800
  }
</style>

<div class="container">
  <header class="header">
    <a href="{{ route('home') }}" class="brand">
      <span class="brand-icon">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M8 3H16L20 7V20H4V7L8 3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M7 10H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
          <path d="M9 14H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        </svg>
      </span>
      Kedai Anda
    </a>
    <nav class="nav">
      <a href="{{ route('home') }}" class="nav-link"></a>
      <a href="{{ route('dashboard') }}" class="nav-link">Menu</a>
      <a href="{{ route('orders.index') }}" class="nav-link">Orders</a>
      @if (auth()->check() && auth()->user()->isAdmin())
      <a href="{{ route('admin.dashboard') }}" class="nav-link">Admin</a>
      <form method="POST" action="{{ route('logout') }}" style="display:inline;margin:0">
        @csrf
        <button type="submit" class="button">Logout</button>
      </form>
      @else
      @if (Route::has('login'))
      <a href="{{ route('login') }}" class="button">Login</a>
      @endif
      @endif
    </nav>
  </header>
</div>