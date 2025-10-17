<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
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
    <title>Nairobi Lounge Bar — Carta</title>

    <!-- Íconos de Material Design -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Framework desde CDN -->
    <link rel="stylesheet" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css">
    <script src="https://www.fernandosalguero.com/cdn/assets/javascript/framework.js" defer></script>

    <style>
        /* Inline: estilos mínimos sin romper el CDN */
        .form-grid { display: grid; gap: 12px; }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .acciones { display: flex; gap: 8px; flex-wrap: wrap; }
        .tabla-card h2 { margin-bottom: 8px; }
        @media (max-width: 768px){ .grid-3 { grid-template-columns: 1fr; } }
        .sr-only{ position: absolute; left: -10000px; top: auto; width:1px; height:1px; overflow:hidden; }

        /* “Detalle” ocupa 3 columnas en desktop */
        .span-all { grid-column: 1 / -1; }

        /* Icono dentro de input (no rompe el framework) */
        .input-icon .mi { display:inline-flex; align-items:center; margin-right:8px; }
        .input-icon { display:flex; align-items:center; }

        /* Acciones de tabla */
        .action-btn { background: transparent; border: none; cursor: pointer; padding: 6px; }
        .action-btn:focus { outline: 2px solid #7c3aed; outline-offset: 2px; }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="material-icons logo-icon">dashboard</span>
            <span class="logo-text">ISO</span>
        </div>
        <nav class="sidebar-menu" aria-label="Menú lateral">
            <ul>
                <li onclick="location.href='admin_dashboard_view.php'">
                    <span class="material-icons" style="color:#5b21b6;">home</span><span class="link-text">Inicio</span>
                </li>
                <li onclick="location.href='admin_carta_view.php'" aria-current="page">
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
            <button class="btn-icon" onclick="toggleSidebar()" aria-label="Colapsar menú">
                <span class="material-icons" id="collapseIcon">chevron_left</span>
            </button>
        </div>
    </aside>

    <div class="main">
        <header class="navbar">
            <button class="btn-icon" onclick="toggleSidebar()" aria-label="Abrir menú">
                <span class="material-icons">menu</span>
            </button>
            <div class="navbar-title">Carta — Alta de producto</div>
        </header>

        <section class="content">
            <div class="card">
                <h2>Nuevo producto</h2>
                <form id="formProducto" autocomplete="off" novalidate>
                    <!-- 3 columnas x 3 filas; “detalle” ocupa las 3 columnas -->
                    <div class="form-grid grid-3">
                        <div class="input-group">
                            <label for="nombre">Nombre</label>
                            <div class="input-icon input-icon-name">
                                <span class="material-icons mi" aria-hidden="true">label</span>
                                <input type="text" id="nombre" name="nombre" placeholder="Ej: Negroni" required aria-required="true" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="precio">Precio</label>
                            <div class="input-icon input-icon-name">
                                <span class="material-icons mi" aria-hidden="true">attach_money</span>
                                <input type="number" step="0.01" min="0" id="precio" name="precio" placeholder="0.00" required aria-required="true" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="categoria">Categoría</label>
                            <div class="input-icon input-icon-name">
                                <span class="material-icons mi" aria-hidden="true">category</span>
                                <select id="categoria" name="categoria" required aria-required="true">
                                    <option value="">Seleccioná…</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="subcategoria">Subcategoría</label>
                            <div class="input-icon input-icon-name">
                                <span class="material-icons mi" aria-hidden="true">subdirectory_arrow_right</span>
                                <select id="subcategoria" name="subcategoria" required aria-required="true" disabled>
                                    <option value="">Seleccioná una categoría primero</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="aclaracion_1">Aclaración 1</label>
                            <div class="input-icon input-icon-name">
                                <span class="material-icons mi" aria-hidden="true">info</span>
                                <input type="text" id="aclaracion_1" name="aclaracion_1" placeholder="Opcional" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="aclaracion_2">Aclaración 2</label>
                            <div class="input-icon input-icon-name">
                                <span class="material-icons mi" aria-hidden="true">info</span>
                                <input type="text" id="aclaracion_2" name="aclaracion_2" placeholder="Opcional" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="aclaracion_3">Aclaración 3</label>
                            <div class="input-icon input-icon-name">
                                <span class="material-icons mi" aria-hidden="true">info</span>
                                <input type="text" id="aclaracion_3" name="aclaracion_3" placeholder="Opcional" />
                            </div>
                        </div>

                        <div class="input-group span-all">
                            <label for="detalle">Detalle</label>
                            <div class="input-icon input-icon-name">
                                <span class="material-icons mi" aria-hidden="true">description</span>
                                <textarea id="detalle" name="detalle" placeholder="Descripción del producto (máx. 255)" rows="3" maxlength="255" aria-describedby="detalleCount"></textarea>
                            </div>
                            <small id="detalleCount" aria-live="polite">0/255</small>
                        </div>
                    </div>

                    <div class="acciones">
                        <button type="submit" class="btn btn-aceptar">Guardar</button>
                        <button type="reset" class="btn btn-cancelar">Limpiar</button>
                        <span class="sr-only" role="status" aria-live="polite" id="estadoForm"></span>
                    </div>
                </form>
            </div>

            <div class="card tabla-card" id="wrapTabla">
                <h2>Productos cargados</h2>
                <div class="tabla-wrapper">
                    <table class="data-table" id="tablaProductos" aria-describedby="tabla de productos">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                            <th>Subcategoría</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modales -->
<div id="modalDelete" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalDeleteTitle">
    <div class="modal-content">
        <h3 id="modalDeleteTitle">Eliminar producto</h3>
        <p>¿Confirmás eliminar este producto?</p>
        <div class="form-buttons">
            <button class="btn btn-aceptar" id="btnConfirmDelete">Eliminar</button>
            <button class="btn btn-cancelar" onclick="closeModalSafe('modalDelete')">Cancelar</button>
        </div>
    </div>
</div>

<div id="modalEdit" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalEditTitle">
    <div class="modal-content">
        <h3 id="modalEditTitle">Editar producto</h3>
        <form id="formEdit" autocomplete="off" novalidate>
            <input type="hidden" id="edit_id" name="id" />
            <div class="form-grid grid-3">
                <div class="input-group">
                    <label for="edit_nombre">Nombre</label>
                    <div class="input-icon input-icon-name">
                        <span class="material-icons mi" aria-hidden="true">label</span>
                        <input type="text" id="edit_nombre" name="nombre" required aria-required="true" />
                    </div>
                </div>
                <div class="input-group">
                    <label for="edit_precio">Precio</label>
                    <div class="input-icon input-icon-name">
                        <span class="material-icons mi" aria-hidden="true">attach_money</span>
                        <input type="number" step="0.01" min="0" id="edit_precio" name="precio" required aria-required="true" />
                    </div>
                </div>
                <div class="input-group">
                    <label for="edit_categoria">Categoría</label>
                    <div class="input-icon input-icon-name">
                        <span class="material-icons mi" aria-hidden="true">category</span>
                        <select id="edit_categoria" name="categoria" required aria-required="true"></select>
                    </div>
                </div>
                <div class="input-group">
                    <label for="edit_subcategoria">Subcategoría</label>
                    <div class="input-icon input-icon-name">
                        <span class="material-icons mi" aria-hidden="true">subdirectory_arrow_right</span>
                        <select id="edit_subcategoria" name="subcategoria" required aria-required="true" disabled></select>
                    </div>
                </div>
                <div class="input-group">
                    <label for="edit_aclaracion_1">Aclaración 1</label>
                    <div class="input-icon input-icon-name">
                        <span class="material-icons mi" aria-hidden="true">info</span>
                        <input type="text" id="edit_aclaracion_1" name="aclaracion_1" />
                    </div>
                </div>
                <div class="input-group">
                    <label for="edit_aclaracion_2">Aclaración 2</label>
                    <div class="input-icon input-icon-name">
                        <span class="material-icons mi" aria-hidden="true">info</span>
                        <input type="text" id="edit_aclaracion_2" name="aclaracion_2" />
                    </div>
                </div>
                <div class="input-group">
                    <label for="edit_aclaracion_3">Aclaración 3</label>
                    <div class="input-icon input-icon-name">
                        <span class="material-icons mi" aria-hidden="true">info</span>
                        <input type="text" id="edit_aclaracion_3" name="aclaracion_3" />
                    </div>
                </div>
                <div class="input-group span-all">
                    <label for="edit_detalle">Detalle</label>
                    <div class="input-icon input-icon-name">
                        <span class="material-icons mi" aria-hidden="true">description</span>
                        <textarea id="edit_detalle" name="detalle" rows="3" maxlength="255" aria-describedby="editDetalleCount"></textarea>
                    </div>
                    <small id="editDetalleCount" aria-live="polite">0/255</small>
                </div>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn btn-aceptar">Guardar cambios</button>
                <button type="button" class="btn btn-cancelar" onclick="closeModalSafe('modalEdit')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="/views/partial/spinner-global.js" defer></script>
<script>
(function(){
    'use strict';
    const $ = (sel) => document.querySelector(sel);
    const $$ = (sel) => Array.from(document.querySelectorAll(sel));

// Helpers de modal seguros (no sobreescriben el CDN)
const openModalSafe = (id) => {
    const el = document.getElementById(id);
    if (!el) { console.error('Modal no encontrado:', id); showAlert('error', 'No se encontró el modal.'); return; }
    el.classList.remove('hidden');
    el.setAttribute('aria-hidden', 'false');
    const focusable = el.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable) focusable.focus();
};
const closeModalSafe = (id) => {
    const el = document.getElementById(id);
    if (!el) { console.error('Modal no encontrado:', id); return; }
    el.classList.add('hidden');
    el.setAttribute('aria-hidden', 'true');
};

    // API helpers
    const api = (action, params = {}) => {
        const url = new URL('../../controllers/admin_carta_controller.php', location.href);
        url.searchParams.set('action', action);
        Object.entries(params).forEach(([k,v]) => url.searchParams.set(k, v));
        return fetch(url.toString(), { headers:{ 'Accept':'application/json' }})
            .then(async r => {
                if(!r.ok){ const t = await r.text().catch(()=> ''); throw new Error(`HTTP ${r.status} ${r.statusText} ${t ? '— '+t.slice(0,120):''}`); }
                return r.json();
            });
    };
    const postJSON = (action, bodyObj) => {
        const url = new URL('../../controllers/admin_carta_controller.php', location.href);
        url.searchParams.set('action', action);
        return fetch(url.toString(), {
            method:'POST',
            headers:{ 'Content-Type':'application/json', 'Accept':'application/json' },
            body: JSON.stringify(bodyObj)
        }).then(async r => {
            if(!r.ok){ const t = await r.text().catch(()=> ''); throw new Error(`HTTP ${r.status} ${r.statusText} ${t ? '— '+t.slice(0,120):''}`); }
            return r.json();
        });
    };

    // DOM refs (crear)
    const form = $('#formProducto');
    const selCat = $('#categoria');
    const selSub = $('#subcategoria');
    const tbody = $('#tablaProductos tbody');
    const txtDetalle = $('#detalle');
    const detalleCount = $('#detalleCount');

    // Estado en memoria
    let categoriasCache = []; // [{id, nombre}]
    let productoAEliminar = null; // {id}
    let productosCache = []; // para editar rápidamente

    function cargarCategorias(){
        api('listCategorias').then(res=>{
            if(!res.ok){ showAlert('error', res.error || 'Error listando categorías'); return; }
            categoriasCache = res.data || [];
            selCat.innerHTML = '<option value="">Seleccioná…</option>';
            categoriasCache.forEach(c=>{
                const opt = document.createElement('option');
                opt.value = c.id; opt.textContent = c.nombre;
                selCat.appendChild(opt);
            });
            // También para modal de edición (se carga al abrir)
        }).catch(()=> showAlert('error','Error de conexión al cargar categorías'));
    }

    function cargarSubcategorias(catId, selectEl){
        const target = selectEl || selSub;
        target.disabled = true;
        target.innerHTML = '<option value="">Cargando…</option>';
        api('listSubcategorias', { categoria: String(catId) }).then(res=>{
            if(!res.ok){ showAlert('error', res.error || 'Error listando subcategorías'); return; }
            target.innerHTML = '<option value="">Seleccioná…</option>';
            res.data.forEach(s=>{
                const opt = document.createElement('option');
                opt.value = s.id; opt.textContent = s.nombre;
                target.appendChild(opt);
            });
            target.disabled = false;
        }).catch(()=> showAlert('error','Error de conexión al cargar subcategorías'));
    }

    function cargarProductos(){
        api('listProductos').then(res=>{
            if(!res.ok){ showAlert('error', res.error || 'No se pudo listar productos'); return; }
            productosCache = res.data || [];
            tbody.innerHTML = '';
            productosCache.forEach((p, idx)=>{
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${idx+1}</td>
                    <td>${p.nombre}</td>
                    <td>${p.precio}</td>
                    <td>${p.categoria_nombre}</td>
                    <td>${p.subcategoria_nombre}</td>
                    <td>
                        <button class="action-btn" title="Editar" aria-label="Editar" data-id="${p.id}">
                            <span class="material-icons">edit</span>
                        </button>
                        <button class="action-btn" title="Eliminar" aria-label="Eliminar" data-id="${p.id}">
                            <span class="material-icons" style="color:red;">delete</span>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    // Crear
    selCat.addEventListener('change', (e)=>{
        const val = e.target.value;
        if(val){ cargarSubcategorias(val, selSub); }
        else{
            selSub.disabled = true;
            selSub.innerHTML = '<option value="">Seleccioná una categoría primero</option>';
        }
    });

    form.addEventListener('submit', (e)=>{
        e.preventDefault();
        const payload = {
            nombre: $('#nombre').value.trim(),
            precio: Number($('#precio').value),
            categoria: Number($('#categoria').value || 0),
            subcategoria: Number($('#subcategoria').value || 0),
            aclaracion_1: $('#aclaracion_1').value.trim(),
            aclaracion_2: $('#aclaracion_2').value.trim(),
            aclaracion_3: $('#aclaracion_3').value.trim(),
            detalle: $('#detalle').value.trim()
        };
        if(!payload.nombre || !payload.precio || !payload.categoria || !payload.subcategoria){
            showAlert('info','Completá nombre, precio, categoría y subcategoría.');
            return;
        }
        if(payload.detalle.length > 255){
            showAlert('info','Detalle no puede superar 255 caracteres.');
            return;
        }
        postJSON('createProducto', payload).then(res=>{
            if(res.ok){
                showAlert('success','Producto guardado');
                form.reset();
                selSub.disabled = true;
                selSub.innerHTML = '<option value="">Seleccioná una categoría primero</option>';
                detalleCount.textContent = '0/255';
                cargarProductos();
            }else{
                showAlert('error', res.error || 'No se pudo guardar');
            }
        }).catch(()=> showAlert('error','Error de red al guardar'));
    });

    // Contador de detalle
    txtDetalle.addEventListener('input', ()=>{
        const len = (txtDetalle.value || '').length;
        detalleCount.textContent = `${len}/255`;
    });

    // Delegación de acciones tabla
    tbody.addEventListener('click', (e)=>{
        const btn = e.target.closest('button.action-btn');
        if(!btn) return;
        const id = Number(btn.getAttribute('data-id'));
        const icon = btn.querySelector('.material-icons')?.textContent || '';
        if(icon === 'delete'){
            productoAEliminar = { id };
            openModalSafe('modalDelete');
        }else if(icon === 'edit'){
            abrirModalEditar(id);
        }
    });

    // Eliminar
    $('#btnConfirmDelete').addEventListener('click', ()=>{
        if(!productoAEliminar){ closeModalSafe('modalDelete'); return; }
        postJSON('deleteProducto', { id: productoAEliminar.id }).then(res=>{
            if(res.ok && res.deleted){
                showAlert('success','Producto eliminado');
                cargarProductos();
            }else{
                showAlert('error', res.error || 'No se pudo eliminar');
            }
            productoAEliminar = null;
            closeModalSafe('modalDelete');
        }).catch(()=> {
            showAlert('error','Error de red al eliminar');
            closeModalSafe('modalDelete');
        });
    });

    // Editar
    function poblarSelectCategorias(selectEl, valor){
        selectEl.innerHTML = '<option value="">Seleccioná…</option>';
        categoriasCache.forEach(c=>{
            const opt = document.createElement('option');
            opt.value = c.id; opt.textContent = c.nombre;
            if(String(valor) === String(c.id)) opt.selected = true;
            selectEl.appendChild(opt);
        });
    }

    function abrirModalEditar(id){
        const p = productosCache.find(x => Number(x.id) === Number(id));
        if(!p){ showAlert('error','Producto no encontrado'); return; }

        // Llenar campos
        $('#edit_id').value = p.id;
        $('#edit_nombre').value = p.nombre;
        $('#edit_precio').value = p.precio;
        $('#edit_aclaracion_1').value = p.aclaracion_1 || '';
        $('#edit_aclaracion_2').value = p.aclaracion_2 || '';
        $('#edit_aclaracion_3').value = p.aclaracion_3 || '';
        $('#edit_detalle').value = p.detalle || '';
        $('#editDetalleCount').textContent = `${($('#edit_detalle').value || '').length}/255`;

        // Categorías y subcategorías dependientes
        const selCatEdit = $('#edit_categoria');
        const selSubEdit = $('#edit_subcategoria');

        // Poblar categorías (desde cache)
        poblarSelectCategorias(selCatEdit, p.categoria);

        // Cargar subcategorías de la categoría del producto y setear valor actual
        selSubEdit.disabled = true;
        selSubEdit.innerHTML = '<option value="">Cargando…</option>';
        api('listSubcategorias', { categoria: String(p.categoria) }).then(res=>{
            if(!res.ok){ showAlert('error', res.error || 'Error listando subcategorías'); return; }
            selSubEdit.innerHTML = '<option value="">Seleccioná…</option>';
            res.data.forEach(s=>{
                const opt = document.createElement('option');
                opt.value = s.id; opt.textContent = s.nombre;
                if(String(p.subcategoria) === String(s.id)) opt.selected = true;
                selSubEdit.appendChild(opt);
            });
            selSubEdit.disabled = false;
            openModalSafe('modalEdit');
        });

        // Cambio de categoría dentro del modal => recargar subcategorías
        selCatEdit.onchange = (ev)=>{
            const nuevaCat = ev.target.value;
            if(nuevaCat){
                cargarSubcategorias(nuevaCat, selSubEdit);
            }else{
                selSubEdit.disabled = true;
                selSubEdit.innerHTML = '<option value="">Seleccioná una categoría primero</option>';
            }
        };

        // Contador detalle modal
        $('#edit_detalle').addEventListener('input', (ev)=>{
            const len = (ev.target.value || '').length;
            $('#editDetalleCount').textContent = `${len}/255`;
        }, { once:false });
    }

    $('#formEdit').addEventListener('submit', (e)=>{
        e.preventDefault();
        const payload = {
            id: Number($('#edit_id').value),
            nombre: $('#edit_nombre').value.trim(),
            precio: Number($('#edit_precio').value),
            categoria: Number($('#edit_categoria').value || 0),
            subcategoria: Number($('#edit_subcategoria').value || 0),
            aclaracion_1: $('#edit_aclaracion_1').value.trim(),
            aclaracion_2: $('#edit_aclaracion_2').value.trim(),
            aclaracion_3: $('#edit_aclaracion_3').value.trim(),
            detalle: $('#edit_detalle').value.trim()
        };
        if(!payload.id || !payload.nombre || !payload.precio || !payload.categoria || !payload.subcategoria){
            showAlert('info','Completá nombre, precio, categoría y subcategoría.');
            return;
        }
        if(payload.detalle.length > 255){
            showAlert('info','Detalle no puede superar 255 caracteres.');
            return;
        }
        postJSON('updateProducto', payload).then(res=>{
            if(res.ok && res.updated){
                showAlert('success','Producto actualizado');
                closeModalSafe('modalEdit');
                cargarProductos();
            }else{
                showAlert('error', res.error || 'No se pudo actualizar');
            }
        }).catch(()=> showAlert('error','Error de red al actualizar'));
    });

    // Init
    cargarCategorias();
    selSub.disabled = true;
    selSub.innerHTML = '<option value="">Seleccioná una categoría primero</option>';
    cargarProductos();
})();
</script>
</body>
</html>
