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
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .acciones { display:flex; gap:8px; flex-wrap: wrap; }
        .tabla-card h2 { margin-bottom: 8px; }
        @media (max-width: 768px) { .grid-2, .grid-3 { grid-template-columns: 1fr; } }
        .sr-only { position:absolute; left:-10000px; top:auto; width:1px; height:1px; overflow:hidden; }
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
                    <div class="form-grid grid-3">
                        <div class="input-group">
                            <label for="nombre">Nombre</label>
                            <div class="input-icon input-icon-name">
                                <input type="text" id="nombre" name="nombre" placeholder="Ej: Negroni" required aria-required="true" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="precio">Precio</label>
                            <div class="input-icon input-icon-name">
                                <input type="number" step="0.01" min="0" id="precio" name="precio" placeholder="0.00" required aria-required="true" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="orden">Orden</label>
                            <div class="input-icon input-icon-name">
                                <input type="number" id="orden" name="orden" placeholder="Autocompletado" readonly aria-readonly="true" />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="categoria">Categoría</label>
                            <div class="input-icon input-icon-name">
                                <select id="categoria" name="categoria" required aria-required="true">
                                    <option value="">Seleccioná…</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="subcategoria">Subcategoría</label>
                            <div class="input-icon input-icon-name">
                                <select id="subcategoria" name="subcategoria" required aria-required="true" disabled>
                                    <option value="">Seleccioná una categoría primero</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-grid grid-3">
                        <div class="input-group">
                            <label for="aclaracion_1">Aclaración 1</label>
                            <div class="input-icon input-icon-name">
                                <input type="text" id="aclaracion_1" name="aclaracion_1" placeholder="Opcional" />
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="aclaracion_2">Aclaración 2</label>
                            <div class="input-icon input-icon-name">
                                <input type="text" id="aclaracion_2" name="aclaracion_2" placeholder="Opcional" />
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="aclaracion_3">Aclaración 3</label>
                            <div class="input-icon input-icon-name">
                                <input type="text" id="aclaracion_3" name="aclaracion_3" placeholder="Opcional" />
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="detalle">Detalle</label>
                        <div class="input-icon input-icon-name">
                            <textarea id="detalle" name="detalle" placeholder="Descripción del producto" rows="3"></textarea>
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
                            <th>Orden</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                            <th>Subcategoría</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="/views/partial/spinner-global.js" defer></script>

<script>
(function(){
    'use strict';

    const $ = (sel) => document.querySelector(sel);
    const api = (action, params = {}) => {
        const url = new URL('admin_carta_controller.php', location.href);
        url.searchParams.set('action', action);
        Object.entries(params).forEach(([k,v]) => url.searchParams.set(k, v));
        return fetch(url.toString(), { headers: { 'Accept': 'application/json' } })
            .then(r => r.json());
    };

    const postJSON = (action, bodyObj) => {
        const url = new URL('admin_carta_controller.php', location.href);
        url.searchParams.set('action', action);
        return fetch(url.toString(), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(bodyObj)
        }).then(r => r.json());
    };

    const form = $('#formProducto');
    const selCat = $('#categoria');
    const selSub = $('#subcategoria');
    const inputOrden = $('#orden');
    const tbody = $('#tablaProductos tbody');

    function cargarCategorias(){
        api('listCategorias').then(res=>{
            if(!res.ok){ showAlert('error', res.error || 'Error listando categorías'); return; }
            selCat.innerHTML = '<option value="">Seleccioná…</option>';
            res.data.forEach(c=>{
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.nombre;
                selCat.appendChild(opt);
            });
        }).catch(()=>showAlert('error','Error de conexión al cargar categorías'));
    }

    function cargarSubcategorias(catId){
        selSub.disabled = true;
        selSub.innerHTML = '<option value="">Cargando…</option>';
        api('listSubcategorias', { categoria: String(catId) }).then(res=>{
            if(!res.ok){ showAlert('error', res.error || 'Error listando subcategorías'); return; }
            selSub.innerHTML = '<option value="">Seleccioná…</option>';
            res.data.forEach(s=>{
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.nombre;
                selSub.appendChild(opt);
            });
            selSub.disabled = false;
        }).catch(()=>showAlert('error','Error de conexión al cargar subcategorías'));
    }

    function cargarOrden(){
        api('nextOrden').then(res=>{
            if(!res.ok){ inputOrden.value = ''; return; }
            inputOrden.value = res.data.next;
        });
    }

    function cargarProductos(){
        api('listProductos').then(res=>{
            if(!res.ok){ showAlert('error', res.error || 'No se pudo listar productos'); return; }
            tbody.innerHTML = '';
            res.data.forEach((p, idx)=>{
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${idx+1}</td>
                    <td>${p.orden}</td>
                    <td>${p.nombre}</td>
                    <td>${p.precio}</td>
                    <td>${p.categoria_nombre}</td>
                    <td>${p.subcategoria_nombre}</td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    selCat.addEventListener('change', (e)=>{
        const val = e.target.value;
        if(val){ cargarSubcategorias(val); }
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
            orden: Number($('#orden').value || 0),
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
        postJSON('createProducto', payload).then(res=>{
            if(res.ok){
                showAlert('success','Producto guardado');
                form.reset();
                cargarOrden();
                selSub.disabled = true;
                selSub.innerHTML = '<option value="">Seleccioná una categoría primero</option>';
                cargarProductos();
            } else {
                showAlert('error', res.error || 'No se pudo guardar');
            }
        }).catch(()=>showAlert('error','Error de red al guardar'));
    });

    // Init
    cargarCategorias();
    cargarSubcategorias(0); // limpia
    cargarOrden();
    cargarProductos();
})();
</script>
</body>
</html>
