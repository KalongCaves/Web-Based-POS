<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  @php $title = 'Admin Dashboard'; @endphp
  @include('partials.head')
  <style>
    /* copy of dashboard styles to ensure admin page layout matches */
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
      padding: 1.5rem 0;
      border-bottom: 1px solid #f1f1f1;
      background: #fff;
      position: relative;
      z-index: 20
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
      transition: color .15s ease
    }

    .nav-link:hover {
      color: #fb7e00
    }

    .nav-link.active {
      color: #111827;
      border-bottom: 2px solid #fb7e00;
      padding-bottom: 0.35rem
    }

    .nav .button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.85rem 1.5rem;
      border-radius: 9999px;
      background: #fb7e00;
      color: #fff;
      font-weight: 600
    }

    .nav .button:hover {
      background: #ea6800
    }

    .page-content {
      padding: 2rem 0 4rem
    }

    .admin-tab-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.5rem 1rem;
      border-radius: 9999px;
      background: #ffffff;
      color: #0f172a;
      font-weight: 600;
      border: 1px solid transparent;
      cursor: pointer;
      transition: all 0.12s ease;
    }

    .admin-tab-button:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
    }

    .admin-tab-button.border {
      border: 1px solid #e5e7eb;
      background: #fff;
    }

    /* Active/selected variants (JS toggles Tailwind-like classes) */
    .admin-tab-button.bg-sky-600 {
      background: #0ea5e9 !important;
    }

    .admin-tab-button.bg-sky-700 {
      background: #0369a1 !important;
    }

    .admin-tab-button.text-white {
      color: #ffffff !important;
    }

    .admin-tab-button.border-sky-700 {
      border-color: #ff8c00 !important;
    }

    .admin-tab-button:hover,
    .admin-tab-button.active {
      background: #ff9100 !important;
      color: #ffffff !important;
      border-color: #ff8400 !important;
      box-shadow: 0 8px 24px rgba(3, 105, 161, 0.08);
      transform: translateY(-1px);
    }

    .dashboard-card {
      background: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 1.75rem;
      box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
      overflow: hidden;
    }

    .dashboard-card-inner {
      padding: 1.5rem;
    }

    .dashboard-card-title {
      font-size: 1rem;
      font-weight: 700;
      color: #111827;
      margin-bottom: 0.5rem;
    }

    .dashboard-card-description {
      font-size: 0.95rem;
      color: #6b7280;
      line-height: 1.5;
    }

    .dashboard-metric-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .dashboard-metric-card {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      padding: 1.35rem 1.5rem;
      border-radius: 1.75rem;
      border: 1px solid rgba(148, 163, 184, 0.16);
      background: #ffffff;
      box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
      min-height: 120px;
      align-items: flex-start;
    }

    .dashboard-metric-label {
      font-size: 0.72rem;
      color: #6b7280;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      margin-bottom: 0.75rem;
    }

    .dashboard-metric-value {
      font-size: 2.3rem;
      font-weight: 800;
      color: #111827;
      line-height: 1;
    }

    .dashboard-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.5rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.78rem;
      font-weight: 700;
      white-space: nowrap;
      min-height: 36px;
      min-width: 80px;
    }

    .dashboard-icon-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 64px;
      height: 64px;
      border-radius: 50%;
      border: 1px solid rgba(148, 163, 184, 0.28);
      background: #eff6ff;
      color: #1d4ed8;
      font-size: 1.4rem;
      box-shadow: 0 12px 26px rgba(15, 23, 42, 0.06);
    }

    .finance-toolbar {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .finance-filter {
      border: 1px solid #e2e8f0;
      background: #ffffff;
      color: #475569;
      font-weight: 700;
      border-radius: 9999px;
      padding: 0.75rem 1rem;
      transition: all 0.15s ease;
      cursor: pointer;
      white-space: nowrap;
    }

    .finance-filter:hover,
    .finance-filter.active {
      border-color: #ff8400;
      background: #ff8400;
      color: #ffffff;
      box-shadow: 0 10px 24px rgba(14, 165, 233, 0.12);
    }

    .finance-input {
      width: 100%;
      padding: 0.9rem 1rem;
      border: 1px solid #e2e8f0;
      border-radius: 0.95rem;
      background: #f8fafc;
      color: #0f172a;
      font-size: 0.95rem;
      outline: none;
      transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .finance-input:focus {
      border-color: #0ea5e9;
      box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
    }

    .finance-submit {
      border: none;
      border-radius: 0.95rem;
      background: #0ea5e9;
      color: white;
      padding: 0.95rem 1.2rem;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.15s ease, transform 0.15s ease;
    }

    .finance-submit:hover {
      background: #0284c7;
      transform: translateY(-1px);
    }

    .finance-action-btn {
      border: none;
      border-radius: 1rem;
      background: #0ea5e9;
      color: #ffffff;
      font-weight: 700;
      padding: 0.85rem 1.2rem;
      box-shadow: 0 10px 24px rgba(14, 165, 233, 0.16);
      cursor: pointer;
      transition: background 0.15s ease, transform 0.15s ease;
    }

    .finance-action-btn:hover {
      background: #0284c7;
      transform: translateY(-1px);
    }

    .finance-summary-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .finance-summary-card {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 1rem;
      padding: 1.35rem 1.5rem;
      border-radius: 1.75rem;
      border: 1px solid rgba(148, 163, 184, 0.16);
      background: #ffffff;
      box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
      min-height: 125px;
    }

    .finance-summary-card>div {
      min-width: 0;
    }

    .finance-chart-period {
      font-size: 0.92rem;
      color: #64748b;
      margin-bottom: 0.85rem;
    }

    .finance-summary-value {
      font-size: 1.7rem;
      font-weight: 800;
      margin: 0.25rem 0 0.35rem;
      color: #111827;
      line-height: 1.1;
    }

    .finance-summary-card {
      min-height: 120px;
    }

    .finance-charts-grid {
      display: grid;
      grid-template-columns: 1.4fr 0.9fr;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .finance-expense-panel {
      border: 1px solid #e5e7eb;
      border-radius: 1.25rem;
      padding: 1rem;
      background: #f8fafc;
    }

    @media (max-width: 960px) {
      .finance-charts-grid {
        grid-template-columns: 1fr;
      }
    }

    #finance-chart,
    #finance-pie {
      background: transparent;
      border-radius: 1.5rem;
      border: none;
      box-shadow: none;
      overflow: hidden;
    }

    #finance-pie-legend {
      display: grid;
      gap: 0.65rem;
      margin-top: 1rem;
      color: #475569;
    }

    #finance-expense-table {
      margin-top: 1rem;
      border-radius: 0.95rem;
      overflow: hidden;
      background: #ffffff;
      border: 1px solid #e5e7eb;
      box-shadow: 0 14px 36px rgba(15, 23, 42, 0.06);
    }

    #finance-expense-table table {
      width: 100%;
      border-collapse: collapse;
    }

    #finance-expense-table th,
    #finance-expense-table td {
      padding: 0.95rem 1rem;
      border-bottom: 1px solid #e5e7eb;
      color: #334155;
    }

    #finance-expense-table th {
      background: #f8fafc;
      text-align: left;
      font-size: 0.85rem;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }

    #finance-expense-table tr:last-child td {
      border-bottom: none;
    }

    #finance-expense-table button {
      border: none;
      background: #ef4444;
      color: #ffffff;
      padding: 0.55rem 0.85rem;
      border-radius: 0.85rem;
      cursor: pointer;
    }

    .dashboard-metric-card .metric-meta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      width: 100%;
    }

    .dashboard-metric-card .metric-text {
      min-width: 0;
    }

    .dashboard-metric-note {
      font-size: 0.87rem;
      color: #64748b;
    }

    .dashboard-list-item {
      padding: 1rem 0;
      border-bottom: 1px solid #f3f4f6;
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 1rem;
      align-items: center;
    }

    .dashboard-list-item:last-child {
      border-bottom: none;
    }

    .dashboard-list-title {
      font-weight: 700;
      color: #111827;
      margin-bottom: 0.25rem;
    }

    .dashboard-list-subtitle {
      color: #6b7280;
      font-size: 0.9rem;
    }

    .dashboard-pill {
      display: inline-flex;
      align-items: center;
      padding: 0.4rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.8rem;
      font-weight: 700;
      background: #fef3c7;
      color: #b45309;
    }
  </style>
</head>

<body>
  @include('partials.header')

  <div class="container page-content">
    @include('dashboard.admin', ['salesMetrics' => $salesMetrics, 'productMenu' => $productMenu])
  </div>
</body>

</html>