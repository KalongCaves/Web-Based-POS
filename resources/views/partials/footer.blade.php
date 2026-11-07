<footer class="kedai-footer">
  <div class="kedai-footer-container">

    ```
    <div class="kedai-footer-grid">

      {{-- Tentang Kedai --}}
      <div class="kedai-footer-section kedai-footer-about">
        <h3>Kedai Anda</h3>
        <p>
          Tempat untuk menikmati makanan dan minuman dengan suasana
          nyaman dan pilihan menu yang siap menemani aktivitas Anda.
        </p>
      </div>

      {{-- Sosial Media --}}
      <div class="kedai-footer-section">
        <h3>Sosial Media</h3>

        <div class="kedai-footer-contact">
          <span class="kedai-footer-label">WhatsApp</span>
          <a href="https://wa.me/6285222206770" target="_blank" rel="noopener">
            +62 852-2220-6770
          </a>
        </div>

        <div class="kedai-footer-contact">
          <span class="kedai-footer-label">Instagram</span>
          <span>@KedaiAnda</span>
        </div>

        <div class="kedai-footer-contact">
          <span class="kedai-footer-label">Facebook</span>
          <span>Kedai Anda</span>
        </div>
      </div>

      {{-- Alamat --}}
      <div class="kedai-footer-section">
        <h3>Alamat</h3>
        <p>
          Jl. Ciguruwik Gang Anis No.44,<br>
          Cinunuk, Kec. Cileunyi,<br>
          Kabupaten Bandung,<br>
          Jawa Barat 40624
        </p>
      </div>

    </div>

    <div class="kedai-footer-divider"></div>

    {{-- Copyright --}}
    <div class="kedai-footer-bottom">
      <p>
        &copy; {{ date('Y') }} Kedai Anda. All rights reserved.
      </p>

      <p>
        Dibuat untuk memberikan pengalaman terbaik bagi pelanggan Kedai Anda.
      </p>
    </div>
    ```

  </div>
</footer>

<style>
  .kedai-footer {
    width: 100%;
    margin-top: 3rem;
    background: #111827;
    color: #ffffff;
    display: block;
    position: relative;
    z-index: 1;
  }

  .kedai-footer-container {
    width: 100%;
    max-width: 1600px;
    margin: 0 auto;
    padding: 3rem 1.25rem 1.5rem;
  }

  .kedai-footer-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1.3fr;
    gap: 3rem;
  }

  .kedai-footer-section h3 {
    margin: 0 0 1rem;
    color: #ffffff;
    font-size: 1.1rem;
    font-weight: 700;
  }

  .kedai-footer-section p {
    margin: 0;
    color: #d1d5db;
    font-size: 0.95rem;
    line-height: 1.7;
  }

  .kedai-footer-about p {
    max-width: 420px;
  }

  .kedai-footer-contact {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    margin-bottom: 0.85rem;
  }

  .kedai-footer-label {
    color: #9ca3af;
    font-size: 0.8rem;
  }

  .kedai-footer-contact a,
  .kedai-footer-contact span:last-child {
    color: #ffffff;
    font-size: 0.95rem;
  }

  .kedai-footer-contact a {
    text-decoration: none;
    transition: color 0.2s ease;
  }

  .kedai-footer-contact a:hover {
    color: #fb7e00;
  }

  .kedai-footer-divider {
    width: 100%;
    height: 1px;
    margin: 2rem 0 1.25rem;
    background: rgba(255, 255, 255, 0.15);
  }

  .kedai-footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
  }

  .kedai-footer-bottom p {
    margin: 0;
    color: #9ca3af;
    font-size: 0.8rem;
  }

  @media (max-width: 768px) {
    .kedai-footer-grid {
      grid-template-columns: 1fr;
      gap: 2rem;
    }

    .kedai-footer-container {
      padding: 2.5rem 1.25rem 1.25rem;
    }

    .kedai-footer-bottom {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5rem;
    }
  }
</style>