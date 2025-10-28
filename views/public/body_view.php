<?php

declare(strict_types=1); ?>
<!-- Fuente única: Forum -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Forum&display=swap" rel="stylesheet">

<main id="app-main" class="carta">


    <!-- Carrusel -->
    <section class="carousel" aria-label="Galería de imágenes destacadas">
        <div class="carousel-viewport">
            <div class="carousel-track" id="carouselTrack" role="list"></div>
        </div>
        <button class="carousel-btn prev" id="btnPrev" aria-label="Imagen anterior" type="button">‹</button>
        <button class="carousel-btn next" id="btnNext" aria-label="Imagen siguiente" type="button">›</button>
        <div class="carousel-dots" id="carouselDots" aria-hidden="true"></div>
    </section>

    <!-- Menú lateral flotante -->
    <aside id="sideMenu" class="sidemenu" aria-hidden="true" inert tabindex="-1">
        <header class="sidemenu-header">
            <div class="sidemenu-brand">
                <img src="/assets/logo_giftCard.svg" alt="Gift Card" class="sidemenu-logo" decoding="async" loading="lazy">
                <h4>Nuestro menú</h4>
            </div>
            <button id="closeMenu" class="close" aria-label="Cerrar menú">×</button>
        </header>
        <nav id="sideNav" class="sidemenu-content" aria-label="Navegación por categorías y subcategorías"></nav>
    </aside>
    <div id="backdrop" class="backdrop" hidden></div>

    <!-- Tarjetas de categorías -->
    <section id="categorias" class="categorias" aria-label="Categorías"></section>

    <!-- Listado de productos -->
    <section id="productos" class="productos"></section>

    <!-- Contenedor para montar el modal de login no eliminar y no tocar ya que se renderizan los modales desde acá-->
    <div id="modalHost" aria-live="polite"></div>
</main>

<!-- Botones menú-->
<button id="fabMenu" class="fab" aria-label="Abrir menú lateral" aria-haspopup="true" aria-controls="sideMenu" aria-expanded="false" type="button">
    <!-- ícono de categorías SVG -->
    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path d="M4 10h4V4H4v6zm6 0h10V4H10v6zm0 10h10v-6H10v6zM4 20h4v-6H4v6z"></path>
    </svg>
</button>
<button id="btnToMenu" class="fab fab-up" aria-label="Ir al menú principal" type="button">
    <!-- ícono flecha arriba SVG -->
    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path d="M12 5l-7 7h4v7h6v-7h4z"></path>
    </svg>
</button>


