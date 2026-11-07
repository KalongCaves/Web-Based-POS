@php
$categories = $categories ?? ['All'];
$productMap = [];
foreach($productMenu as $p) { $productMap[$p['id']] = ['title' => $p['title'], 'price' => ($p['price'])]; }
@endphp

<style>
  /* layout & cart */
  .menu-layout {
    display: flex;
    gap: 1.5rem;
    align-items: flex-start
  }

  .menu-main {
    flex: 1
  }

  .cart-sidebar {
    width: 360px;
    flex: 0 0 360px
  }

  .cart-card {
    background: #fff;
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #eee;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
  }

  .card-hero {
    display: grid;
    place-items: center;
    min-height: 140px;
    background: #fef3c7;
    font-size: 2.4rem;
  }

  .card-body {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .card-title {
    font-size: 1.05rem;
    font-weight: 700;
    margin: 0;
  }

  .card-copy {
    margin: 0;
    color: #6b7280;
    line-height: 1.6;
    font-size: 0.95rem;
  }

  .pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .45rem .9rem;
    border-radius: 9999px;
    font-size: .75rem;
    font-weight: 700;
    background: #f3f4f6;
    color: #374151;
  }

  .price-tag {
    font-size: 1.25rem;
    font-weight: 700;
    color: #f97316;
  }

  .stock-label {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    font-size: .85rem;
    background: #d1fae5;
    color: #166534;
    padding: .45rem .6rem;
    border-radius: 9999px;
  }

  .button-add {
    border: none;
    border-radius: 9999px;
    background: #0ea5e9;
    color: #fff;
    padding: .65rem 1rem;
    font-weight: 700;
    cursor: pointer;
  }

  .button-add:hover {
    background: #0284c7;
  }

  .search-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    padding: 1.15rem 1.25rem;
    margin-bottom: 1rem;
  }

  .search-row {
    display: grid;
    gap: 1rem;
    grid-template-columns: 1fr auto;
    align-items: center;
    width: 100%;
  }

  .search-wrapper {
    position: relative;
    width: 100%;
  }

  .search-input {
    width: 100%;
    padding: .85rem 1rem .85rem 3rem;
    border-radius: 9999px;
    border: 1px solid #e5e7eb;
    background: #f8fafc;
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    font-size: .95rem;
    color: #111827;
    transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
  }

  .search-input:focus {
    outline: none;
    border-color: #fb923c;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(251, 146, 60, .12);
  }

  .search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 1rem;
    pointer-events: none;
  }

  .filter-bar {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    padding-top: .75rem;
    margin-top: .5rem;
    border-top: 1px solid #f3f4f6;
    justify-content: flex-start;
  }

  .filter-pill {
    border: 1px solid #e5e7eb;
    background: #f8fafc;
    color: #334155;
    border-radius: 9999px;
    padding: .65rem 1rem;
    cursor: pointer;
    transition: transform .15s ease, background .15s ease, border-color .15s ease;
    font-size: .9rem;
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.04);
    min-height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .filter-pill:hover {
    background: #eff6ff;
    transform: translateY(-1px);
  }

  .filter-pill.active {
    border-color: #fb923c;
    background: #fb923c;
    color: #ffffff;
    box-shadow: 0 10px 25px rgba(251, 146, 60, 0.18);
  }

  @media(max-width:900px) {
    .search-row {
      grid-template-columns: 1fr;
    }

    .filter-bar {
      justify-content: flex-start;
    }
  }

  .menu-panel .search-row {
    margin-bottom: 0;
  }

  .card-hero {
    display: grid;
    place-items: center;
    min-height: 160px;
    background: #fef3c7;
    font-size: 2.4rem;
    position: relative;
    overflow: hidden;
  }

  .card-hero img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    display: block;
    transition: opacity .2s ease;
  }

  .fallback-icon {
    width: 100%;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 3rem;
    background: linear-gradient(180deg, #fef3c7 0%, #fde68a 100%);
  }

  @media(max-width:900px) {
    .menu-layout {
      flex-direction: column
    }

    .cart-sidebar {
      width: 100%
    }
  }

  /* grid & card styles (missing previously) */
  .menu-panel-padding {
    padding: 1.25rem
  }

  .grid-cards {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)) !important;
    gap: 1rem !important;
    margin-top: 0.75rem !important;
  }

  .card {
    background: #ffffff !important;
    border-radius: 18px !important;
    overflow: hidden !important;
    border: 1px solid #e5e7eb !important;
    box-shadow: 0 18px 50px rgba(15, 23, 42, 0.12) !important;
    transition: transform .18s ease, box-shadow .18s ease !important;
  }

  .card:hover {
    transform: translateY(-8px);
    box-shadow: 0 26px 64px rgba(15, 23, 42, 0.16);
  }

  .cart-header {
    display: flex;
    gap: .75rem;
    align-items: center;
    padding-bottom: .75rem;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: .75rem;
  }

  .cart-items .cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .5rem 0;
    border-bottom: 1px dashed #eee;
  }

  .qty-controls {
    display: flex;
    gap: .5rem;
    align-items: center;
  }

  .qty-btn {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
    background: #fff;
    cursor: pointer;
  }

  .remove-btn {
    background: transparent;
    border: none;
    cursor: pointer
  }

  .button-add {
    background: #fb7e00 !important;
  }

  .place-order {
    width: 100%;
    margin-top: 1rem;
    padding: .85rem;
    border-radius: 8px;
    background: #fb7e00 !important;
    color: #fff;
    border: none;
    font-weight: 700;
    cursor: pointer;
  }

  .place-order:hover {
    background: #ea6800 !important;
  }
</style>

