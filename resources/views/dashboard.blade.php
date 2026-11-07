<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  @php $title = 'Dashboard'; @endphp
  @include('partials.head')
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

    .nav-link {
      color: rgba(17, 24, 39, .9);
      font-weight: 600;
      transition: color .15s ease;
    }

    .nav-link:hover {
      color: #fb7e00;
    }

    .nav-link.active {
      color: #111827;
      border-bottom: 2px solid #fb7e00;
      padding-bottom: 0.35rem;
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

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem 0;
      border-bottom: 1px solid #f1f1f1;
      background: #fff;
      position: relative;
      z-index: 20;
    }

    .page-hero {
      padding: 3rem 0;
      background: linear-gradient(135deg, #ff7a00 0%, #ff3d00 100%);
      color: #fff
    }

    .page-hero .hero-body {
      display: grid;
      gap: 1rem;
      max-width: 860px
    }

    .page-hero .hero-title {
      font-size: clamp(2.25rem, 4vw, 3.5rem);
      line-height: 1.05;
      font-weight: 800;
      margin: 0;
    }

    .page-hero .hero-copy {
      max-width: 40rem;
      font-size: 1.05rem;
      line-height: 1.8;
      color: rgba(255, 255, 255, .92);
      margin: 0
    }

    .page-hero .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.85rem 1rem;
      border-radius: 9999px;
      background: rgba(255, 255, 255, .18);
      font-weight: 600;
      font-size: 0.95rem;
    }

    .page-content {
      padding: 2rem 0 4rem
    }

    @media (max-width:900px) {
      .page-hero {
        padding: 2rem 0
      }

      .page-hero .hero-body {
        gap: 1.25rem
      }
    }

    @media (max-width:640px) {
      .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem
      }

      .nav {
        flex-wrap: wrap;
        gap: 0.75rem
      }

      .page-hero {
        padding: 1.75rem 0
      }
    }
  </style>
</head>

<body>
  @include('partials.header')

  <div class="container page-content">
    @include('dashboard.pelanggan', ['productMenu' => $productMenu])
  </div>
  @include('partials.footer')
</body>

</html>