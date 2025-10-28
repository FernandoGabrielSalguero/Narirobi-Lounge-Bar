<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nairobi Lounge Bar — Ordenar Catálogo</title>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Framework CDN -->
    <link rel="stylesheet" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css">
    <script src="https://www.fernandosalguero.com/cdn/assets/javascript/framework.js" defer></script>

    <!-- PDF libs -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" defer></script>

    <style>
        /* Tarjetas estilo lista ordenable */
        .sortable-card {
            background: #f5f3ff;
            border-radius: 12px;
            padding: .75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            border: 1px solid #e9d5ff;
        }

        /* Acordeones */
        .accordion {
            border-radius: 12px;
            border: 1px solid #ede9fe;
            background: #faf5ff;
            margin-bottom: 1rem;
        }

        .accordion-header {
            padding: .75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            cursor: pointer;
            user-select: none;
        }

        .accordion-header .title {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .accordion-content {
            padding: .5rem 1rem 1rem;
            display: block;
        }

        .accordion.collapsed .accordion-content {
            display: none;
        }

        .drag-handle {
            cursor: grab;
            padding: .25rem;
            border-radius: 8px;
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        .prod-actions {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .prod-select {
            width: 18px;
            height: 18px;
        }

        .sortable-card+.sortable-card {
            margin-top: .5rem;
        }

        .pill {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            background: #7c3aed;
            color: #fff;
            font-weight: 700;
            font-size: .9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 26px;
        }

        .text-ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .handle {
            cursor: grab;
            padding: .25rem;
            border-radius: 8px;
        }

        .handle:active {
            cursor: grabbing;
        }

        .level-title {
            font-weight: 700;
            color: #5b21b6;
            margin: .25rem 0 .5rem;
        }

        .cat-block {
            margin-bottom: 1.5rem;
        }

        .sub-block {
            margin: .75rem 0 1rem 1rem;
            padding-left: .75rem;
            border-left: 3px solid #ede9fe;
        }

        .prod-block {
            margin: .5rem 0 0 2rem;
            padding-left: .75rem;
            border-left: 2px dashed #efe9ff;
        }

        .muted {
            color: #6b7280;
            font-size: .875rem;
        }

        .btn-row {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .ghost {
            opacity: .4;
        }

        .drag-over {
            outline: 2px dashed #7c3aed;
            outline-offset: 2px;
        }

        /* A11y helpers */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }
    </style>
</head>

<body>
    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <span class="material-icons logo-icon">dashboard</span>
                <span class="logo-text">ISO</span>
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li onclick="location.href='admin_dashboard_view.php'">
                        <span class="material-icons" style="color:#5b21b6;">home</span><span class="link-text">Inicio</span>
                    </li>
                    <li onclick="location.href='admin_carta_view.php'" aria-current="page">
                        <span class="material-icons" style="color:#5b21b6;">restaurant_menu</span><span class="link-text">Carta</span>
                    </li>
                    <li onclick="location.href='admin_orden_view.php'" aria-current="page">
                        <span class="material-icons" style="color:#5b21b6;">format_list_numbered</span><span class="link-text">Orden Carta</span>
                    </li>
                    <li onclick="location.href='admin_gift_card_view.php'">
                        <span class="material-icons" style="color:#5b21b6;">card_giftcard</span><span class="link-text">Gift Card</span>
                    </li>
                    <li onclick="location.href='admin_reservas_view.php'">
                        <span class="material-icons" style="color:#5b21b6;">event</span><span class="link-text">Reservas</span>
                    </li>
                    <li onclick="location.href='../../logout.php'">
                        <span class="material-icons" style="color:red;">logout</span><span class="link-text">Salir</span>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <button class="btn-icon" onclick="toggleSidebar()">
                    <span class="material-icons" id="collapseIcon">chevron_left</span>
                </button>
            </div>
        </aside>

        <div class="main">
            <header class="navbar">
                <button class="btn-icon" onclick="toggleSidebar()">
                    <span class="material-icons">menu</span>
                </button>
                <div class="navbar-title">Ordenar Catálogo</div>
            </header>

            <section class="content">
                <div class="card">
                    <h2>Orden de Categorías, Subcategorías y Productos</h2>
                    <p class="muted">Arrastrá para reordenar. El número dentro del círculo refleja el <strong>orden</strong> en base de datos.</p>
                    <div class="btn-row">
                        <button id="btnGuardar" class="btn btn-aceptar" aria-label="Guardar cambios de orden">Guardar cambios</button>
                        <button id="btnRevertir" class="btn btn-cancelar" aria-label="Revertir cambios no guardados">Revertir</button>
                        <button id="btnImprimir" class="btn btn-info" aria-label="Imprimir selección">Imprimir</button>
                    </div>
                </div>

                <div class="card" id="bloqueOrden">
                    <h3 class="level-title">Catálogo</h3>
                    <p class="muted" style="margin-top:.25rem">Arrastrá categorías o subcategorías completas. Podés tildar productos para imprimir.</p>
                    <div id="estructuraContainer" aria-label="Categorías" role="list"></div>
                </div>
                <!-- Área oculta para render de impresión -->
                <div id="printArea" class="sr-only"></div>
            </section>
        </div>
    </div>

    <script>
        (() => {
            'use strict';

            const API = '../../controllers/admin_rorden_controller.php';

            const estado = {
                original: null, // estructura recibida desde el backend
                working: null, // estructura en memoria que se reordena
                indices: { // mapeos rápidos
                    categorias: new Map(), // idCat -> orden
                    subcategorias: new Map(), // idSub -> orden
                    productos: new Map() // idProd -> orden
                }
            };

            const $estruct = document.getElementById('estructuraContainer');
            const $btnGuardar = document.getElementById('btnGuardar');
            const $btnRevertir = document.getElementById('btnRevertir');

            function notify(type, msg) {
                try {
                    if (typeof showAlert === 'function') showAlert(type, msg);
                    else alert(msg);
                } catch (e) {
                    alert(msg);
                }
            }

            // makeSortable permite arrastrar bloques homogéneos (categorías completas, subcategorías completas o tarjetas de producto)
            function makeSortable(container, onSort, dragSelector = '[draggable="true"]', itemSelector = '[draggable="true"]') {
                let dragEl = null;

                container.addEventListener('dragstart', (e) => {
                    const t = e.target;
                    const handle = (t instanceof Element) ? t.closest(dragSelector) : null;
                    const item = handle ? handle.closest(itemSelector) : null;
                    if (!item) return;
                    dragEl = item;
                    dragEl.classList.add('ghost');
                    e.dataTransfer.effectAllowed = 'move';
                    // usar data-id en lugar de dataset para evitar cast
                    const idForDnd = item.getAttribute ? (item.getAttribute('data-id') || '') : '';
                    e.dataTransfer.setData('text/plain', idForDnd);
                });

                container.addEventListener('dragend', (e) => {
                    const item = e.target.closest('[draggable="true"]');
                    if (item) item.classList.remove('ghost');
                    dragEl = null;
                    container.querySelectorAll('.drag-over').forEach(x => x.classList.remove('drag-over'));
                });

                container.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    const t = e.target;
                    const over = (t instanceof Element) ? t.closest(itemSelector) : null;
                    if (!over || over === dragEl) return;
                    over.classList.add('drag-over');
                    const rect = over.getBoundingClientRect();
                    const next = (e.clientY - rect.top) / rect.height > 0.5;
                    if (dragEl) {
                        container.insertBefore(dragEl, next ? over.nextElementSibling : over);
                    }
                });


                container.addEventListener('dragleave', (e) => {
                    const item = e.target.closest('[draggable="true"]');
                    if (item) item.classList.remove('drag-over');
                });

                container.addEventListener('drop', (e) => {
                    e.preventDefault();
                    container.querySelectorAll('.drag-over').forEach(x => x.classList.remove('drag-over'));
                    if (typeof onSort === 'function') onSort();
                });
            }

            // --------- Render ----------
            function pill(n) {
                return `<span class="pill" aria-hidden="true">${n}</span>`;
            }

            // Control al final de cada tarjeta/encabezado
            function endControl(level, item) {
                if (level === 'prod') {
                    const catName = item.categoria_nombre || '';
                    const subName = item.subcategoria_nombre || '';
                    const price = (item && typeof item.precio !== 'undefined') ? item.precio : '';
                    return `
                      <label class="prod-actions" title="Seleccionar para imprimir">
                        <input type="checkbox" class="prod-select"
                          data-producto-id="${item.id}"
                          data-producto-nombre="${item.nombre}"
                          data-precio="${price}"
                          data-cat="${catName}"
                          data-sub="${subName}">
                      </label>`;
                }
                return `<span class="material-icons drag-handle" aria-hidden="true">drag_indicator</span>`;
            }


            function renderEstructura(data) {
                // CONTENEDOR PRINCIPAL DE CATEGORÍAS (cada categoría es un acordeón y draggable como bloque)
                $estruct.innerHTML = data.categorias
                    .sort((a, b) => a.orden - b.orden)
                    .map(c => {
                        const subs = (data.mapa_subcategorias_por_categoria[c.id] || [])
                            .sort((a, b) => a.orden - b.orden);
                        const subsHtml = subs.map(s => {
                            const prods = (data.mapa_productos_por_cat_sub[`${c.id}_${s.id}`] || [])
                                .sort((a, b) => a.orden - b.orden);
                            const prodsHtml = prods.map(p => `
                                <div class="sortable-card" draggable="true" data-id="${p.id}" data-level="prod" role="listitem" aria-label="${p.nombre}">
                                  <div style="display:flex; align-items:center; gap:.75rem; min-width:0;">
                                    ${pill(p.orden)}
                                    <div class="text-ellipsis">${p.nombre}</div>
                                  </div>
                                ${endControl('prod', { id: p.id, nombre: p.nombre, categoria_nombre: c.nombre, subcategoria_nombre: s.nombre, precio: (p.precio ?? '') })}
                                </div>
                            `).join('');
                            return `
                            <div class="accordion sub-accordion" data-sub="${s.id}" data-cat="${c.id}">
                              <div class="accordion-header" draggable="true" data-level="sub">
                                <div class="title">
                                  ${pill(s.orden)} <div class="text-ellipsis"><strong>${s.nombre}</strong></div>
                                </div>
                                ${endControl('sub', s)}
                              </div>
                              <div class="accordion-content">
                                <div class="prod-block" id="prod-${c.id}-${s.id}" role="list" aria-label="Productos de ${s.nombre}">
                                  ${prodsHtml}
                                </div>
                              </div>
                            </div>`;
                        }).join('');
                        return `
                        <div class="accordion cat-accordion" data-cat="${c.id}">
                          <div class="accordion-header" draggable="true" data-level="cat">
                            <div class="title">
                              ${pill(c.orden)} <div class="text-ellipsis"><strong>${c.nombre}</strong></div>
                            </div>
                            ${endControl('cat', c)}
                          </div>
                          <div class="accordion-content">
                            <div class="sub-block" id="subc-${c.id}" aria-label="Subcategorías de ${c.nombre}" role="list">
                              ${subsHtml}
                            </div>
                          </div>
                        </div>`;
                    }).join('');

                // Sortables de bloque
                makeSortable($estruct, recomputeOrdersCategorias, '[data-level="cat"]', '.cat-accordion'); // categorías
                document.querySelectorAll('[id^="subc-"]').forEach(cont => {
                    makeSortable(cont, () => recomputeOrdersSubcategorias(cont), '[data-level="sub"]', '.sub-accordion');
                });
                document.querySelectorAll('[id^="prod-"]').forEach(cont => {
                    makeSortable(cont, () => recomputeOrdersProductos(cont), '[draggable="true"].sortable-card', '.sortable-card');
                });

                // Toggle acordeones
                $estruct.querySelectorAll('.accordion-header').forEach(h => {
                    h.addEventListener('click', (e) => {
                        // evitar que el click en el handle o checkbox colapse
                        const t = e.target;
                        if (t instanceof Element) {
                            if (t.closest('.drag-handle') || t.closest('.prod-actions')) return;
                        }
                        const acc = h.parentElement;
                        if (acc) acc.classList.toggle('collapsed');
                    });
                });
            }

            // --------- Recalcular órdenes en memoria (y reflejar en UI) ----------
            function renumerarPills(container) {
                [...container.querySelectorAll('.sortable-card')].forEach((el, idx) => {
                    const pillEl = el.querySelector('.pill');
                    if (pillEl) pillEl.textContent = String(idx + 1);
                });
            }

            function recomputeOrdersCategorias() {
                const items = [...$estruct.querySelectorAll(':scope > .cat-accordion')];
                items.forEach((el, idx) => {
                    const id = Number(el.getAttribute('data-cat'));
                    estado.indices.categorias.set(id, idx + 1);
                    // pill en header
                    const pillEl = el.querySelector('.accordion-header .pill');
                    if (pillEl) pillEl.textContent = String(idx + 1);
                });
            }

            function recomputeOrdersSubcategorias(cont) {
                const items = [...cont.querySelectorAll(':scope > .sub-accordion')];
                items.forEach((el, idx) => {
                    const id = Number(el.getAttribute('data-sub'));
                    estado.indices.subcategorias.set(id, idx + 1);
                    const pillEl = el.querySelector('.accordion-header .pill');
                    if (pillEl) pillEl.textContent = String(idx + 1);
                });
            }


            function recomputeOrdersProductos(cont) {
                const items = [...cont.querySelectorAll(':scope > .sortable-card')];
                items.forEach((el, idx) => {
                    estado.indices.productos.set(Number(el.dataset.id), idx + 1);
                });
                renumerarPills(cont);
            }

            // --------- API ----------
            async function cargar() {
                const res = await fetch(API, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const json = await res.json();
                if (!json.ok) throw new Error(json.error || 'No se pudo cargar la estructura');
                estado.original = structuredClone(json.data);
                estado.working = structuredClone(json.data);

                estado.indices.categorias.clear();
                estado.indices.subcategorias.clear();
                estado.indices.productos.clear();

                json.data.categorias.forEach(c => estado.indices.categorias.set(c.id, c.orden));
                json.data.subcategorias.forEach(s => estado.indices.subcategorias.set(s.id, s.orden));
                json.data.productos.forEach(p => estado.indices.productos.set(p.id, p.orden));

                renderEstructura(json.data);
            }


            async function guardar() {
                // Orden de categorías según acordeones raíz
                const cats = [...$estruct.querySelectorAll(':scope > .cat-accordion')].map((el, i) => ({
                    id: Number(el.getAttribute('data-cat')),
                    orden: i + 1
                }));

                // Orden de subcategorías dentro de cada categoría
                const subsSet = new Map();
                document.querySelectorAll('[id^="subc-"]').forEach(cont => {
                    [...cont.querySelectorAll(':scope > .sub-accordion')].forEach((el, i) => {
                        subsSet.set(Number(el.getAttribute('data-sub')), i + 1);
                    });
                });
                const subs = [...subsSet.entries()].map(([id, orden]) => ({
                    id,
                    orden
                }));

                // Orden de productos dentro de cada subcategoría
                const prodsSet = new Map();
                document.querySelectorAll('[id^="prod-"]').forEach(cont => {
                    [...cont.querySelectorAll(':scope > .sortable-card')].forEach((el, i) => {
                        prodsSet.set(Number(el.getAttribute('data-id')), i + 1);
                    });
                });
                const prods = [...prodsSet.entries()].map(([id, orden]) => ({
                    id,
                    orden
                }));

                const payload = {
                    action: 'updateOrder',
                    categorias: cats,
                    subcategorias: subs,
                    productos: prods
                };

                const res = await fetch(API, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!json.ok) throw new Error(json.error || 'No se pudo guardar');
                notify('success', '¡Orden actualizado!');
                await cargar();
            }

            // --------- Impresión PDF ----------
            function construirPrintArea() {
                const checks = [...document.querySelectorAll('.prod-select:checked')];
                const byCatSub = new Map(); // Map(cat => Map(sub => [{nombre, precio}]))
                checks.forEach(chk => {
                    const cat = chk.getAttribute('data-cat') || 'Sin categoría';
                    const sub = chk.getAttribute('data-sub') || 'Sin subcategoría';
                    const nombre = chk.getAttribute('data-producto-nombre') || '';
                    const precio = chk.getAttribute('data-precio') || '';
                    if (!byCatSub.has(cat)) byCatSub.set(cat, new Map());
                    const m = byCatSub.get(cat);
                    if (!m.has(sub)) m.set(sub, []);
                    m.get(sub).push({
                        nombre,
                        precio
                    });
                });

                const $print = document.getElementById('printArea');
                $print.innerHTML = '';

                // Header
                const header = document.createElement('div');
                header.style.textAlign = 'center';
                header.style.margin = '0 0 16px 0';
                header.innerHTML = `
                    <img src="../../assets/logo negro.png" alt="Logo" style="height:60px; display:block; margin:0 auto 8px;">
                    <h1 style="margin:0; font-size:24px;">Nuestro Menú</h1>
                `;
                $print.appendChild(header);

                // Contenido agrupado
                byCatSub.forEach((subMap, cat) => {
                    const catEl = document.createElement('div');
                    catEl.innerHTML = `<h2 style="text-align:center; margin:16px 0 8px; font-size:20px;">${cat}</h2>`;
                    $print.appendChild(catEl);

                    subMap.forEach((items, sub) => {
                        const subEl = document.createElement('div');
                        subEl.innerHTML = `<h3 style="text-align:center; margin:8px 0 8px; font-size:18px;">${sub}</h3>`;
                        $print.appendChild(subEl);

                        // grid 2 columnas como en la captura
                        const grid = document.createElement('div');
                        grid.style.display = 'grid';
                        grid.style.gridTemplateColumns = '1fr 1fr';
                        grid.style.gap = '12px';
                        items.forEach(it => {
                            const card = document.createElement('div');
                            card.style.border = '1px solid #ddd';
                            card.style.borderRadius = '10px';
                            card.style.padding = '10px 12px';
                            card.style.display = 'flex';
                            card.style.justifyContent = 'space-between';
                            card.style.alignItems = 'center';
                            card.innerHTML = `<div style="font-weight:700;">${it.nombre}</div><div>$ ${it.precio}</div>`;
                            grid.appendChild(card);
                        });
                        $print.appendChild(grid);
                    });
                });

                return $print;
            }

            async function imprimirPDF() {
                const $print = construirPrintArea();
                if ($print.querySelectorAll('input, .prod-select').length === 0 && $print.textContent.trim() === '') {
                    // si no hay nada seleccionado, mostrar aviso
                    if (![...document.querySelectorAll('.prod-select:checked')].length) {
                        notify('info', 'Seleccioná al menos un producto.');
                        return;
                    }
                }
                // Render con html2canvas y exportar con jsPDF
                const canvas = await html2canvas($print, {
                    scale: 2,
                    backgroundColor: '#ffffff'
                });
                const imgData = canvas.toDataURL('image/png');
                const {
                    jsPDF
                } = window.jspdf;
                const pdf = new jsPDF({
                    unit: 'pt',
                    format: 'a4'
                });
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const imgWidth = pageWidth;
                const imgHeight = canvas.height * (imgWidth / canvas.width);

                let y = 0;
                if (imgHeight <= pageHeight) {
                    pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
                } else {
                    // paginado simple vertical
                    let position = 0;
                    while (position < imgHeight) {
                        pdf.addImage(imgData, 'PNG', 0, -position, imgWidth, imgHeight);
                        position += pageHeight;
                        if (position < imgHeight) pdf.addPage();
                    }
                }
                pdf.save('menu.pdf');
            }

            // --------- Eventos ----------
            document.addEventListener('DOMContentLoaded', () => {
                cargar().catch(err => notify('error', err.message));
            });

            $btnGuardar.addEventListener('click', () => {
                guardar().catch(err => notify('error', err.message));
            });

            $btnRevertir.addEventListener('click', () => {
                if (!estado.original) {
                    notify('info', 'Nada para revertir');
                    return;
                }
                renderEstructura(estado.original);
                notify('info', 'Se revirtió al último estado cargado.');
            });

            // Imprimir
            const $btnImprimir = document.getElementById('btnImprimir');
            $btnImprimir.addEventListener('click', () => {
                imprimirPDF().catch(err => notify('error', err.message));
            });


        })();
    </script>

    <div id="alert-container" class="alert-container" style="position:fixed;bottom:1rem;right:1rem;z-index:9999;"></div>
</body>

</html>