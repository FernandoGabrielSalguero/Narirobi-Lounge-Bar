<?php

declare(strict_types=1); ?>
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
    <button id="fabMenu" class="fab" aria-haspopup="true" aria-controls="sideMenu" aria-expanded="false">☰</button>
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

<style>
    :root {
        --color-texto: #111111;
        --color-fondo: #ffffff;
        --color-acento: #7c3aed;
        --spacing: 16px;
        --radius: 16px;
        --header-offset: 96px;
        /* offset para que no tape el header al hacer scroll */
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

    /* FAB + Side menu */
    .fab {
        position: fixed;
        right: 16px;
        bottom: 16px;
        width: 56px;
        height: 56px;
        border-radius: 999px;
        border: none;
        background: var(--color-acento);
        color: #fff;
        font-size: 22px;
        cursor: pointer;
        z-index: 30;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
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
        /* sin borde visible */
        cursor: pointer;
        user-select: none;
        min-height: 120px;
        display: grid;
        place-items: center;
        padding: 0;
        /* Colorea el patrón PNG con el color_acento usando mask */
        background: transparent;
        isolation: isolate;
    }

    /* Capa con máscara: pinta las “líneas” del PNG con color_acento */
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

    .categoria-card .categoria-title {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
        margin: 0;
        padding: 10px 16px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: .5px;
        color: #fff;
        background: rgba(0, 0, 0, 0.65);
        border-radius: 8px;
        line-height: 1.2;
        text-align: center;
        /* permitir múltiples líneas sin cortar palabras */
        white-space: normal;
        word-break: normal;
        overflow-wrap: normal;
        /* limitar ancho para respiración en bordes */
        max-width: calc(100% - 24px);
    }

    /* sin outline de hover/focus para mantener tarjetas sin borde */
    .categoria-card:focus-visible,
    .categoria-card:hover {
        outline: none;
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
        color: #fff;
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

        // Scroll con offset para no tapar títulos con el header
        function scrollToId(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Construye tarjetas de categorías
        function buildCategoryCards(grouped) {
            const $cat = document.getElementById('categorias');
            if (!$cat) return;

            const grid = document.createElement('div');
            grid.className = 'categorias-grid';

            sortByOrden(grouped).forEach(cat => {
                const card = document.createElement('article');
                card.className = 'categoria-card';
                card.setAttribute('role', 'button');
                card.setAttribute('tabindex', '0');
                card.setAttribute('aria-label', 'Ir a ' + cat.categoria_nombre);

                const title = document.createElement('h4');
                title.className = 'categoria-title';
                title.textContent = String(cat.categoria_nombre || '').toUpperCase();

                // Click / Enter para navegar
                const go = () => scrollToId('cat-' + cat.categoria_id);
                card.addEventListener('click', go);
                card.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        go();
                    }
                });

                card.appendChild(title);
                grid.appendChild(card);
            });

            $cat.innerHTML = '';
            $cat.appendChild(grid);
        }


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
                        scrollToId('sub-' + sub.subcategoria_id);
                        toggleMenu(false);
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
                        precio.textContent = '$ ' + entero.toLocaleString('es-AR');

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

        $fab.addEventListener('click', () => toggleMenu());
        $close.addEventListener('click', () => toggleMenu(false));
        $backdrop.addEventListener('click', () => toggleMenu(false));

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