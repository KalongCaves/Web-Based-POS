<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  @php $title = 'Orders'; @endphp
  @include('partials.head')
  <style>
    body {
      background: #f9fafb;
      color: #111827;
      font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    }

    .container {
      max-width: 1600px;
      margin: 0 auto;
      padding: 1.5rem 1.25rem 2rem;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      gap: 1rem;
      flex-wrap: wrap;
      margin-bottom: 1.5rem;
    }

    .page-heading {
      font-weight: 800;
      font-size: 1.75rem;
      color: #111827;
    }

    .page-nav {
      display: flex;
      align-items: center;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .page-nav a {
      color: #374151;
      text-decoration: none;
      font-weight: 600;
    }

    .page-nav a:last-child {
      color: #111827;
    }

    .order-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .order-card {
      background: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 1rem;
      padding: 1.35rem 1.5rem;
      box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
    }

    .order-card-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .order-card-title {
      font-weight: 700;
      font-size: 1.05rem;
      color: #111827;
    }

    .order-card-meta {
      color: #6b7280;
      font-size: 0.95rem;
      margin-top: 0.35rem;
      line-height: 1.5;
    }

    .order-card-status {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      width: 100%;
      max-width: 240px;
    }

    .order-status-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.65rem 1rem;
      border-radius: 999px;
      border: 1px solid #d1d5db;
      background: #f8fafc;
      color: #111827;
      font-weight: 700;
      font-size: 0.95rem;
      text-transform: capitalize;
      min-width: 170px;
      text-align: center;
    }

    .order-card-divider {
      border: none;
      border-top: 1px solid #eef2f6;
      margin: 1rem 0;
    }

    .order-item-list {
      display: flex;
      flex-direction: column;
      gap: 0.9rem;
    }

    .order-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
    }

    .order-item-name {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
      min-width: 0;
    }

    .order-item-name strong {
      font-weight: 700;
      color: #111827;
    }

    .order-item-name span {
      color: #6b7280;
      font-size: 0.95rem;
    }

    .order-item-price {
      font-weight: 700;
      color: #111827;
      white-space: nowrap;
      min-width: 100px;
      text-align: right;
    }

    .order-total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 1rem;
      margin-top: 1rem;
      border-top: 1px solid #f3f4f6;
    }

    .order-total-label {
      color: #111827;
      font-weight: 700;
      font-size: 0.98rem;
    }

    .order-total-value {
      color: #fb7e00;
      font-weight: 800;
      font-size: 1.4rem;
    }

    .no-items {
      color: #6b7280;
      font-size: 0.95rem;
    }

    @media (max-width: 680px) {
      .order-card-header {
        align-items: stretch;
      }

      .order-card-status {
        max-width: 100%;
      }
    }
  </style>
</head>

