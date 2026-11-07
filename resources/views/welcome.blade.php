<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Snack Stall — {{ config('app.name', 'Laravel') }}</title>
  <link rel="icon" href="/favicon.ico" sizes="any">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0
    }

    html {
      font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
      line-height: 1.5;
      -webkit-text-size-adjust: 100%
    }

    body {
      min-height: 100vh;
      background: #f9fafb;
      color: #111827
    }

    a {
      text-decoration: none;
      color: inherit
    }

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
      padding: 1.5rem 0
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 0.85rem;
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
      font-size: 0.95rem;
      font-weight: 500;
      color: rgba(17, 24, 39, .8)
    }

    .nav .button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.85rem 1.5rem;
      border-radius: 9999px;
      background: #fb7e00;
      color: #fff;
      font-weight: 600;
      transition: background .2s ease
    }

    .nav .button:hover {
      background: #ea6800
    }

    .hero {
      position: relative;
      background: linear-gradient(135deg, #ff7a00 0%, #ff3d00 100%);
      color: #fff;
      overflow: hidden
    }

    .hero::after {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at top right, rgba(255, 255, 255, .25), transparent 35%)
    }

    .hero-content {
      position: relative;
      display: grid;
      grid-template-columns: 1.2fr .8fr;
      gap: 3rem;
      align-items: center;
      padding: 5rem 0
    }

    .hero-tag {
      display: inline-flex;
      align-items: center;
      gap: 0.65rem;
      padding: 0.75rem 1rem;
      border-radius: 9999px;
      background: rgba(255, 255, 255, .15);
      font-size: 0.95rem;
      font-weight: 600;
      backdrop-filter: blur(12px)
    }

    .hero-tag svg {
      width: 1.1rem;
      height: 1.1rem;
      stroke: currentColor;
      stroke-width: 1.7
    }

    .hero-title {
      font-size: clamp(3rem, 4.5vw, 5.25rem);
      line-height: 1.02;
      font-weight: 800;
      margin: 1.25rem 0
    }

    .hero-copy {
      max-width: 40rem;
      font-size: 1.05rem;
      line-height: 1.9;
      color: rgba(255, 255, 255, .92);
      margin-bottom: 2rem
    }

    .hero-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem
    }

    .button-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.95rem 1.8rem;
      border-radius: 9999px;
      background: #fff;
      color: #111827;
      font-weight: 700;
      box-shadow: 0 20px 60px rgba(0, 0, 0, .14);
      transition: transform .2s ease, background .2s ease
    }

    .button-primary:hover {
      transform: translateY(-1px);
      background: #f7efe5
    }

    .button-secondary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.95rem 1.8rem;
      border-radius: 9999px;
      background: #111827;
      color: #fff;
      font-weight: 700;
      transition: transform .2s ease, background .2s ease
    }

    .button-secondary:hover {
      transform: translateY(-1px);
      background: #090a0b
    }

    .hero-card {
      position: relative;
      border-radius: 2rem;
      background: rgba(255, 255, 255, .12);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, .18);
      padding: 2rem;
      box-shadow: 0 30px 80px rgba(0, 0, 0, .14);
    }

    .hero-card h2 {
      font-size: 1.8rem;
      font-weight: 700;
      margin: 0.2rem 0 0.95rem
    }

    .hero-card p {
      color: rgba(255, 255, 255, .82);
      line-height: 1.85;
      margin-bottom: 1.25rem
    }

    .hero-card-grid {
      display: grid;
      gap: 1rem
    }

    .hero-card-item {
      border-radius: 1.5rem;
      background: rgba(255, 255, 255, .14);
      padding: 1rem
    }

    .hero-card-item p {
      margin: 0;
      font-size: 0.95rem;
      color: rgba(255, 255, 255, .85)
    }

    .hero-card-item strong {
      display: block;
      margin-top: 0.45rem;
      font-weight: 700
    }

    .features {
      padding: 4.5rem 0;
      background: #f9fafb
    }

    .features-grid {
      display: grid;
      gap: 1.25rem;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr))
    }

    .feature-card {
      background: #fff;
      border: 1px solid rgba(243, 228, 216, .8);
      border-radius: 1.75rem;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 30px 60px rgba(0, 0, 0, .06)
    }

    .feature-icon {
      width: 3.5rem;
      height: 3.5rem;
      margin: 0 auto 1rem;
      border-radius: 9999px;
      background: #fff2e6;
      color: #fb7e00;
      display: flex;
      align-items: center;
      justify-content: center
    }

    .feature-icon svg {
      width: 1.1rem;
      height: 1.1rem;
      stroke: currentColor;
      stroke-width: 1.8
    }

    .feature-title {
      font-size: 1rem;
      font-weight: 700;
      margin-bottom: 0.65rem
    }

    .feature-text {
      font-size: 0.96rem;
      line-height: 1.85;
      color: #6b7280
    }

    @media (max-width: 900px) {
      .hero-content {
        grid-template-columns: 1fr;
        gap: 2rem;
        padding: 3rem 0
      }

      .hero-card {
        margin: 0 auto;
        max-width: 26rem
      }
    }

    @media (max-width: 640px) {
      .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem
      }

      .nav {
        flex-wrap: wrap;
        gap: 0.75rem
      }

      .hero-content {
        padding: 2.5rem 0
      }

      .hero-title {
        font-size: 2.8rem
      }
    }
  </style>
