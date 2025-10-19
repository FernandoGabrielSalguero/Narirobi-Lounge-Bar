<?php

declare(strict_types=1); ?>
<header class="nlb-header" role="banner">
    <div class="brand">Nairobi Lounge Bar</div>
    <button id="openLogin"
        class="btn"
        aria-haspopup="dialog"
        aria-controls="modal"
        aria-expanded="false"
        data-testid="btn-open-login">
        Ingresar
    </button>
</header>

<style>
    /* Scoped al header para evitar colisiones */
    .nlb-header {
        position: sticky;
        top: 0;
        background: #000;
        border-bottom: 1px solid rgba(255, 255, 255, .08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .75rem 1rem;
        color: #fff;
        z-index: 10;
    }

    .nlb-header .brand {
        font-weight: 700;
        letter-spacing: .3px;
    }

    .btn {
        appearance: none;
        border: 0;
        background: #fff;
        color: #000;
        padding: .6rem .95rem;
        border-radius: 999px;
        font-weight: 600;
        cursor: pointer;
        transition: transform .15s ease, opacity .15s ease;
    }

    .btn:focus {
        outline: 2px solid #9ecbff;
        outline-offset: 2px;
    }

    .btn:hover {
        transform: translateY(-1px);
        opacity: .95;
    }
</style>