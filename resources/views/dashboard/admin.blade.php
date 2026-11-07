@php
$periodOptions = [
'day' => 'Per Hari',
'week' => 'Per Minggu',
'month' => 'Per Bulan',
'year' => 'Per Tahun',
];

$pieSlices = [
['label' => 'Produk Terlaris', 'value' => 46, 'color' => '#0ea5e9'],
['label' => 'Kategori Baru', 'value' => 28, 'color' => '#22c55e'],
['label' => 'Pembayaran Online', 'value' => 16, 'color' => '#f97316'],
['label' => 'Pelanggan Setia', 'value' => 10, 'color' => '#a855f7'],
];

$pieTotal = array_sum(array_column($pieSlices, 'value')) ?: 1;
$pieOffsets = [];
$running = 0;

foreach ($pieSlices as $slice) {
$start = $running;
$end = $running + ($slice['value'] / $pieTotal) * 100;
$pieOffsets[] = sprintf('%s %s%% %s%%', $slice['color'], $start, $end);
$running = $end;
}

$pieGradient = 'conic-gradient('.implode(', ', $pieOffsets).')';
@endphp

<script>
  // SIMPLE TAB SYSTEM - CLEAN & WORKING
  (function initTabs() {
    function setupTabs() {
      const buttons = document.querySelectorAll('[data-admin-tab]');
      const sections = document.querySelectorAll('[data-tab-content]');

      buttons.forEach(btn => {
        btn.addEventListener('click', e => {
          e.preventDefault();
          const tab = btn.dataset.adminTab;

          buttons.forEach(b => {
            b.classList.remove('active', 'bg-sky-600', 'text-white');
            b.classList.add('border');
          });
          btn.classList.add('active', 'bg-sky-600', 'text-white');
          btn.classList.remove('border');

          sections.forEach(s => {
            s.style.display = s.dataset.tabContent === tab ? 'block' : 'none';
          });

          // Load data for the clicked tab
          setTimeout(() => {
            if (tab === 'orders' && window.renderOrders) renderOrders();
            if (tab === 'menu' && window.renderManageMenu) renderManageMenu();
            if (tab === 'stock' && window.renderStock) renderStock();
            if (tab === 'blacklist' && window.renderBlacklists) renderBlacklists();
            if (tab === 'finance' && window.renderFinance) renderFinance();
          }, 50);
        });
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', setupTabs);
    } else {
      setupTabs();
    }
  })();
</script>

<div id="admin-root" class="flex flex-col gap-6" style="width:100%;max-width:1600px;margin:0 auto;padding:0 1.5rem;box-sizing:border-box">
  <nav class="flex gap-3 mb-2">
    <button type="button" data-admin-tab="dashboard" class="admin-tab-button px-4 py-2 rounded-full bg-sky-600 text-white">Dashboard</button>
    <button type="button" data-admin-tab="orders" class="admin-tab-button px-4 py-2 rounded-full border">Orders</button>
    <button type="button" data-admin-tab="stock" class="admin-tab-button px-4 py-2 rounded-full border">Stock</button>
    <button type="button" data-admin-tab="menu" class="admin-tab-button px-4 py-2 rounded-full border">Menu</button>
    <button type="button" data-admin-tab="finance" class="admin-tab-button px-4 py-2 rounded-full border">Alur Kas</button>
    <button type="button" data-admin-tab="blacklist" class="admin-tab-button px-4 py-2 rounded-full border">Blacklist</button>
  </nav>
  <!-- global add button removed — Blacklist add button lives inside Blacklist tab -->

  <div class="grid gap-6" data-tab-content="dashboard" style="display:block;">
    @php
    use App\Models\Order;
    use App\Models\Menu;
    use App\Models\Pengeluaran;
    use Carbon\Carbon;

    $totalOrders = Order::count();
    $totalProducts = Menu::count();

    $lowMenusQuery = Menu::where('stock', '<=', 10)->orderBy('stock', 'asc')->get();
      $lowCount = $lowMenusQuery->count();

      $start = Carbon::now()->subDays(29)->startOfDay();
      $end = Carbon::now()->endOfDay();

      $ordersRange = Order::where('status', 'completed')->whereBetween('created_at', [$start, $end])->get();
      $expensesRange = Pengeluaran::whereBetween('expenseDate', [$start->toDateString(), $end->toDateString()])->get();

      $series = [];
      $cursor = $start->copy();
      while ($cursor->lte($end)) {
      $key = $cursor->format('Y-m-d');
      $series[$key] = ['label' => $cursor->format('M j'), 'income' => 0, 'expense' => 0];
      $cursor->addDay();
      }

      foreach ($ordersRange as $o) {
      $k = Carbon::parse($o->created_at)->format('Y-m-d');
      if (isset($series[$k])) $series[$k]['income'] += floatval($o->total_price ?? 0);
      }
      foreach ($expensesRange as $e) {
      $k = Carbon::parse($e->expenseDate)->format('Y-m-d');
      if (isset($series[$k])) $series[$k]['expense'] += floatval($e->expenseAmount ?? 0);
      }

      $seriesArr = array_values($series);
      $recentOrders = Order::orderBy('created_at', 'desc')->limit(6)->get();
      @endphp

      <div class="dashboard-metric-grid">
        <div class="dashboard-metric-card">
          <div class="metric-meta">
            <div class="metric-text">
              <div class="dashboard-metric-label">Total Orders</div>
              <div id="admin-total-orders" class="dashboard-metric-value">{{ number_format($totalOrders) }}</div>
            </div>
            <div class="dashboard-icon-badge" style="background:#fef3c7;color:#b45309;border-color:rgba(217,119,6,0.19)">🛍️</div>
          </div>
        </div>
        <div class="dashboard-metric-card">
          <div class="metric-meta">
            <div class="metric-text">
              <div class="dashboard-metric-label">Total Products</div>
              <div id="admin-total-products" class="dashboard-metric-value">{{ number_format($totalProducts) }}</div>
            </div>
            <div class="dashboard-icon-badge" style="background:#dbeafe;color:#1d4ed8;border-color:rgba(59,130,246,0.18)">📦</div>
          </div>
        </div>
        <div class="dashboard-metric-card">
          <div class="metric-meta">
            <div class="metric-text">
              <div class="dashboard-metric-label">Low Stock Items</div>
              <div id="admin-low-stock-items" class="dashboard-metric-value">{{ number_format($lowCount) }}</div>
            </div>
            <div class="dashboard-icon-badge" style="background:#fee2e2;color:#b91c1c;border-color:rgba(220,38,38,0.18)">⚠️</div>
          </div>
        </div>
      </div>

      <div class="dashboard-card">
        <div class="dashboard-card-inner">
          <div class="dashboard-card-title">Income vs Expenses (Last 30 Days)</div>
          <div class="dashboard-card-description">Compare revenue and spending by day to spot trends and cash flow changes over the past month.</div>
        </div>
        <div id="dashboard-chart" style="height:320px;min-height:320px;padding:0 1.5rem 1.5rem"></div>
      </div>

      <div style="display:grid;grid-template-columns:1.35fr 0.85fr;gap:1rem">
        <div class="dashboard-card">
          <div class="dashboard-card-inner">
            <div class="dashboard-card-title">Recent Orders</div>
            <div class="dashboard-card-description">Latest order activity with customer names, order status, and order value.</div>
          </div>
          <div style="padding:0 1.5rem 1.5rem">
            <div id="admin-dashboard-recent-orders">
              @if($recentOrders->isEmpty())
              <div style="padding:1.5rem 0;color:#6b7280">No recent orders</div>
              @else
              @foreach($recentOrders as $o)
              <div class="dashboard-list-item">
                <div>
                  <div class="dashboard-list-title">Order #{{ $o->id }}</div>
                  <div class="dashboard-list-subtitle">{{ $o->nama_customer ?? ($o->customer_no_hp ?? 'Guest') }}</div>
                </div>
                <div style="text-align:right;min-width:120px">
                  <div style="font-weight:700;color:#fb7e00">Rp{{ number_format($o->total_price ?? 0, 0, ',', '.') }}</div>
                  <div style="font-size:.78rem;color:#6b7280;text-transform:capitalize">{{ $o->status }}</div>
                </div>
              </div>
              @endforeach
              @endif
            </div>
          </div>

        </div>

        <div class="dashboard-card">
          <div class="dashboard-card-inner">
            <div class="dashboard-card-title">Low Stock Alert</div>
            <div class="dashboard-card-description">Products with stock running low so you can replenish before they run out.</div>
          </div>
          <div style="padding:0 1.5rem 1.5rem">
            @if($lowMenusQuery->isEmpty())
            <div style="padding:1.5rem 0;color:#6b7280">All products have sufficient stock.</div>
            @else
            @foreach($lowMenusQuery as $m)
            <div class="dashboard-list-item">
              <div>
                <div class="dashboard-list-title">{{ $m->name ?? 'Unnamed product' }}</div>
                <div class="dashboard-list-subtitle">Category: {{ $m->category ?? '---' }}</div>
              </div>
              <div style="text-align:right;min-width:110px">
                <div style="font-weight:700;color:{{ $m->stock <= 5 ? '#dc2626' : '#f59e0b' }}">{{ number_format($m->stock) }} left</div>
              </div>
            </div>
            @endforeach
            @endif
          </div>
        </div>
      </div>
  </div>

  <script>
    (function() {
      const series = @json($seriesArr);
      const chart = document.getElementById('dashboard-chart');
      if (!chart) return;
      chart.innerHTML = '';
      chart.style.position = 'relative';
      if (!series || series.length === 0) {
        chart.innerHTML = '<div style="color:#6b7280">No data</div>';
        return;
      }

      const maxV = Math.max(...series.map(s => Math.max(s.income || 0, s.expense || 0)), 1);
      const svgNS = 'http://www.w3.org/2000/svg';
      const width = 800,
        height = 260;
      const margin = {
        left: 56,
        right: 20,
        top: 16,
        bottom: 36
      };
      const innerW = width - margin.left - margin.right;
      const innerH = height - margin.top - margin.bottom;

      const svg = document.createElementNS(svgNS, 'svg');
      svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
      svg.setAttribute('width', '100%');
      svg.setAttribute('height', String(height));

      // grid + y-axis labels
      const yTicks = 5;
      for (let i = 0; i <= yTicks; i++) {
        const val = (maxV * (yTicks - i)) / yTicks;
        const y = margin.top + (innerH * i) / yTicks;
        const line = document.createElementNS(svgNS, 'line');
        line.setAttribute('x1', String(margin.left));
        line.setAttribute('x2', String(margin.left + innerW));
        line.setAttribute('y1', String(y));
        line.setAttribute('y2', String(y));
        line.setAttribute('stroke', '#f3f4f6');
        line.setAttribute('stroke-width', '1');
        svg.appendChild(line);

        const lbl = document.createElementNS(svgNS, 'text');
        lbl.setAttribute('x', String(margin.left - 8));
        lbl.setAttribute('y', String(y + 4));
        lbl.setAttribute('text-anchor', 'end');
        lbl.setAttribute('font-size', '11');
        lbl.setAttribute('fill', '#6b7280');
        lbl.textContent = 'Rp' + Math.round(val).toLocaleString();
        svg.appendChild(lbl);
      }

      // x-axis labels (limit density)
      const n = series.length;
      const stepX = n > 1 ? innerW / (n - 1) : 0;
      const labelEvery = Math.max(1, Math.ceil(n / 8));
      for (let i = 0; i < n; i++) {
        if (i % labelEvery !== 0 && i !== n - 1) continue;
        const x = margin.left + i * stepX;
        const lbl = document.createElementNS(svgNS, 'text');
        lbl.setAttribute('x', String(x));
        lbl.setAttribute('y', String(margin.top + innerH + 20));
        lbl.setAttribute('text-anchor', 'middle');
        lbl.setAttribute('font-size', '11');
        lbl.setAttribute('fill', '#6b7280');
        lbl.textContent = series[i].label;
        svg.appendChild(lbl);
      }

      // lines points
      const incPoints = [];
      const expPoints = [];
      series.forEach((s, i) => {
        const x = margin.left + (n === 1 ? innerW / 2 : i * stepX);
        const incY = margin.top + innerH - ((s.income || 0) / maxV) * innerH;
        const expY = margin.top + innerH - ((s.expense || 0) / maxV) * innerH;
        incPoints.push([x, incY]);
        expPoints.push([x, expY]);
      });

      function buildPath(points) {
        return points.map((p, idx) => (idx === 0 ? `M ${p[0]} ${p[1]}` : `L ${p[0]} ${p[1]}`)).join(' ');
      }

      const incPath = document.createElementNS(svgNS, 'path');
      incPath.setAttribute('d', buildPath(incPoints));
      incPath.setAttribute('fill', 'none');
      incPath.setAttribute('stroke', '#10b981');
      incPath.setAttribute('stroke-width', '2');
      svg.appendChild(incPath);

      const expPath = document.createElementNS(svgNS, 'path');
      expPath.setAttribute('d', buildPath(expPoints));
      expPath.setAttribute('fill', 'none');
      expPath.setAttribute('stroke', '#ef4444');
      expPath.setAttribute('stroke-width', '2');
      svg.appendChild(expPath);

      // point markers + interactive tooltip (match finance tooltip behavior)
      const tooltip = document.createElement('div');
      tooltip.className = 'dashboard-tooltip';
      tooltip.style.position = 'absolute';
      tooltip.style.pointerEvents = 'none';
      tooltip.style.background = '#ffffff';
      tooltip.style.border = '1px solid rgba(148, 163, 184, 0.24)';
      tooltip.style.borderRadius = '14px';
      tooltip.style.boxShadow = '0 16px 40px rgba(15, 23, 42, 0.08)';
      tooltip.style.padding = '0.9rem 1rem';
      tooltip.style.fontSize = '13px';
      tooltip.style.color = '#0f172a';
      tooltip.style.lineHeight = '1.5';
      tooltip.style.display = 'none';
      tooltip.style.zIndex = '20';
      tooltip.style.minWidth = '170px';
      chart.appendChild(tooltip);

      series.forEach((s, i) => {
        const x = incPoints[i][0];
        const incY = incPoints[i][1];
        const expY = expPoints[i][1];

        const incC = document.createElementNS(svgNS, 'circle');
        incC.setAttribute('cx', String(x));
        incC.setAttribute('cy', String(incY));
        incC.setAttribute('r', '4');
        incC.setAttribute('fill', '#10b981');
        incC.setAttribute('opacity', String((s.income || 0) ? 1 : 0.0));
        svg.appendChild(incC);

        const expC = document.createElementNS(svgNS, 'circle');
        expC.setAttribute('cx', String(x));
        expC.setAttribute('cy', String(expY));
        expC.setAttribute('r', '4');
        expC.setAttribute('fill', '#ef4444');
        expC.setAttribute('opacity', String((s.expense || 0) ? 1 : 0.0));
        svg.appendChild(expC);

        // invisible hover area
        const hit = document.createElementNS(svgNS, 'rect');
        hit.setAttribute('x', String(Math.max(margin.left + i * stepX - stepX / 2, margin.left)));
        hit.setAttribute('y', String(margin.top));
        hit.setAttribute('width', String(Math.max(stepX, 8)));
        hit.setAttribute('height', String(innerH));
        hit.setAttribute('fill', 'transparent');
        hit.style.cursor = 'pointer';
        hit.addEventListener('mouseenter', (ev) => {
          tooltip.style.display = '';
          tooltip.innerHTML = `<div style="font-weight:700">${s.label}</div><div style="color:#10b981;margin-top:4px">Income: Rp${Number(s.income||0).toLocaleString('id-ID')}</div><div style="color:#ef4444;margin-top:4px">Expenses: Rp${Number(s.expense||0).toLocaleString('id-ID')}</div>`;
        });
        hit.addEventListener('mousemove', (ev) => {
          const rect = chart.getBoundingClientRect();
          const left = ev.clientX - rect.left + 12;
          const top = ev.clientY - rect.top + 12;
          tooltip.style.left = `${Math.min(left, rect.width - tooltip.clientWidth - 12)}px`;
          tooltip.style.top = `${Math.max(12, Math.min(top, rect.height - tooltip.clientHeight - 12))}px`;
        });
        hit.addEventListener('mouseleave', () => {
          tooltip.style.display = 'none';
        });
        svg.appendChild(hit);
      });

      chart.appendChild(svg);
    })();
  </script>
</div>

<!-- Orders placeholder -->
<section class="rounded-3xl bg-white p-6" data-tab-content="orders" style="display:none">
  <div>
    <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;margin-bottom:1rem">
      <button id="admin-refresh-orders" class="px-3 py-2 rounded bg-sky-600 text-white">Refresh</button>
      <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:center;justify-content:flex-end">
        <button id="admin-write-payment-text-btn" style="display:inline-flex;align-items:center;gap:.5rem;background:#f97316;color:#fff;padding:.75rem 1rem;border-radius:999px;cursor:pointer;font-weight:600;border:none">
          Tulis Text Pembayaran
        </button>
        <span id="admin-payment-text-status" style="color:#6b7280;font-size:.95rem"></span>
      </div>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:center;justify-content:space-between;margin-bottom:1rem">
      <label style="flex:1;min-width:260px;display:flex;flex-direction:column;gap:.35rem;color:#374151;font-size:.95rem">
        Cari order
        <input id="admin-orders-search" type="search" placeholder="Cari #id, nama, telepon, status..." style="width:100%;padding:.75rem 1rem;border:1px solid #d1d5db;border-radius:999px;outline:none;background:#f8fafc;color:#111827" />
      </label>
      <button id="admin-clear-orders-search" class="px-3 py-2 rounded bg-gray-100 text-slate-700 border border-slate-200">Reset</button>
    </div>
    <div id="admin-orders-list">Loading orders...</div>
  </div>
</section>

<!-- Payment Text Modal -->
<div id="admin-payment-text-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;padding:16px;box-sizing:border-box;">
  <div style="background:#fff;padding:2rem;border-radius:12px;max-width:600px;width:100%;box-shadow:0 25px 50px rgba(0,0,0,0.15);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
      <div style="font-weight:800;font-size:1.1rem">Tulis Text Pembayaran</div>
      <button id="admin-payment-text-modal-close" style="background:transparent;border:none;font-size:24px;cursor:pointer;color:#6b7280">✕</button>
    </div>
    <form id="admin-payment-text-form" style="display:flex;flex-direction:column;gap:1rem">
      <div style="display:flex;flex-direction:column;gap:0.5rem">
        <label style="font-weight:600;color:#374151;font-size:0.95rem">Text Pembayaran (Maksimal 1000 karakter)</label>
        <textarea id="admin-payment-text-input" name="payment_text" style="width:100%;min-height:220px;padding:0.75rem;border:1px solid #d1d5db;border-radius:8px;font-family:monospace;font-size:0.95rem;resize:vertical;box-sizing:border-box" placeholder="Tulis instruksi pembayaran, nomor rekening, atau informasi pembayaran lainnya..."></textarea>
        <div style="font-size:0.85rem;color:#6b7280;text-align:right"><span id="admin-payment-text-count">0</span>/1000</div>
      </div>
      <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:0.5rem">
        <button type="button" id="admin-payment-text-modal-cancel" style="background:#fff;color:#111;border:1px solid #d1d5db;padding:0.75rem 1.25rem;border-radius:8px;cursor:pointer;font-weight:600">Cancel</button>
        <button type="submit" id="admin-payment-text-modal-save" style="background:#fb7e00;color:#fff;border:none;padding:0.75rem 1.25rem;border-radius:8px;cursor:pointer;font-weight:600">Save</button>
      </div>
      <div id="admin-payment-text-message" style="font-size:0.9rem;color:#475569;margin-top:0.5rem"></div>
    </form>
  </div>
</div>

<style>
  .stock-section {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 1.75rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
  }

  .stock-header {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #f0f1f3;
  }

  .stock-header-top {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1.5rem;
  }

  .stock-header-content h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin: 0.5rem 0 0 0;
    letter-spacing: -0.02em;
  }

  .stock-header-content p {
    color: #6b7280;
    font-size: 0.95rem;
    margin: 0.5rem 0 0 0;
    line-height: 1.5;
  }

  .stock-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.16em;
    color: #fb7e00;
    margin: 0;
  }

  .stock-info-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: #fef3c7;
    border: 1px solid #fcd34d;
    border-radius: 9999px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #92400e;
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.1);
  }

  .stock-table-wrapper {
    overflow-x: auto;
    border-radius: 1.25rem;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.02);
  }

  .stock-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
  }

  .stock-table thead {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border-bottom: 2px solid #e5e7eb;
  }

  .stock-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 700;
    color: #374151;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    background: inherit;
  }

  .stock-table tbody tr {
    border-bottom: 1px solid #f0f1f3;
    transition: all 0.15s ease;
  }

  .stock-table tbody tr:hover {
    background: #f9fafb;
    box-shadow: inset 0 0 8px rgba(251, 126, 0, 0.03);
  }

  .stock-table tbody tr:last-child {
    border-bottom: none;
  }

  .stock-table td {
    padding: 1.25rem 1rem;
    vertical-align: middle;
  }

  .product-name-cell {
    font-weight: 700;
    color: #111827;
    font-size: 0.95rem;
  }

  .category-badge {
    display: inline-block;
    padding: 0.4rem 0.75rem;
    background: #ff9500;
    color: #ffffff;
    border-radius: 0.5rem;
    font-size: 0.8rem;
    font-weight: 600;
  }

  .price-cell {
    color: #111827;
    font-weight: 600;
    font-size: 0.95rem;
  }

  .stock-controls {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  @media (min-width: 640px) {
    .stock-controls {
      flex-direction: row;
      align-items: center;
      gap: 1rem;
    }
  }

  .stock-button-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #ffffff;
    border: 1.5px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 0.35rem;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
  }

  .stock-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border: none;
    background: transparent;
    cursor: pointer;
    font-weight: 700;
    font-size: 1rem;
    color: #fb7e00;
    border-radius: 0.5rem;
    transition: all 0.15s ease;
    user-select: none;
  }

  .stock-btn:hover {
    background: #fef3c7;
    color: #d97706;
    transform: scale(1.05);
  }

  .stock-btn:active {
    transform: scale(0.95);
  }

  .stock-input {
    width: 3.5rem;
    padding: 0.4rem;
    border: none;
    background: transparent;
    text-align: center;
    font-weight: 700;
    font-size: 1rem;
    color: #111827;
    outline: none;
  }

  .stock-input::-webkit-outer-spin-button,
  .stock-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .stock-display {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #ff8800 0%, #ffaa2a 100%);
    border: 1px solid #ffffff;
    border-radius: 9999px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #f8f8f8;
    box-shadow: 0 4px 12px rgba(3, 105, 161, 0.12);
  }

  .stock-display-value {
    font-weight: 700;
    color: #feffff;
    font-size: 1rem;
  }

  .stock-low-indicator {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    background: #ff0000;
    color: #fdfcfc;
    font-size: 0.7rem;
    font-weight: 700;
  }

  @media (max-width: 768px) {
    .stock-table th:nth-child(4) {
      width: auto;
    }

    .stock-controls {
      gap: 0.75rem;
    }
  }

  /* Menu Section Styles */
  .menu-section {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 1.75rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
  }

  .menu-header {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #f0f1f3;
  }

  .menu-header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1.5rem;
  }

  .menu-header-content h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin: 0.5rem 0 0 0;
    letter-spacing: -0.02em;
  }

  .menu-header-content p {
    color: #6b7280;
    font-size: 0.95rem;
    margin: 0.5rem 0 0 0;
    line-height: 1.5;
  }

  .menu-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.16em;
    color: #fb7e00;
    margin: 0;
  }

  .menu-add-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.85rem 1.5rem;
    background: linear-gradient(135deg, #fb7e00 0%, #ea6800 100%);
    color: #ffffff;
    border: none;
    border-radius: 9999px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(251, 126, 0, 0.3);
    transition: all 0.2s ease;
  }

  .menu-add-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(251, 126, 0, 0.4);
  }

  .menu-add-btn:active {
    transform: translateY(0);
  }

  .menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
  }

  .menu-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
  }

  .menu-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
    border-color: #d1d5db;
  }

  .menu-card-header {
    padding: 1.5rem 1.5rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
  }

  .menu-card-title-group {
    flex: 1;
  }

  .menu-card-name {
    font-size: 1.05rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    margin-bottom: 0.35rem;
  }

  .menu-card-category {
    display: inline-block;
    padding: 0.35rem 0.7rem;
    background: #dbeafe;
    color: #1d4ed8;
    border-radius: 0.4rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .menu-card-actions {
    display: flex;
    gap: 0.5rem;
  }

  .menu-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 0.85rem;
    border: none;
    border-radius: 0.6rem;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
  }

  .menu-btn-edit {
    background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(3, 105, 161, 0.25);
  }

  .menu-btn-edit:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(3, 105, 161, 0.35);
  }

  .menu-btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
  }

  .menu-btn-delete:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(220, 38, 38, 0.35);
  }

  .menu-card-body {
    padding: 0 1.5rem 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .menu-card-price {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
  }

  .menu-card-price-label {
    font-size: 0.8rem;
    color: #6b7280;
    font-weight: 600;
  }

  .menu-card-price-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: #fb7e00;
  }

  .menu-card-stock {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 0.85rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-radius: 0.75rem;
    border: 1px solid #fcd34d;
    font-size: 0.9rem;
    font-weight: 600;
    color: #92400e;
  }

  .menu-card-stock-label {
    font-size: 0.8rem;
    color: #b45309;
    font-weight: 700;
  }

  .menu-card-stock-value {
    font-size: 1.1rem;
    font-weight: 800;
    color: #b45309;
  }

  .menu-card-description {
    font-size: 0.9rem;
    color: #6b7280;
    line-height: 1.5;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 0.6rem;
    border-left: 3px solid #fb7e00;
  }

  .menu-form-wrapper {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .menu-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .menu-form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }

  .menu-form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e5e7eb;
    border-radius: 0.75rem;
    font-size: 0.95rem;
    font-family: inherit;
    transition: all 0.2s ease;
    background: #ffffff;
  }

  .menu-form-input:focus {
    outline: none;
    border-color: #fb7e00;
    box-shadow: 0 0 0 3px rgba(251, 126, 0, 0.1);
  }

  .menu-form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e5e7eb;
    border-radius: 0.75rem;
    font-size: 0.95rem;
    font-family: inherit;
    min-height: 100px;
    resize: vertical;
    transition: all 0.2s ease;
    background: #ffffff;
  }

  .menu-form-textarea:focus {
    outline: none;
    border-color: #fb7e00;
    box-shadow: 0 0 0 3px rgba(251, 126, 0, 0.1);
  }

  .menu-form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
  }

  .menu-form-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.85rem 1.5rem;
    background: linear-gradient(135deg, #fb7e00 0%, #ea6800 100%);
    color: #ffffff;
    border: none;
    border-radius: 0.75rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
    box-shadow: 0 4px 12px rgba(251, 126, 0, 0.25);
  }

  .menu-form-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(251, 126, 0, 0.35);
  }

  .menu-form-cancel {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.85rem 1.5rem;
    background: #ffffff;
    color: #111827;
    border: 1.5px solid #e5e7eb;
    border-radius: 0.75rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .menu-form-cancel:hover {
    background: #f9fafb;
    border-color: #d1d5db;
  }

  @media (max-width: 768px) {
    .menu-header-top {
      flex-direction: column;
      gap: 1rem;
    }

    .menu-grid {
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }

    .menu-form-row {
      grid-template-columns: 1fr;
    }
  }
</style>

<!-- Stock Section -->
<section class="stock-section" data-tab-content="stock" style="display:none;">
  <div class="stock-header">
    <div class="stock-header-top">
      <div class="stock-header-content">
        <p class="stock-label">📦 Manajemen Stok</p>
        <h2>Kelola Inventaris Produk</h2>
        <p>Perbarui jumlah stok produk dengan cepat dan mudah</p>
      </div>
    </div>
  </div>

  <div class="stock-table-wrapper">
    <table class="stock-table">
      <thead>
        <tr>
          <th style="width:200px">Produk</th>
          <th style="width:120px">Kategori</th>
          <th style="width:120px">Harga</th>
          <th style="width:auto">Stok</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($productMenu as $item)
        <tr>
          <td>
            <div class="product-name-cell">{{ data_get($item, 'name') ?: data_get($item, 'title') ?: data_get($item, 'nama') ?: data_get($item, 'menu_name') ?: data_get($item, 'description') ?: 'Unnamed product' }}</div>
          </td>
          <td>
            <span class="category-badge">{{ data_get($item, 'category') ?: data_get($item, 'badge') ?: 'N/A' }}</span>
          </td>
          <td>
            <div class="price-cell">Rp {{ number_format(data_get($item, 'price', 0), 0, ',', '.') }}</div>
          </td>
          <td>
            <div class="stock-controls">
              <div class="stock-button-group">
                <button type="button" data-admin-stock-action="decrement" data-product-id="{{ $item['id'] }}" class="stock-btn" title="Kurangi stok">−</button>
                <input type="number" data-admin-stock-value="{{ $item['id'] }}" value="{{ $item['stock'] }}" min="0" class="stock-input" title="Jumlah stok saat ini" />
                <button type="button" data-admin-stock-action="increment" data-product-id="{{ $item['id'] }}" class="stock-btn" title="Tambah stok">+</button>
              </div>
              <div class="stock-display">
                Stok: <span class="stock-display-value" data-admin-stock-id="{{ $item['id'] }}">{{ $item['stock'] }}</span>
                @if ($item['stock'] <= 10)
                  <span class="stock-low-indicator" title="Stok rendah">!</span>
                  @endif
              </div>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>

<!-- Menu manager -->
<section class="menu-section" data-tab-content="menu" style="display:none;padding:2rem">
  <div class="menu-header">
    <div class="menu-header-top">
      <div class="menu-header-content">
        <p class="menu-label">🍽️ Manajemen Menu</p>
        <h2>Kelola Item Menu</h2>
        <p>Tambah, edit, atau hapus item menu yang akan ditampilkan kepada pelanggan</p>
      </div><button id="admin-show-add-menu" class="menu-add-btn">+Tambah Menu</button>
    </div>
  </div>
  <div id="admin-add-menu-form" style="display:none" class="menu-form-wrapper">
    <form id="admin-menu-form" enctype="multipart/form-data" class="menu-form">
      <div class="menu-form-row"><input name="name" placeholder="Nama produk" required class="menu-form-input" /><input name="category" placeholder="Kategori" class="menu-form-input" /><input name="price" type="number" step="0.01" placeholder="Harga" required class="menu-form-input" /><input name="stock" type="number" placeholder="Stok" required class="menu-form-input" /></div>
      <div class="menu-form-row"><input name="image" type="file" accept="image/*" class="menu-form-input" /></div><textarea name="description" placeholder="Deskripsi produk..." class="menu-form-textarea"></textarea>
      <div class="menu-form-actions"><button type="submit" class="menu-form-submit">✓ Tambah Menu</button><button type="button" id="admin-cancel-add-menu" class="menu-form-cancel">Batal</button></div>
    </form>
  </div>
  <div id="admin-manage-menu-list">Loading menu...</div>
</section>
<!-- Finance -->
<section class="rounded-3xl bg-white p-6 dark:bg-slate-900" data-tab-content="finance" style="display:none">
  <div class="finance-header" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div>
      <div class="dashboard-card-title" style="margin-bottom:0.35rem">Alur Kas</div>
      <div style="color:#475569;font-size:0.95rem">Lihat arus kas penjualan dan pengeluaran dengan filter aktif.</div>
    </div>
  </div>

  <div class="finance-toolbar" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center">
      <button class="finance-filter active" data-filter="today">Today</button>
      <button class="finance-filter" data-filter="last7">Last 7 Days</button>
      <button class="finance-filter" data-filter="last30">Last 30 Days</button>
      <button class="finance-filter" data-filter="year">Last Year</button>
    </div>
    <div style="color:#475569;font-size:0.95rem">Pilih periode untuk melihat pengeluaran dan pemasukan yang sesuai.</div>
  </div>

  <div class="finance-summary-grid" style="margin-bottom:1.5rem">
    <div class="finance-summary-card">
      <div>
        <div class="dashboard-metric-label">Total Income</div>
        <div id="finance-gross" class="finance-summary-value">Rp0</div>
        <div id="finance-gross-note" class="dashboard-metric-note">— sales</div>
      </div>
      <span class="dashboard-icon-badge" style="background:#dcfce7;color:#15803d;border-color:rgba(16, 185, 129, 0.18)">💰</span>
    </div>
    <div class="finance-summary-card">
      <div>
        <div class="dashboard-metric-label">Total Expenditure</div>
        <div id="finance-expenses" class="finance-summary-value" style="color:#ef4444">Rp0</div>
        <div id="finance-expense-note" class="dashboard-metric-note">— transactions</div>
      </div>
      <span class="dashboard-icon-badge" style="background:#fee2e2;color:#b91c1c;border-color:rgba(239, 68, 68, 0.18)">📉</span>
    </div>
    <div class="finance-summary-card">
      <div>
        <div class="dashboard-metric-label">Net Cash</div>
        <div id="finance-net" class="finance-summary-value" style="color:#f97316">Rp0</div>
        <div id="finance-net-note" class="dashboard-metric-note">— margin</div>
      </div>
      <span class="dashboard-icon-badge" style="background:#fef9c3;color:#b45309;border-color:rgba(234, 179, 8, 0.18)">📊</span>
    </div>
    <div class="finance-summary-card">
      <div>
        <div class="dashboard-metric-label">Avg Order Value</div>
        <div id="finance-avg" class="finance-summary-value" style="color:#fb7e00">Rp0</div>
        <div class="dashboard-metric-note">per transaction</div>
      </div>
      <span class="dashboard-icon-badge" style="background:#e0f2fe;color:#0369a1;border-color:rgba(14, 165, 233, 0.18)">🧾</span>
    </div>
  </div>

  <div class="finance-charts-grid" style="display:grid;grid-template-columns:1.4fr 0.8fr;gap:1rem;margin-bottom:1.5rem">
    <div class="dashboard-card">
      <div class="dashboard-card-inner">
        <div class="dashboard-card-title">Cash Flow Trend</div>
        <div id="finance-chart-period" class="finance-chart-period" style="font-size:0.9rem;color:#64748b;margin-bottom:1rem"></div>
        <div id="finance-chart" style="height:320px;position:relative;min-height:320px"></div>
      </div>
    </div>
    <div class="dashboard-card">
      <div class="dashboard-card-inner">
        <div class="dashboard-card-title">Expenses by Category</div>
        <div id="finance-pie" style="height:320px;display:flex;align-items:center;justify-content:center"></div>
        <div id="finance-pie-legend" style="margin-top:1rem"></div>
      </div>
    </div>
  </div>

  <div class="dashboard-card">
    <div class="dashboard-card-inner">
      <div class="dashboard-card-title" style="display:flex;justify-content:space-between;align-items:center;gap:1rem">
        <span>Cash Flow Management</span>
      </div>
      <div style="display:grid;grid-template-columns:1.2fr 0.9fr;gap:1rem;margin-top:1rem">
        <div style="background:#f8fafc;padding:1rem;border-radius:1.5rem;border:1px solid #e2e8f0">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
            <div>
              <div class="dashboard-card-title" style="margin-bottom:0.35rem;font-size:1rem">Expenditure Management</div>
              <div style="font-size:0.9rem;color:#64748b">Kelola pengeluaran yang sesuai filter saat ini.</div>
            </div>
            <button type="button" class="finance-action-btn" style="background:#ef4444;color:#fff;border:none;padding:.75rem 1rem;border-radius:999px">+ Add Expense</button>
          </div>
          <form id="finance-add-expense" style="display:grid;grid-template-columns:1.8fr 1fr 1fr 1fr auto;gap:.75rem;margin-bottom:1rem">
            <input name="expenseDesc" placeholder="Description" class="finance-input" />
            <input name="expenseCategory" placeholder="Category" class="finance-input" />
            <input name="expenseAmount" type="number" step="0.01" placeholder="Amount" required class="finance-input" />
            <input name="expenseDate" type="date" class="finance-input" />
            <button type="submit" class="finance-submit" style="background:#ef4444;color:#fff;border:none;padding:.75rem 1rem;border-radius:999px">Add</button>
          </form>
          <div id="finance-expense-table">Loading expenses...</div>
        </div>
        <div style="background:#f8fafc;padding:1rem;border-radius:1.5rem;border:1px solid #e2e8f0">
          <div style="margin-bottom:1rem">
            <div class="dashboard-card-title" style="margin-bottom:0.35rem;font-size:1rem">Income Management</div>
            <div style="font-size:0.9rem;color:#64748b">Data pemasukan dari penjualan dengan kolom tanggal, nama, order ID, dan jumlah total.</div>
          </div>
          <div id="finance-income-table">Loading income records...</div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Blacklist placeholder -->
<section class="rounded-3xl bg-white p-6 dark:bg-slate-900" data-tab-content="blacklist" style="display:none">
  <h2 class="text-xl font-semibold mb-4">Blacklist</h2>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
    <div></div>
    <div><button id="admin-show-add-blacklist" class="px-3 py-2 rounded bg-sky-600 text-white" style="background:#f97316;color:#fff;padding:.5rem .75rem;border:none">+ Tambah Blacklist</button></div>
  </div>
  <div id="admin-add-blacklist-form" style="display:none;border:1px solid #eef2f6;padding:1rem;border-radius:8px;margin-bottom:1rem">
    <form id="admin-blacklist-form" style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap"><input name="name" placeholder="Nama" style="padding:.5rem;border:1px solid #e5e7eb;border-radius:8px;min-width:180px" /><input name="phone" placeholder="08... (required)" required style="padding:.5rem;border:1px solid #e5e7eb;border-radius:8px;min-width:150px" /><input name="description" placeholder="Deskripsi" style="padding:.5rem;border:1px solid #e5e7eb;border-radius:8px;min-width:220px" />
      <div style="display:flex;gap:.5rem"><button type="submit" class="px-3 py-2 rounded bg-sky-600 text-white" style="background:#0ea5e9;color:#fff;padding:.5rem .75rem;border:none;border-radius:8px">Tambah</button><button type="button" id="admin-cancel-add-blacklist" class="px-3 py-2 rounded border" style="background:#fff;color:#111;border:1px solid #cbd5e1;padding:.5rem .75rem;border-radius:8px">Batal</button></div>
    </form>
  </div>
  <div id="admin-blacklist-list">Loading blacklist...</div>
</section>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const periodButtons = document.querySelectorAll('[data-period]');
    const panels = document.querySelectorAll('[data-sales-period]');
    const selectedPeriodText = document.querySelector('[data-selected-period]');

    const labels = {
      day: 'Per Hari',
      week: 'Per Minggu',
      month: 'Per Bulan',
      year: 'Per Tahun',
    }

    ;

    const stockKey = 'snack-stall-stock';

    const parseStored = (value, fallback) => {
      try {
        return JSON.parse(value) ?? fallback;
      } catch {
        return fallback;
      }
    }

    ;

    const getStockStore = () => parseStored(localStorage.getItem(stockKey), {});
    const saveStockStore = (value) => localStorage.setItem(stockKey, JSON.stringify(value));

    const updateAdminStockDisplay = (id, amount) => {
      const stockValue = document.querySelector(`[data-admin-stock-value='${id}']`);
      const stockLabel = document.querySelector(`[data-admin-stock-id='${id}']`);
      if (stockValue) stockValue.value = amount;
      if (stockLabel) stockLabel.textContent = amount;
    }

    ;

    const initializeAdminStock = () => {
      const store = getStockStore();

      document.querySelectorAll('[data-admin-stock-value]').forEach(input => {
        const id = input.dataset.adminStockValue;
        const defaultValue = Number(input.value);
        const storedValue = store[id];
        input.value = storedValue !== undefined ? storedValue : defaultValue;
        updateAdminStockDisplay(id, Number(input.value));
      });
      Object.entries(store).forEach(([id, amount]) => updateAdminStockDisplay(id, Number(amount)));
    }

    ;

    document.querySelectorAll('[data-admin-stock-action]').forEach(button => {
      button.addEventListener('click', async function() {
        const id = this.dataset.productId;
        const action = this.dataset.adminStockAction;
        const store = getStockStore();
        const current = Number(store[id] ?? document.querySelector(`[data-admin-stock-value='${id}']`).value);
        const next = action === 'increment' ? current + 1 : Math.max(0, current - 1);
        store[id] = next;
        saveStockStore(store);
        updateAdminStockDisplay(id, next);

        // persist to server-side DB so customer view reflects change
        try {
          const fd = new FormData();
          fd.append('stock', String(next));
          const res = await api.updateMenu(id, fd);

          if (!res || res.ok === false) {
            console.error('Failed to persist stock update', res);
            // optionally notify user
            // alert('Failed to save stock to server');
          }
        } catch (e) {
          console.error('Error updating stock on server', e);
        }

        // refresh low-stock UI so Dashboard reflects latest server state
        try {
          refreshLowStockUI();
        } catch (e) {}
      });
    });

    document.querySelectorAll('[data-admin-stock-value]').forEach(input => {
      input.addEventListener('change', async function() {
        const id = this.dataset.adminStockValue;
        const next = Math.max(0, Number(this.value));
        const store = getStockStore();
        store[id] = next;
        saveStockStore(store);
        updateAdminStockDisplay(id, next);

        // persist to server-side DB
        try {
          const fd = new FormData();
          fd.append('stock', String(next));
          const res = await api.updateMenu(id, fd);

          if (!res || res.ok === false) {
            console.error('Failed to persist stock update', res);
          }
        } catch (e) {
          console.error('Error updating stock on server', e);
        }
      });
    });

    function setActivePeriod(period) {
      panels.forEach(panel => {
        panel.classList.toggle('hidden', panel.dataset.salesPeriod !== period);
      });

      periodButtons.forEach(button => {
        const active = button.dataset.period === period;
        button.classList.toggle('bg-sky-700', active);
        button.classList.toggle('text-white', active);
        button.classList.toggle('border-sky-700', active);
        button.classList.toggle('border-slate-200', !active);
        button.classList.toggle('text-slate-700', !active);
      });

      if (selectedPeriodText) {
        selectedPeriodText.textContent = labels[period] ?? labels.day;
      }
    }

    periodButtons.forEach(button => {
      button.addEventListener('click', function() {
        setActivePeriod(this.dataset.period);
      });
    });

    setActivePeriod('day');
    initializeAdminStock();
    // Admin tab behavior
    const adminButtons = document.querySelectorAll('.admin-tab-button');
    const tabSections = document.querySelectorAll('[data-tab-content]');
    let currentAdminTab = 'dashboard';
    let adminRefreshTimer = null;

    async function refreshCurrentAdminTab() {
      if (window.__adminRefreshInProgress || document.visibilityState !== 'visible') return;
      window.__adminRefreshInProgress = true;

      try {
        if (currentAdminTab === 'dashboard') return renderDashboardOverview();
        if (currentAdminTab === 'orders') return renderOrders();
        if (currentAdminTab === 'blacklist') return renderBlacklists();
        if (currentAdminTab === 'menu') return renderManageMenu();
        if (currentAdminTab === 'finance') return renderFinance(currentFinanceFilter);
        if (currentAdminTab === 'stock') return refreshLowStockUI();
      } finally {
        window.__adminRefreshInProgress = false;
      }
    }

    // Global fix for corrupted template literals - replace all $ { with ${
    const fixTemplateStrings = (code) => code.replace(/\$\s\{/g, '${');


    function startAdminAutoRefreshPolling() {
      const intervalMs = 15000;
      if (adminRefreshTimer) clearInterval(adminRefreshTimer);

      adminRefreshTimer = setInterval(async () => {
          await refreshCurrentAdminTab();
        }

        , intervalMs);

      document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
          refreshCurrentAdminTab();
        }
      });
    }

    function setActiveTab(name) {
      currentAdminTab = name;

      adminButtons.forEach(b => {
        const active = b.dataset.adminTab === name;
        // remove any legacy tailwind-like classes to avoid duplicate highlights
        b.classList.remove('bg-sky-600', 'text-white', 'bg-sky-700');
        b.classList.toggle('active', active);
        b.classList.toggle('border', !active);

        if (active) {
          // ensure active has visible styles even if original markup had different classes
          b.classList.add('text-white');
        }
      });
      tabSections.forEach(s => s.style.display = (s.dataset.tabContent === name) ? '' : 'none');
      if (name === 'dashboard') renderDashboardOverview();
      if (name === 'orders') renderOrders();
      if (name === 'blacklist') renderBlacklists();
      if (name === 'menu') renderManageMenu();
      if (name === 'finance') renderFinance();
    }

    async function renderDashboardOverview() {
      try {
        const [orders, menus, financeData] = await Promise.all([api.getOrders(),
          api.getMenus(),
          api.getFinance('last30'),
        ]);

        const totalOrdersEl = document.getElementById('admin-total-orders');
        const totalProductsEl = document.getElementById('admin-total-products');
        const lowStockItemsEl = document.getElementById('admin-low-stock-items');
        const recentOrdersEl = document.getElementById('admin-dashboard-recent-orders');
        const chart = document.getElementById('dashboard-chart');

        if (totalOrdersEl) totalOrdersEl.textContent = String((orders || []).length);
        if (totalProductsEl) totalProductsEl.textContent = String((menus || []).length);

        if (lowStockItemsEl) {
          const lowCount = (menus || []).filter(m => Number(m.stock) <= 10).length;
          lowStockItemsEl.textContent = String(lowCount);
        }

        if (recentOrdersEl) {
          if (!orders || orders.length === 0) {
            recentOrdersEl.innerHTML = '<div style="padding:1.5rem 0;color:#6b7280">No recent orders</div>';
          } else {
            const items = orders.slice(0, 6);

            recentOrdersEl.innerHTML = items.map(o => {
              const name = o.nama_customer || (o.customer && (o.customer.name || o.customer.nama_customer)) || o.customer_no_hp || 'Guest';
              const total = Number(o.total_price || o.total || 0).toLocaleString('id-ID');
              const status = (o.status || o.order_status || 'placed').toLowerCase();

              return `<div class="dashboard-list-item" ><div><div class="dashboard-list-title" >Order #${o.id}</div><div class="dashboard-list-subtitle" >${name}</div></div><div style="text-align:right;min-width:120px" ><div style="font-weight:700;color:#fb7e00" >Rp${total}</div><div style="font-size:.78rem;color:#6b7280;text-transform:capitalize" >${status}</div></div></div>`;
            }).join('');
          }
        }

        if (chart && financeData && Array.isArray(financeData.series)) {
          const series = financeData.series;
          chart.innerHTML = '';
          chart.style.position = 'relative';

          if (!series.length) {
            chart.innerHTML = '<div style="color:#6b7280">No data</div>';
            return;
          }

          const maxV = Math.max(...series.map(s => Math.max(s.income || 0, s.expense || 0)), 1);
          const svgNS = 'http://www.w3.org/2000/svg';
          const width = 800;
          const height = 260;

          const margin = {
            left: 56,
            right: 20,
            top: 16,
            bottom: 36
          }

          ;
          const innerW = width - margin.left - margin.right;
          const innerH = height - margin.top - margin.bottom;
          const svg = document.createElementNS(svgNS, 'svg');

          svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
          svg.setAttribute('width', '100%');
          svg.setAttribute('height', String(height));
          const yTicks = 5;

          for (let i = 0; i <= yTicks; i++) {
            const val = (maxV * (yTicks - i)) / yTicks;
            const y = margin.top + (innerH * i) / yTicks;
            const line = document.createElementNS(svgNS, 'line');
            line.setAttribute('x1', String(margin.left));
            line.setAttribute('x2', String(margin.left + innerW));
            line.setAttribute('y1', String(y));
            line.setAttribute('y2', String(y));
            line.setAttribute('stroke', '#f3f4f6');
            line.setAttribute('stroke-width', '1');
            svg.appendChild(line);
            const lbl = document.createElementNS(svgNS, 'text');
            lbl.setAttribute('x', String(margin.left - 8));
            lbl.setAttribute('y', String(y + 4));
            lbl.setAttribute('text-anchor', 'end');
            lbl.setAttribute('font-size', '11');
            lbl.setAttribute('fill', '#6b7280');
            lbl.textContent = 'Rp' + Math.round(val).toLocaleString();
            svg.appendChild(lbl);
          }

          const n = series.length;
          const stepX = n > 1 ? innerW / (n - 1) : 0;
          const labelEvery = Math.max(1, Math.ceil(n / 8));

          series.forEach((s, i) => {
            if (i % labelEvery !== 0 && i !== n - 1) return;
            const x = margin.left + i * stepX;
            const lbl = document.createElementNS(svgNS, 'text');
            lbl.setAttribute('x', String(x));
            lbl.setAttribute('y', String(margin.top + innerH + 20));
            lbl.setAttribute('text-anchor', 'middle');
            lbl.setAttribute('font-size', '11');
            lbl.setAttribute('fill', '#6b7280');
            lbl.textContent = s.label;
            svg.appendChild(lbl);
          });
          const incPoints = [];
          const expPoints = [];

          series.forEach((s, i) => {
            const x = margin.left + (n === 1 ? innerW / 2 : i * stepX);
            const incY = margin.top + innerH - ((s.income || 0) / maxV) * innerH;
            const expY = margin.top + innerH - ((s.expense || 0) / maxV) * innerH;
            incPoints.push([x, incY]);
            expPoints.push([x, expY]);
          });

          const lineFor = (points, color) => {
            const path = document.createElementNS(svgNS, 'path');

            const pathData = points.map((p, i) => `${i===0 ? 'M' : 'L'} ${p[0]} ${p[1]}`).join(' ');
            path.setAttribute('d', pathData);
            path.setAttribute('fill', 'none');
            path.setAttribute('stroke', color);
            path.setAttribute('stroke-width', '2');
            svg.appendChild(path);
          }

          ;
          lineFor(incPoints, '#10b981');
          lineFor(expPoints, '#ef4444');

          series.forEach((s, i) => {
            const x = margin.left + (n === 1 ? innerW / 2 : i * stepX);
            const incY = margin.top + innerH - ((s.income || 0) / maxV) * innerH;
            const expY = margin.top + innerH - ((s.expense || 0) / maxV) * innerH;
            const incC = document.createElementNS(svgNS, 'circle');
            incC.setAttribute('cx', String(x));
            incC.setAttribute('cy', String(incY));
            incC.setAttribute('r', '4');
            incC.setAttribute('fill', '#10b981');
            svg.appendChild(incC);
            const expC = document.createElementNS(svgNS, 'circle');
            expC.setAttribute('cx', String(x));
            expC.setAttribute('cy', String(expY));
            expC.setAttribute('r', '4');
            expC.setAttribute('fill', '#ef4444');
            expC.setAttribute('opacity', String((s.expense || 0) ? 1 : 0.0));
            svg.appendChild(expC);
          });
          chart.appendChild(svg);
        }
      } catch (e) {
        console.error('renderDashboardOverview error', e);
      }
    }

    function escapeHtml(str) {
      return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')
        .replace(/'/g, ' &#039; ');

    }

    // Admin orders & blacklist simple functions
    const api = {
      getOrders: () => fetch('/admin/api/orders', {
        cache: 'no-store'

      }).then(r => {
        if (!r.ok) throw new Error(`API error: ${r.status}`);
        return r.json();
      }),
      getMenus: async () => {
        console.log('API getMenus called');
        const r = await fetch('/admin/api/menus', {
          cache: 'no-store'
        });
        console.log('Menus fetch response:', r.status, r.statusText);
        if (!r.ok) throw new Error(`API error: ${r.status} ${r.statusText}`);
        const data = await r.json();
        console.log('Menus JSON parsed:', data);
        return data;
      },
      getFinance: (filter) => {
        const params = new URLSearchParams();
        params.append('filter', filter ? String(filter) : 'last30');
        const todayLocal = new Date();
        const baseDate = `${todayLocal.getFullYear()}-${String(todayLocal.getMonth() + 1).padStart(2, '0')}-${String(todayLocal.getDate()).padStart(2, '0')}`;
        params.append('baseDate', baseDate);
        return fetch(`/admin/api/finance?${params.toString()}`, {
          cache: 'no-store'
        }).then(r => {
          if (!r.ok) throw new Error(`API error: ${r.status}`);
          return r.json();
        });
      },
      addMenu: (formData) => fetch('/admin/api/menus', {

        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }

        ,
        body: formData

      }).then(r => {
        if (!r.ok) throw new Error(`API error: ${r.status}`);
        return r.json();
      }),
      updateMenu: (id, formData) => {

          // Some servers / proxies have trouble with multipart PATCH requests.
          // Use method spoofing: append _method=PATCH and send as POST.
          try {
            formData.append('_method', 'PATCH');
          } catch (e) {
            /* ignore if not FormData */
          }

          return fetch(`/admin/api/menus/${id}`, {

            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }

            ,
            body: formData

          }).then(async r => {
            const json = await r.json().catch(() => null);

            return {
              ok: r.ok,
              status: r.status,
              json
            }

            ;
          });
        }

        ,
      delMenu: (id) => fetch(`/admin/api/menus/${id}`, {

        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }

      }).then(async r => {
        const text = await r.text().catch(() => null);

        try {
          const json = text ? JSON.parse(text) : null;

          return {
            ok: r.ok,
            status: r.status,
            json
          }

          ;
        } catch (e) {
          return {
            ok: r.ok,
            status: r.status,
            text
          }

          ;
        }

      }),
      updateOrderStatus: (id, status) => fetch(`/admin/api/orders/${id}/status`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }

        ,
        body: JSON.stringify({
          status
        })

      }).then(async r => {
        const json = await r.json().catch(() => ({}));

        return {
          ok: r.ok,
          json
        }

        ;

      }),
      uploadOrderQr: (id, file) => {
        const formData = new FormData();
        formData.append('qr_image', file);
        return fetch(`/admin/api/orders/${id}/qr`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: formData
        }).then(async r => {
          const json = await r.json().catch(() => ({}));
          return {
            ok: r.ok,
            json
          };
        });
      },
      getBlacklists: () => fetch('/admin/api/blacklists').then(r => {
        if (!r.ok) throw new Error(`API error: ${r.status}`);
        return r.json();
      }),
      addBlacklist: (data) => fetch('/admin/api/blacklists', {

        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }

        ,
        body: JSON.stringify(data)

      }).then(r => {
        if (!r.ok) throw new Error(`API error: ${r.status}`);
        return r.json();
      }),
      delBlacklist: (id) => fetch(`/admin/api/blacklists/${id}`, {

        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      }).then(r => {
        if (!r.ok) throw new Error(`API error: ${r.status}`);
        return r.json();
      }),
    }

    ;

    adminButtons.forEach(b => b.addEventListener('click', () => setActiveTab(b.dataset.adminTab)));
    setActiveTab('dashboard');
    startAdminAutoRefreshPolling();

    const ordersList = document.getElementById('admin-orders-list');
    const refreshOrdersBtn = document.getElementById('admin-refresh-orders');

    async function renderOrders() {
      if (!ordersList) return;
      ordersList.innerHTML = '<div style="color:#6b7280;padding:1rem 0">Loading orders...</div>';

      try {
        console.log('Fetching orders from /admin/api/orders');
        const orders = await api.getOrders();
        console.log('Orders received:', orders);

        if (!orders || orders.length === 0) {
          ordersList.innerHTML = '<div style="color:#6b7280;padding:1rem 0">No orders</div>';
          return;
        }

        const rawSearch = adminOrdersSearch ? (adminOrdersSearch.value || '').trim() : '';
        const normalizeForSearch = value => value.toString().toLowerCase().replace(/[^a-z0-9]+/g, ' ');
        const normalizedSearch = normalizeForSearch(rawSearch.replace(/#/g, ' '));
        const searchTokens = normalizedSearch.split(/\s+/).filter(Boolean);
        const filteredOrders = searchTokens.length ? orders.filter(o => {
          const idText = String(o.id || '');
          const customerName = (o.nama_customer || (o.customer && (o.customer.name || o.customer.nama_customer)) || '').toString();
          const customerPhone = (o.customer_no_hp || o.customerPhone || o.phone || (o.customer && (o.customer.customer_no_hp || o.customer.phone || o.customer.phone_number)) || '').toString();
          const status = (o.status || o.order_status || '').toString();
          const tableNo = (o.no_table || o.table_no || '').toString();
          const address = (o.customer_alamat || o.customerAddress || o.address || (o.customer && (o.customer.customer_alamat || o.customer.address)) || '').toString();

          const searchable = normalizeForSearch(`${idText} ${customerName} ${customerPhone} ${status} ${tableNo} ${address}`);
          return searchTokens.every(token => searchable.includes(token));
        }) : orders;

        if (filteredOrders.length === 0) {
          ordersList.innerHTML = '<div style="color:#6b7280;padding:1rem 0">No orders match your search.</div>';
          if (uploadOrderSelect) {
            uploadOrderSelect.innerHTML = '<option value="">Pilih order...</option>' + orders.map(o => `<option value="${o.id}">Order #${o.id} - ${o.nama_customer || (o.customer && (o.customer.name || o.customer.nama_customer)) || 'Customer'}</option>`).join('');
          }
          return;
        }

        const formatCurrency = value => 'Rp' + Number(value || 0).toLocaleString('id-ID');

        const formatDate = value => {
          const date = new Date(value);
          if (Number.isNaN(date.getTime())) return '';

          return date.toLocaleString('en-US', {
            month: 'numeric',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            second: '2-digit',
            hour12: true,
          });
        }

        ;

        const wrap = document.createElement('div');
        wrap.style.display = 'flex';
        wrap.style.flexDirection = 'column';
        wrap.style.gap = '1rem';

        filteredOrders.forEach(o => {
          const card = document.createElement('div');
          card.style.background = '#ffffff';
          card.style.border = '1px solid #e5e7eb';
          card.style.borderRadius = '1rem';
          card.style.padding = '1.35rem 1.5rem';
          card.style.boxShadow = '0 16px 30px rgba(15, 23, 42, 0.08)';
          card.style.color = '#111827';

          const items = Array.isArray(o.detailOrders) && o.detailOrders.length ? o.detailOrders : (Array.isArray(o.detail_orders) && o.detail_orders.length ? o.detail_orders : (Array.isArray(o.order_items) && o.order_items.length ? o.order_items : (Array.isArray(o.items) && o.items.length ? o.items : [])));
          const status = (o.status || o.order_status || 'placed').toLowerCase();
          const customerName = o.nama_customer || (o.customer && (o.customer.name || o.customer.nama_customer)) || 'customer';
          const customerPhone = o.customer_no_hp || o.customerPhone || o.phone || (o.customer && (o.customer.customer_no_hp || o.customer.phone || o.customer.phone_number)) || '';
          const customerAddress = o.customer_alamat || o.customerAddress || o.address || (o.customer && (o.customer.customer_alamat || o.customer.address)) || '';
          const tableNo = o.no_table || o.table_no || o.table || (o.customer && (o.customer.no_table || o.customer.table_no || o.customer.table)) || '';
          const createdAt = formatDate(o.created_at || o.createdAt || o.createdAtRaw || '');
          const totalPrice = formatCurrency(o.total_price ?? o.totalPrice ?? o.total ?? 0);

          const infoLines = [];

          if (tableNo) {
            infoLines.push(`Table: ${tableNo}`);
          } else {
            if (customerPhone) infoLines.push(`Phone: ${customerPhone}`);

            if (customerAddress) infoLines.push(`Address: ${customerAddress}`);
          }

          let itemsHtml = '';

          if (!items.length) {
            itemsHtml = '<div style="color:#6b7280;font-size:0.95rem">No item details available for this order.</div>';
          } else {
            items.forEach(item => {
              const quantity = item.order_item_quantity ?? item.quantity ?? item.quantity ?? 0;
              const itemName = (item.menu && (item.menu.name || item.menu.nama || item.menu.menu_name)) || item.menu_name || item.menu_name || ('Item #' + (item.menu_id || item.id || ''));
              const lineTotal = item.order_subtotal ?? item.subtotal ?? item.line_total ?? ((item.unit_price ?? item.price ?? item.unitPrice ?? 0) * quantity);

              itemsHtml += `<div style="display:flex;justify-content:space-between;align-items:center;gap:1rem" ><div style="display:flex;flex-direction:column;gap:.25rem;min-width:0" ><strong style="font-weight:700;color:#111827" >${itemName}</strong><span style="color:#6b7280;font-size:0.95rem" >× ${quantity}</span></div><div style="font-weight:700;color:#111827;white-space:nowrap;min-width:100px;text-align:right" >${formatCurrency(lineTotal)}</div></div>`;
            });
          }

          const proofSection = o.payment_proof_url ? `<div style="display:flex;flex-wrap:wrap;align-items:center;gap:.5rem;padding:.75rem 0"><button type="button" data-view-proof-id="${o.id}" data-payment-proof-url="${o.payment_proof_url}" style="background:#f97316;color:#fff;padding:.6rem .9rem;border:none;border-radius:999px;cursor:pointer">Lihat Bukti Pembayaran</button></div>` : `<div style="color:#6b7280;font-size:.94rem;padding:.75rem 0">Bukti pembayaran belum diunggah.</div>`;
          card.innerHTML = `<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap" ><div style="min-width:0;" ><div style="font-size:1rem;font-weight:700;color:#111827" >Order #${o.id}</div><div style="margin-top:.35rem;color:#6b7280;font-size:.95rem" >Customer: ${customerName}</div><div style="display:flex;flex-direction:column;gap:.35rem;margin-top:.5rem;color:#6b7280;font-size:.92rem" >${infoLines.map(line=> `<div>${line}</div>`).join('')}</div><div style="margin-top:.35rem;color:#6b7280;font-size:.95rem" >${createdAt}</div></div><div style="width:240px;min-width:180px;display:flex;justify-content:flex-end;align-items:flex-end" ><select data-order-id="${o.id}" style="width:100%;min-width:170px;padding:.72rem .9rem;border-radius:999px;border:1px solid #d1d5db;background:#ffffff;color:#111827;font-weight:700;font-size:.95rem;outline:none" ><option value="placed">Placed</option><option value="processing">Processing</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div></div><hr style="border:none;border-top:1px solid #eef2f6;margin:1rem 0" ><div style="display:flex;flex-direction:column;gap:.9rem" >${itemsHtml}</div>${proofSection}<div style="display:flex;justify-content:space-between;align-items:center;padding-top:1rem;margin-top:1rem;border-top:1px solid #f3f4f6" ><div style="color:#111827;font-weight:700;font-size:.98rem" >Total:</div><div style="color:#fb7e00;font-weight:800;font-size:1.4rem" >${totalPrice}</div></div>`;

          wrap.appendChild(card);
          const viewProofBtn = card.querySelector(`[data-view-proof-id="${o.id}"]`);
          if (viewProofBtn) {
            viewProofBtn.addEventListener('click', function() {
              const url = this.dataset.paymentProofUrl;
              if (url) {
                window.open(url, '_blank');
              }
            });
          }

        });

        ordersList.innerHTML = '';
        ordersList.appendChild(wrap);

        if (uploadOrderSelect) {
          const orderOptions = (searchTokens.length ? filteredOrders : orders);
          uploadOrderSelect.innerHTML = '<option value="">Pilih order...</option>' + orderOptions.map(o => `<option value="${o.id}">Order #${o.id} - ${o.nama_customer || (o.customer && (o.customer.name || o.customer.nama_customer)) || 'Customer'}</option>`).join('');
        }

        orders.forEach(o => {
          const sel = ordersList.querySelector(`select[data-order-id='${o.id}']`);
          if (sel) sel.value = (o.status || o.order_status || 'placed').toLowerCase();
        });

        ordersList.querySelectorAll('select[data-order-id]').forEach(sel => {
          sel.addEventListener('change', async function() {
            const id = this.getAttribute('data-order-id');
            const statusValue = this.value;
            this.disabled = true;

            try {
              const res = await api.updateOrderStatus(id, statusValue);

              if (!res.ok) {
                alert('Failed to update order: ' + (res.json && res.json.message ? res.json.message : 'server error'));
              }
            } catch (e) {
              console.error(e);
              alert('Failed to update order (network)');
            }

            this.disabled = false;
          });
        });
      } catch (e) {
        ordersList.innerHTML = '<div style="color:#ef4444;padding:1rem 0">Error loading orders</div>';
        console.error(e);
      }
    }

    refreshOrdersBtn && refreshOrdersBtn.addEventListener('click', renderOrders);

    const adminOrdersSearch = document.getElementById('admin-orders-search');
    const adminClearOrdersSearch = document.getElementById('admin-clear-orders-search');
    let orderSearchTimeout = null;

    if (adminOrdersSearch) {
      adminOrdersSearch.addEventListener('input', function() {
        if (orderSearchTimeout) {
          clearTimeout(orderSearchTimeout);
        }
        orderSearchTimeout = setTimeout(renderOrders, 250);
      });
    }

    if (adminClearOrdersSearch) {
      adminClearOrdersSearch.addEventListener('click', function() {
        if (adminOrdersSearch) {
          adminOrdersSearch.value = '';
        }
        renderOrders();
      });
    }

    const uploadQrInput = document.getElementById('admin-upload-qr-input');
    const uploadOrderSelect = document.getElementById('admin-upload-qr-order');
    const uploadQrStatus = document.getElementById('admin-upload-qr-status');

    // Payment Text Modal Handler
    const paymentTextModal = document.getElementById('admin-payment-text-modal');
    const paymentTextBtn = document.getElementById('admin-write-payment-text-btn');
    const paymentTextForm = document.getElementById('admin-payment-text-form');
    const paymentTextInput = document.getElementById('admin-payment-text-input');
    const paymentTextMessage = document.getElementById('admin-payment-text-message');
    const paymentTextCount = document.getElementById('admin-payment-text-count');

    // Character counter
    if (paymentTextInput) {
      paymentTextInput.addEventListener('input', function() {
        paymentTextCount.textContent = this.value.length;
      });
    }

    // Load current payment text when modal opens
    async function loadPaymentText() {
      try {
        const res = await fetch('/admin/api/settings/payment-text', {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          }
        });
        const data = await res.json();
        if (paymentTextInput && data.payment_text) {
          paymentTextInput.value = data.payment_text;
          paymentTextCount.textContent = data.payment_text.length;
        }
      } catch (e) {
        console.error('Failed to load payment text:', e);
      }
    }

    // Open modal
    if (paymentTextBtn) {
      paymentTextBtn.addEventListener('click', async function() {
        paymentTextMessage.textContent = '';
        paymentTextModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        await loadPaymentText();
      });
    }

    // Close modal
    if (document.getElementById('admin-payment-text-modal-close')) {
      document.getElementById('admin-payment-text-modal-close').addEventListener('click', function() {
        paymentTextModal.style.display = 'none';
        document.body.style.overflow = '';
      });
    }

    if (document.getElementById('admin-payment-text-modal-cancel')) {
      document.getElementById('admin-payment-text-modal-cancel').addEventListener('click', function() {
        paymentTextModal.style.display = 'none';
        document.body.style.overflow = '';
      });
    }

    // Save payment text
    if (paymentTextForm) {
      paymentTextForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const text = paymentTextInput.value.trim();

        if (!text) {
          paymentTextMessage.textContent = 'Text pembayaran tidak boleh kosong.';
          paymentTextMessage.style.color = '#b91c1c';
          return;
        }

        const saveBtn = document.getElementById('admin-payment-text-modal-save');
        saveBtn.disabled = true;
        paymentTextMessage.textContent = 'Menyimpan...';
        paymentTextMessage.style.color = '#475569';

        try {
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
          const res = await fetch('/admin/api/settings/payment-text', {
            method: 'PATCH',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
              payment_text: text
            })
          }).then(async r => {
            const json = await r.json().catch(() => ({}));
            return {
              ok: r.ok,
              json
            };
          });

          if (res.ok) {
            paymentTextMessage.textContent = 'Text pembayaran berhasil disimpan!';
            paymentTextMessage.style.color = '#16a34a';
            setTimeout(() => {
              paymentTextModal.style.display = 'none';
              document.body.style.overflow = '';
            }, 1000);
          } else {
            paymentTextMessage.textContent = res.json?.message || 'Gagal menyimpan text.';
            paymentTextMessage.style.color = '#b91c1c';
          }
        } catch (error) {
          console.error(error);
          paymentTextMessage.textContent = 'Error: ' + error.message;
          paymentTextMessage.style.color = '#b91c1c';
        } finally {
          saveBtn.disabled = false;
        }
      });
    }

    if (uploadQrInput) {
      uploadQrInput.addEventListener('change', async function() {
        const file = this.files?.[0];
        const orderId = uploadOrderSelect ? uploadOrderSelect.value : '';
        if (!orderId) {
          uploadQrStatus.textContent = 'Pilih order terlebih dahulu.';
          this.value = '';
          return;
        }
        if (!file) {
          uploadQrStatus.textContent = 'Tidak ada file yang dipilih.';
          return;
        }

        uploadQrStatus.textContent = 'Uploading QR...';
        const res = await api.uploadOrderQr(orderId, file);
        if (res.ok) {
          uploadQrStatus.textContent = 'QR uploaded successfully.';
          await renderOrders();
        } else {
          uploadQrStatus.textContent = res.json?.message || 'Upload failed';
        }
        this.value = '';
      });
    }

    // Manage menu rendering
    const manageMenuList = document.getElementById('admin-manage-menu-list');
    const showAddMenuBtn = document.getElementById('admin-show-add-menu');
    const addMenuFormWrap = document.getElementById('admin-add-menu-form');
    const cancelAddMenuBtn = document.getElementById('admin-cancel-add-menu');
    const menuForm = document.getElementById('admin-menu-form');

    async function renderManageMenu() {
      if (!manageMenuList) return;
      manageMenuList.innerHTML = '<div style="color:#6b7280;padding:1rem 0">Loading menu...</div>';

      try {
        console.log('Fetching menus from /admin/api/menus');
        const items = await api.getMenus();
        console.log('Menus received:', items, 'count:', items?.length);

        if (!items || items.length === 0) {
          manageMenuList.innerHTML = '<div style="text-align:center;padding:2rem;color:#9ca3af">No menu items yet. Add one to get started!</div>';
          return;
        }

        // Build the HTML
        let html = '<div class="menu-grid">';

        items.forEach((it, idx) => {
          const stockValue = Number(it.stock || 0);
          const stockIndicator = stockValue <= 10 ? '<span class="menu-card-stock-value" style="color:#ef4444">⚠</span>' : '';
          const priceNum = Number(it.price || 0);
          const priceFmt = priceNum.toLocaleString('id-ID');
          const desc = it.description ? `<div class="menu-card-description">${escapeHtml(it.description)}</div>` : '';

          html += `
            <div class="menu-card" data-item-id="${it.id}">
              <div class="menu-card-header">
                <div class="menu-card-title-group">
                  <h3 class="menu-card-name">${escapeHtml(it.name || 'Unnamed')}</h3>
                  <span class="menu-card-category">${escapeHtml(it.category || 'General')}</span>
                </div>
                <div class="menu-card-actions">
                  <button type="button" data-menu-edit="${it.id}" class="menu-btn menu-btn-edit">✏️ Edit</button>
                  <button type="button" data-menu-del="${it.id}" class="menu-btn menu-btn-delete">🗑️ Delete</button>
                </div>
              </div>
              <div class="menu-card-body">
                <div class="menu-card-price">
                  <span class="menu-card-price-label">Harga:</span>
                  <span class="menu-card-price-value">Rp${priceFmt}</span>
                </div>
                <div class="menu-card-stock">
                  <span class="menu-card-stock-label">📦 Stok:</span>
                  <span class="menu-card-stock-value">${stockValue}</span>
                  ${stockIndicator}
                </div>
                ${desc}
              </div>
            </div>
          `;
        });

        html += '</div>';

        manageMenuList.innerHTML = html;
        console.log('Menu cards rendered, attaching event listeners');

        // Attach delete listeners
        manageMenuList.querySelectorAll('button[data-menu-del]').forEach(btn => {
          btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const id = this.dataset.menuDel;
            console.log('Delete clicked for id:', id);

            if (!confirm('Delete menu item?')) return;

            const res = await api.delMenu(id);
            console.log('Delete response:', res);

            if (!res || res.ok === false) {
              const body = res?.json?.message || res?.text || 'server error';
              alert('Failed to delete: ' + body);
              return;
            }

            await renderManageMenu();
          });
        });

        // Attach edit listeners
        manageMenuList.querySelectorAll('button[data-menu-edit]').forEach(btn => {
          btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.menuEdit;
            const item = items.find(x => String(x.id) === String(id));

            if (!item) {
              console.warn('Item not found for id:', id);
              return;
            }

            const card = this.closest('.menu-card');
            if (!card) return;

            console.log('Edit clicked for item:', item);

            card.innerHTML = `
              <div style="padding:1.5rem">
                <form data-edit-form class="menu-form">
                  <div class="menu-form-row">
                    <input name="name" value="${escapeHtml(item.name||'')}" placeholder="Nama produk" class="menu-form-input" required />
                    <input name="category" value="${escapeHtml(item.category||'')}" placeholder="Kategori" class="menu-form-input" />
                  </div>
                  <div class="menu-form-row">
                    <input name="price" type="number" step="0.01" value="${item.price||0}" placeholder="Harga" class="menu-form-input" required />
                    <input name="stock" type="number" value="${item.stock||0}" placeholder="Stok" class="menu-form-input" required />
                  </div>
                  <div class="menu-form-row">
                    <input name="image" type="file" accept="image/*" class="menu-form-input" />
                  </div>
                  <textarea name="description" placeholder="Deskripsi..." class="menu-form-textarea">${escapeHtml(item.description||'')}</textarea>
                  <div class="menu-form-actions">
                    <button type="button" data-save-edit class="menu-form-submit">✓ Simpan</button>
                    <button type="button" data-cancel-edit class="menu-form-cancel">Batal</button>
                  </div>
                </form>
              </div>
            `;

            const form = card.querySelector('form[data-edit-form]');

            card.querySelector('[data-cancel-edit]').addEventListener('click', async () => {
              await renderManageMenu();
            });

            card.querySelector('[data-save-edit]').addEventListener('click', async (ev) => {
              ev.preventDefault();
              const fd = new FormData();
              fd.append('name', form.name.value);
              fd.append('price', form.price.value);
              fd.append('stock', form.stock.value);
              fd.append('category', form.category.value);
              fd.append('description', form.description.value);

              const fileInput = form.querySelector('input[name="image"]');
              if (fileInput?.files?.[0]) fd.append('image', fileInput.files[0]);

              const res = await api.updateMenu(id, fd);

              if (!res || res.ok === false) {
                const msg = res?.json?.message || 'server error';
                alert('Failed to update: ' + msg);
                return;
              }

              await renderManageMenu();
            });
          });
        });

        console.log('Event listeners attached successfully');

      } catch (e) {
        const errMsg = e?.message || e?.toString() || 'Unknown error';
        console.error('Error loading menu:', errMsg, e);
        manageMenuList.innerHTML = `<div style="color:#ef4444;padding:1rem"><strong>Error:</strong> ${escapeHtml(errMsg)}</div>`;
      }
    }

    if (showAddMenuBtn) showAddMenuBtn.addEventListener('click', () => {
      setActiveTab('menu');
      if (addMenuFormWrap) addMenuFormWrap.style.display = '';
    });

    if (cancelAddMenuBtn) cancelAddMenuBtn.addEventListener('click', () => {
      if (addMenuFormWrap) addMenuFormWrap.style.display = 'none';
    });

    if (menuForm) {
      menuForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        // send multipart formdata so file uploads work
        await api.addMenu(fd);
        this.reset();
        if (addMenuFormWrap) addMenuFormWrap.style.display = 'none';
        await renderManageMenu();
      });
    }

    const blForm = document.getElementById('admin-blacklist-form');
    const blList = document.getElementById('admin-blacklist-list');

    async function renderBlacklists() {
      if (!blList) return;
      blList.innerHTML = '<div style="color:#6b7280;padding:1rem 0">Loading blacklist...</div>';

      try {
        const items = await api.getBlacklists();

        if (!items || items.length === 0) {
          blList.innerHTML = '<div style="color:#6b7280;padding:1rem 0">No blacklist entries</div>';
          return;
        }

        // Build table
        let html = '<table style="width:100%;border-collapse:collapse">';
        html += '<thead><tr style="background:#f3f4f6"><th style="text-align:left;padding:12px;border-bottom:1px solid #e5e7eb">Nama</th><th style="text-align:left;padding:12px;border-bottom:1px solid #e5e7eb">ID</th><th style="text-align:left;padding:12px;border-bottom:1px solid #e5e7eb">NoHP</th><th style="text-align:left;padding:12px;border-bottom:1px solid #e5e7eb">Deskripsi</th><th style="padding:12px;border-bottom:1px solid #e5e7eb"></th></tr></thead>';
        html += '<tbody>';

        items.forEach(it => {
          const name = (it.blacklistNama || it.name || it.blacklist_name || 'Nama');
          const phone = (it.blacklistNoHp || it.phone || it.blacklistNoHp || '');
          const desc = (it.blacklistDesc || it.description || '');

          html += `<tr style="border-bottom:1px solid #f3f4f6" ><td style="padding:12px" >${name}</td><td style="padding:12px" >${it.id}</td><td style="padding:12px" >${phone}</td><td style="padding:12px" >${desc}</td><td style="padding:12px;text-align:right" ><button data-bl-del='${it.id}' style='background:#ef4444;color:#fff;padding:.4rem .6rem;border-radius:6px;border:none' >Delete</button></td></tr>`;
        });
        html += '</tbody></table>';

        // top bar (title only)
        const topBar = `<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem" ><div style="font-size:.95rem;color:#374151" >List of blacklisted customers</div><div></div></div>`;

        blList.innerHTML = topBar + html;

        // attach delete handlers
        blList.querySelectorAll('button[data-bl-del]').forEach(b => {
          b.addEventListener('click', async function() {
            const id = this.dataset.blDel;
            if (!confirm('Delete blacklist entry?')) return;
            await api.delBlacklist(id);
            await renderBlacklists();
          });
        });
      } catch (e) {
        if (blList) blList.innerHTML = '<div style="color:#ef4444;padding:1rem 0">Could not load blacklist data.</div>';
        console.error(e);
      }
    }

    // blacklist form visibility and submit
    const addFormWrap = document.getElementById('admin-add-blacklist-form');
    const showAddBtn = document.getElementById('admin-show-add-blacklist');
    const cancelAddBtn = document.getElementById('admin-cancel-add-blacklist');

    if (showAddBtn) showAddBtn.addEventListener('click', () => {
      setActiveTab('blacklist');
      if (addFormWrap) addFormWrap.style.display = '';
    });

    if (cancelAddBtn) cancelAddBtn.addEventListener('click', () => {
      if (addFormWrap) addFormWrap.style.display = 'none';
    });

    if (blForm) {
      blForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        const phone = (fd.get('phone') || '').trim();
        const name = (fd.get('name') || '').trim();
        const description = (fd.get('description') || '').trim();

        if (!phone) {
          alert('Phone required');
          return;
        }

        const added = await api.addBlacklist({
          phone,
          name,
          description
        });
        this.reset();
        if (addFormWrap) addFormWrap.style.display = 'none';
        await renderBlacklists();
      });
    }

    // initial fetch when admin opens respective tabs (handled via setActiveTab)

    // finance API helpers
    // api.getFinance is already defined in const api with proper error handling

    api.addExpense = (data) => fetch('/admin/api/pengeluarans', {

      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }

      ,
      body: JSON.stringify(data)

    }).then(async r => {
      if (!r.ok) throw new Error(`API error: ${r.status}`);
      const json = await r.json().catch(() => null);
      return json;
    });

    api.delExpense = (id) => fetch(`/admin/api/pengeluarans/${id}`, {

      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    }).then(r => {
      if (!r.ok) throw new Error(`API error: ${r.status}`);
      return r.json();
    });

    // helper to refresh low-stock UI
    async function refreshLowStockUI() {
      try {
        const menus = await api.getMenus();
        if (!menus) return;
        const low = (menus || []).filter(m => Number(m.stock) <= 10);
        const lowCount = low.length;
        const countEl = document.getElementById('low-stock-count');
        if (countEl) countEl.textContent = String(lowCount);

        const detailsEl = document.getElementById('low-stock-details');
        if (!detailsEl) return;

        if (!low.length) {
          detailsEl.innerHTML = '<div>All products have sufficient stock.</div>';
          return;
        }

        let html = '';

        low.forEach(m => {
          const s = Number(m.stock || 0);

          if (s <= 0) {
            html += `<div style="border-bottom:1px solid #f3f4f6;padding:.5rem 0" ><div style="color:#ef4444;font-weight:700" >${escapeHtml(m.name||'Unnamed product')}is out of stock</div></div>`;
          } else if (s <= 5) {
            html += `<div style="border-bottom:1px solid #f3f4f6;padding:.5rem 0" ><div style="color:#ef4444;font-weight:700" >${escapeHtml(m.name||'Unnamed product')}is critically low (${s}left)</div></div>`;
          } else {
            html += `<div style="border-bottom:1px solid #f3f4f6;padding:.5rem 0" ><div style="color:#111827" >${escapeHtml(m.name||'Unnamed product')}is low on stock (${s}left)</div></div>`;
          }
        });
        detailsEl.innerHTML = html;
      } catch (e) {
        console.error('refreshLowStockUI error', e);
      }
    }

    // finance renderers
    const financeFilters = document.querySelectorAll('.finance-filter');
    const activeFinanceButton = document.querySelector('.finance-filter.active');
    let currentFinanceFilter = activeFinanceButton ? activeFinanceButton.dataset.filter : 'last30';

    function updateFinanceFilterStyles(selectedFilter) {
      financeFilters.forEach(button => {
        const active = button.dataset.filter === selectedFilter;
        button.classList.toggle('bg-orange-500', active);
        button.classList.toggle('text-white', active);
        button.classList.toggle('active', active);
      });
    }

    function getSelectedFinanceFilter() {
      const activeButton = document.querySelector('.finance-filter.active');
      return activeButton ? activeButton.dataset.filter : currentFinanceFilter || 'last30';
    }

    updateFinanceFilterStyles(currentFinanceFilter);

    financeFilters.forEach(b => {
      b.addEventListener('click', function() {
        const selected = this.dataset.filter;
        updateFinanceFilterStyles(selected);
        currentFinanceFilter = selected;
        renderFinance(selected);
      });
    });

    async function renderFinance(filter) {
      filter = filter || getSelectedFinanceFilter();
      currentFinanceFilter = filter;
      updateFinanceFilterStyles(currentFinanceFilter);
      const data = await api.getFinance(filter);
      // metrics
      document.getElementById('finance-gross').textContent = 'Rp' + Number(data.gross || 0).toLocaleString('id-ID');
      document.getElementById('finance-expenses').textContent = 'Rp' + Number(data.totalExpenses || 0).toLocaleString('id-ID');
      document.getElementById('finance-net').textContent = 'Rp' + Number(data.net || 0).toLocaleString('id-ID');
      document.getElementById('finance-avg').textContent = 'Rp' + Number(data.avgOrder || 0).toLocaleString('id-ID');
      // update metric notes for counts
      const grossNoteEl = document.getElementById('finance-gross-note');
      if (grossNoteEl) grossNoteEl.textContent = (Number(data.ordersCount || 0)).toLocaleString('id-ID') + ' orders';
      const expenseNoteEl = document.getElementById('finance-expense-note');
      if (expenseNoteEl) expenseNoteEl.textContent = (Number(data.expensesCount || 0)).toLocaleString('id-ID') + ' transactions';
      // update net margin note: safe calculation (avoid divide-by-zero)
      const gross = Number(data.gross || 0);
      const net = Number(data.net || 0);
      const netNoteEl = document.getElementById('finance-net-note');
      if (netNoteEl) {
        if (!gross) {
          netNoteEl.textContent = '— margin';
        } else {
          const pct = ((net / gross) * 100);
          // show one decimal, handle very small values
          netNoteEl.textContent = `${pct.toFixed(1)}% margin`;
        }
      }
      const periodLabelEl = document.getElementById('finance-chart-period');
      if (periodLabelEl) {
        periodLabelEl.textContent = `${data.start || ''} — ${data.end || ''}`;
      }

      // chart: simple svg bars for income/expense
      const chart = document.getElementById('finance-chart');
      chart.innerHTML = '';
      const series = data.series || [];

      if (series.length === 0) {
        chart.innerHTML = '<div style="color:#6b7280">No data for selected period</div>';
      } else {
        const maxVal = Math.max(...series.map(s => Math.max(s.income || 0, s.expense || 0)), 1);
        const svgNS = 'http://www.w3.org/2000/svg';
        const svg = document.createElementNS(svgNS, 'svg');
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', '260');
        svg.setAttribute('viewBox', '0 0 800 260');
        const padding = 50;
        const w = 800 - padding * 2;
        const h = 180;
        const step = w / series.length;

        const tooltip = document.createElement('div');
        tooltip.className = 'finance-tooltip';
        tooltip.style.position = 'absolute';
        tooltip.style.pointerEvents = 'none';
        tooltip.style.background = '#ffffff';
        tooltip.style.border = '1px solid rgba(148, 163, 184, 0.24)';
        tooltip.style.borderRadius = '14px';
        tooltip.style.boxShadow = '0 16px 40px rgba(15, 23, 42, 0.08)';
        tooltip.style.padding = '0.9rem 1rem';
        tooltip.style.fontSize = '0.92rem';
        tooltip.style.color = '#0f172a';
        tooltip.style.lineHeight = '1.5';
        tooltip.style.display = 'none';
        tooltip.style.zIndex = '20';
        tooltip.style.minWidth = '170px';
        chart.appendChild(tooltip);

        // axes
        const axis = document.createElementNS(svgNS, 'path');
        axis.setAttribute('d', `M ${padding} ${padding} L ${padding} ${padding + h} L ${padding + w} ${padding + h}`);
        axis.setAttribute('stroke', '#cbd5e1');
        axis.setAttribute('stroke-width', '1.5');
        axis.setAttribute('fill', 'none');
        svg.appendChild(axis);

        const yTicks = 4;
        for (let i = 0; i <= yTicks; i++) {
          const y = padding + (h / yTicks) * i;
          const value = Math.round(maxVal * (1 - i / yTicks));
          const line = document.createElementNS(svgNS, 'line');
          line.setAttribute('x1', padding - 6);
          line.setAttribute('y1', y);
          line.setAttribute('x2', padding);
          line.setAttribute('y2', y);
          line.setAttribute('stroke', '#cbd5e1');
          line.setAttribute('stroke-width', '1');
          svg.appendChild(line);

          const label = document.createElementNS(svgNS, 'text');
          label.setAttribute('x', padding - 10);
          label.setAttribute('y', y + 4);
          label.setAttribute('text-anchor', 'end');
          label.setAttribute('font-size', '13');
          label.setAttribute('fill', '#64748b');
          label.textContent = `Rp${Number(value).toLocaleString('id-ID')}`;
          svg.appendChild(label);
        }

        let xLabelEvery = 1;
        if (filter === 'last30' || filter === 'month') {
          xLabelEvery = Math.max(1, Math.floor(series.length / 6));
        }
        if (filter === 'year' || filter === 'lastyear' || filter === '365') {
          xLabelEvery = Math.max(1, Math.floor(series.length / 6));
        }

        series.forEach((s, i) => {
          const x = padding + i * step;
          const incomeH = (s.income / maxVal) * h;
          const expenseH = (s.expense / maxVal) * h;
          const barWidth = Math.max(12, step * 0.22);

          const incRect = document.createElementNS(svgNS, 'rect');
          incRect.setAttribute('x', x + step * 0.15);
          incRect.setAttribute('y', padding + (h - incomeH));
          incRect.setAttribute('width', barWidth);
          incRect.setAttribute('height', incomeH);
          incRect.setAttribute('fill', '#10b981');
          svg.appendChild(incRect);

          const expRect = document.createElementNS(svgNS, 'rect');
          expRect.setAttribute('x', x + step * 0.15 + barWidth + 6);
          expRect.setAttribute('y', padding + (h - expenseH));
          expRect.setAttribute('width', barWidth);
          expRect.setAttribute('height', expenseH);
          expRect.setAttribute('fill', '#ef4444');
          svg.appendChild(expRect);

          const hoverArea = document.createElementNS(svgNS, 'rect');
          hoverArea.setAttribute('x', String(Math.max(x - step * 0.1, padding)));
          hoverArea.setAttribute('y', String(padding));
          hoverArea.setAttribute('width', String(Math.max(step, barWidth * 4)));
          hoverArea.setAttribute('height', String(h));
          hoverArea.setAttribute('fill', 'transparent');
          hoverArea.style.cursor = 'pointer';

          hoverArea.addEventListener('mouseenter', (ev) => {
            tooltip.style.display = '';
            tooltip.innerHTML = `
              <div style="font-size:0.95rem;font-weight:700;color:#0f172a;margin-bottom:0.35rem">${s.label || 'Period'}</div>
              <div style="display:flex;justify-content:space-between;align-items:center;gap:0.5rem;margin-bottom:0.5rem">
                <span style="color:#475569">Income</span>
                <span style="font-weight:700;color:#10b981">Rp${Number(s.income||0).toLocaleString('id-ID')}</span>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;gap:0.5rem">
                <span style="color:#475569">Expenses</span>
                <span style="font-weight:700;color:#ef4444">Rp${Number(s.expense||0).toLocaleString('id-ID')}</span>
              </div>
            `;
          });

          hoverArea.addEventListener('mousemove', (ev) => {
            const rect = chart.getBoundingClientRect();
            const left = ev.clientX - rect.left + 12;
            const top = ev.clientY - rect.top + 12;
            tooltip.style.left = `${Math.min(left, rect.width - tooltip.clientWidth - 12)}px`;
            tooltip.style.top = `${Math.max(12, Math.min(top, rect.height - tooltip.clientHeight - 12))}px`;
          });

          hoverArea.addEventListener('mouseleave', () => {
            tooltip.style.display = 'none';
          });

          svg.appendChild(hoverArea);

          if (i % xLabelEvery === 0 || i === series.length - 1) {
            const label = document.createElementNS(svgNS, 'text');
            label.setAttribute('x', x + step / 2);
            label.setAttribute('y', padding + h + 24);
            label.setAttribute('text-anchor', 'middle');
            label.setAttribute('font-size', '12');
            label.setAttribute('fill', '#475569');
            label.textContent = s.label || '';
            svg.appendChild(label);

            const tick = document.createElementNS(svgNS, 'line');
            tick.setAttribute('x1', x + step / 2);
            tick.setAttribute('y1', padding + h);
            tick.setAttribute('x2', x + step / 2);
            tick.setAttribute('y2', padding + h + 6);
            tick.setAttribute('stroke', '#cbd5e1');
            tick.setAttribute('stroke-width', '1');
            svg.appendChild(tick);
          }
        });

        svg.style.width = '100%';
        chart.appendChild(svg);
        // ensure tooltip overlays the svg by appending it after the svg
        chart.appendChild(tooltip);
      }

      // pie chart
      const pie = document.getElementById('finance-pie');
      const legend = document.getElementById('finance-pie-legend');
      pie.innerHTML = '';
      legend.innerHTML = '';
      const cats = data.byCategory || [];

      if (cats.length === 0) {
        pie.innerHTML = '<div style="color:#6b7280">No expense categories</div>';
      } else {
        const total = cats.reduce((s, c) => s + Number(c.amount || 0), 0) || 1;
        let running = 0;

        const slices = cats.map(c => {
          const pct = (Number(c.amount || 0) / total) * 100;
          const start = running;
          running += pct;

          return {
            label: c.category,
            amount: Number(c.amount || 0),
            pct,
            start,
            end: running,
          };
        });

        const colors = ['#3b82f6', '#06b6d4', '#f97316', '#ef4444', '#8b5cf6', '#10b981', '#f59e0b', '#e11d48'];

        const svgNS = 'http://www.w3.org/2000/svg';
        const size = 240;
        const center = size / 2;
        const outerRadius = 86;
        const innerRadius = 54;
        const svg = document.createElementNS(svgNS, 'svg');
        svg.setAttribute('viewBox', `0 0 ${size} ${size}`);
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', String(size));
        svg.style.maxWidth = '260px';
        svg.style.display = 'block';
        svg.style.margin = '0 auto';

        const tooltip = document.createElement('div');
        tooltip.style.position = 'absolute';
        tooltip.style.pointerEvents = 'none';
        tooltip.style.background = '#ffffff';
        tooltip.style.border = '1px solid rgba(148, 163, 184, 0.24)';
        tooltip.style.borderRadius = '12px';
        tooltip.style.boxShadow = '0 16px 40px rgba(15, 23, 42, 0.08)';
        tooltip.style.padding = '0.85rem 1rem';
        tooltip.style.fontSize = '0.92rem';
        tooltip.style.color = '#0f172a';
        tooltip.style.lineHeight = '1.4';
        tooltip.style.display = 'none';
        tooltip.style.zIndex = '20';
        tooltip.style.minWidth = '160px';
        pie.style.position = 'relative';
        pie.appendChild(tooltip);

        slices.forEach((slice, index) => {
          const startAngle = (slice.start / 100) * Math.PI * 2 - Math.PI / 2;
          const endAngle = (slice.end / 100) * Math.PI * 2 - Math.PI / 2;
          const largeArc = slice.pct > 50 ? 1 : 0;

          const outerStartX = center + Math.cos(startAngle) * outerRadius;
          const outerStartY = center + Math.sin(startAngle) * outerRadius;
          const outerEndX = center + Math.cos(endAngle) * outerRadius;
          const outerEndY = center + Math.sin(endAngle) * outerRadius;
          const innerStartX = center + Math.cos(startAngle) * innerRadius;
          const innerStartY = center + Math.sin(startAngle) * innerRadius;
          const innerEndX = center + Math.cos(endAngle) * innerRadius;
          const innerEndY = center + Math.sin(endAngle) * innerRadius;

          const path = document.createElementNS(svgNS, 'path');
          const d = `M ${outerStartX} ${outerStartY} A ${outerRadius} ${outerRadius} 0 ${largeArc} 1 ${outerEndX} ${outerEndY} L ${innerEndX} ${innerEndY} A ${innerRadius} ${innerRadius} 0 ${largeArc} 0 ${innerStartX} ${innerStartY} Z`;
          path.setAttribute('d', d);
          path.setAttribute('fill', colors[index % colors.length]);
          path.style.cursor = 'pointer';
          svg.appendChild(path);

          path.addEventListener('mouseenter', () => {
            tooltip.style.display = '';
            tooltip.innerHTML = `
              <div style="font-weight:700;color:#0f172a;margin-bottom:0.2rem">${slice.label}</div>
              <div style="color:#475569;font-size:0.9rem">Total Pengeluaran</div>
              <div style="margin-top:0.45rem;font-weight:700;color:#ef4444">Rp${slice.amount.toLocaleString('id-ID')}</div>
            `;
          });

          path.addEventListener('mousemove', (ev) => {
            const rect = pie.getBoundingClientRect();
            const left = ev.clientX - rect.left + 12;
            const top = ev.clientY - rect.top + 12;
            tooltip.style.left = `${Math.min(left, rect.width - tooltip.clientWidth - 16)}px`;
            tooltip.style.top = `${Math.max(12, Math.min(top, rect.height - tooltip.clientHeight - 12))}px`;
          });

          path.addEventListener('mouseleave', () => {
            tooltip.style.display = 'none';
          });

          const midAngle = (startAngle + endAngle) / 2;
          const labelRadius = outerRadius + 26;
          const labelX = center + Math.cos(midAngle) * labelRadius;
          const labelY = center + Math.sin(midAngle) * labelRadius;
          const label = document.createElementNS(svgNS, 'text');
          label.setAttribute('x', String(labelX));
          label.setAttribute('y', String(labelY));
          label.setAttribute('font-size', '11');
          label.setAttribute('fill', '#334155');
          label.setAttribute('font-weight', '700');
          label.setAttribute('dominant-baseline', 'middle');
          const anchor = Math.cos(midAngle) > 0.1 ? 'start' : Math.cos(midAngle) < -0.1 ? 'end' : 'middle';
          label.setAttribute('text-anchor', anchor);
          label.textContent = `${slice.label} ${Math.round(slice.pct)}%`;
          svg.appendChild(label);
        });

        pie.appendChild(svg);

        cats.forEach((c, i) => {
          const row = document.createElement('div');
          row.style.display = 'flex';
          row.style.justifyContent = 'space-between';
          row.style.padding = '.25rem 0';
          const left = document.createElement('div');
          left.style.display = 'flex';
          left.style.gap = '.5rem';
          const sw = document.createElement('span');
          sw.style.display = 'inline-block';
          sw.style.width = '12px';
          sw.style.height = '12px';
          sw.style.background = colors[i % colors.length];
          sw.style.borderRadius = '3px';
          left.appendChild(sw);
          const lbl = document.createElement('span');
          lbl.textContent = c.category + ' (' + ((Number(c.amount) || 0).toLocaleString('id-ID')) + ')';
          left.appendChild(lbl);
          row.appendChild(left);
          legend.appendChild(row);
        });
      }

      // expense table
      const tableWrap = document.getElementById('finance-expense-table');
      const expenses = data.expenses || [];

      if (!expenses || expenses.length === 0) {
        tableWrap.innerHTML = '<div>No expenses</div>';
      } else {
        let html = '<table style="width:100%;border-collapse:collapse"><thead><tr style="background:#f3f4f6"><th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb">Date</th><th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb">Description</th><th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb">Category</th><th style="text-align:right;padding:8px;border-bottom:1px solid #e5e7eb">Amount</th><th style="padding:8px;border-bottom:1px solid #e5e7eb"></th></tr></thead><tbody>';

        expenses.forEach(ex => {
          html += `<tr style="border-bottom:1px solid #f3f4f6" ><td style="padding:8px" >${ex.effectiveDate||ex.expenseDate||ex.created_at||''}</td><td style="padding:8px" >${ex.expenseDesc||''}</td><td style="padding:8px" >${ex.expenseCategory||''}</td><td style="padding:8px;text-align:right" >Rp${Number(ex.expenseAmount||0).toLocaleString('id-ID')}</td><td style="padding:8px;text-align:right" ><button data-exp-del='${ex.id}' style='background:#ef4444;color:#fff;padding:.25rem .5rem;border-radius:6px;border:none' >Delete</button></td></tr>`;
        });
        html += '</tbody></table>';
        tableWrap.innerHTML = html;

        tableWrap.querySelectorAll('button[data-exp-del]').forEach(b => {
          b.addEventListener('click', async function() {
            if (!confirm('Delete expense?')) return;
            const id = this.dataset.expDel;
            await api.delExpense(id);
            await renderFinance();
          });
        });
      }

      // income table
      const incomeWrap = document.getElementById('finance-income-table');
      const incomes = data.incomeRows || [];

      if (!incomes || incomes.length === 0) {
        incomeWrap.innerHTML = '<div>No income records</div>';
      } else {
        let html = '<table style="width:100%;border-collapse:collapse"><thead><tr style="background:#f3f4f6"><th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb">Date</th><th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb">Name</th><th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb">Order ID</th><th style="text-align:right;padding:8px;border-bottom:1px solid #e5e7eb">Total</th></tr></thead><tbody>';

        incomes.forEach(row => {
          html += `<tr style="border-bottom:1px solid #f3f4f6" ><td style="padding:8px" >${row.date||''}</td><td style="padding:8px" >${row.name||''}</td><td style="padding:8px" >${row.order_id||''}</td><td style="padding:8px;text-align:right" >Rp${Number(row.total||0).toLocaleString('id-ID')}</td></tr>`;
        });
        html += '</tbody></table>';
        incomeWrap.innerHTML = html;
      }
    }

    // add expense form
    const addExpenseForm = document.getElementById('finance-add-expense');

    if (addExpenseForm) addExpenseForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      const fd = new FormData(this);
      const todayLocal = new Date();
      const defaultExpenseDate = `${todayLocal.getFullYear()}-${String(todayLocal.getMonth() + 1).padStart(2, '0')}-${String(todayLocal.getDate()).padStart(2, '0')}`;

      const payload = {
        expenseDesc: fd.get('expenseDesc'),
        expenseCategory: fd.get('expenseCategory'),
        expenseAmount: Number(fd.get('expenseAmount') || 0),
        expenseDate: fd.get('expenseDate') || defaultExpenseDate,
      };

      await api.addExpense(payload);
      this.reset();
      await renderFinance(currentFinanceFilter);
    });

    // if orders tab is active on load, fetch
    if (document.querySelector('[data-tab-content="orders"]') && document.querySelector('[data-tab-content="orders"]').style.display !== 'none') {
      renderOrders();
    }

    if (document.querySelector('[data-tab-content="blacklist"]') && document.querySelector('[data-tab-content="blacklist"]').style.display !== 'none') {
      renderBlacklists();
    }

    if (document.querySelector('[data-tab-content="menu"]') && document.querySelector('[data-tab-content="menu"]').style.display !== 'none') {
      renderManageMenu();
    }

    if (document.querySelector('[data-tab-content="finance"]') && document.querySelector('[data-tab-content="finance"]').style.display !== 'none') {
      renderFinance();
    }

    // Make render functions globally accessible for tab switching
    window.renderOrders = renderOrders;
    window.renderManageMenu = renderManageMenu;
    window.renderBlacklists = renderBlacklists;
    window.renderFinance = renderFinance;
    window.renderDashboardOverview = renderDashboardOverview;
    window.refreshLowStockUI = refreshLowStockUI;
    window.setActiveTab = setActiveTab;
  });
</script>