<div class="menu-page">
  <div class="menu-panel menu-panel-padding menu-layout">
    <div class="menu-main">
      <div class="menu-panel menu-panel-padding search-card">
        <div class="search-row">
          <div class="search-wrapper">
            <span class="search-icon">🔍</span>
            <input id="menu-search" class="search-input" type="search" placeholder="Search snacks..." />
          </div>
        </div>
        <div class="filter-bar" id="category-filters">
          @foreach ($categories as $category)
          <button type="button" class="filter-pill" data-category="{{ $category }}">{{ $category }}</button>
          @endforeach
        </div>
      </div>

      <div class="grid-cards" id="menu-cards" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;margin-top:.75rem;">
        @foreach ($productMenu as $item)
        <article class="card" data-product-id="{{ $item['id'] }}" data-category="{{ $item['category'] }}" style="background:#fff;border:none;border-radius:18px;overflow:hidden;transition:transform .18s ease,box-shadow .18s ease;">
          <div class="card-hero" style="display:grid;place-items:center;min-height:140px;background:#fef3c7;font-size:2.4rem;position:relative;overflow:hidden;">
            @if(!empty($item['image_url']))
            <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}" style="width:100%;height:140px;object-fit:cover;display:block;" onerror="this.style.display='none'; this.closest('.card-hero').querySelector('.fallback-icon').style.display='flex'" />
            @endif
            <span class="fallback-icon" style="display:{{ empty($item['image_url']) ? 'flex' : 'none' }};align-items:center;justify-content:center;width:100%;height:140px;font-size:3rem;">{{ $item['icon'] ?: '📦' }}</span>
          </div>
          <div class="card-body" style="padding:1.25rem;display:flex;flex-direction:column;gap:1rem">
            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;">
              <div>
                <div class="card-title" style="font-size:1.05rem;font-weight:700;margin:0;color:#111827">{{ $item['title'] }}</div>
                <p class="card-copy" style="margin:0;color:#6b7280;line-height:1.5;font-size:0.95rem">{{ $item['description'] }}</p>
              </div>
              <span class="pill" style="display:inline-flex;align-items:center;justify-content:center;padding:.35rem .7rem;border-radius:9999px;background:#f3f4f6;color:#374151;font-weight:700;font-size:.75rem">{{ $item['badge'] }}</span>
            </div>

            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;flex-wrap:wrap;">
              <div>
                <p style="font-size:0.85rem;color:#6b7280;margin-bottom:0.25rem;margin:0">Price</p>
                <div class="price-tag" style="font-size:1.25rem;font-weight:700;color:#f97316">Rp{{ number_format($item['price'], 0, ',', '.') }}</div>
              </div>
              <div class="stock-label" data-stock-id="{{ $item['id'] }}" style="display:inline-flex;align-items:center;gap:.5rem;font-size:.85rem;background:#d1fae5;color:#166534;padding:.45rem .6rem;border-radius:9999px">{{ $item['stock'] }} in stock</div>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
              <div></div>
              <button
                type="button"
                data-add-id="{{ $item['id'] }}"
                data-name="{{ $item['title'] }}"
                data-stock="{{ $item['stock'] }}"
                class="button-add"
                aria-label="Tambahkan {{ $item['title'] }} ke keranjang"
                style="border:none;border-radius:9999px;background:#fb7e00;color:#fff;padding:.65rem 1rem;font-weight:700;cursor:pointer;min-width:110px;text-align:center;position:relative;z-index:20;pointer-events:auto !important">
                + Tambahkan
              </button>
            </div>
          </div>
        </article>
        @endforeach
      </div>
    </div>

    <aside class="cart-sidebar">
      <div class="cart-card" id="cart-root">
        <div class="cart-header">
          <span style="font-size:1.25rem">🛒</span>
          <div>
            <div style="font-weight:700">Your Cart</div>
            <div style="font-size:0.85rem;color:#6b7280">Items: <span id="cart-count">0</span></div>
          </div>
        </div>
        <div class="cart-items" id="cart-items"></div>
        <div class="cart-total">
          <div>Total:</div>
          <div id="cart-total">Rp0</div>
        </div>
        <button id="place-order" class="place-order" onclick="window.placeOrder && window.placeOrder()">Place Order</button>
      </div>
    </aside>
  </div>
</div>

<!-- Place Order Modal -->
<div id="place-order-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;padding:1rem;border-radius:12px;max-width:560px;width:92%;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem">
      <div style="font-weight:800">Place Order</div>
      <button id="place-order-modal-close" style="background:transparent;border:none;font-size:20px;cursor:pointer">✕</button>
    </div>

    <div style="margin-bottom:0.75rem">Where are you?</div>
    <div style="display:flex;gap:0.5rem;margin-bottom:0.75rem">
      <label style="display:inline-flex;align-items:center;gap:.5rem"><input type="radio" name="place_where" value="dirumah" checked> Dirumah</label>
      <label style="display:inline-flex;align-items:center;gap:.5rem"><input type="radio" name="place_where" value="ditempat"> Di Tempat</label>
    </div>

    <form id="place-order-form">
      <div id="dirumah-fields">
        <div style="margin-bottom:.5rem"><label>Nama (required)<br><input type="text" name="customer_name" style="width:100%" data-required="true" required pattern="[A-Za-zÀ-ÿ .'-]+" title="Nama hanya boleh huruf"></label></div>
        <div style="margin-bottom:.5rem"><label>Nomor HP (required)<br><input type="text" name="customer_phone" placeholder="08..." style="width:100%" data-required="true" required inputmode="numeric" pattern="[0-9]{10,}" title="Nomor HP minimal 10 angka"></label></div>
        <div style="margin-bottom:.5rem"><label>Alamat (opsional)<br><textarea name="customer_address" style="width:100%"></textarea></label></div>
      </div>

      <div id="ditempat-fields" style="display:none">
        <div style="margin-bottom:.5rem"><label>Nama (required)<br><input type="text" name="customer_name_dt" style="width:100%" data-required="true"></label></div>
        <div style="margin-bottom:.5rem"><label>Nomor Meja (required)<br><input type="text" name="table_no" style="width:100%" data-required="true"></label></div>
      </div>

      <div style="display:flex;justify-content:flex-end;gap:.5rem;margin-top:0.5rem">
        <button type="button" id="place-order-modal-cancel" style="background:#f3f4f6;border:none;padding:.5rem .75rem;border-radius:8px;cursor:pointer">Cancel</button>
        <button type="submit" id="place-order-modal-submit" style="background:#fb7e00;color:#fff;border:none;padding:.5rem .75rem;border-radius:8px;cursor:pointer">Bayar</button>
      </div>
    </form>
  </div>
