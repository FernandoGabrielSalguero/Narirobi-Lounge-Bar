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
                    </div>
                </div>

                <div class="card" id="bloqueOrden">
                    <h3 class="level-title">Categorías</h3>
                    <div id="categoriasContainer" role="list" aria-label="Lista de categorías"></div>
                    <p class="muted" style="margin-top:.75rem">Dentro de cada categoría se muestran sus subcategorías y productos asociados.</p>
                    <div id="estructuraContainer"></div>
                </div>
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

            const $catContainer = document.getElementById('categoriasContainer');
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

            // --------- Drag & Drop helper (intra-list) ----------
            function makeSortable(container, onSort) {
                let dragEl = null;

                container.addEventListener('dragstart', (e) => {
                    const item = e.target.closest('[draggable="true"]');
                    if (!item) return;
                    dragEl = item;
                    item.classList.add('ghost');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', item.dataset.id);
                });

                container.addEventListener('dragend', (e) => {
                    const item = e.target.closest('[draggable="true"]');
                    if (item) item.classList.remove('ghost');
                    dragEl = null;
                    container.querySelectorAll('.drag-over').forEach(x => x.classList.remove('drag-over'));
                });

                container.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    const item = e.target.closest('[draggable="true"]');
                    if (!item || item === dragEl) return;
                    item.classList.add('drag-over');
                    const rect = item.getBoundingClientRect();
                    const next = (e.clientY - rect.top) / (rect.height) > 0.5;
                    container.insertBefore(dragEl, next ? item.nextElementSibling : item);
                });

                container.addEventListener('dragleave', (e) => {
                    const item = e.target.closest('[draggable="true"]');
                    if (item) item.classList.remove('drag-over');
                });

                container.addEventListener('drop', (e) => {
                    e.preventDefault();
                    container.querySelectorAll('.drag-over').forEach(x => x.classList.remove('drag-over'));
                    onSort?.();
                });
            }

            // --------- Render ----------
            function pill(n) {
                return `<span class="pill" aria-hidden="true">${n}</span>`;
            }

            function dots() {
                return `<span class="material-icons handle" aria-hidden="true">more_vert</span>`;
            }

            function renderEstructura(data) {
                // Categorías (lista global para mover orden de categorías)
                $catContainer.innerHTML = data.categorias
                    .sort((a, b) => a.orden - b.orden)
                    .map(c => `
        <div class="sortable-card" draggable="true" data-id="${c.id}" data-level="cat" role="listitem" aria-label="${c.nombre}">
          <div style="display:flex; align-items:center; gap:.75rem; min-width:0;">
            ${pill(c.orden)}
            <div class="text-ellipsis">${c.nombre}</div>
          </div>
          ${dots()}
        </div>
      `).join('');

                // Estructura: por categoría -> subcategorías -> productos
                $estruct.innerHTML = data.categorias
                    .sort((a, b) => a.orden - b.orden)
                    .map(c => `
        <div class="cat-block" data-cat="${c.id}">
          <h4 class="level-title">${c.nombre}</h4>

          <div class="sub-block" id="subc-${c.id}" aria-label="Subcategorías de ${c.nombre}" role="list">
            ${(data.mapa_subcategorias_por_categoria[c.id]||[])
              .sort((a,b)=>a.orden-b.orden)
              .map(s => `
                <div class="sortable-card" draggable="true" data-id="${s.id}" data-level="sub" role="listitem" aria-label="${s.nombre}">
                  <div style="display:flex; align-items:center; gap:.75rem; min-width:0;">
                    ${pill(s.orden)}
                    <div class="text-ellipsis">${s.nombre}</div>
                  </div>
                  ${dots()}
                </div>

                <div class="prod-block" id="prod-${c.id}-${s.id}" role="list" aria-label="Productos de ${s.nombre}">
                  ${(data.mapa_productos_por_cat_sub[`${c.id}_${s.id}`]||[])
                    .sort((a,b)=>a.orden-b.orden)
                    .map(p => `
                      <div class="sortable-card" draggable="true" data-id="${p.id}" data-level="prod" role="listitem" aria-label="${p.nombre}">
                        <div style="display:flex; align-items:center; gap:.75rem; min-width:0;">
                          ${pill(p.orden)}
                          <div class="text-ellipsis">${p.nombre}</div>
                        </div>
                        ${dots()}
                      </div>
                    `).join('')
                  }
                </div>
              `).join('')}
          </div>
        </div>
      `).join('');

                // Activar sortable para: categorías, cada bloque de subcategorías y cada bloque de productos
                makeSortable($catContainer, recomputeOrdersCategorias);

                document.querySelectorAll('[id^="subc-"]').forEach(cont => {
                    makeSortable(cont, () => recomputeOrdersSubcategorias(cont));
                });

                document.querySelectorAll('[id^="prod-"]').forEach(cont => {
                    makeSortable(cont, () => recomputeOrdersProductos(cont));
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
                const items = [...$catContainer.querySelectorAll('.sortable-card')];
                items.forEach((el, idx) => estado.indices.categorias.set(Number(el.dataset.id), idx + 1));
                renumerarPills($catContainer);
            }

            function recomputeOrdersSubcategorias(cont) {
                const items = [...cont.querySelectorAll(':scope > .sortable-card')];
                items.forEach((el, idx) => estado.indices.subcategorias.set(Number(el.dataset.id), idx + 1));
                renumerarPills(cont);
            }

            function recomputeOrdersProductos(cont) {
                const items = [...cont.querySelectorAll(':scope > .sortable-card')];
                items.forEach((el, idx) => estado.indices.productos.set(Number(el.dataset.id), idx + 1));
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

                // pre-cargar mapas de orden actuales
                estado.indices.categorias.clear();
                estado.indices.subcategorias.clear();
                estado.indices.productos.clear();

                json.data.categorias.forEach(c => estado.indices.categorias.set(c.id, c.orden));
                json.data.subcategorias.forEach(s => estado.indices.subcategorias.set(s.id, s.orden));
                json.data.productos.forEach(p => estado.indices.productos.set(p.id, p.orden));

                renderEstructura(json.data);
            }

            async function guardar() {
                // Construir arrays a partir de DOM (preferible a confiar en estado)
                const cats = [...$catContainer.querySelectorAll('.sortable-card')].map((el, i) => ({
                    id: Number(el.dataset.id),
                    orden: i + 1
                }));

                const subsSet = new Map(); // id -> orden (última vista en DOM)
                document.querySelectorAll('[id^="subc-"]').forEach(cont => {
                    [...cont.querySelectorAll(':scope > .sortable-card')].forEach((el, i) => {
                        subsSet.set(Number(el.dataset.id), i + 1);
                    });
                });
                const subs = [...subsSet.entries()].map(([id, orden]) => ({
                    id,
                    orden
                }));

                const prodsSet = new Map();
                document.querySelectorAll('[id^="prod-"]').forEach(cont => {
                    [...cont.querySelectorAll(':scope > .sortable-card')].forEach((el, i) => {
                        prodsSet.set(Number(el.dataset.id), i + 1);
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

        })();
    </script>

    <div id="alert-container" class="alert-container" style="position:fixed;bottom:1rem;right:1rem;z-index:9999;"></div>
</body>

</html>