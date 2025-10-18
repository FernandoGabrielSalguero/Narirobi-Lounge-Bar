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

    <!-- jsPDF para generar PDF en cliente -->
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js" defer></script>

    <!-- CSS mínimo específico (inline, sin romper el CDN) -->
    <style>
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 992px) {
            .form-grid-3 {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {

            .form-grid-2,
            .form-grid-3 {
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

        .badge.vencida {
            background: #ef4444;
            color: #fff;
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

                        <div class="input-group" style="margin-top:.5rem;">
                            <label for="texto">Texto</label>
                            <div class="input-icon input-icon-info">
                                <textarea id="texto" name="texto" placeholder="Mensaje de la gift card" rows="3" required aria-required="true"></textarea>
                            </div>
                        </div>

                        <div class="form-buttons" style="margin-top:1rem;">
                            <button type="submit" class="btn btn-aceptar">Crear</button>
                            <button type="reset" class="btn btn-cancelar">Limpiar</button>
                        </div>
                    </form>
                </div>

                <!-- Card 2: Filtros -->
                <div class="card" aria-labelledby="gc-filter-title">
                    <h2 id="gc-filter-title">Filtros</h2>
                    <div class="form-grid-3">
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
                    <!-- Sin botones: el filtrado se hace por AJAX con debounce -->
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

    <!-- Modal confirmación de creación -->
    <div id="modalConfirmCreate" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modal-create-title">
        <div class="modal-content">
            <h3 id="modal-create-title">Confirmar nueva Gift Card</h3>
            <p>Revisá los datos antes de guardar:</p>
            <ul id="confirmList" style="margin: .5rem 0 1rem 1rem;">
                <!-- se completa por JS -->
            </ul>
            <div class="form-buttons">
                <button class="btn btn-aceptar" id="btnCrearConfirmado">Confirmar</button>
                <button class="btn btn-cancelar" id="btnCancelarCrear">Cancelar</button>
            </div>
        </div>
    </div>


    <!-- Modal para canjear -->
    <div id="modal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-content">
            <h3 id="modal-title">Canjear Gift Card</h3>
            <p>Ingresá el código de 6 dígitos para canjear. <small>(Las vencidas no pueden canjearse)</small></p>
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

            // Modal canje
            const modal = document.getElementById('modal');
            const codigoCanjeInput = document.getElementById('codigo_canje');
            const btnConfirmarCanje = document.getElementById('btnConfirmarCanje');
            const btnCancelarCanje = document.getElementById('btnCancelarCanje');

            // Modal confirm create
            const modalConfirm = document.getElementById('modalConfirmCreate');
            const btnCrearConfirmado = document.getElementById('btnCrearConfirmado');
            const btnCancelarCrear = document.getElementById('btnCancelarCrear');
            const confirmList = document.getElementById('confirmList');

            function openModal(m) {
                m.classList.remove('hidden');
            }

            function closeModal(m) {
                m.classList.add('hidden');
            }

            window.closeModal = () => closeModal(modal); // compat

            function estadoBadge(estado, vencida) {
                if (vencida) return `<span class="badge vencida">Vencida</span>`;
                const cls = estado === 'canjeado' ? 'badge canjeado' : 'badge pendiente';
                const txt = estado.charAt(0).toUpperCase() + estado.slice(1);
                return `<span class="${cls}">${txt}</span>`;
            }

            function esVencida(fechaStr) {
                // fechaStr YYYY-MM-DD
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);
                const f = new Date(fechaStr + 'T00:00:00');
                return f < hoy;
            }

            function renderTabla(rows) {
                tablaBody.innerHTML = '';
                if (!rows || rows.length === 0) {
                    tablaBody.innerHTML = `<tr><td colspan="6">Sin resultados</td></tr>`;
                    return;
                }
                rows.forEach((r, idx) => {
                    const vencida = esVencida(r.fecha_vencimiento);
                    const canjeDisabled = (r.estado === 'canjeado' || vencida) ? 'disabled aria-disabled="true"' : '';
                    tablaBody.insertAdjacentHTML('beforeend', `
        <tr>
          <td>${idx + 1}</td>
          <td>${r.nombre}</td>
          <td>${r.fecha_vencimiento}</td>
          <td><strong>${r.codigo}</strong></td>
          <td>${estadoBadge(r.estado, vencida)}</td>
          <td>
            <div class="acciones-wrap">
              <button class="btn btn-info btn-pdf" data-row='${JSON.stringify(r).replace(/'/g,"&apos;")}' aria-label="Descargar PDF ${r.codigo}">
                <span class="material-icons" aria-hidden="true">download</span>
              </button>
              <button class="btn btn-aceptar" ${canjeDisabled} data-code="${r.codigo}" data-vencida="${vencida}" aria-label="Canjear ${r.codigo}">
                <span class="material-icons" aria-hidden="true">shopping_bag</span>
              </button>
            </div>
          </td>
        </tr>
      `);
                });

                // Bind PDF
                tablaBody.querySelectorAll('.btn-pdf').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const row = JSON.parse(btn.getAttribute('data-row').replaceAll('&apos;', "'"));
                        generarPDF(row);
                    });
                });

                // Bind canjear
                tablaBody.querySelectorAll('button.btn-aceptar').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const vencida = btn.getAttribute('data-vencida') === 'true';
                        if (vencida) {
                            showAlert('error', 'La Gift Card está vencida.');
                            return;
                        }
                        const code = btn.getAttribute('data-code');
                        codigoCanjeInput.value = code;
                        openModal(modal);
                        setTimeout(() => codigoCanjeInput.focus(), 50);
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
                                    await cargarTabla();
                                    closeModal(modal);
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

            // Confirmación de creación
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const nombre = (document.getElementById('nombre').value || '').trim();
                const fecha = (document.getElementById('fecha_vencimiento').value || '').trim();
                const texto = (document.getElementById('texto').value || '').trim();
                if (!nombre || !fecha || !texto) {
                    showAlert('error', 'Completá todos los campos.');
                    return;
                }
                confirmList.innerHTML = `
      <li><strong>Nombre:</strong> ${nombre}</li>
      <li><strong>Vencimiento:</strong> ${fecha}</li>
      <li><strong>Texto:</strong> ${texto}</li>
    `;
                openModal(modalConfirm);
            });

            document.getElementById('btnCrearConfirmado').addEventListener('click', async () => {
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
                } finally {
                    closeModal(modalConfirm);
                }
            });
            document.getElementById('btnCancelarCrear').addEventListener('click', () => closeModal(modalConfirm));

            // Auto-filtros con debounce
            const debounce = (fn, ms = 350) => {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), ms);
                };
            };
            const onFilterChange = debounce(cargarTabla, 350);
            filtroNombre.addEventListener('input', onFilterChange);
            filtroCodigo.addEventListener('input', onFilterChange);
            filtroEstado.addEventListener('change', onFilterChange);

            // Cerrar modales con ESC
            document.addEventListener('keydown', (ev) => {
                if (ev.key === 'Escape') {
                    closeModal(modal);
                    closeModal(modalConfirm);
                }
            });

            // Generador PDF (jsPDF)
            function generarPDF(row) {
                // row: {nombre, fecha_vencimiento, codigo, estado, texto?}
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF();

                doc.setFontSize(18);
                doc.text('Nairobi Lounge Bar', 105, 20, {
                    align: 'center'
                });
                doc.setFontSize(14);
                doc.text('Gift Card', 105, 30, {
                    align: 'center'
                });

                doc.setFontSize(12);
                const y0 = 50;
                doc.text(`Nombre: ${row.nombre}`, 20, y0);
                doc.text(`Vencimiento: ${row.fecha_vencimiento}`, 20, y0 + 10);
                doc.text(`Código: ${row.codigo}`, 20, y0 + 20);
                if (row.texto) {
                    const split = doc.splitTextToSize(`Texto: ${row.texto}`, 170);
                    doc.text(split, 20, y0 + 30);
                }
                doc.line(20, y0 - 8, 190, y0 - 8);

                doc.save(`giftcard_${row.codigo}.pdf`);
            }

            // Primera carga
            cargarTabla();
        })();
    </script>

</body>

</html>