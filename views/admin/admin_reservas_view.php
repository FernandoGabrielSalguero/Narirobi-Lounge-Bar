<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Opcional: datos del usuario
$usuario = $user['username'] ?? 'Sin usuario';
$email = $user['email'] ?? 'Sin email';

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nairobi Lounge Bar</title>

    <!-- Íconos de Material Design -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Framework Success desde CDN -->
    <link rel="stylesheet" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css">
    <script src="https://www.fernandosalguero.com/cdn/assets/javascript/framework.js" defer></script>
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
                <div class="navbar-title">Inicio</div>
            </header>

            <!-- ======= RESERVAS: FILTROS + ACCIONES ======= -->
            <section id="reservas-ui" class="card" aria-labelledby="reservas-filtros-title">
                <h2 id="reservas-filtros-title">Reservas</h2>

                <!-- Filtros -->
                <form id="form-filtros" class="form-grid grid-4" role="search" aria-label="Filtros de reservas">
                    <div class="input-group">
                        <label for="filtro_fecha_desde">Fecha desde</label>
                        <div class="input-icon input-icon-date_range">
                            <input type="date" id="filtro_fecha_desde" name="fecha_desde" />
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="filtro_fecha_hasta">Fecha hasta</label>
                        <div class="input-icon input-icon-date_range">
                            <input type="date" id="filtro_fecha_hasta" name="fecha_hasta" />
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="filtro_estado">Estado</label>
                        <div class="input-icon input-icon-tune">
                            <select id="filtro_estado" name="estado" aria-label="Estado">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="confirmada">Confirmada</option>
                                <option value="finalizada">Finalizada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="filtro_q">Nombre o Teléfono</label>
                        <div class="input-icon input-icon-search">
                            <input type="text" id="filtro_q" name="q" placeholder="Buscar…" />
                        </div>
                    </div>
                    <div class="form-grid grid-3" style="grid-column: 1 / -1; margin-top: .5rem">
                        <button type="button" class="btn btn-info" id="btn-aplicar-filtros" aria-label="Aplicar filtros">
                            <span class="material-icons" aria-hidden="true">filter_alt</span> Aplicar
                        </button>
                        <button type="button" class="btn btn-cancelar" id="btn-limpiar-filtros" aria-label="Limpiar filtros">
                            <span class="material-icons" aria-hidden="true">backspace</span> Limpiar
                        </button>
                        <span aria-hidden="true"></span>
                    </div>
                </form>

                <!-- Acciones -->
                <div class="form-grid grid-3" style="margin-top:.5rem">
                    <button class="btn btn-aceptar" id="btn-abrir-crear" aria-controls="modal-crear-reserva" aria-expanded="false">
                        <span class="material-icons" aria-hidden="true">add_circle</span> Nueva reserva
                    </button>
                    <button class="btn btn-info" id="btn-abrir-almanaque" aria-controls="modal-almanaque" aria-expanded="false">
                        <span class="material-icons" aria-hidden="true">event</span> Almanaque
                    </button>
                    <div></div>
                </div>

                <!-- Paginación -->
                <div id="paginacion" class="form-grid grid-3" style="margin-top:.75rem">
                    <button type="button" class="btn btn-cancelar" id="btn-prev" disabled aria-label="Página anterior">
                        <span class="material-icons" aria-hidden="true">chevron_left</span> Anterior
                    </button>
                    <div id="pagina-info" class="badge info" aria-live="polite">Página 1</div>
                    <button type="button" class="btn btn-aceptar" id="btn-next" disabled aria-label="Página siguiente">
                        Siguiente <span class="material-icons" aria-hidden="true">chevron_right</span>
                    </button>
                </div>
            </section>

            <!-- ======= LISTADO EN CARDS ======= -->
            <section id="reservas-listado" class="card tabla-card" aria-labelledby="reservas-listado-title" style="margin-top:1rem">
                <h2 id="reservas-listado-title">Reservas realizadas</h2>
                <div id="cards-wrapper" class="tabla-wrapper" aria-live="polite"></div>
            </section>

            <!-- ======= MODAL: CREAR / EDITAR RESERVA ======= -->
            <div id="modal-crear-reserva" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="crear-title">
                <div class="modal-content">
                    <h3 id="crear-title">Nueva / Editar reserva</h3>
                    <form id="form-reserva" class="form-grid grid-2">
                        <input type="hidden" id="reserva_id" name="id" />

                        <div class="input-group">
                            <label for="reserva_nombre">Nombre</label>
                            <div class="input-icon input-icon-name">
                                <input type="text" id="reserva_nombre" name="nombre" required placeholder="Nombre y apellido" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="reserva_telefono">Teléfono</label>
                            <div class="input-icon input-icon-call">
                                <input type="tel" id="reserva_telefono" name="telefono" inputmode="tel" placeholder="+54 9 11 1234 5678" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="reserva_fecha">Fecha</label>
                            <div class="input-icon input-icon-date_range">
                                <input type="date" id="reserva_fecha" name="fecha" required />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="reserva_hora">Hora</label>
                            <div class="input-icon input-icon-schedule">
                                <input type="time" id="reserva_hora" name="hora" required step="3600" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="reserva_comensales">Cantidad de comensales</label>
                            <div class="input-icon input-icon-group">
                                <input type="number" id="reserva_comensales" name="comensales" min="1" max="200" required placeholder="Ej: 4" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="reserva_estado">Estado</label>
                            <div class="input-icon input-icon-flag">
                                <select id="reserva_estado" name="estado" required>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="finalizada">Finalizada</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-group" style="grid-column:1/-1">
                            <label for="reserva_detalle">Detalle</label>
                            <div class="input-icon input-icon-description">
                                <input type="text" id="reserva_detalle" name="detalle" placeholder="Notas, preferencias, ocasión…" />
                            </div>
                        </div>

                        <div class="input-group" id="grupo_cancelacion" style="grid-column:1/-1; display:none">
                            <label for="reserva_detalle_cancelacion">Motivo de cancelación</label>
                            <div class="input-icon input-icon-report">
                                <input type="text" id="reserva_detalle_cancelacion" name="detalle_cancelacion" placeholder="Motivo…" />
                            </div>
                        </div>

                        <div class="form-buttons" style="grid-column:1/-1">
                            <button type="button" class="btn btn-aceptar" id="btn-guardar-reserva">Guardar</button>
                            <button type="button" class="btn btn-cancelar" onclick="closeModal('modal-crear-reserva')">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ======= MODAL: ALMANAQUE ======= -->
            <div id="modal-almanaque" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="almanaque-title">
                <div class="modal-content">
                    <h3 id="almanaque-title">Almanaque de reservas</h3>

                    <div class="form-grid grid-3" style="margin-bottom:.5rem">
                        <button class="btn btn-cancelar" id="mes-anterior" aria-label="Mes anterior">
                            <span class="material-icons" aria-hidden="true">chevron_left</span>
                        </button>
                        <div id="mes-ano" class="badge info" aria-live="polite">Mes Año</div>
                        <button class="btn btn-aceptar" id="mes-siguiente" aria-label="Mes siguiente">
                            <span class="material-icons" aria-hidden="true">chevron_right</span>
                        </button>
                    </div>

                    <div id="calendario" class="almanaque-grid" role="grid" aria-label="Calendario mensual"></div>

                    <div id="slots" class="slots-grid" aria-live="polite" aria-label="Disponibilidad del día" style="margin-top:.75rem"></div>

                    <div class="form-buttons">
                        <button class="btn btn-cancelar" onclick="closeModal('modal-almanaque')">Cerrar</button>
                    </div>
                </div>
            </div>

            <!-- ======= MODAL: DETALLE / EDITAR (misma UI que crear, se reutiliza) ======= -->
            <!-- Abrimos el mismo modal 'modal-crear-reserva' precargado -->

            <!-- ======= MODAL: HISTORIAL ======= -->
            <div id="modal-historial" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="historial-title">
                <div class="modal-content">
                    <h3 id="historial-title">Historial de cambios</h3>
                    <div id="historial-wrapper" class="tabla-wrapper" style="max-height:50vh; overflow:auto"></div>
                    <div class="form-buttons">
                        <button class="btn btn-cancelar" onclick="closeModal('modal-historial')">Cerrar</button>
                    </div>
                </div>
            </div>

            <!-- ======= ESTILOS ESPECÍFICOS (inline para esta vista) ======= -->
            <style>
                /* Evitar FOUC mínimo para esta vista */
                #reservas-ui,
                #reservas-listado {
                    visibility: visible;
                }

                .almanaque-grid {
                    display: grid;
                    grid-template-columns: repeat(7, 1fr);
                    gap: .5rem;
                }

                .almanaque-grid .day {
                    padding: .75rem;
                    border: 1px solid #eee;
                    border-radius: .5rem;
                    cursor: pointer;
                    display: flex;
                    flex-direction: column;
                    gap: .25rem;
                    min-height: 5rem;
                    transition: transform .2s ease, box-shadow .2s ease;
                }

                .almanaque-grid .day:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
                }

                .almanaque-grid .day .date {
                    font-weight: 600;
                }

                .badge-dot {
                    display: inline-flex;
                    align-items: center;
                    gap: .25rem;
                    font-size: .8rem
                }

                .badge-dot::before {
                    content: "";
                    width: .6rem;
                    height: .6rem;
                    border-radius: 50%;
                    background: #7c3aed;
                }

                .slots-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                    gap: .5rem;
                }

                .slot-btn {
                    width: 100%;
                }

                .card-reserva {
                    border: 1px solid #eee;
                    border-radius: .75rem;
                    padding: .75rem;
                    margin-bottom: .75rem;
                    display: grid;
                    grid-template-columns: 1fr auto;
                    gap: .5rem;
                }

                .card-reserva h3 {
                    margin: 0;
                    font-size: 1.05rem
                }

                .estado {
                    display: inline-block;
                    margin-left: .25rem
                }

                .estado.pendiente {
                    color: #b45309
                }

                .estado.confirmada {
                    color: #065f46
                }

                .estado.finalizada {
                    color: #1f2937
                }

                .estado.cancelada {
                    color: #991b1b
                }

                .acciones-rapidas {
                    display: flex;
                    gap: .5rem;
                    flex-wrap: wrap;
                    align-items: center;
                }

                .acciones-rapidas .btn {
                    padding: .35rem .6rem
                }

                .grid-card {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: .5rem
                }

                @media (min-width: 720px) {
                    .grid-card {
                        grid-template-columns: repeat(2, 1fr);
                    }
                }
            </style>

            <!-- ======= LÓGICA (JS) ======= -->
            <script type="module">
                // Respetar CDN framework.js: showAlert, etc.
                // Supuestos de endpoints (ajustar si tu router difiere):
                const API = {
                    LIST: 'admin_reservas_controller.php?action=list', // GET con filtros & paginación
                    CREATE: 'admin_reservas_controller.php?action=create', // POST
                    DETAIL: id => `admin_reservas_controller.php?action=detail&id=${encodeURIComponent(id)}`, // GET
                    UPDATE: id => `admin_reservas_controller.php?action=update&id=${encodeURIComponent(id)}`, // PUT/POST
                    CHANGE_STATE: id => `admin_reservas_controller.php?action=change_state&id=${encodeURIComponent(id)}`, // POST
                    OCUPACION: 'admin_reservas_controller.php?action=ocupacion', // GET from,to o date
                    HISTORY: id => `admin_reservas_controller.php?action=history&id=${encodeURIComponent(id)}` // GET
                };

                const PAGE_SIZE = 12;
                const HORARIO_INICIO = 8; // 08:00
                const HORARIO_FIN = 24; // 24:00
                const SLOT_HORAS = 2; // turnos de 2hs

                const qs = sel => document.querySelector(sel);
                const qsa = sel => Array.from(document.querySelectorAll(sel));

                // Accesibilidad: helpers para abrir/cerrar modales
                function openModal(id) {
                    const m = document.getElementById(id);
                    if (!m) return;
                    m.classList.remove('hidden');
                    m.setAttribute('aria-hidden', 'false');
                    const focusable = m.querySelector('input,select,button,textarea');
                    if (focusable) focusable.focus();
                }

                function closeModal(id) {
                    const m = document.getElementById(id);
                    if (!m) return;
                    m.classList.add('hidden');
                    m.setAttribute('aria-hidden', 'true');
                }
                window.closeModal = closeModal; // para botones inline

                // Estado de UI/paginación
                const state = {
                    page: 1,
                    totalPages: 1,
                    filtros: {
                        fecha_desde: '',
                        fecha_hasta: '',
                        estado: '',
                        q: ''
                    }
                };

                // Eventos principales
                document.getElementById('btn-abrir-crear').addEventListener('click', () => {
                    resetForm();
                    openModal('modal-crear-reserva');
                });
                document.getElementById('btn-abrir-almanaque').addEventListener('click', () => {
                    renderCalendario(new Date());
                    openModal('modal-almanaque');
                });
                document.getElementById('btn-aplicar-filtros').addEventListener('click', () => {
                    state.page = 1;
                    takeFilters();
                    listar();
                });
                document.getElementById('btn-limpiar-filtros').addEventListener('click', () => {
                    qs('#form-filtros').reset();
                    state.filtros = {
                        fecha_desde: '',
                        fecha_hasta: '',
                        estado: '',
                        q: ''
                    };
                    state.page = 1;
                    listar();
                });
                document.getElementById('btn-prev').addEventListener('click', () => {
                    if (state.page > 1) {
                        state.page--;
                        listar();
                    }
                });
                document.getElementById('btn-next').addEventListener('click', () => {
                    if (state.page < state.totalPages) {
                        state.page++;
                        listar();
                    }
                });

                // Crear / Editar
                document.getElementById('btn-guardar-reserva').addEventListener('click', guardarReserva);
                document.getElementById('reserva_estado').addEventListener('change', (e) => {
                    qs('#grupo_cancelacion').style.display = (e.target.value === 'cancelada') ? 'block' : 'none';
                });

                // Utilidades
                function takeFilters() {
                    const fd = new FormData(qs('#form-filtros'));
                    state.filtros = {
                        fecha_desde: fd.get('fecha_desde') || '',
                        fecha_hasta: fd.get('fecha_hasta') || '',
                        estado: fd.get('estado') || '',
                        q: (fd.get('q') || '').trim()
                    };
                }

                function telefonoARFormato(tel) {
                    // Normalización básica AR: quita no dígitos, deja +54 si viene, sin formatear visualmente
                    const d = (tel || '').replace(/[^\d+]/g, '');
                    return d;
                }

                function resetForm() {
                    const f = qs('#form-reserva');
                    f.reset();
                    qs('#reserva_id').value = '';
                    qs('#grupo_cancelacion').style.display = 'none';
                    // Defaults: estado pendiente
                    qs('#reserva_estado').value = 'pendiente';
                }

                function fillForm(data) {
                    qs('#reserva_id').value = data.id || '';
                    qs('#reserva_nombre').value = data.nombre || '';
                    qs('#reserva_telefono').value = data.telefono || '';
                    qs('#reserva_fecha').value = data.fecha || '';
                    qs('#reserva_hora').value = data.hora || '';
                    qs('#reserva_comensales').value = data.comensales || 1;
                    qs('#reserva_detalle').value = data.detalle || '';
                    qs('#reserva_estado').value = data.estado || 'pendiente';
                    qs('#reserva_detalle_cancelacion').value = data.detalle_cancelacion || '';
                    qs('#grupo_cancelacion').style.display = (data.estado === 'cancelada') ? 'block' : 'none';
                }

                async function listar() {
                    try {
                        showGlobalSpinner?.();
                        const p = new URLSearchParams({
                            page: String(state.page),
                            per_page: String(PAGE_SIZE),
                            ...state.filtros
                        });
                        const res = await fetch(`${API.LIST}&${p.toString()}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const json = await res.json();
                        if (!json.ok) {
                            showAlert('error', json.error || 'Error al listar');
                            return;
                        }
                        renderListado(json.data.items || []);
                        state.totalPages = Math.max(1, Math.ceil((json.data.total || 0) / PAGE_SIZE));
                        qs('#btn-prev').disabled = state.page <= 1;
                        qs('#btn-next').disabled = state.page >= state.totalPages;
                        qs('#pagina-info').textContent = `Página ${state.page} de ${state.totalPages}`;
                    } catch (e) {
                        showAlert('error', 'No se pudo cargar el listado.');
                    } finally {
                        hideGlobalSpinner?.();
                    }
                }

                function renderListado(items) {
                    const wrap = qs('#cards-wrapper');
                    wrap.innerHTML = '';
                    if (!items.length) {
                        wrap.innerHTML = `<div class="badge warning">Sin resultados</div>`;
                        return;
                    }
                    // Grid responsivo
                    const grid = document.createElement('div');
                    grid.className = 'grid-card';
                    items.forEach(it => {
                        const card = document.createElement('article');
                        card.className = 'card-reserva';
                        card.setAttribute('aria-label', `Reserva de ${it.nombre} el ${it.fecha} ${it.hora}`);

                        card.innerHTML = `
        <div>
          <h3>
            ${escapeHTML(it.nombre)} 
            <span class="estado ${it.estado}">(${it.estado})</span>
          </h3>
          <div>
            <span class="material-icons" aria-hidden="true">schedule</span> ${it.fecha} ${it.hora} &nbsp; 
            <span class="material-icons" aria-hidden="true">groups</span> ${it.comensales} &nbsp; 
            <span class="material-icons" aria-hidden="true">call</span> ${escapeHTML(it.telefono)}
          </div>
          <div style="margin-top:.25rem">
            <small>${escapeHTML(it.detalle || '')}</small>
          </div>
        </div>
        <div class="acciones-rapidas">
          <button class="btn btn-info" aria-label="Historial" data-action="historial" data-id="${it.id}" title="Historial">
            <span class="material-icons" aria-hidden="true">history</span>
          </button>
          <button class="btn btn-aceptar" aria-label="Ver y editar" data-action="editar" data-id="${it.id}">
            <span class="material-icons" aria-hidden="true">edit</span>
          </button>
          <button class="btn" aria-label="Marcar confirmada" data-action="estado" data-estado="confirmada" data-id="${it.id}" title="Confirmar">
            <span class="material-icons" aria-hidden="true">done</span>
          </button>
          <button class="btn" aria-label="Marcar finalizada" data-action="estado" data-estado="finalizada" data-id="${it.id}" title="Finalizar">
            <span class="material-icons" aria-hidden="true">flag</span>
          </button>
          <button class="btn btn-cancelar" aria-label="Cancelar reserva" data-action="cancelar" data-id="${it.id}" title="Cancelar">
            <span class="material-icons" aria-hidden="true">cancel</span>
          </button>
        </div>
      `;
                        grid.appendChild(card);
                    });
                    wrap.appendChild(grid);

                    // Delegación de eventos
                    wrap.onclick = async (ev) => {
                        const btn = ev.target.closest('button[data-action]');
                        if (!btn) return;
                        const id = btn.dataset.id;
                        const action = btn.dataset.action;
                        if (action === 'editar') {
                            await abrirEditar(id);
                        } else if (action === 'estado') {
                            const nuevo = btn.dataset.estado;
                            await cambiarEstado(id, nuevo);
                        } else if (action === 'cancelar') {
                            const motivo = prompt('Ingrese motivo de cancelación:');
                            if (motivo === null) return;
                            await cambiarEstado(id, 'cancelada', motivo.trim());
                        } else if (action === 'historial') {
                            await verHistorial(id);
                        }
                    };
                }

                async function abrirEditar(id) {
                    try {
                        showGlobalSpinner?.();
                        const res = await fetch(API.DETAIL(id), {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const json = await res.json();
                        if (!json.ok) {
                            showAlert('error', json.error || 'No se pudo cargar la reserva');
                            return;
                        }
                        fillForm(json.data);
                        openModal('modal-crear-reserva');
                    } catch {
                        showAlert('error', 'Error al obtener la reserva');
                    } finally {
                        hideGlobalSpinner?.();
                    }
                }

                async function guardarReserva() {
                    const fd = new FormData(qs('#form-reserva'));
                    // Normalización teléfono AR
                    const tel = telefonoARFormato(fd.get('telefono'));
                    fd.set('telefono', tel);

                    const id = fd.get('id');
                    const isEdit = Boolean(id);

                    const opts = {
                        method: 'POST',
                        body: fd
                    };

                    const url = isEdit ? API.UPDATE(id) : API.CREATE;

                    try {
                        showGlobalSpinner?.();
                        const res = await fetch(url, opts);
                        const json = await res.json();
                        if (!json.ok) {
                            showAlert('error', json.error || 'No se pudo guardar');
                            return;
                        }
                        showAlert('success', '¡Operación completada con éxito!');
                        closeModal('modal-crear-reserva');
                        listar();
                    } catch {
                        showAlert('error', 'Error de red al guardar');
                    } finally {
                        hideGlobalSpinner?.();
                    }
                }

                async function cambiarEstado(id, estado, motivo = '') {
                    try {
                        showGlobalSpinner?.();
                        const form = new FormData();
                        form.append('estado', estado);
                        if (estado === 'cancelada' && motivo) form.append('detalle_cancelacion', motivo);
                        const res = await fetch(API.CHANGE_STATE(id), {
                            method: 'POST',
                            body: form
                        });
                        const json = await res.json();
                        if (!json.ok) {
                            showAlert('error', json.error || 'No se pudo cambiar el estado');
                            return;
                        }
                        showAlert('success', 'Estado actualizado');
                        listar();
                    } catch {
                        showAlert('error', 'Error al cambiar estado');
                    } finally {
                        hideGlobalSpinner?.();
                    }
                }

                async function verHistorial(id) {
                    try {
                        showGlobalSpinner?.();
                        const res = await fetch(API.HISTORY(id), {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const json = await res.json();
                        if (!json.ok) {
                            showAlert('error', json.error || 'No se pudo cargar el historial');
                            return;
                        }
                        const cont = qs('#historial-wrapper');
                        const rows = (json.data || []).map((h, i) => `
        <div class="card-reserva" role="article" aria-label="Cambio ${i+1}">
          <div>
            <strong>${h.campo}</strong>: "${escapeHTML(h.valor_anterior || '')}" → "${escapeHTML(h.valor_nuevo || '')}"
            <div><small>${h.fecha} - ${escapeHTML(h.usuario || 'sistema')}</small></div>
          </div>
        </div>
      `).join('');
                        cont.innerHTML = rows || '<div class="badge warning">Sin cambios</div>';
                        openModal('modal-historial');
                    } catch {
                        showAlert('error', 'Error al cargar historial');
                    } finally {
                        hideGlobalSpinner?.();
                    }
                }

                // ====== Almanaque ======
                let currentViewDate = new Date();
                qs('#mes-anterior').addEventListener('click', () => {
                    currentViewDate.setMonth(currentViewDate.getMonth() - 1);
                    renderCalendario(currentViewDate);
                });
                qs('#mes-siguiente').addEventListener('click', () => {
                    currentViewDate.setMonth(currentViewDate.getMonth() + 1);
                    renderCalendario(currentViewDate);
                });

                function renderCalendario(baseDate) {
                    currentViewDate = new Date(baseDate.getFullYear(), baseDate.getMonth(), 1);
                    const year = currentViewDate.getFullYear();
                    const month = currentViewDate.getMonth(); // 0-11
                    qs('#mes-ano').textContent = currentViewDate.toLocaleDateString('es-AR', {
                        month: 'long',
                        year: 'numeric'
                    });

                    const firstDay = new Date(year, month, 1);
                    const startWeekday = (firstDay.getDay() + 6) % 7; // lunes=0
                    const daysInMonth = new Date(year, month + 1, 0).getDate();

                    const grid = qs('#calendario');
                    grid.innerHTML = '';

                    // Cabecera de días
                    ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'].forEach(d => {
                        const h = document.createElement('div');
                        h.className = 'badge info';
                        h.textContent = d;
                        h.setAttribute('role', 'columnheader');
                        grid.appendChild(h);
                    });

                    // Huecos antes del 1° (si los hay)
                    for (let i = 0; i < startWeekday; i++) {
                        const s = document.createElement('div');
                        grid.appendChild(s);
                    }

                    // Días
                    for (let day = 1; day <= daysInMonth; day++) {
                        const cell = document.createElement('button');
                        cell.type = 'button';
                        cell.className = 'day';
                        const dateISO = toISO(new Date(year, month, day));
                        cell.innerHTML = `
        <span class="date" aria-hidden="true">${day}</span>
        <span class="badge-dot">Ocupación</span>
      `;
                        cell.addEventListener('click', () => cargarSlots(dateISO));
                        grid.appendChild(cell);
                    }

                    // Limpio slots al cambiar mes
                    qs('#slots').innerHTML = '';
                }

                async function cargarSlots(dateISO) {
                    try {
                        showGlobalSpinner?.();
                        const p = new URLSearchParams({
                            date: dateISO
                        });
                        const res = await fetch(`${API.OCUPACION}&${p.toString()}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const json = await res.json();
                        if (!json.ok) {
                            showAlert('error', json.error || 'No se pudo cargar ocupación');
                            return;
                        }

                        const ocupados = new Set((json.data?.ocupados || []).map(h => h.hora)); // ['08:00','10:00',...]
                        const slots = [];
                        for (let h = HORARIO_INICIO; h < HORARIO_FIN; h += SLOT_HORAS) {
                            const hh = String(h).padStart(2, '0') + ':00';
                            const ocupado = ocupados.has(hh);
                            slots.push({
                                hora: hh,
                                ocupado
                            });
                        }
                        const cont = qs('#slots');
                        cont.innerHTML = slots.map(s => `
        <button class="btn ${s.ocupado ? 'btn-cancelar' : 'btn-aceptar'} slot-btn" type="button"
                ${s.ocupado ? 'disabled' : ''} data-hora="${s.hora}" data-date="${dateISO}">
          ${s.hora} ${s.ocupado ? '(Ocupado)' : '(Disponible)'}
        </button>
      `).join('');

                        cont.onclick = (ev) => {
                            const b = ev.target.closest('button.slot-btn:not([disabled])');
                            if (!b) return;
                            // Prellenar alta
                            resetForm();
                            qs('#reserva_fecha').value = b.dataset.date;
                            qs('#reserva_hora').value = b.dataset.hora;
                            closeModal('modal-almanaque');
                            openModal('modal-crear-reserva');
                            qs('#reserva_nombre').focus();
                        };
                    } catch {
                        showAlert('error', 'Error al cargar slots');
                    } finally {
                        hideGlobalSpinner?.();
                    }
                }

                // ====== Helpers generales ======
                function toISO(d) {
                    const y = d.getFullYear(),
                        m = String(d.getMonth() + 1).padStart(2, '0'),
                        day = String(d.getDate()).padStart(2, '0');
                    return `${y}-${m}-${day}`;
                }

                function escapeHTML(s) {
                    return String(s ?? '').replace(/[&<>"']/g, c => ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    } [c]));
                }

                // Carga inicial
                (function init() {
                    // Prefijar fecha_desde = hoy, ordenar por fecha asc en backend
                    const hoy = new Date();
                    qs('#filtro_fecha_desde').value = toISO(hoy);
                    takeFilters();
                    listar();
                })();
            </script>

        </div>
    </div>

    <script src="/views/partial/spinner-global.js" defer></script>
</body>

</html>