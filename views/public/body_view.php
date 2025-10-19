<?php

declare(strict_types=1); ?>
<main id="app-main">
    <!-- Contenedor para montar el modal de login -->
    <div id="modalHost" aria-live="polite"></div>
</main>

<style>
    #app-main {
        padding: 2rem 1rem;
        color: #fff;
        background: #000;
        min-height: calc(100vh - 56px);
    }

    #modalHost {
        position: relative;
        z-index: 5;
    }
</style>