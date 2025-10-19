<?php

declare(strict_types=1); ?>
<main id="app-main">
    <h3>Este es el body</h3>
    <!-- Contenedor para montar el modal de login no eliminar y no tocar ya que se renderizan los modales desde acá-->
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