<style>
    :root {
        --color-texto: #111111;
        --color-fondo: #ffffff;
        --color-acento: #7c3aed;
        --spacing: 16px;
        --radius: 16px;
        --header-offset: 96px;

        /* NUEVO: variables usadas por los FABs */
        --fab-gap: 16px;
        --fab-size: 56px;
        /* altura estimada de la barra/banda inferior que te lo tapa (ajustá si cambia) */
        --footer-height: 64px;
    }


    /* Botón flotante secundario (volver al menú principal) */
    .fab-up {
        position: fixed;
        right: 16px;
        bottom: 88px;
        /* arriba del fab principal */
        width: 56px;
        height: 56px;
        border-radius: 999px;
        border: none;
        background: var(--color-acento);
        color: #fff;
        font-size: 22px;
        cursor: pointer;
        z-index: 1000;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        pointer-events: auto;
    }

    .fab svg,
    .fab-up svg {
        width: 24px;
        height: 24px;
        fill: currentColor;
        display: block;
    }

    /* FABs (unificado y por encima de barras inferiores) */
    .fab,
    .fab-up {
        position: fixed;
        right: var(--fab-gap);
        z-index: 1200;
        width: var(--fab-size);
        height: var(--fab-size);
        border-radius: 999px;
        border: 0;
        background: var(--color-acento);
        color: #fff;
        font-size: 22px;
        box-shadow: 0 6px 24px rgba(0, 0, 0, .25);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        pointer-events: auto;
    }

    /* principal (abre el drawer) */
    .fab {
        /* coloca el botón por encima de la banda inferior + margen */
        bottom: calc(env(safe-area-inset-bottom, 0px) + var(--footer-height) + var(--fab-gap));
    }

    /* secundario (subir al menú) */
    .fab-up {
        /* apoya encima del principal con un gap adicional */
        bottom: calc(env(safe-area-inset-bottom, 0px) + var(--footer-height) + var(--fab-gap) + var(--fab-size) + 12px);
    }

    /* ====== Tipografía global única ====== */
    html,
    body,
    * {
        font-family: "Forum", cursive !important;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
    }


    .carta {
        padding: 0;
        min-height: calc(100vh - 56px);
        background: var(--color-fondo);
        color: var(--color-texto);
    }

    /* Carrusel */
    .carousel {
        position: relative;
        width: 100%;
        margin: 0;
        overflow: hidden;
        /* sin bordes ni fondo visible */
        background: transparent;
        user-select: none;
        -webkit-user-select: none;
        touch-action: pan-y;
    }

    .carousel-viewport {
        width: 100%;
        overflow: hidden;
    }

    .carousel-track {
        display: flex;
        transition: transform 0.5s ease;
        will-change: transform;
    }

    .carousel-slide {
        min-width: 100%;
        width: 100%;
        aspect-ratio: 16/9;
        display: block;
        position: relative;
        overflow: hidden;
        background: transparent;
    }

    .carousel-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        pointer-events: none;
    }

    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.35);
        border: 0;
        width: 40px;
        height: 40px;
        border-radius: 999px;
        color: #fff;
        font-size: 24px;
        line-height: 40px;
        text-align: center;
        cursor: pointer;
        z-index: 2;
    }

    .carousel-btn.prev {
        left: 8px;
    }

    .carousel-btn.next {
        right: 8px;
    }

    .carousel-dots {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 8px;
        display: flex;
        gap: 6px;
        z-index: 2;
    }

    .carousel-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.6);
    }

    .carousel-dot.active {
        background: rgba(255, 255, 255, 0.95);
    }

    .sidemenu {
        position: fixed;
        top: 0;
        right: -320px;
        width: 320px;
        max-width: 86vw;
        height: 100vh;
        background: var(--color-fondo);
        color: #ffffffff;
        box-shadow: -8px 0 24px rgba(0, 0, 0, 0.2);
        transition: right 0.3s ease;
        z-index: 40;
        display: flex;
        flex-direction: column;
    }

    .sidemenu.open {
        right: 0;
    }

    .sidemenu-header {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }

    .sidemenu-brand {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 8px;
    }

    .sidemenu-logo {
        width: 150px;
        /* <-- ajustar tamaño aquí */
        height: 150px;
        /* <-- ajustar tamaño aquí */
        display: inline-block;
        filter: invert(1) brightness(100%);
        /* PNG/SVG en blanco */
    }

    .sidemenu-content {
        padding: 8px 0 16px 0;
        overflow: auto;
        flex: 1;
    }

    .sidemenu .group {
        padding: 8px 16px;
    }

    .sidemenu .group h5 {
        margin: 12px 0 6px 0;
        font-size: 14px;
        text-transform: uppercase;
        opacity: .8;
        color: var(--color-texto);
    }

    .sidemenu .group a {
        display: block;
        padding: 8px 10px;
        margin: 2px 0;
        border-radius: 8px;
        text-decoration: none;
        color: #ffffff;
    }

    .sidemenu .group a:hover {
        background: var(--color-texto);
    }

    .close {
        background: transparent;
        border: 0;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        color: var(--color-texto);
    }

    .backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
        z-index: 35;
    }

    /* Categorías (tarjetas) */
    .categorias {
        padding: 16px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .categorias-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    @media (min-width: 768px) {
        .categorias-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    .categoria-card {
        position: relative;
        border-radius: var(--radius);
        overflow: hidden;
        cursor: pointer;
        user-select: none;
        min-height: 120px;
        display: block;
        background: transparent;
        isolation: isolate;
        border: 1px solid rgba(255, 255, 255, 0.12);
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .categoria-card:hover {
        transform: translateY(-1px);
    }

    .categoria-card.open {
        box-shadow: 0 10px 28px rgba(0, 0, 0, .25);
    }

    .categoria-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: var(--color-acento);
        -webkit-mask: url('/assets/backgroundCards.png') center / cover no-repeat;
        mask: url('/assets/backgroundCards.png') center / cover no-repeat;
        z-index: 0;
        opacity: 1;
    }

    /* Header / botón de la tarjeta */
    .categoria-header {
        position: relative;
        z-index: 1;
        display: grid;
        place-items: center;
        min-height: 120px;
        padding: 0;
    }

    .categoria-toggle {
        appearance: none;
        background: rgba(0, 0, 0, 0.55);
        border: 0;
        border-radius: 10px;
        padding: 10px 16px;
        color: #fff;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: .5px;
        cursor: pointer;
    }

    .categoria-toggle:focus {
        outline: 2px solid rgba(255, 255, 255, .6);
        outline-offset: 2px;
    }

    .categoria-title {
        margin: 0;
        line-height: 1.2;
        text-align: center;
        white-space: normal;
        word-break: normal;
        overflow-wrap: normal;
        max-width: calc(100% - 24px);
    }

    /* Panel de subcategorías */
    .categoria-panel {
        position: relative;
        z-index: 1;
        padding: 10px 12px 12px 12px;
        background: rgba(0, 0, 0, 0.45);
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        display: none;
    }

    .categoria-card.open .categoria-panel {
        display: block;
    }

    .categoria-sublist {
        display: grid;
        grid-template-columns: 1fr;
        /* una sola columna */
        gap: 8px;
    }

    .categoria-sublist a {
        display: block;
        padding: 8px 10px;
        border-radius: 8px;
        text-decoration: none;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.08);
    }

    .categoria-sublist a:hover {
        background: rgba(255, 255, 255, 0.18);
    }

    .categoria-sublist a,
    .sub-list a {
        line-height: 1.25;
        word-break: normal;
        white-space: normal;
        background-color: #00000090;
    }

    /* Anclas con offset para evitar que el header tape el título */
    .anchor-offset {
        scroll-margin-top: var(--header-offset);
    }

    /* Productos */
    .productos {
        padding: 16px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .productos h3 {
        /* categoría */
        text-align: center;
        margin: 28px 0 12px 0;
        font-size: 22px;
    }

    .productos h2 {
        /* subcategoría */
        text-align: center;
        margin: 18px 0 14px 0;
        font-size: 20px;
    }

    .lista-productos {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .item {
        border-radius: var(--radius);
        padding: 12px;
        background-color: #0000004f;
    }

    .item-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 6px;
        color: var(--color-texto);
    }

    .item-nombre {
        font-weight: 600;
    }

    .item-precio {
        font-weight: 700;
    }

    .item-detalle {
        color: #ffffffff;
        opacity: .9;
        margin-bottom: 6px;
    }

    .badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        margin-left: 6px;
        background: var(--color-acento);
        color: #000000ff;
        vertical-align: middle;
        line-height: 1.8;
    }

    .aclaracion {
        display: inline-block;
        background: var(--color-acento);
        color: #000000ff;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 12px;
        margin: 2px 6px 0 0;
    }

    /* A11y y pequeños ajustes */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    #modalHost {
        position: relative;
        z-index: 50;
    }

    @media (max-width: 480px) {
        .item {
            background-color: #0000004f;
            padding: 10px;
        }
    }

    .sidemenu .group {
        padding: 8px 16px;
    }

    .cat-accordion {
        width: 100%;
        text-align: left;
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        color: #ffffff;
        font-weight: 600;
        padding: 10px 12px;
        cursor: pointer;
    }

    .cat-accordion[aria-expanded="true"] {
        background: rgba(255, 255, 255, 0.06);
    }

    .sub-list {
        padding: 6px 6px 0 6px;
    }

    .sub-list a {
        display: block;
        padding: 8px 10px;
        margin: 6px 0 0 0;
        border-radius: 8px;
        text-decoration: none;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.03);
    }

    .sub-list a:hover {
        background: var(--color-texto);
    }

    .item-precio {
        white-space: nowrap;
    }
