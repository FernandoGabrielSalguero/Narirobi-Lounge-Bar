<?php declare(strict_types=1); ?>
<header class="nlb-header" role="banner">
  <div class="brand">NAIROBI Lounge Bar</div>

  <!-- Botón hamburguesa (abre panel con acciones) -->
<button id="menuToggle"
        class="icon-btn"
        aria-label="Abrir panel superior"
        aria-haspopup="true"
        aria-expanded="false"
        aria-controls="menuPanel">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M3 6h18v2H3V6zm0 5h18v2H3v-2zm0 5h18v2H3v-2z"/>
    </svg>
</button>

  <!-- Panel de menú -->
  <div id="menuPanel" class="menu-panel hidden" role="menu" aria-labelledby="menuToggle">
    <button id="openLogin"
            class="btn btn-menu"
            role="menuitem"
            aria-haspopup="dialog"
            aria-controls="modal"
            aria-expanded="false"
            data-testid="btn-open-login">
      Ingresar
    </button>
    <button id="openReserva"
            class="btn btn-menu"
            role="menuitem"
            aria-haspopup="dialog"
            aria-controls="modalReserva"
            aria-expanded="false"
            data-testid="btn-open-reserva">
      Reservas
    </button>
  </div>
</header>

<style>
  .nlb-header{
    position: sticky; top: 0; background:#000;
    border-bottom:1px solid rgba(255,255,255,.08);
    display:flex; align-items:center; justify-content:space-between;
    padding:.75rem 1rem; color:#fff; z-index:20;
  }
  .nlb-header .brand{ font-weight:700; letter-spacing:.3px; }
  .icon-btn{
    appearance:none; border:0; background:transparent; color:#fff;
    padding:.35rem; border-radius:.5rem; cursor:pointer;
  }
  .icon-btn:focus{ outline:2px solid #9ecbff; outline-offset:2px; }
  .menu-panel{
    position:absolute; right:.75rem; top:3.25rem;
    background:#111; border:1px solid rgba(255,255,255,.08);
    border-radius:.75rem; padding:.5rem; display:flex; gap:.5rem; flex-direction:column;
    min-width:180px; box-shadow:0 10px 30px rgba(0,0,0,.4);
  }
  .menu-panel.hidden{ display:none; }
  .btn{
    appearance:none; border:0; background:#fff; color:#000;
    padding:.6rem .95rem; border-radius:999px; font-weight:600; cursor:pointer;
    transition:transform .15s ease, opacity .15s ease;
  }
  .btn:focus{ outline:2px solid #9ecbff; outline-offset:2px; }
  .btn:hover{ transform:translateY(-1px); opacity:.95; }
  .btn-menu{ width:100%; text-align:center; }
</style>
