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
                <div class="navbar-title">Inicio</div>
            </header>

            <section class="content">
                <div class="card">
                    <h2>Gestión de Reservas</h2>
                    <p>Creá nuevas reservas y administrá las existentes.</p>
                </div>

                <!-- Formulario: Nueva Reserva -->
                <div class="card">
                    <h3>Nueva reserva</h3>
                    <form id="formNuevaReserva" autocomplete="off" novalidate aria-labelledby="tituloNuevaReserva">
                        <div class="form-grid grid-3">
                            <div class="input-group">
                                <label for="nombre">Nombre y apellido</label>
                                <div class="input-icon input-icon-name">
                                    <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan Pérez" required
                                        aria-required="true" />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="telefono">Teléfono</label>
                                <div class="input-icon input-icon-phone">
                                    <input type="tel" id="telefono" name="telefono" placeholder="+54 9 11 1234 5678" required
                                        aria-required="true" />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="fecha">Fecha</label>
                                <div class="input-icon input-icon-calendar">
                                    <input type="date" id="fecha" name="fecha" required aria-required="true" />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="hora">Hora</label>
                                <div class="input-icon input-icon-time">
                                    <input type="time" id="hora" name="hora" required aria-required="true" />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="personas">Personas</label>
                                <div class="input-icon input-icon-group">
                                    <input type="number" id="personas" name="personas" min="1" max="50" value="2" required aria-required="true" />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="estado">Estado</label>
                                <div class="input-icon input-icon-check">
                                    <select id="estado" name="estado" required aria-required="true">
                                        <option value="pendiente" selected>Pendiente</option>
                                        <option value="confirmada">Confirmada</option>
                                        <option value="finalizada">Finalizada</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="input-group" style="grid-column: 1 / -1;">
                                <label for="notas">Notas</label>
                                <div class="input-icon input-icon-edit">
                                    <textarea id="notas" name="notas" placeholder="Preferencia de mesa, alergias, etc." rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn btn-aceptar" aria-label="Crear reserva">Crear reserva</button>
                            <button type="reset" class="btn btn-cancelar" aria-label="Limpiar formulario">Limpiar</button>
                        </div>
                    </form>
                </div>

                <!-- Tabla: Reservas -->
                <div class="card tabla-card">
                    <h3>Reservas</h3>
                    <div class="tabla-wrapper">
                        <table class="data-table" id="tablaReservas" aria-describedby="descripcionTabla">
                            <caption id="descripcionTabla" class="sr-only">Listado de reservas con opciones de edición y eliminación.</caption>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Personas</th>
                                    <th>Estado</th>
                                    <th>Notas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Modal edición -->
            <div id="modalReserva" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitulo">
                <div class="modal-content" style="max-width: 720px;">
                    <h3 id="modalTitulo">Editar reserva</h3>
                    <form id="formEditarReserva" novalidate>
                        <input type="hidden" id="edit_id" name="id" />
                        <div class="form-grid grid-3">
                            <div class="input-group">
                                <label for="edit_nombre">Nombre y apellido</label>
                                <div class="input-icon input-icon-name">
                                    <input type="text" id="edit_nombre" name="nombre" required />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="edit_telefono">Teléfono</label>
                                <div class="input-icon input-icon-phone">
                                    <input type="tel" id="edit_telefono" name="telefono" required />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="edit_fecha">Fecha</label>
                                <div class="input-icon input-icon-calendar">
                                    <input type="date" id="edit_fecha" name="fecha" required />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="edit_hora">Hora</label>
                                <div class="input-icon input-icon-time">
                                    <input type="time" id="edit_hora" name="hora" required />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="edit_personas">Personas</label>
                                <div class="input-icon input-icon-group">
                                    <input type="number" id="edit_personas" name="personas" min="1" max="50" required />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="edit_estado">Estado</label>
                                <div class="input-icon input-icon-check">
                                    <select id="edit_estado" name="estado" required>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="confirmada">Confirmada</option>
                                        <option value="finalizada">Finalizada</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="input-group" style="grid-column: 1 / -1;">
                                <label for="edit_notas">Notas</label>
                                <div class="input-icon input-icon-edit">
                                    <textarea id="edit_notas" name="notas" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn btn-aceptar">Guardar cambios</button>
                            <button type="button" class="btn btn-cancelar" onclick="reservasCloseModal()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal confirmación eliminar -->
            <div id="modalEliminar" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalEliminarTitulo">
                <div class="modal-content" style="max-width: 520px;">
                    <h3 id="modalEliminarTitulo">Eliminar reserva</h3>
                    <p id="modalEliminarTexto">¿Confirmás eliminar la reserva seleccionada?</p>
                    <div class="form-buttons">
                        <button id="btnConfirmarEliminar" class="btn btn-aceptar" type="button">Eliminar</button>
                        <button id="btnCancelarEliminar" class="btn btn-cancelar" type="button">Cancelar</button>
                    </div>
                </div>
            </div>

            <style>
                /* Accesibilidad mínima */
                .sr-only {
                    position: absolute;
                    width: 1px;
                    height: 1px;
                    margin: -1px;
                    padding: 0;
                    overflow: hidden;
                    clip: rect(0, 0, 0, 0);
                    border: 0;
                }

                /* Píldoras de estado */
                .badge.state {
                    padding: .25rem .5rem;
                    border-radius: 999px;
                    display: inline-block;
                    font-size: .85rem;
                    white-space: nowrap
                }

                .badge.state.pendiente {
                    background: #fff7ed;
                    color: #9a3412;
                    border: 1px solid #fed7aa
                }

                .badge.state.confirmada {
                    background: #ecfdf5;
                    color: #065f46;
                    border: 1px solid #a7f3d0
                }

                .badge.state.finalizada {
                    background: #eef2ff;
                    color: #3730a3;
                    border: 1px solid #c7d2fe
                }

                .badge.state.cancelada {
                    background: #fef2f2;
                    color: #991b1b;
                    border: 1px solid #fecaca
                }

                /* Botones de acción sólo ícono en tabla */
                .data-table .btn-icon {
                    padding: .25rem;
                    line-height: 1;
                    vertical-align: middle
                }

                .data-table .btn-icon .material-icons {
                    font-size: 20px
                }

                /* Evitar FOUC sencillo en modal */
                .modal.hidden {
                    display: none
                }
            </style>


            <script>
                (() => {

                    // Asegura contenedor para alerts del framework si lo requiere
                    (() => {
                        const candidates = ['.alert-container', '.alert-wrapper', '#alert-container'];
                        const found = candidates.some(sel => document.querySelector(sel));
                        if (!found) {
                            const div = document.createElement('div');
                            div.className = 'alert-container';
                            div.style.position = 'fixed';
                            div.style.bottom = '1rem';
                            div.style.right = '1rem';
                            div.style.zIndex = '9999';
                            document.body.appendChild(div);
                            console.info('[reservas] Se creó .alert-container dinámicamente para showAlert.');
                        } else {
                            console.info('[reservas] Contenedor de alertas existente.');
                        }
                    })();

                    const API = '../../controllers/admin_reservas_controller.php';

                    const formNueva = document.getElementById('formNuevaReserva');
                    const formEditar = document.getElementById('formEditarReserva');
                    const tablaBody = document.querySelector('#tablaReservas tbody');

                    // Modal Edición
                    function getModalEdit() {
                        return document.getElementById('modalReserva');
                    }
                    window.reservasOpenModal = () => {
                        const m = getModalEdit();
                        if (!m) {
                            console.error('[reservas] modalReserva no encontrado en el DOM al abrir.');
                            throw new Error('No se encontró el modal de edición (modalReserva).');
                        }
                        m.classList.remove('hidden');
                        const first = m.querySelector('input,select,textarea');
                        if (first) first.focus();
                    };
                    window.reservasCloseModal = () => {
                        const m = getModalEdit();
                        if (!m) {
                            console.warn('[reservas] modalReserva no encontrado al cerrar.');
                            return;
                        }
                        m.classList.add('hidden');
                    };

                    // Diagnóstico: asegurar que no colisionamos con el framework
                    console.info('[reservas] funciones de modal listas:', {
                        reservasOpenModal: typeof window.reservasOpenModal,
                        reservasCloseModal: typeof window.reservasCloseModal,
                        frameworkOpenModalType: typeof window.openModal, // del framework
                        frameworkCloseModalType: typeof window.closeModal // del framework
                    });

                    // Modal Eliminar
                    const modalEliminar = document.getElementById('modalEliminar');
                    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
                    const btnCancelarEliminar = document.getElementById('btnCancelarEliminar');
                    let idAEliminar = null;
                    const openModalEliminar = (id) => {
                        idAEliminar = id;
                        modalEliminar.classList.remove('hidden');
                        btnConfirmarEliminar.focus();
                    };
                    const closeModalEliminar = () => {
                        modalEliminar.classList.add('hidden');
                        idAEliminar = null;
                    };
                    btnCancelarEliminar.addEventListener('click', closeModalEliminar);
                    btnConfirmarEliminar.addEventListener('click', async () => {
                        if (!idAEliminar) return;
                        const fd = new FormData();
                        fd.append('_method', 'delete');
                        fd.append('id', String(idAEliminar));
                        try {
                            const res = await fetch(API, {
                                method: 'POST',
                                body: fd
                            });
                            const json = await res.json();
                            if (!json.ok) throw new Error(json.error || 'No se pudo eliminar la reserva');
                            notify('success', 'Reserva eliminada');
                            closeModalEliminar();
                            await cargarReservas();
                        } catch (err) {
                            notify('error', err.message);
                        }
                    });

                    // Notificaciones: wrapper seguro para evitar errores en showAlert + log
                    function notify(type, message) {
                        try {
                            if (typeof showAlert === 'function') {
                                try {
                                    showAlert(type, message);
                                } catch (internalErr) {
                                    console.error('[reservas] showAlert lanzó un error interno:', internalErr);
                                    alert((type ? `[${type.toUpperCase()}] ` : '') + message);
                                }
                            } else {
                                console.warn('[reservas] showAlert no disponible, uso fallback.');
                                alert((type ? `[${type.toUpperCase()}] ` : '') + message);
                            }
                        } catch (outerErr) {
                            console.error('[reservas] notify falló:', outerErr);
                            alert(message);
                        }
                    }


                    // Render helpers
                    const estadoBadge = (estado) => {
                        const e = (estado || '').toLowerCase();
                        return `<span class="badge state ${e}">${estado}</span>`;
                    };

                    const filaReserva = (r) => {
                        const notas = r.notas ? r.notas : '';
                        return `
            <tr data-id="${r.id}">
                <td>${r.nombre}</td>
                <td>${r.telefono}</td>
                <td>${r.fecha}</td>
                <td>${r.hora?.slice(0,5) || r.hora}</td>
                <td>${r.personas}</td>
                <td>${estadoBadge(r.estado)}</td>
                <td>${notas}</td>
                <td>
                    <button class="btn-icon" title="Editar" aria-label="Editar reserva" data-action="edit" data-id="${r.id}">
                        <span class="material-icons" aria-hidden="true">edit</span>
                    </button>
<button class="btn-icon" title="Eliminar" aria-label="Eliminar reserva" data-action="delete" data-id="${r.id}">
    <span class="material-icons" aria-hidden="true" style="color:#dc2626;">delete</span>
</button>
                </td>
            </tr>
        `;
                    };

                    const cargarReservas = async () => {
                        try {
                            const res = await fetch(API, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            const json = await res.json();
                            if (!json.ok) throw new Error(json.error || 'Error al cargar reservas');
                            tablaBody.innerHTML = json.data.map(filaReserva).join('');
                        } catch (err) {
                            notify('error', err.message);
                        }
                    };

                    // Create
                    formNueva.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const fd = new FormData(formNueva);
                        try {
                            const res = await fetch(API, {
                                method: 'POST',
                                body: fd
                            });
                            const json = await res.json();
                            if (!json.ok) throw new Error(json.error || 'No se pudo crear la reserva');
                            notify('success', '¡Reserva creada!');
                            formNueva.reset();
                            await cargarReservas();
                        } catch (err) {
                            notify('error', err.message);
                        }
                    });

                    // Delegación: Acciones en tabla
                    document.addEventListener('click', async (e) => {
                        const btn = e.target.closest('button[data-action]');
                        if (!btn) return;
                        const id = btn.dataset.id;
                        const action = btn.dataset.action;

                        if (action === 'edit') {
                            console.group('[reservas] Editar', id);
                            try {
                                console.time('[reservas] fetch detalle');
                                const res = await fetch(`${API}?id=${encodeURIComponent(id)}`, {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                });
                                console.timeEnd('[reservas] fetch detalle');
                                console.debug('[reservas] HTTP', res.status);
                                const json = await res.json();
                                console.debug('[reservas] JSON', json);
                                if (!json.ok) throw new Error(json.error || 'No se pudo obtener la reserva');

                                const r = json.data;
                                const elId = document.getElementById('edit_id');
                                const elNombre = document.getElementById('edit_nombre');
                                const elTelefono = document.getElementById('edit_telefono');
                                const elFecha = document.getElementById('edit_fecha');
                                const elHora = document.getElementById('edit_hora');
                                const elPersonas = document.getElementById('edit_personas');
                                const elEstado = document.getElementById('edit_estado');
                                const elNotas = document.getElementById('edit_notas');

                                if (!elId || !elNombre || !elTelefono || !elFecha || !elHora || !elPersonas || !elEstado || !elNotas) {
                                    console.error('[reservas] Faltan elementos del formulario de edición', {
                                        elId,
                                        elNombre,
                                        elTelefono,
                                        elFecha,
                                        elHora,
                                        elPersonas,
                                        elEstado,
                                        elNotas
                                    });
                                    throw new Error('Formulario de edición incompleto en el DOM.');
                                }

                                elId.value = r.id;
                                elNombre.value = r.nombre ?? '';
                                elTelefono.value = r.telefono ?? '';
                                elFecha.value = r.fecha ?? '';
                                elHora.value = (r.hora && r.hora.slice) ? r.hora.slice(0, 5) : (r.hora ?? '');
                                elPersonas.value = r.personas ?? 1;
                                elEstado.value = r.estado ?? 'pendiente';
                                elNotas.value = r.notas ?? '';

                                // Abrir modal con guardas
                                try {
                                    reservasOpenModal();
                                    console.debug('[reservas] modal de edición abierto');
                                } catch (modalErr) {
                                    console.error('[reservas] fallo al abrir modal:', modalErr);
                                    throw modalErr; // re-lanza para notificar
                                }
                            } catch (err) {
                                console.error('[reservas] error en editar:', err);
                                notify('error', err.message || String(err));
                            } finally {
                                console.groupEnd();
                            }
                        }

                        if (action === 'delete') {
                            openModalEliminar(id);
                        }
                    });

                    // Update
                    formEditar.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const fd = new FormData(formEditar);
                        fd.append('_method', 'put');
                        try {
                            const res = await fetch(API, {
                                method: 'POST',
                                body: fd
                            });
                            const json = await res.json();
                            if (!json.ok) throw new Error(json.error || 'No se pudo actualizar la reserva');
                            // Primero cerramos modal, luego notificamos (evita problemas de overlay/foco/containers)
                            reservasCloseModal();
                            notify('success', 'Reserva actualizada');
                            await cargarReservas();
                        } catch (err) {
                            notify('error', err.message);
                        }
                    });

                    // Init
                    document.addEventListener('DOMContentLoaded', () => {
                        cargarReservas();
                    });
                })();
            </script>


        </div>
    </div>

    <script src="/views/partial/spinner-global.js" defer></script>
    <div id="alert-container" class="alert-container" style="position:fixed;bottom:1rem;right:1rem;z-index:9999;"></div>

</body>

</html>