</style>

<script>
    (function() {
        const API = '/controllers/body_controller.php?action=carta_data';

        const $track = document.getElementById('carouselTrack');
        const $dots = document.getElementById('carouselDots');
        const $btnPrev = document.getElementById('btnPrev');
        const $btnNext = document.getElementById('btnNext');

        const $productos = document.getElementById('productos');
        const $fab = document.getElementById('fabMenu');
        const $menu = document.getElementById('sideMenu');
        const $nav = document.getElementById('sideNav');
        const $close = document.getElementById('closeMenu');
        const $backdrop = document.getElementById('backdrop');

        const $btnToMenu = document.getElementById('btnToMenu');
        if ($btnToMenu) {
            $btnToMenu.addEventListener('click', () => {
                navigateTo('categorias');
            });
        }


        let slideIndex = 0;
        let autoTimer = null;
        let slidesCount = 0;
        let touchStartX = 0;

        function setTheme(colors) {
            if (!colors) return;
            document.documentElement.style.setProperty('--color-texto', colors.color_texto || '#111111');
            document.documentElement.style.setProperty('--color-fondo', colors.color_fondo || '#ffffff');
            document.documentElement.style.setProperty('--color-acento', colors.color_acento || '#7c3aed');
        }

        function buildCarousel(images) {
            $track.innerHTML = '';
            $dots.innerHTML = '';
            if (!images || images.length === 0) {
                document.querySelector('.carousel').style.display = 'none';
                return;
            }
            images.forEach((img, idx) => {
                const li = document.createElement('div');
                li.className = 'carousel-slide';
                li.setAttribute('role', 'listitem');

                const image = document.createElement('img');
                image.loading = 'lazy';
                image.alt = 'Imagen ' + (idx + 1);
                image.src = img.url;

                li.appendChild(image);
                $track.appendChild(li);

                const dot = document.createElement('span');
                dot.className = 'carousel-dot' + (idx === 0 ? ' active' : '');
                dot.dataset.index = String(idx);
                dot.addEventListener('click', () => goTo(idx));
                $dots.appendChild(dot);
            });
            slidesCount = images.length;
            goTo(0);
            startAuto();
        }

        function goTo(i) {
            slideIndex = (i + slidesCount) % slidesCount;
            const offset = -slideIndex * 100;
            $track.style.transform = 'translateX(' + offset + '%)';
            Array.from($dots.children).forEach((d, idx) => {
                d.classList.toggle('active', idx === slideIndex);
            });
            restartAuto();
        }

        function next() {
            goTo(slideIndex + 1);
        }

        function prev() {
            goTo(slideIndex - 1);
        }

        function startAuto() {
            stopAuto();
            autoTimer = setInterval(next, 5000);
        }

        function stopAuto() {
            if (autoTimer) clearInterval(autoTimer);
            autoTimer = null;
        }

        function restartAuto() {
            startAuto();
        }

        // Swipe
        $track.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            stopAuto();
        }, {
            passive: true
        });
        $track.addEventListener('touchend', (e) => {
            const dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 40) {
                if (dx < 0) next();
                else prev();
            } else {
                startAuto();
            }
        });

        $btnNext.addEventListener('click', next);
        $btnPrev.addEventListener('click', prev);

        // Orden ascendente por propiedad 'orden'. Los que no tienen 'orden' van al final.
        function sortByOrden(list) {
            if (!Array.isArray(list)) return [];
            return [...list].sort((a, b) => {
                const ao = (a && typeof a.orden !== 'undefined') ? Number(a.orden) : Number.POSITIVE_INFINITY;
                const bo = (b && typeof b.orden !== 'undefined') ? Number(b.orden) : Number.POSITIVE_INFINITY;
                return ao - bo;
            });
        }

        // Scroll con offset (usa .anchor-offset y espera al reflow si es necesario)
        function scrollToId(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Navega a un id, ejecutando una acción previa (cerrar acordeón/menú) y
        // esperando dos frames para evitar que el header tape el título tras el reflow.
        function navigateTo(id, beforeAction) {
            try {
                if (typeof beforeAction === 'function') beforeAction();
            } catch (_) {}
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    scrollToId(id);
                });
            });
        }


        // Construye tarjetas de categorías como acordeón (muestra subcategorías al expandir)
        function buildCategoryCards(grouped) {
            const $cat = document.getElementById('categorias');
            if (!$cat) return;

            const grid = document.createElement('div');
            grid.className = 'categorias-grid';

            sortByOrden(grouped).forEach(cat => {
                const card = document.createElement('article');
                card.className = 'categoria-card';
                card.setAttribute('aria-labelledby', 'cat-title-' + cat.categoria_id);

                // Header + botón accesible que alterna la apertura
                const header = document.createElement('div');
                header.className = 'categoria-header';

                const toggle = document.createElement('button');
                toggle.type = 'button';
                toggle.className = 'categoria-toggle';
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-controls', 'cat-panel-' + cat.categoria_id);

                const title = document.createElement('h4');
                title.id = 'cat-title-' + cat.categoria_id;
                title.className = 'categoria-title';
                title.textContent = String(cat.categoria_nombre || '').toUpperCase();

                toggle.appendChild(title);
                header.appendChild(toggle);

                // Panel con subcategorías
                const panel = document.createElement('div');
                panel.id = 'cat-panel-' + cat.categoria_id;
                panel.className = 'categoria-panel';
                panel.hidden = true;

                const subGrid = document.createElement('div');
                subGrid.className = 'categoria-sublist';

                sortByOrden(cat.subcategorias).forEach(sub => {
                    const a = document.createElement('a');
                    a.href = '#sub-' + sub.subcategoria_id;
                    a.textContent = sub.subcategoria_nombre;
                    a.addEventListener('click', (e) => {
                        e.preventDefault();
                        navigateTo('sub-' + sub.subcategoria_id, () => {
                            toggle.setAttribute('aria-expanded', 'false');
                            panel.hidden = true;
                            card.classList.remove('open');
                        });
                    });

                    subGrid.appendChild(a);
                });

                panel.appendChild(subGrid);

                // Toggle de apertura/cierre
                const onToggle = () => {
                    const open = toggle.getAttribute('aria-expanded') === 'true';
                    const willOpen = !open;
                    toggle.setAttribute('aria-expanded', String(willOpen));
                    panel.hidden = !willOpen;
                    card.classList.toggle('open', willOpen);
                };
                toggle.addEventListener('click', onToggle);
                // soporte teclado desde la tarjeta completa (Enter/Espacio)
                card.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        onToggle();
                    }
                });

                card.appendChild(header);
                card.appendChild(panel);
                grid.appendChild(card);
            });

            $cat.innerHTML = '';
            $cat.appendChild(grid);
        }

        window.addEventListener('hashchange', () => {
            const id = (location.hash || '').slice(1);
            if (id) navigateTo(id);
        });

        function buildMenu(grouped) {
            $nav.innerHTML = '';

            sortByOrden(grouped).forEach(cat => {
                const wrapper = document.createElement('div');
                wrapper.className = 'group';

                // Botón de categoría (acordeón)
                const catBtn = document.createElement('button');
                catBtn.type = 'button';
                catBtn.className = 'cat-accordion';
                catBtn.setAttribute('aria-expanded', 'false');
                catBtn.textContent = cat.categoria_nombre;

                const subList = document.createElement('div');
                subList.className = 'sub-list';
                subList.hidden = true;

                sortByOrden(cat.subcategorias).forEach(sub => {
                    const a = document.createElement('a');
                    a.href = '#sub-' + sub.subcategoria_id;
                    a.textContent = sub.subcategoria_nombre;
                    a.addEventListener('click', (e) => {
                        e.preventDefault();
                        navigateTo('sub-' + sub.subcategoria_id, () => {
                            toggleMenu(false);
                        });
                    });

                    subList.appendChild(a);
                });

                catBtn.addEventListener('click', () => {
                    const open = catBtn.getAttribute('aria-expanded') === 'true';
                    catBtn.setAttribute('aria-expanded', String(!open));
                    subList.hidden = open;
                });

                wrapper.appendChild(catBtn);
                wrapper.appendChild(subList);
                $nav.appendChild(wrapper);
            });
        }

        function buildProductos(grouped) {
            const frag = document.createDocumentFragment();

            sortByOrden(grouped).forEach(cat => {
                const catAnchor = document.createElement('div');
                catAnchor.id = 'cat-' + cat.categoria_id;
                catAnchor.className = 'anchor-offset';

                const h3 = document.createElement('h3');
                h3.textContent = cat.categoria_nombre;
                catAnchor.appendChild(h3);

                sortByOrden(cat.subcategorias).forEach(sub => {
                    const subWrap = document.createElement('section');
                    subWrap.id = 'sub-' + sub.subcategoria_id;
                    subWrap.className = 'anchor-offset';

                    const h2 = document.createElement('h2');
                    h2.textContent = sub.subcategoria_nombre;
                    subWrap.appendChild(h2);

                    const grid = document.createElement('div');
                    grid.className = 'lista-productos';

                    sub.productos.forEach(p => {
                        const card = document.createElement('article');
                        card.className = 'item';

                        const header = document.createElement('div');
                        header.className = 'item-header';

                        const nombre = document.createElement('div');
                        nombre.className = 'item-nombre';
                        nombre.textContent = p.nombre;

                        // icono (badge) si existe y tiene valor
                        if (typeof p.icono !== 'undefined' && p.icono && String(p.icono).trim() !== '') {
                            const badge = document.createElement('span');
                            badge.className = 'badge';
                            badge.textContent = String(p.icono).trim();
                            nombre.appendChild(badge);
                        }

                        const precio = document.createElement('div');
                        precio.className = 'item-precio';

                        // Mostrar sólo entero y con símbolo $
                        const n = Number(p.precio);
                        const entero = Number.isFinite(n) ? Math.trunc(n) : 0;
                        precio.textContent = '$\u00A0' + entero.toLocaleString('es-AR');

                        header.appendChild(nombre);
                        header.appendChild(precio);
                        card.appendChild(header);

                        if (p.detalle && String(p.detalle).trim() !== '') {
                            const detalle = document.createElement('div');
                            detalle.className = 'item-detalle';
                            detalle.textContent = p.detalle;
                            card.appendChild(detalle);
                        }

                        ['aclaracion_1', 'aclaracion_2', 'aclaracion_3'].forEach(k => {
                            const v = p[k];
                            if (v && String(v).trim() !== '') {
                                const chip = document.createElement('span');
                                chip.className = 'aclaracion';
                                chip.textContent = v;
                                card.appendChild(chip);
                            }
                        });

                        grid.appendChild(card);
                    });

                    subWrap.appendChild(grid);
                    frag.appendChild(catAnchor);
                    frag.appendChild(subWrap);
                });
            });

            $productos.innerHTML = '';
            $productos.appendChild(frag);
        }



        function toggleMenu(force) {
            const willOpen = typeof force === 'boolean' ? force : !$menu.classList.contains('open');

            if (willOpen) {
                // Mostrar menú: habilitar interacción y foco
                $menu.removeAttribute('inert');
                $menu.setAttribute('aria-hidden', 'false');
                $menu.classList.add('open');
                $backdrop.hidden = false;
                $fab.setAttribute('aria-expanded', 'true');

                // Enfocar un elemento dentro DEL MENÚ (evita warning de aria-hidden)
                setTimeout(() => {
                    $close.focus();
                }, 0);
            } else {
                // Mover foco FUERA del menú antes de ocultarlo (evita warning)
                $fab.focus();
                $menu.classList.remove('open');
                $backdrop.hidden = true;
                $fab.setAttribute('aria-expanded', 'false');
                $menu.setAttribute('aria-hidden', 'true');
                $menu.setAttribute('inert', '');
            }
        }

        // Cerrar con Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && $menu.classList.contains('open')) {
                toggleMenu(false);
            }
        });

        $close.addEventListener('click', () => toggleMenu(false));
        $backdrop.addEventListener('click', () => toggleMenu(false));


        $fab.addEventListener('click', () => {
            const willOpen = !$menu.classList.contains('open');
            toggleMenu(willOpen);
        });

        // Carga inicial
        fetch(API, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(payload => {
                if (!payload.ok) throw new Error(payload.error || 'Error desconocido');
                const {
                    colors,
                    images,
                    products
                } = payload.data;
                setTheme(colors);
                buildCarousel(images);

                // Aseguramos orden por 'orden' en categorías y subcategorías
                const categoriasOrdenadas = sortByOrden(products).map(cat => ({
                    ...cat,
                    subcategorias: sortByOrden(cat.subcategorias || [])
                }));

                buildMenu(categoriasOrdenadas);
                buildCategoryCards(categoriasOrdenadas);
                buildProductos(categoriasOrdenadas);
            })

            .catch(err => {
                console.error('Error cargando carta:', err);
            });
    })();
</script>