</head>

<body>
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
        Snack Stall
      </a>
      <nav class="nav">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('dashboard') }}">Menu</a>
        @if (Route::has('login'))
        <a href="{{ route('login') }}" class="button">Login</a>
        @endif
      </nav>
    </header>
  </div>
  <main class="hero">
    <div class="container">
      <div class="hero-content">
        <div>
          <div class="hero-tag">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6 7V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M4 7H20V21H4V7Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M9 11H15" stroke="currentColor" stroke-linecap="round" />
              <path d="M9 15H13" stroke="currentColor" stroke-linecap="round" />
            </svg>
            Welcome to Snack Stall
          </div>
          <h1 class="hero-title">Your favorite snacks, just a click away</h1>
          <p class="hero-copy">Pilih camilan favoritmu. Pesan cepat. Nikmati langsung. Semua tersedia dengan pengalaman belanja yang mudah dan aman.</p>
          <div class="hero-actions">
            <a href="{{ route('dashboard') }}" class="button-primary">Browse Menu</a>
            @if (Route::has('login'))
            <a href="{{ route('login') }}" class="button-secondary">Login to Order</a>
            @endif
          </div>
        </div>
        <div class="hero-card">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
            <div>
              <p style="font-size:0.82rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:rgba(255,255,255,.78);margin-bottom:0.65rem;">Best Seller</p>
              <h2 style="font-size:1.75rem;font-weight:700;line-height:1.1;">Snack Bundle</h2>
            </div>
            <div style="width:3.5rem;height:3.5rem;border-radius:1.5rem;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:#fff;">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:1.1rem;height:1.1rem;">
                <path d="M7 8H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                <path d="M7 12H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                <path d="M7 16H11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
              </svg>
            </div>
          </div>
          <p style="font-size:0.96rem;line-height:1.85;color:rgba(255,255,255,.82);margin-bottom:1.5rem;">Kumpulan camilan favorit dengan potongan harga spesial buat kamu yang ingin pesan cepat.</p>
          <div class="hero-card-grid">
            <div class="hero-card-item">
              <p>Camilan</p><strong>Keripik Pedas</strong>
            </div>
            <div class="hero-card-item">
              <p>Minuman</p><strong>Es Teh Segar</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <section class="features">
    <div class="container">
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M4 7H20V17H4V7Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M4 11H20" stroke="currentColor" stroke-linecap="round" />
            </svg>
          </div>
          <div class="feature-title">Wide Selection</div>
          <div class="feature-text">Choose from a variety of snacks and beverages.</div>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5 12H19" stroke="currentColor" stroke-linecap="round" />
              <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <div class="feature-title">Quick Ordering</div>
          <div class="feature-text">Order in seconds and track your order status.</div>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 3C7.58172 3 4 6.58172 4 11C4 15.4183 7.58172 19 12 19C16.4183 19 20 15.4183 20 11C20 6.58172 16.4183 3 12 3Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M12 7V11L14.5 13.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <div class="feature-title">Real-time Stock</div>
          <div class="feature-text">See available stock before placing your order.</div>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5 11V7C5 4.79086 6.79086 3 9 3H15C17.2091 3 19 4.79086 19 7V11" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M7 11V19H17V11" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <div class="feature-title">Secure Access</div>
          <div class="feature-text">Login required to ensure order accuracy.</div>
        </div>
      </div>
    </div>
  </section>
</body>

</html>