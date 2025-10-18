<?php

declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Opcional: datos del usuario
$usuario = $user['username'] ?? 'Sin usuario';
$email   = $user['email'] ?? 'Sin email';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nairobi Lounge Bar — Gift Cards</title>

    <!-- Íconos de Material Design -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Framework Success desde CDN -->
    <link rel="stylesheet" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css">
    <script src="https://www.fernandosalguero.com/cdn/assets/javascript/framework.js" defer></script>

    <!-- CSS mínimo específico (inline, sin romper el CDN) -->
    <style>
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .form-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .acciones-wrap {
            display: flex;
            gap: .5rem;
            align-items: center;
            justify-content: flex-start;
        }

        .tabla-card .data-table td,
        .tabla-card .data-table th {
            white-space: nowrap;
        }

        .badge.canjeado {
            background: #059669;
            color: #fff;
        }

        .badge.pendiente {
            background: #f59e0b;
            color: #111;
        }

        .modal.hidden {
            display: none;
        }

        .w-100 {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="layout">
        <aside class="sidebar" id="sidebar" aria-label="Barra lateral">
            <div class="sidebar-header">
                <span class="material-icons logo-icon">dashboard</span>
                <span class="logo-text">ISO</span>
            </div>
            <nav class="sidebar-menu" aria-label="Navegación principal">
                <ul>
                    <li onclick="location.href='admin_dashboard_view.php'">
                        <span class="material-icons" style="color:#5b21b6;">home</span><span class="link-text">Inicio</span>
                    </li>
                    <li onclick="location.href='admin_carta_view.php'">
                        <span class="material-icons" style="color:#5b21b6;">restaurant_menu</span><span class="link-text">Carta</span>
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
                <button class="btn-icon" onclick="toggleSidebar()" aria-label="Colapsar barra lateral">
                    <span class="material-icons" id="collapseIcon">chevron_left</span>
                </button>
            </div>
        </aside>

        <div class="main">
            <header class="navbar">
                <button class="btn-icon" onclick="toggleSidebar()" aria-label="Abrir menú">
                    <span class="material-icons">menu</span>
                </button>
                <div class="navbar-title">Gift Cards</div>
            </header>

            <section class="content">
                <!-- Card 1: Formulario de creación -->
                <div class="card" aria-labelledby="gc-form-title">
                    <h2 id="gc-form-title">Crear Gift Card</h2>
                    <p>El código de 6 dígitos se genera automáticamente al guardar.</p>

                    <form id="giftForm" class="w-100" autocomplete="off" novalidate>
                        <div class="form-grid-2">
                            <div class="input-group">
                                <label for="nombre">Nombre</label>
                                <div class="input-icon input-icon-name">
                                    <input type="text" id="nombre" name="nombre" placeholder="Nombre del destinatario" required aria-required="true" />
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="fecha_vencimiento">Fecha de vencimiento</label>
                                <div class="input-icon input-icon-date">
                                    <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" required aria-required="true" />
                                </div>
                            </div>
                        </div>

                        <div class="form-buttons" style="margin-top:1rem;">
                            <button type="submit" class="btn btn-aceptar">
                                <span class="material-icons" aria-hidden="true">add_circle</span> Crear
                            </button>
                            <button type="reset" class="btn btn-cancelar">
                                <span class="material-icons" aria-hidden="true">close</span> Limpiar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Card 2: Filtros -->
                <div class="card" aria-labelledby="gc-filter-title">
                    <h2 id="gc-filter-title">Filtros</h2>
                    <div class="form-grid-2">
                        <div class="input-group">
                            <label for="filtro_nombre">Por nombre</label>
                            <div class="input-icon input-icon-name">
                                <input type="text" id="filtro_nombre" placeholder="Ej: Ana" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="filtro_codigo">Por código</label>
                            <div class="input-icon input-icon-key">
                                <input type="text" id="filtro_codigo" placeholder="6 dígitos" maxlength="6" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="filtro_estado">Por estado</label>
                            <div class="input-icon input-icon-info">
                                <select id="filtro_estado" aria-label="Estado">
                                    <option value="">Todos</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="canjeado">Canjeado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-buttons" style="margin-top:1rem;">
                        <button class="btn btn-info" id="btnAplicarFiltros">
                            <span class="material-icons" aria-hidden="true">filter_alt</span> Aplicar filtros
                        </button>
                        <button class="btn btn-cancelar" id="btnLimpiarFiltros">
                            <span class="material-icons" aria-hidden="true">backspace</span> Limpiar
                        </button>
                    </div>
                </div>

                <!-- Card 3: Tabla -->
                <div class="card tabla-card" aria-labelledby="gc-table-title">
                    <h2 id="gc-table-title">Gift Cards creadas</h2>
                    <div class="tabla-wrapper">
                        <table class="data-table" id="tablaGift">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Fecha vencimiento</th>
                                    <th>Código</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaBody">
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Modal para canjear -->
    <div id="modal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-content">
            <h3 id="modal-title">Canjear Gift Card</h3>
            <p>Ingresá el código de 6 dígitos para canjear.</p>
            <div class="input-group">
                <label for="codigo_canje">Código</label>
                <div class="input-icon input-icon-key">
                    <input type="text" id="codigo_canje" placeholder="######" maxlength="6" inputmode="numeric" />
                </div>
            </div>
            <div class="form-buttons">
                <button class="btn btn-aceptar" id="btnConfirmarCanje">Aceptar</button>
                <button class="btn btn-cancelar" id="btnCancelarCanje">Cancelar</button>
            </div>
        </div>
    </div>

    <script src="/views/partial/spinner-global.js" defer></script>

    <script>
        (() => {
            'use strict';
            const API_URL = '../../controllers/admin_gift_controller.php';

            const form = document.getElementById('giftForm');
            const tablaBody = document.getElementById('tablaBody');

            const filtroNombre = document.getElementById('filtro_nombre');
            const filtroCodigo = document.getElementById('filtro_codigo');
            const filtroEstado = document.getElementById('filtro_estado');

            const btnAplicarFiltros = document.getElementById('btnAplicarFiltros');
            const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');

            // Modal
            const modal = document.getElementById('modal');
            const codigoCanjeInput = document.getElementById('codigo_canje');
            const btnConfirmarCanje = document.getElementById('btnConfirmarCanje');
            const btnCancelarCanje = document.getElementById('btnCancelarCanje');

            function openModal() {
                modal.classList.remove('hidden');
                setTimeout(() => codigoCanjeInput.focus(), 50);
            }

            function closeModal() {
                modal.classList.add('hidden');
                codigoCanjeInput.value = '';
            }
            window.closeModal = closeModal; // por compatibilidad con CDN ejemplo

            function estadoBadge(estado) {
                const cls = estado === 'canjeado' ? 'badge canjeado' : 'badge pendiente';
                const txt = estado.charAt(0).toUpperCase() + estado.slice(1);
                return `<span class="badge ${estado} ${cls.includes('canjeado')?'success':''}">${txt}</span>`;
            }

            function renderTabla(rows) {
                tablaBody.innerHTML = '';
                if (!rows || rows.length === 0) {
                    tablaBody.innerHTML = `<tr><td colspan="6">Sin resultados</td></tr>`;
                    return;
                }
                rows.forEach((r, idx) => {
                    const disabled = r.estado === 'canjeado' ? 'disabled aria-disabled="true"' : '';
                    tablaBody.insertAdjacentHTML('beforeend', `
        <tr>
          <td>${idx + 1}</td>
          <td>${r.nombre}</td>
          <td>${r.fecha_vencimiento}</td>
          <td><strong>${r.codigo}</strong></td>
          <td>${estadoBadge(r.estado)}</td>
          <td>
            <div class="acciones-wrap">
              <button class="btn btn-aceptar" ${disabled} data-code="${r.codigo}" aria-label="Canjear ${r.codigo}">
                <span class="material-icons" aria-hidden="true">shopping_bag</span>
              </button>
            </div>
          </td>
        </tr>
      `);
                });

                // Bind canjear
                tablaBody.querySelectorAll('button.btn-aceptar').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const code = btn.getAttribute('data-code');
                        // Pre-rellenar modal con el código seleccionado (puede editarse)
                        codigoCanjeInput.value = code;
                        openModal();
                        btnConfirmarCanje.onclick = async () => {
                            const codigo = codigoCanjeInput.value.trim();
                            if (codigo.length !== 6) {
                                showAlert('error', 'El código debe tener 6 dígitos.');
                                return;
                            }
                            try {
                                const fd = new FormData();
                                fd.append('_action', 'redeem');
                                fd.append('codigo', codigo);

                                const res = await fetch(API_URL, {
                                    method: 'POST',
                                    body: fd
                                });
                                const json = await res.json();
                                if (json.ok) {
                                    showAlert('success', json.data?.message || 'Canje realizado.');
                                    await cargarTabla(); // refrescar
                                    closeModal();
                                } else {
                                    showAlert('error', json.error || 'No se pudo canjear.');
                                }
                            } catch (e) {
                                showAlert('error', 'Error de red al canjear.');
                            }
                        };
                    });
                });
            }

            async function cargarTabla() {
                const params = new URLSearchParams();
                if (filtroNombre.value.trim()) params.set('nombre', filtroNombre.value.trim());
                if (filtroCodigo.value.trim()) params.set('codigo', filtroCodigo.value.trim());
                if (filtroEstado.value) params.set('estado', filtroEstado.value);

                const url = `${API_URL}?${params.toString()}`;
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const json = await res.json();
                if (json.ok) {
                    renderTabla(json.data);
                } else {
                    renderTabla([]);
                    showAlert('error', json.error || 'No se pudieron cargar las Gift Cards.');
                }
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const fd = new FormData(form);
                try {
                    const res = await fetch(API_URL, {
                        method: 'POST',
                        body: fd
                    });
                    const json = await res.json();
                    if (json.ok) {
                        showAlert('success', `Creada: código ${json.data.codigo}`);
                        form.reset();
                        await cargarTabla();
                    } else {
                        showAlert('error', json.error || 'No se pudo crear.');
                    }
                } catch (err) {
                    showAlert('error', 'Error de red al crear.');
                }
            });

            btnAplicarFiltros.addEventListener('click', (e) => {
                e.preventDefault();
                cargarTabla();
            });
            btnLimpiarFiltros.addEventListener('click', (e) => {
                e.preventDefault();
                filtroNombre.value = '';
                filtroCodigo.value = '';
                filtroEstado.value = '';
                cargarTabla();
            });
            btnCancelarCanje.addEventListener('click', closeModal);

            // Evitar FOUC de modal
            document.addEventListener('keydown', (ev) => {
                if (ev.key === 'Escape') closeModal();
            });

            // Primera carga
            cargarTabla();
        })();
    </script>
</body>

</html>