</div>

<!-- Payment Proof Modal -->
<div id="payment-proof-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:1100;align-items:flex-start;justify-content:center;overflow-y:auto;overflow-x:hidden;overscroll-behavior:y-contain;touch-action:auto;">
  <div style="background:#fff;padding:1rem;border-radius:12px;max-width:560px;width:92%;margin:16px auto;max-height:calc(100vh - 32px);overflow-y:auto;overflow-x:hidden;box-shadow:0 24px 80px rgba(0,0,0,0.16);-webkit-overflow-scrolling:touch;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem">
      <div style="font-weight:800">QRIS Payment</div>
      <button id="payment-proof-modal-close" style="background:transparent;border:none;font-size:20px;cursor:pointer">✕</button>
    </div>
    <div id="payment-proof-card" style="display:grid;gap:1rem">
      <div style="text-align:center">
        <div style="font-weight:700;margin-bottom:0.5rem">pembayaran</div>
        <div id="payment-qris-card" style="max-width:300px;width:100%;min-height:220px;margin:0 auto;border:1px solid #e5e7eb;border-radius:16px;background:#fff;display:flex;align-items:center;justify-content:center;padding:1rem;box-sizing:border-box;"></div>
        <div id="payment-proof-preview" style="display:none;color:#111;font-size:0.95rem;padding:1rem;border:1px dashed #cbd5e1;border-radius:16px;margin-top:.75rem;">
          <div id="payment-proof-preview-title" style="font-weight:700;margin-bottom:0.5rem">Preview Bukti Pembayaran</div>
          <img id="payment-proof-image" src="" alt="Bukti Pembayaran" style="max-width:100%;border-radius:16px" />
        </div>
      </div>
      <form id="payment-proof-form" style="display:grid;gap:0.75rem" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <label style="display:flex;flex-direction:column;gap:0.35rem;font-size:0.95rem;color:#374151">Upload Bukti Pembayaran
          <input id="payment-proof-file" type="file" name="payment_proof" accept="image/*" style="border:1px solid #d1d5db;border-radius:12px;padding:.75rem .75rem;cursor:pointer;background:#fafafa;" />
        </label>
        <div style="display:flex;justify-content:center;gap:0.5rem;margin-top:0.5rem">
          <button type="button" id="payment-proof-modal-later" style="background:#fff;color:#111;border:1px solid #d1d5db;padding:.75rem 1rem;border-radius:999px;cursor:pointer">Nanti</button>
          <button type="button" id="payment-proof-modal-upload" style="background:#fb7e00;color:#fff;border:none;padding:.75rem 1rem;border-radius:999px;cursor:pointer">Upload Bukti</button>
        </div>
        <div id="payment-proof-status" style="font-size:0.9rem;color:#475569"></div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    try {
      console.log('[pelanggan] script loaded');
    } catch (e) {}

    const PRODUCT_DATA = @json($productMap);
    const STOCK_KEY = 'snack-stall-stock';
    const CART_KEY = 'snack-stall-cart';
    const MENU_API_URL = '/api/menus';

    function parseJSON(v, def) {
      try {
        const parsed = JSON.parse(v);
        return parsed === null ? def : parsed;
      } catch {
        return def;
      }
    }

    function getStock() {
      return parseJSON(localStorage.getItem(STOCK_KEY), {});
    }

    function setStock(s) {
      localStorage.setItem(STOCK_KEY, JSON.stringify(s));
    }

    function getCart() {
      return parseJSON(localStorage.getItem(CART_KEY), {});
    }

    function setCart(c) {
      localStorage.setItem(CART_KEY, JSON.stringify(c));
    }

    window.__orderInProgress = window.__orderInProgress || false;

    function formatRp(v) {
      return 'Rp' + Number(v).toLocaleString('id-ID');
    }

    async function fetchMenuStock(id) {
      try {
        const res = await fetch(MENU_API_URL);
        if (!res.ok) return null;
        const items = await res.json();
        const m = (items || []).find(it => String(it.id) === String(id));
        return m ? Number(m.stock || 0) : null;
      } catch (e) {
        return null;
      }
    }

    const headerCartCount = document.getElementById('header-cart-count');
    const cartCountEl = document.getElementById('cart-count');
    const cartItemsEl = document.getElementById('cart-items');
    const cartTotalEl = document.getElementById('cart-total');

    function updateBadge() {
      const cart = getCart();
      const count = Object.values(cart || {}).reduce((s, n) => s + Number(n), 0);
      if (cartCountEl) cartCountEl.textContent = count;
      if (headerCartCount) headerCartCount.textContent = count;
    }

    function filterMenuCards() {
      const searchTerm = (document.getElementById('menu-search')?.value || '').trim().toLowerCase();
      const activeCategory = document.querySelector('.filter-pill.active')?.dataset.category || 'All';
      document.querySelectorAll('#menu-cards .card').forEach(function(card) {
        const title = (card.querySelector('.card-title')?.textContent || '').toLowerCase();
        const description = (card.querySelector('.card-copy')?.textContent || '').toLowerCase();
        const category = (card.dataset.category || '').toLowerCase();
        const matchesSearch = !searchTerm || title.includes(searchTerm) || description.includes(searchTerm) || category.includes(searchTerm);
        const matchesCategory = activeCategory === 'All' || (card.dataset.category || '') === activeCategory;
        card.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
      });
    }

    function setupMenuFilters() {
      const searchInput = document.getElementById('menu-search');
      const filterContainer = document.getElementById('category-filters');
      if (!searchInput || !filterContainer) return;

      const pills = Array.from(filterContainer.querySelectorAll('.filter-pill'));
      if (pills.length > 0 && !pills.some(b => b.classList.contains('active'))) {
        pills[0].classList.add('active');
      }

      searchInput.addEventListener('input', filterMenuCards);
      filterContainer.addEventListener('click', function(ev) {
        const button = ev.target.closest('.filter-pill');
        if (!button) return;
        pills.forEach(p => p.classList.toggle('active', p === button));
        filterMenuCards();
      });
    }

    function renderCart() {
      const cart = getCart();
      cartItemsEl.innerHTML = '';
      let total = 0;
      if (!cart || Object.keys(cart).length === 0) {
        cartItemsEl.innerHTML = '<div style="padding:.75rem;color:#6b7280">Keranjang kosong</div>';
      } else {
        Object.keys(cart).forEach(function(id) {
          const qty = Number(cart[id] || 0);
          const info = PRODUCT_DATA[id] || {
            title: id,
            price: 0
          };
          const row = document.createElement('div');
          row.className = 'cart-item';
          const left = document.createElement('div');
          left.className = 'left';
          left.innerHTML = '<div style="font-weight:700">' + (info.title || id) + '</div><div style="font-size:0.9rem;color:#6b7280">' + formatRp(info.price) + ' each</div>';
          const right = document.createElement('div');
          right.innerHTML = '<div class="qty-controls"><button class="qty-btn" data-decrease="' + id + '">-</button><span style="min-width:28px;text-align:center;display:inline-block">' + qty + '</span><button class="qty-btn" data-increase="' + id + '">+</button></div><div style="margin-top:6px;text-align:right"><button class="remove-btn" data-remove="' + id + '">🗑</button></div>';
          row.appendChild(left);
          row.appendChild(right);
          cartItemsEl.appendChild(row);
          total += qty * Number(info.price);
        });
      }
      if (cartTotalEl) cartTotalEl.textContent = formatRp(total);
      updateBadge();
    }

    async function changeQty(id, delta) {
      if (!window.__qtyLocks) window.__qtyLocks = {};
      if (window.__qtyLocks[id]) return;
      window.__qtyLocks[id] = true;
      const stock = getStock();
      const cart = getCart();
      const cur = Number(cart[id] || 0);
      let available = Number(stock[id] ?? '') || Number(document.querySelector('[data-stock-id="' + id + '"]')?.textContent.replace(/[^0-9]/g, '') || 0);
      // verify with server to avoid stale localStorage
      try {
        const server = await fetchMenuStock(id);
        if (server !== null) {
          // synchronize local store to server value if server has less
          if (Number(server) < available) {
            stock[id] = Number(server);
            setStock(stock);
            const stockEl = document.querySelector('[data-stock-id="' + id + '"]');
            if (stockEl) {
              stockEl.textContent = stock[id] + ' in stock';
            }
          }
          // refresh available after possible sync
          available = Number(stock[id] ?? '') || Number(document.querySelector('[data-stock-id="' + id + '"]')?.textContent.replace(/[^0-9]/g, '') || 0);
        }
      } catch (e) {}
      const next = Math.max(0, cur + delta);
      if (delta > 0) {
        // re-check server stock to avoid allowing increase beyond server's available
        try {
          const serverCur = await fetchMenuStock(id);
          if (serverCur !== null) {
            const cart = getCart();
            const curInCart = Number(cart[id] || 0);
            if (curInCart >= Number(serverCur)) {
              return;
            }
          }
        } catch (e) {}
        if (available <= 0) return;
      }
      if (delta > 0) {
        stock[id] = Math.max(0, available - 1);
        setStock(stock);
        const stockEl = document.querySelector('[data-stock-id="' + id + '"]');
        if (stockEl) {
          stockEl.textContent = stock[id] + ' in stock';
        }
      }
      if (delta < 0) {
        stock[id] = (Number(stock[id] || 0) + 1);
        setStock(stock);
        const stockEl = document.querySelector('[data-stock-id="' + id + '"]');
        if (stockEl) {
          stockEl.textContent = stock[id] + ' in stock';
        }
      }
      try {
        if (next <= 0) {
          delete cart[id];
        } else {
          cart[id] = next;
        }
        setCart(cart);
        renderCart();
      } finally {
        window.__qtyLocks[id] = false;
      }
    }

    document.addEventListener('click', async function(ev) {
      const add = ev.target.closest && ev.target.closest('[data-add-id]');
      if (add) {
        const id = add.getAttribute('data-add-id');
        if (add.dataset.adding) return;
        add.dataset.adding = '1';
        const stock = getStock();
        const localCur = Number(stock[id] || add.getAttribute('data-stock') || 0);
        // verify server stock to avoid stale local values; retry once if needed
        let serverCur = null;
        try {
          serverCur = await fetchMenuStock(id);
          if (serverCur === null) {
            serverCur = await fetchMenuStock(id);
          }
        } catch (e) {
          serverCur = null;
        }

        if (serverCur !== null) {
          if (Number(serverCur) <= 0) {
            alert('Stok habis untuk produk ini.');
            delete add.dataset.adding;
            return;
          }
          const cart = getCart();
          const curInCart = Number(cart[id] || 0);
          if (curInCart >= Number(serverCur)) {
            alert('Stok habis untuk produk ini.');
            delete add.dataset.adding;
            return;
          }
          stock[id] = Number(serverCur) - curInCart;
          setStock(stock);
          const stockEl = document.querySelector('[data-stock-id="' + id + '"]');
          if (stockEl) {
            stockEl.textContent = stock[id] + ' in stock';
          }
        }

        const cur = Number(stock[id] || add.getAttribute('data-stock') || 0);
        if (cur <= 0) {
          alert('Stok habis untuk produk ini.');
          delete add.dataset.adding;
          return;
        }

        stock[id] = cur - 1;
        setStock(stock);
        const stockEl = document.querySelector('[data-stock-id="' + id + '"]');
        if (stockEl) {
          stockEl.textContent = stock[id] + ' in stock';
        }

        const cart = getCart();
        cart[id] = Number(cart[id] || 0) + 1;
        setCart(cart);

        try {
          const old = add.innerHTML;
          add.innerHTML = '✓ Added';
          setTimeout(function() {
            add.innerHTML = old;
          }, 700);
        } catch (e) {} finally {
          delete add.dataset.adding;
        }

        renderCart();
        return;
      }

      const inc = ev.target.closest && ev.target.closest('[data-increase]');
      if (inc) {
        await changeQty(inc.getAttribute('data-increase'), +1);
        return;
      }

      const dec = ev.target.closest && ev.target.closest('[data-decrease]');
      if (dec) {
        await changeQty(dec.getAttribute('data-decrease'), -1);
        return;
      }

      const rem = ev.target.closest && ev.target.closest('[data-remove]');
      if (rem) {
        const id = rem.getAttribute('data-remove');
        const cart = getCart();
        const qty = Number(cart[id] || 0);
        const stock = getStock();
        try {
          const serverCur = await fetchMenuStock(id);
          if (serverCur !== null) {
            stock[id] = Number(serverCur);
          } else {
            stock[id] = Number(stock[id] || 0) + qty;
          }
        } catch (e) {
          stock[id] = Number(stock[id] || 0) + qty;
        }
        setStock(stock);
        const stockEl = document.querySelector('[data-stock-id="' + id + '"]');
        if (stockEl) {
          stockEl.textContent = String(stock[id]) + ' in stock';
        }
        const cardLabel = document.querySelector('[data-product-id="' + id + '"] .stock-label');
        if (cardLabel) {
          cardLabel.textContent = String(stock[id]) + ' in stock';
        }
        delete cart[id];
        setCart(cart);
        renderCart();
        return;
      }
    });

    // Reconcile local stock and cart with authoritative server values.
    async function reconcileWithServer() {
      if (window.__orderInProgress) return;
      if (window.__reconcileInProgress) return;
      window.__reconcileInProgress = true;
      try {
        // read DOM-rendered stock as fallback
        const domStock = {};
        document.querySelectorAll('[data-stock-id]').forEach(function(el) {
          const id = el.getAttribute('data-stock-id');
          const textValue = Number((el.textContent || '').replace(/[^0-9]/g, ''));
          domStock[id] = !Number.isNaN(textValue) && textValue >= 0 ? textValue : 0;
        });

        // fetch server menus once
        let menus = null;
        try {
          const res = await fetch(MENU_API_URL);
          if (res.ok) menus = await res.json();
        } catch (e) {
          menus = null;
        }

        const serverMap = {};
        if (menus && Array.isArray(menus)) {
          menus.forEach(m => {
            serverMap[String(m.id)] = Number(m.stock || 0);
          });
        }

        const cart = getCart();
        const store = getStock();
        const hasServerData = menus && Array.isArray(menus);

        if (!hasServerData) {
          // if we could not fetch authoritative server stock, keep the current local store
          Object.keys(domStock).forEach(id => {
            if (!store.hasOwnProperty(id)) {
              store[id] = domStock[id];
            }
          });
          setStock(store);
          return;
        }

        // reconcile each product id present in DOM using authoritative server values
        Object.keys(domStock).forEach(id => {
          const hasServerVal = serverMap.hasOwnProperty(id);
          const serverVal = hasServerVal ? serverMap[id] : domStock[id];
          const inCart = Number(cart[id] || 0);
          if (hasServerVal && inCart > serverVal) {
            cart[id] = serverVal;
          }
          const curCart = Number(cart[id] || 0);
          const avail = hasServerVal ? Math.max(0, serverVal - curCart) : domStock[id];
          store[id] = avail;
          const stockEl = document.querySelector('[data-stock-id="' + id + '"]');
          if (stockEl) {
            stockEl.textContent = String(avail) + ' in stock';
          }
        });

        // persist reconciled values
        setStock(store);
        setCart(cart);
      } catch (e) {
        console.error('reconcileWithServer error', e);
      } finally {
        window.__reconcileInProgress = false;
      }
    }

    // initial reconcile and periodic polling to keep UI responsive when admin updates stock
    (function startReconcilePolling() {
      try {
        // run immediately
        reconcileWithServer();
        // poll every 7s when visible
        const intervalMs = 7000;
        let intervalId = setInterval(() => {
          if (window.__orderInProgress) return;
          if (document.visibilityState === 'visible') reconcileWithServer();
        }, intervalMs);

        document.addEventListener('visibilitychange', function() {
          if (window.__orderInProgress) return;
          if (document.visibilityState === 'visible') {
            // immediate reconcile when user returns
            reconcileWithServer();
          }
        });
      } catch (e) {
        console.error('startReconcilePolling error', e);
      }
    })();

    renderCart();
    setupMenuFilters();
    filterMenuCards();

    window.__addToCart = function(id) {
      var btn = document.querySelector('[data-add-id="' + id + '"]');
      if (btn) btn.click();
    };

    // Place order handler
    (function() {
      const ORDERS_STORE_URL = '{{ route("orders.store") }}';
      const BLACKLIST_CHECK_URL = '{{ route("orders.blacklist-check") }}';
      const ORDERS_INDEX_URL = '{{ route("orders.index") }}';
      const PENDING_PAYMENT_TIMEOUT_MS = 30000;
      let pendingOrderPayload = null;
      let pendingOrderId = null;
      let pendingOrderExpiryAt = null;
      let pendingPaymentTimer = null;
      let pendingPaymentTicker = null;

      function clearPendingOrderState() {
        pendingOrderPayload = null;
        pendingOrderId = null;
        pendingOrderExpiryAt = null;
        if (pendingPaymentTimer) {
          clearTimeout(pendingPaymentTimer);
          pendingPaymentTimer = null;
        }
        if (pendingPaymentTicker) {
          clearInterval(pendingPaymentTicker);
          pendingPaymentTicker = null;
        }
      }

      function secondsUntilExpiry() {
        return pendingOrderExpiryAt ? Math.max(0, Math.ceil((pendingOrderExpiryAt - Date.now()) / 1000)) : 0;
      }

      async function cancelPendingOrder(orderId, onStatus) {
        if (!orderId) return;
        try {
          const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
          const res = await fetch(ORDERS_STORE_URL.replace(/\/$/, '') + '/' + orderId + '/cancel', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': token || '',
              'Accept': 'application/json'
            }
          });
          const data = await res.json().catch(() => ({}));
          if (res.ok) {
            if (pendingPaymentTimer) {
              clearTimeout(pendingPaymentTimer);
              pendingPaymentTimer = null;
            }
            if (pendingPaymentTicker) {
              clearInterval(pendingPaymentTicker);
              pendingPaymentTicker = null;
            }
            clearPendingOrderState();
            if (typeof onStatus === 'function') {
              onStatus('Waktu pembayaran habis. Pesanan telah dibatalkan.', true);
            }
            setTimeout(closePaymentModal, 1500);
          } else {
            if (typeof onStatus === 'function') {
              onStatus(data.message || 'Gagal membatalkan pesanan.', true);
            }
          }
        } catch (err) {
          console.error(err);
          if (typeof onStatus === 'function') {
            onStatus('Gagal membatalkan pesanan.', true);
          }
        }
      }

      function buildCustomerPayload(form, where) {
        if (where === 'dirumah') {
          const nameInput = form.querySelector('input[name="customer_name"]');
          const phoneInput = form.querySelector('input[name="customer_phone"]');
          const addressInput = form.querySelector('textarea[name="customer_address"]');
          return {
            name: (nameInput ? nameInput.value : '').trim(),
            phone: (phoneInput ? phoneInput.value : '').trim(),
            address: (addressInput ? addressInput.value : '').trim() || null
          };
        }

        return {
          name: (form.querySelector('input[name="customer_name_dt"]')?.value || '').trim() || null
        };
      }

      // show modal to collect customer info
      function openPlaceModal() {
        const modal = document.getElementById('place-order-modal');
        if (!modal) return;
        modal.style.display = 'flex';
      }

      function closePlaceModal() {
        const modal = document.getElementById('place-order-modal');
        if (!modal) return;
        modal.style.display = 'none';
      }

      window.placeOrder = function() {
        const cart = getCart();
        if (!cart || Object.keys(cart).length === 0) {
          alert('Keranjang kosong — tambahkan beberapa item dulu.');
          return;
        }
        openPlaceModal();
      };

      // modal handlers
      const modal = document.getElementById('place-order-modal');
      if (modal) {
        modal.querySelector('#place-order-modal-close').addEventListener('click', closePlaceModal);
        modal.querySelector('#place-order-modal-cancel').addEventListener('click', closePlaceModal);

        // toggle fields and ensure hidden inputs are disabled so browser won't block submit
        const dirumahFields = document.getElementById('dirumah-fields');
        const ditempatFields = document.getElementById('ditempat-fields');

        function setModalFieldsFor(val) {
          // show/hide blocks
          if (dirumahFields) dirumahFields.style.display = val === 'dirumah' ? '' : 'none';
          if (ditempatFields) ditempatFields.style.display = val === 'ditempat' ? '' : 'none';

          const submitButton = document.getElementById('place-order-modal-submit');
          if (submitButton) {
            submitButton.textContent = val === 'dirumah' ? 'Bayar' : 'Confirm';
          }

          // enable/disable inputs and toggle required based on data-required
          if (dirumahFields) {
            Array.from(dirumahFields.querySelectorAll('input,textarea,select')).forEach(function(inp) {
              inp.disabled = val !== 'dirumah';
              if (inp.hasAttribute && inp.hasAttribute('data-required')) {
                inp.required = val === 'dirumah';
              }
            });
          }
          if (ditempatFields) {
            Array.from(ditempatFields.querySelectorAll('input,textarea,select')).forEach(function(inp) {
              inp.disabled = val !== 'ditempat';
              if (inp.hasAttribute && inp.hasAttribute('data-required')) {
                inp.required = val === 'ditempat';
              }
            });
          }
        }

        // attach radio change listeners
        modal.querySelectorAll('input[name="place_where"]').forEach(function(r) {
          r.addEventListener('change', function() {
            setModalFieldsFor(this.value);
          });
        });

        // initialize state
        const initial = modal.querySelector('input[name="place_where"]:checked');
        if (initial) setModalFieldsFor(initial.value);
      }

      function openPaymentModal() {
        const paymentModal = document.getElementById('payment-proof-modal');
        if (!paymentModal) return;
        const qrisCard = paymentModal.querySelector('#payment-qris-card');
        const proofPreview = paymentModal.querySelector('#payment-proof-preview');
        const proofImage = paymentModal.querySelector('#payment-proof-image');
        const proofStatus = paymentModal.querySelector('#payment-proof-status');
        const proofFileInput = paymentModal.querySelector('#payment-proof-file');

        paymentModal.dataset.orderId = pendingOrderId || '';
        proofStatus.textContent = '';
        proofImage.src = '';
        proofPreview.style.display = 'none';
        if (proofFileInput) proofFileInput.value = '';

        // Load global payment text
        if (qrisCard) {
          qrisCard.innerHTML = '<div style="color:#9ca3af;font-size:0.9rem">Memuat informasi pembayaran...</div>';
          fetch('/admin/api/settings/payment-text')
            .then(r => r.json())
            .catch(() => ({}))
            .then(data => {
              if (data.payment_text) {
                qrisCard.innerHTML = '<div style="color:#111827;font-size:0.95rem;line-height:1.6;white-space:pre-wrap;word-break:break-word;text-align:left">' +
                  data.payment_text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                  '</div>';
              } else {
                qrisCard.innerHTML = '<div style="color:#9ca3af;font-size:0.9rem">Belum ada informasi pembayaran</div>';
              }
            });
        }

        if (pendingOrderId && pendingOrderExpiryAt) {
          const secondsLeft = secondsUntilExpiry();
          if (secondsLeft > 0) {
            proofStatus.textContent = 'Pesanan menunggu pembayaran. Upload bukti dalam ' + secondsLeft + ' detik untuk mengonfirmasi.';
            proofStatus.style.color = '#475569';
          } else {
            proofStatus.textContent = 'Waktu pembayaran habis. Pesanan akan dibatalkan.';
            proofStatus.style.color = '#b91c1c';
          }
        }

        document.body.style.overflow = 'hidden';
        paymentModal.style.display = 'flex';
      }

      function closePaymentModal() {
        const paymentModal = document.getElementById('payment-proof-modal');
        if (!paymentModal) return;
        document.body.style.overflow = '';
        paymentModal.style.display = 'none';
      }

      const paymentModal = document.getElementById('payment-proof-modal');
      if (paymentModal) {
        paymentModal.querySelector('#payment-proof-modal-close').addEventListener('click', closePaymentModal);
        const paymentForm = paymentModal.querySelector('#payment-proof-form');
        const proofFileInput = paymentForm.querySelector('#payment-proof-file');
        const uploadButton = paymentModal.querySelector('#payment-proof-modal-upload');
        const laterButton = paymentModal.querySelector('#payment-proof-modal-later');
        const proofStatus = paymentModal.querySelector('#payment-proof-status');
        const proofPreview = paymentModal.querySelector('#payment-proof-preview');
        const proofImage = paymentModal.querySelector('#payment-proof-image');
        let selectedProofFile = null;

        function setProofStatus(text, isError = false) {
          if (!proofStatus) return;
          proofStatus.textContent = text;
          proofStatus.style.color = isError ? '#b91c1c' : '#475569';
        }

        if (laterButton) {
          laterButton.addEventListener('click', async function() {
            if (!pendingOrderPayload) {
              alert('Data pesanan belum siap.');
              return;
            }
            if (pendingOrderId) {
              alert('Pesanan sudah menunggu pembayaran. Silakan unggah bukti.');
              return;
            }

            const formData = new FormData();
            formData.append('cart', JSON.stringify(pendingOrderPayload.cart || {}));
            formData.append('customer', JSON.stringify(pendingOrderPayload.customer || {}));
            if (pendingOrderPayload.table_no) {
              formData.append('table_no', pendingOrderPayload.table_no);
            }

            try {
              const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
              if (token) {
                formData.append('_token', token);
              }
              const res = await fetch(ORDERS_STORE_URL, {
                method: 'POST',
                headers: {
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': token
                },
                body: formData
              });
              const data = await res.json().catch(() => ({}));
              if (!res.ok) {
                const errorDetail = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : null) || res.statusText || 'Gagal memproses pesanan.';
                alert(errorDetail);
                return;
              }

              if (data.order && data.order.id) {
                pendingOrderId = data.order.id;
                pendingOrderExpiryAt = Date.now() + PENDING_PAYMENT_TIMEOUT_MS;
                if (pendingPaymentTimer) {
                  clearTimeout(pendingPaymentTimer);
                }
                if (pendingPaymentTicker) {
                  clearInterval(pendingPaymentTicker);
                }

                setProofStatus('Pesanan diterima. Unggah bukti dalam 30 detik untuk mengonfirmasi.');
                localStorage.removeItem(CART_KEY);
                setCart({});
                renderCart();

                pendingPaymentTicker = setInterval(function() {
                  const secondsLeft = secondsUntilExpiry();
                  if (secondsLeft <= 0) {
                    clearInterval(pendingPaymentTicker);
                    pendingPaymentTicker = null;
                    return;
                  }
                  setProofStatus('Pesanan menunggu pembayaran. Upload bukti dalam ' + secondsLeft + ' detik untuk mengonfirmasi.');
                }, 1000);

                pendingPaymentTimer = setTimeout(function() {
                  pendingPaymentTimer = null;
                  cancelPendingOrder(pendingOrderId, setProofStatus);
                }, PENDING_PAYMENT_TIMEOUT_MS);

                pendingOrderPayload = null;
              }
            } catch (err) {
              console.error(err);
              alert('Gagal memproses pesanan.');
            }
          });
        }

        paymentForm.addEventListener('submit', function(e) {
          e.preventDefault();
        });

        if (proofFileInput) {
          proofFileInput.addEventListener('change', function() {
            const input = this;
            if (!input.files || !input.files[0]) {
              selectedProofFile = null;
              proofPreview.style.display = 'none';
              if (proofStatus) proofStatus.textContent = '';
              return;
            }
            selectedProofFile = input.files[0];
            const reader = new FileReader();
            reader.onload = function(ev) {
              proofImage.src = ev.target.result;
              proofPreview.style.display = '';
              if (proofStatus) proofStatus.textContent = 'Bukti pembayaran ditampilkan di atas. Tekan Upload Bukti untuk konfirmasi.';
            };
            reader.readAsDataURL(selectedProofFile);
          });
        }

        if (uploadButton) {
          uploadButton.addEventListener('click', async function() {
            if (!selectedProofFile) {
              alert('Pilih file bukti pembayaran terlebih dahulu.');
              return;
            }

            uploadButton.disabled = true;
            const formData = new FormData();
            formData.append('payment_proof', selectedProofFile);
            let submitUrl = ORDERS_STORE_URL;

            if (pendingOrderId) {
              submitUrl = ORDERS_STORE_URL.replace(/\/$/, '') + '/' + pendingOrderId + '/payment-proof';
            } else {
              if (!pendingOrderPayload) {
                alert('Data pesanan belum siap.');
                uploadButton.disabled = false;
                return;
              }
              formData.append('cart', JSON.stringify(pendingOrderPayload.cart || {}));
              formData.append('customer', JSON.stringify(pendingOrderPayload.customer || {}));
              if (pendingOrderPayload.table_no) {
                formData.append('table_no', pendingOrderPayload.table_no);
              }
            }

            try {
              const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
              if (token) {
                formData.append('_token', token);
              }
              const res = await fetch(submitUrl, {
                method: 'POST',
                headers: {
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': token
                },
                body: formData
              });
              const data = await res.json().catch(() => ({}));
              console.log('[pelanggan] payment proof submission', res.status, data);
              if (!res.ok) {
                const errorDetail = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : null) || res.statusText || 'Gagal upload bukti pembayaran';
                alert(errorDetail);
                uploadButton.disabled = false;
                return;
              }

              if (pendingPaymentTimer) {
                clearTimeout(pendingPaymentTimer);
                pendingPaymentTimer = null;
              }
              if (pendingPaymentTicker) {
                clearInterval(pendingPaymentTicker);
                pendingPaymentTicker = null;
              }
              pendingOrderId = null;
              pendingOrderExpiryAt = null;
              pendingOrderPayload = null;

              proofStatus.textContent = 'Order berhasil dibuat. Bukti pembayaran diterima.';
              proofStatus.style.color = '#475569';
              localStorage.removeItem(CART_KEY);
              setCart({});
              renderCart();
              if (data.order && data.order.payment_proof_url) {
                proofImage.src = data.order.payment_proof_url;
                proofPreview.style.display = '';
              }
              setTimeout(function() {
                closePaymentModal();
                window.location.href = ORDERS_INDEX_URL;
              }, 1200);
            } catch (err) {
              console.error(err);
              alert('Gagal upload bukti pembayaran');
            } finally {
              uploadButton.disabled = false;
            }
          });
        }
      }

      modal.querySelector('#place-order-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const whereEl = (modal && modal.querySelector('input[name="place_where"]:checked')) || form.querySelector('input[name="place_where"]:checked');
        const where = whereEl ? whereEl.value : 'dirumah';
        const cart = getCart();
        if (!cart || Object.keys(cart).length === 0) {
          alert('Keranjang kosong — tambahkan beberapa item dulu.');
          return;
        }
        if (where === 'dirumah') {
          const nameInput = form.querySelector('input[name="customer_name"]');
          const phoneInput = form.querySelector('input[name="customer_phone"]');
          const addressInput = form.querySelector('textarea[name="customer_address"]');
          const name = (nameInput ? nameInput.value : '').trim();
          const phone = (phoneInput ? phoneInput.value : '').trim();
          const address = (addressInput ? addressInput.value : '').trim();

          if (!name || !/^[A-Za-zÀ-ÿ .'-]+$/.test(name)) {
            alert('Nama harus diisi dan hanya boleh huruf.');
            return;
          }
          if (!phone || !/^[0-9]+$/.test(phone)) {
            alert('Nomor HP harus berupa angka.');
            return;
          }

          pendingOrderPayload = {
            cart,
            customer: {
              name: name,
              phone: phone,
              address: address || null
            }
          };
        } else {
          const name = form.querySelector('input[name="customer_name_dt"]').value || null;
          const tableNo = form.querySelector('input[name="table_no"]').value || null;
          if (!name || !tableNo) {
            alert('Nama dan nomor meja diperlukan untuk pemesanan di tempat.');
            return;
          }
          pendingOrderPayload = {
            cart,
            customer: {
              name: name
            },
            table_no: tableNo
          };
        }

        if (pendingOrderPayload.customer.phone) {
          try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const blacklistResponse = await fetch(BLACKLIST_CHECK_URL, {
              method: 'POST',
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
              },
              body: JSON.stringify({
                phone: pendingOrderPayload.customer.phone
              })
            });
            const blacklistData = await blacklistResponse.json().catch(() => ({}));
            if (!blacklistResponse.ok) {
              alert(blacklistData.message || 'Mohon Maaf, Nomor telepon yang anda gunakan tidak bisa digunakan. Mohon untuk menggunakan nomor telepon lain.');
              return;
            }
          } catch (err) {
            console.error(err);
            alert('Gagal memeriksa status blacklist. Silakan coba lagi.');
            return;
          }
        }

        closePlaceModal();
        openPaymentModal();
      });
    })();
  });
</script>