<body>
  @include('partials.header')

  <div class="container" style="margin-top:1rem">
    <header class="page-header">
      <div class="page-heading">My Orders</div>
      <nav class="page-nav">
        <a href="{{ route('dashboard') }}">Menu</a>
        <a href="{{ route('orders.index') }}">Orders</a>
      </nav>
    </header>

    <section class="order-list">
      @foreach($orders as $order)
      <article class="order-card" data-order-id="{{ $order->id }}">
        <div class="order-card-header">
          <div>
            <div class="order-card-title">Order #{{ $order->id }}</div>
            <div class="order-card-meta">Customer: {{ optional($order->customer)->name ?? $order->nama_customer ?? 'Customer' }}</div>
            <div class="order-card-meta">{{ optional($order->created_at)->format('n/j/Y, g:i:s A') ?? '' }}</div>
          </div>

          <div class="order-card-status">
            @php $currentStatus = strtolower($order->status ?? 'pending'); @endphp
            <div class="order-status-badge">{{ ucfirst($currentStatus) }}</div>
          </div>
        </div>

        <hr class="order-card-divider">

        <div class="order-item-list">
          @php
          $displayItems = $order->detailOrders ?? ($order->items ?? collect());
          @endphp

          @if(!$displayItems || $displayItems->isEmpty())
          <div class="no-items">No item details available for this order.</div>
          @else
          @foreach($displayItems as $item)
          @php
          $quantity = $item->order_item_quantity ?? $item->quantity ?? 0;
          $lineTotal = $item->order_subtotal ?? (($item->unit_price ?? 0) * $quantity);
          $itemName = optional($item->menu)->name ?? $item->menu_name ?? ('Item #' . ($item->menu_id ?? $item->id));
          @endphp
          <div class="order-item">
            <div class="order-item-name">
              <strong>{{ $itemName }}</strong>
              <span>× {{ $quantity }}</span>
            </div>
            <div class="order-item-price">{{ number_format($lineTotal, 0, ',', '.') }}</div>
          </div>
          @endforeach
          @endif
        </div>

        <div class="order-total-row">
          <div>
            <div class="order-total-label">Total:</div>
            @if($currentStatus === 'pending_payment')
            @php
            $createdAt = optional($order->created_at);
            $elapsed = $createdAt->diffInSeconds(now());
            $remaining = max(0, 30 - $elapsed);
            @endphp
            <div class="order-card-meta order-countdown-row" style="margin-top:.5rem">Waktu tersisa: <span class="order-countdown" data-order-created-at="{{ $createdAt->timestamp ?? 0 }}" data-order-id="{{ $order->id }}">{{ $remaining }}</span> detik</div>
            @endif
          </div>
          <div class="order-total-value">{{ number_format($order->total_price ?? 0, 0, ',', '.') }}</div>
        </div>
        @if($currentStatus === 'pending_payment')
        <div style="margin-top:1rem;display:flex;justify-content:flex-end;gap:.5rem">
          <button type="button" class="order-pay-button" data-order-id="{{ $order->id }}" style="background:#fb7e00;color:#fff;border:none;padding:.75rem 1rem;border-radius:999px;cursor:pointer">Bayar</button>
        </div>
        @endif
        @if($currentStatus === 'placed')
        <div style="margin-top:1rem;display:flex;justify-content:flex-end;gap:.5rem">
          <button
            type="button"
            class="order-cancel-placed-button"
            data-order-id="{{ $order->id }}"
            style="background:#f97316;color:#fff;border:1px solid #fdfdfd;padding:.75rem 1rem;border-radius:999px;cursor:pointer">
            Batalkan Pesanan
          </button>
        </div>
        @endif
      </article>
      @endforeach
    </section>
  </div>

  <div id="order-payment-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1100;align-items:flex-start;justify-content:center;overflow-y:auto;overflow-x:hidden;padding:16px;box-sizing:border-box;">
    <div style="background:#fff;padding:1rem;border-radius:12px;max-width:560px;width:100%;margin:auto;max-height:calc(100vh - 32px);overflow-y:auto;overflow-x:hidden;box-shadow:0 24px 80px rgba(0,0,0,0.16);-webkit-overflow-scrolling:touch;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem">
        <div style="font-weight:800">QRIS Payment</div>
        <button id="order-payment-modal-close" style="background:transparent;border:none;font-size:20px;cursor:pointer">✕</button>
      </div>
      <div id="order-payment-card" style="display:grid;gap:1rem">
        <div style="text-align:center">
          <div style="font-weight:700;margin-bottom:0.5rem">QRIS / pembayaran</div>
          <div id="order-payment-qris-card" style="max-width:300px;width:100%;min-height:220px;margin:0 auto;border:1px solid #e5e7eb;border-radius:16px;background:#fff;display:flex;align-items:center;justify-content:center;padding:1rem;box-sizing:border-box;"></div>
          <div id="order-payment-proof-preview" style="display:none;color:#111;font-size:0.95rem;padding:1rem;border:1px dashed #cbd5e1;border-radius:16px;margin-top:.75rem;">
            <div style="font-weight:700;margin-bottom:0.5rem">Preview Bukti Pembayaran</div>
            <img id="order-payment-proof-image" src="" alt="Bukti Pembayaran" style="max-width:100%;border-radius:16px" />
          </div>
        </div>
        <form id="order-payment-proof-form" style="display:grid;gap:0.75rem" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" id="order-payment-id" name="order_id" value="">
          <label style="display:flex;flex-direction:column;gap:0.35rem;font-size:0.95rem;color:#374151">Upload Bukti Pembayaran
            <input id="order-payment-proof-file" type="file" name="payment_proof" accept="image/*" style="border:1px solid #d1d5db;border-radius:12px;padding:.75rem .75rem;cursor:pointer;background:#fafafa;" />
          </label>
          <div style="display:flex;justify-content:center;gap:0.5rem;margin-top:0.5rem">
            <button type="button" id="order-payment-modal-cancel" style="background:#fff;color:#111;border:1px solid #d1d5db;padding:.75rem 1rem;border-radius:999px;cursor:pointer">Close</button>
            <button type="button" id="order-payment-modal-upload" style="background:#fb7e00;color:#fff;border:none;padding:.75rem 1rem;border-radius:999px;cursor:pointer">Upload Bukti</button>
          </div>
          <div id="order-payment-proof-status" style="font-size:0.9rem;color:#475569"></div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const payButtons = document.querySelectorAll('.order-pay-button');
      const cancelPlacedButtons = document.querySelectorAll('.order-cancel-placed-button');
      const paymentModal = document.getElementById('order-payment-modal');
      const paymentClose = document.getElementById('order-payment-modal-close');
      const paymentCancel = document.getElementById('order-payment-modal-cancel');
      const paymentUpload = document.getElementById('order-payment-modal-upload');
      const proofFileInput = document.getElementById('order-payment-proof-file');
      const proofPreview = document.getElementById('order-payment-proof-preview');
      const proofImage = document.getElementById('order-payment-proof-image');
      const proofStatus = document.getElementById('order-payment-proof-status');
      const paymentQrCard = document.getElementById('order-payment-qris-card');
      const orderPaymentId = document.getElementById('order-payment-id');
      let selectedProofFile = null;

      function openOrderPaymentModal(orderId) {
        if (!paymentModal) return;
        orderPaymentId.value = orderId;
        proofStatus.textContent = 'Unggah bukti pembayaran untuk order #' + orderId + '.';
        proofStatus.style.color = '#475569';
        proofPreview.style.display = 'none';
        selectedProofFile = null;
        proofFileInput.value = '';

        // Load global payment text
        if (paymentQrCard) {
          paymentQrCard.innerHTML = '<div style="color:#9ca3af;font-size:0.9rem">Memuat informasi pembayaran...</div>';
          fetch('/admin/api/settings/payment-text')
            .then(r => r.json())
            .catch(() => ({}))
            .then(data => {
              if (data.payment_text) {
                paymentQrCard.innerHTML = '<div style="color:#111827;font-size:0.95rem;line-height:1.6;white-space:pre-wrap;word-break:break-word;text-align:left">' +
                  data.payment_text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                  '</div>';
              } else {
                paymentQrCard.innerHTML = '<div style="color:#9ca3af;font-size:0.9rem">Belum ada informasi pembayaran</div>';
              }
            });
        }

        paymentModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }

      function closeOrderPaymentModal() {
        if (!paymentModal) return;
        paymentModal.style.display = 'none';
        document.body.style.overflow = '';
      }

      if (paymentClose) {
        paymentClose.addEventListener('click', closeOrderPaymentModal);
      }
      if (paymentCancel) {
        paymentCancel.addEventListener('click', closeOrderPaymentModal);
      }

      payButtons.forEach(function(button) {
        button.addEventListener('click', function() {
          const orderId = this.dataset.orderId;
          if (!orderId) return;
          openOrderPaymentModal(orderId);
        });
      });
      cancelPlacedButtons.forEach(function(button) {
        button.addEventListener('click', async function() {
          const orderId = this.dataset.orderId;

          if (!orderId) {
            return;
          }

          const confirmed = confirm(
            'Yakin ingin membatalkan Order #' + orderId + '?'
          );

          if (!confirmed) {
            return;
          }

          button.disabled = true;
          button.textContent = 'Membatalkan...';

          try {
            const token =
              document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute('content') || '';

            const res = await fetch(
              '/orders/' + orderId + '/cancel-placed', {
                method: 'POST',
                headers: {
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': token
                }
              }
            );

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
              alert(
                data.message ||
                'Order tidak dapat dibatalkan.'
              );

              button.disabled = false;
              button.textContent = 'Batalkan Pesanan';
              return;
            }

            alert(
              data.message ||
              'Order berhasil dibatalkan.'
            );

            window.location.reload();

          } catch (err) {
            console.error(
              'Failed to cancel placed order',
              err
            );

            alert('Gagal membatalkan order.');

            button.disabled = false;
            button.textContent = 'Batalkan Pesanan';
          }
        });
      });
      if (proofFileInput) {
        proofFileInput.addEventListener('change', function() {
          if (!this.files || !this.files[0]) {
            selectedProofFile = null;
            proofPreview.style.display = 'none';
            proofStatus.textContent = 'Pilih file bukti pembayaran.';
            return;
          }
          selectedProofFile = this.files[0];
          const reader = new FileReader();
          reader.onload = function(ev) {
            proofImage.src = ev.target.result;
            proofPreview.style.display = '';
            proofStatus.textContent = 'Bukti pembayaran siap diunggah.';
          };
          reader.readAsDataURL(selectedProofFile);
        });
      }

      if (paymentUpload) {
        paymentUpload.addEventListener('click', async function() {
          const orderId = orderPaymentId.value;
          if (!orderId) {
            alert('Order ID tidak tersedia.');
            return;
          }
          if (!selectedProofFile) {
            alert('Pilih file bukti pembayaran terlebih dahulu.');
            return;
          }
          paymentUpload.disabled = true;
          const formData = new FormData();
          formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
          formData.append('payment_proof', selectedProofFile);
          try {
            const res = await fetch('/orders/' + orderId + '/payment-proof', {
              method: 'POST',
              headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
              },
              body: formData
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
              const errorDetail = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : null) || res.statusText || 'Gagal upload bukti pembayaran';
              alert(errorDetail);
              return;
            }
            proofStatus.textContent = 'Bukti pembayaran berhasil diunggah. Order berhasil dikonfirmasi.';
            proofStatus.style.color = '#16a34a';
            setTimeout(function() {
              window.location.reload();
            }, 1000);
          } catch (err) {
            console.error(err);
            alert('Gagal upload bukti pembayaran');
          } finally {
            paymentUpload.disabled = false;
          }
        });
      }

      const countdownEls = document.querySelectorAll('.order-countdown');
      if (countdownEls.length > 0) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const updateCountdowns = async function() {
          for (const el of countdownEls) {
            const createdAt = Number(el.dataset.orderCreatedAt) * 1000;
            if (!createdAt) continue;
            const seconds = Math.max(0, 30 - Math.floor((Date.now() - createdAt) / 1000));
            el.textContent = seconds;

            const orderId = el.dataset.orderId;
            const orderCard = document.querySelector(`.order-card[data-order-id="${orderId}"]`);
            const payButton = orderCard?.querySelector('.order-pay-button');

            if (seconds <= 0) {
              el.textContent = '0';
              if (payButton && !payButton.disabled) {
                payButton.disabled = true;
                payButton.textContent = 'Kadaluarsa';
                payButton.style.opacity = '0.65';
                payButton.style.cursor = 'not-allowed';
              }

              if (orderCard && !orderCard.dataset.cancelled) {
                orderCard.dataset.cancelled = 'true';
                try {
                  const res = await fetch('/orders/' + orderId + '/cancel', {
                    method: 'POST',
                    headers: {
                      'Accept': 'application/json',
                      'X-CSRF-TOKEN': token,
                    },
                  });
                  if (res.ok) {
                    const statusBadge = orderCard.querySelector('.order-status-badge');
                    if (statusBadge) {
                      statusBadge.textContent = 'Cancelled';
                    }
                    const countdownLabel = orderCard.querySelector('.order-countdown-row');
                    if (countdownLabel) {
                      countdownLabel.textContent = 'Pesanan dibatalkan otomatis karena waktu habis.';
                    }
                  }
                } catch (err) {
                  console.error('Failed to cancel expired order', err);
                }
              }
            }
          }
        };
        updateCountdowns();
        setInterval(updateCountdowns, 1000);
      }
    });
  </script>
</body>

</html>