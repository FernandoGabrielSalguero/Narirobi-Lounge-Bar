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

    <!-- Picker avanzado (solo CDN, sin archivos nuevos locales) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js" defer></script>
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
                    <h2>Hola 👋</h2>
                    <p>Configurá los <strong>colores de entorno</strong> del sistema (texto, fondo y acento). Se aceptan valores <code>#hex</code> (3/6 dígitos) o <code>rgb(r,g,b)</code>.</p>
                </div>

                <div class="card" id="card-form-entorno">
                    <h3>Colores del sistema</h3>

                    <form id="form-entorno" novalidate>
                        <div class="form-grid grid-3">
                            <div class="input-group">
                                <label for="color_texto">Color de texto</label>
                                <div class="input-icon input-icon-name">
                                    <input type="text" id="color_texto" name="color_texto" placeholder="#111111 o rgb(17,17,17)" aria-describedby="color_texto_help" required />
                                </div>
                                <button type="button" class="btn btn-info" id="btn-paleta-texto" aria-controls="modal-color" aria-label="Elegir color de texto">
                                    <span class="material-icons">palette</span>
                                </button>
                            </div>

                            <div class="input-group">
                                <label for="color_fondo">Color de fondo</label>
                                <div class="input-icon input-icon-name">
                                    <input type="text" id="color_fondo" name="color_fondo" placeholder="#ffffff o rgb(255,255,255)" aria-describedby="color_fondo_help" required />
                                </div>
                                <button type="button" class="btn btn-info" id="btn-paleta-fondo" aria-controls="modal-color" aria-label="Elegir color de fondo">
                                    <span class="material-icons">palette</span>
                                </button>
                            </div>

                            <div class="input-group">
                                <label for="color_acento">Color de acento</label>
                                <div class="input-icon input-icon-name">
                                    <input type="text" id="color_acento" name="color_acento" placeholder="#7c3aed o rgb(124,58,237)" aria-describedby="color_acento_help" required />
                                </div>
                                <button type="button" class="btn btn-info" id="btn-paleta-acento" aria-controls="modal-color" aria-label="Elegir color de acento">
                                    <span class="material-icons">palette</span>
                                </button>
                            </div>

                        </div>

                        <br>

                        <div class="form-grid grid-3" role="region" aria-label="Vista previa de colores">
                            <div class="preview-area" id="preview_area" aria-live="polite">
                                <p>
                                    Este es un <strong>ejemplo</strong> de texto. El
                                    <span class="hl">resaltado</span> usa el color de acento.
                                </p>
                            </div>
                            <div class="preview-swatch" id="preview_texto" aria-label="Color de texto" tabindex="0">
                                <span>Texto</span>
                            </div>
                            <div class="preview-swatch" id="preview_fondo" aria-label="Color de fondo" tabindex="0">
                                <span>Fondo</span>
                            </div>
                            <div class="preview-swatch" id="preview_acento" aria-label="Color de acento" tabindex="0">
                                <span>Acento</span>
                            </div>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn btn-aceptar">Guardar cambios</button>
                            <button type="button" class="btn btn-cancelar" id="btn-reestablecer">Reestablecer</button>
                        </div>


                    </form>
                </div>

                <!-- modal -->
                <div id="modal-color" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modal-color-title">
                    <div class="modal-content">
                        <h3 id="modal-color-title">Elegí un color</h3>
                        <div class="input-group">
                            <label for="color_picker_mount">Selector</label>
                            <div id="color_picker_mount" class="input-icon input-icon-name" aria-live="polite"></div>
                        </div>
                        <div class="form-buttons">
                            <button type="button" class="btn btn-aceptar" id="btn-color-aceptar" title="Usar este color">Aceptar</button>
                            <button type="button" class="btn btn-cancelar" id="btn-color-cancelar">Cancelar</button>
                        </div>
                    </div>
                </div>

                <!-- Categorias y sub categorias -->
                <div class="card" id="card-crud-taxonomia">
                    <h3>Categorías, Subcategorías y Relaciones</h3>

                    <!-- Controles de alta rápida -->
                    <div class="form-grid grid-3" role="region" aria-label="ABM rápido">
                        <form id="form-add-categoria" class="input-group" autocomplete="off">
                            <label for="categoria_nombre">Nueva categoría</label>
                            <div class="input-icon input-icon-name">
                                <input type="text" id="categoria_nombre" name="nombre" placeholder="Ej: Tragos" required aria-required="true" />
                            </div>
                            <button type="submit" class="btn btn-aceptar" aria-label="Crear categoría">Crear</button>
                        </form>

                        <form id="form-add-subcategoria" class="input-group" autocomplete="off">
                            <label for="subcategoria_nombre">Nueva subcategoría</label>
                            <div class="input-icon input-icon-name">
                                <input type="text" id="subcategoria_nombre" name="nombre" placeholder="Ej: Clásicos" required aria-required="true" />
                            </div>
                            <button type="submit" class="btn btn-aceptar" aria-label="Crear subcategoría">Crear</button>
                        </form>

                        <div class="input-group" aria-live="polite">
                            <label for="filtro_categorias">Filtrar categorías</label>
                            <div class="input-icon input-icon-name">
                                <input type="text" id="filtro_categorias" placeholder="Escribí para filtrar por nombre" />
                            </div>
                            <button type="button" id="btn-refrescar-taxonomia" class="btn btn-info" aria-label="Refrescar datos">Refrescar</button>
                        </div>
                    </div>

                    <!-- Tabla Categorías + Subcategorías (checkbox en desplegable) -->
                    <div class="card tabla-card" style="margin-top:1rem;">
                        <h2>Relaciones Categoría ↔ Subcategorías</h2>
                        <div class="tabla-wrapper">
                            <table class="data-table" id="tabla-categorias">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                        <th>Subcategorías (asignar/desasignar)</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-categorias">
                                    <!-- filas via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ====== Subcategorías: editar nombre / eliminar ====== -->
                <div class="card tabla-card" id="card-subcategorias" style="margin-top:1rem;">
                    <h2>Subcategorías</h2>
                    <div class="form-grid grid-3" role="region" aria-label="ABM Subcategorías">
                        <div class="input-group" style="grid-column:1 / -1;">
                            <label for="filtro_subcategorias">Filtrar subcategorías</label>
                            <div class="input-icon input-icon-name">
                                <input type="text" id="filtro_subcategorias" placeholder="Escribí para filtrar por nombre" />
                            </div>
                        </div>
                    </div>
                    <div class="tabla-wrapper">
                        <table class="data-table" id="tabla-subcategorias">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Subcategoría</th>
                                    <th>Estado</th>
                                    <th style="width:120px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-subcategorias">
                                <!-- filas via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- Imágenes: subir / listar / eliminar -->
                <div class="card" id="card-media">
                    <h3>Imágenes</h3>
                    <p>Subí imágenes a <code>/uploads</code>. Formatos permitidos: JPG, PNG, WEBP, GIF. Máx 5MB c/u.</p>

                    <form id="form-upload-imagenes" autocomplete="off" enctype="multipart/form-data">
                        <div class="form-grid grid-3">
                            <div class="input-group" style="grid-column: 1 / -1;">
                                <label for="imagenes_input">Seleccioná una o más imágenes</label>

                                <!-- Campo de subida real: abre el selector de archivos de la PC -->
                                <input type="file" id="imagenes_input" name="imagenes[]" accept="image/*" multiple required style="display:none;" />

                                <!-- Botón accesible que dispara el selector nativo -->
                                <div class="input-icon input-icon-name">
                                    <button type="button" id="btn-abrir-file" class="btn btn-info" aria-controls="imagenes_input">
                                        <span class="material-icons">upload_file</span>
                                        <span style="margin-left:.35rem;">Elegir archivo(s)</span>
                                    </button>
                                </div>

                                <!-- Resumen de selección -->
                                <div id="file-info" class="file-info" aria-live="polite"></div>
                            </div>
                        </div>
                        <div class="form-buttons">
                            <button type="submit" class="btn btn-aceptar">Subir</button>
                            <button type="button" class="btn btn-cancelar" id="btn-refrescar-media">Refrescar</button>
                        </div>
                    </form>

                    <div class="card tabla-card" style="margin-top:1rem;">
                        <h2>Galería</h2>
                        <div id="galeria_media" class="galeria-grid" aria-live="polite"></div>
                    </div>
                </div>
            </section>

            <style>
                /* Inline mínimo y no intrusivo */
                #card-form-entorno .help {
                    opacity: .8;
                    font-size: .85rem
                }

                .preview-swatch {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 64px;
                    border-radius: 12px;
                    border: 1px solid rgba(0, 0, 0, .08);
                    transition: transform .15s ease, box-shadow .15s ease, background-color .2s ease, color .2s ease;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, .06)
                }

                .preview-swatch:focus {
                    outline: 2px solid #5b21b6;
                    outline-offset: 2px
                }

                .preview-swatch:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 4px 10px rgba(0, 0, 0, .08)
                }

                /* Área de ejemplo completa (texto+fondo+resaltado) */
                .preview-area {
                    grid-column: 1 / -1;
                    padding: 16px;
                    border-radius: 12px;
                    border: 1px solid rgba(0, 0, 0, .08);
                    background: #f8fafc;
                    color: #0f172a
                }

                .preview-area .hl {
                    padding: .15rem .35rem;
                    border-radius: .35rem;
                    background: #7c3aed;
                    color: #ffffff
                }

                /* Evitar FOUC de previews */
                #preview_texto,
                #preview_fondo,
                #preview_acento {
                    background: #f8fafc;
                    color: #0f172a
                }

                /* Pickr en modal: ajustar ancho sin romper layout del framework */
                #color_picker_mount .pcr-app {
                    box-shadow: none;
                    border: 1px solid rgba(0, 0, 0, .08);
                    border-radius: 10px
                }

                #color_picker_mount .pcr-app .pcr-selection .pcr-color-preview {
                    display: none
                }

                #color_picker_mount .pcr-app .pcr-result {
                    width: 100%
                }

                /* === Taxonomía: dropdown de subcategorías === */
                .subs-dropdown {
                    position: relative;
                    display: inline-block;
                }

                .subs-panel {
                    position: absolute;
                    top: 100%;
                    left: 0;
                    z-index: 50;
                    min-width: 260px;
                    max-height: 280px;
                    overflow: auto;
                    background: #fff;
                    border: 1px solid rgba(0, 0, 0, .08);
                    border-radius: 10px;
                    box-shadow: 0 8px 18px rgba(0, 0, 0, .12);
                    padding: .5rem;
                    transform-origin: top left;
                    transform: scale(.98);
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity .15s ease, transform .15s ease;
                }

                .subs-panel.open {
                    opacity: 1;
                    transform: scale(1);
                    pointer-events: auto;
                }

                .subs-item {
                    display: flex;
                    align-items: center;
                    gap: .5rem;
                    padding: .35rem .25rem;
                    border-radius: .5rem;
                }

                .subs-item:hover {
                    background: #f8fafc;
                }

                .chip {
                    display: inline-block;
                    padding: .15rem .5rem;
                    border-radius: 999px;
                    border: 1px solid rgba(0, 0, 0, .08);
                    font-size: .85rem;
                    background: #fff;
                }

                .acciones-grid {
                    display: flex;
                    gap: .5rem;
                    flex-wrap: wrap;
                }

                /* === Media / Galería === */
                .galeria-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                    gap: 12px;
                }

                .galeria-item {
                    position: relative;
                    border: 1px solid rgba(0, 0, 0, .08);
                    border-radius: 12px;
                    overflow: hidden;
                    background: #fff;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
                }

                .galeria-thumb {
                    width: 100%;
                    aspect-ratio: 4/3;
                    object-fit: cover;
                    display: block;
                    background: #f3f4f6;
                }

                .galeria-footer {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: .5rem .6rem;
                    gap: .5rem;
                }

                .file-name {
                    font-size: .85rem;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    max-width: 70%;
                }

                /* File info */
                .file-info {
                    margin-top: .5rem;
                    font-size: .9rem;
                    color: #334155;
                }

                .file-info .file-pill {
                    display: inline-block;
                    margin: .15rem .25rem .15rem 0;
                    padding: .15rem .5rem;
                    border: 1px solid rgba(0, 0, 0, .08);
                    border-radius: 999px;
                    background: #f8fafc;
                }

                /* Icon-only actions */
                .icon-btn {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 36px;
                    height: 36px;
                    border-radius: 8px;
                    border: 1px solid rgba(0, 0, 0, .08);
                    background: #fff;
                    cursor: pointer;
                }

                .icon-btn:hover {
                    box-shadow: 0 4px 10px rgba(0, 0, 0, .08);
                    transform: translateY(-1px);
                }

                .icon-stack {
                    display: flex;
                    gap: .35rem;
                    flex-wrap: wrap;
                }

                /* Categoría: icono + input */
                .cat-name-wrap {
                    display: flex;
                    align-items: center;
                    gap: .5rem;
                }

                .cat-icon {
                    width: 28px;
                    height: 28px;
                    border-radius: 6px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    border: 1px solid rgba(0, 0, 0, .08);
                }

                .cat-icon .material-icons {
                    font-size: 18px;
                }
            </style>

            <script type="module">
                (() => {
                    const $ = (sel) => document.querySelector(sel);
                    const inputs = {
                        texto: $("#color_texto"),
                        fondo: $("#color_fondo"),
                        acento: $("#color_acento"),
                    };
                    const previews = {
                        texto: $("#preview_texto"),
                        fondo: $("#preview_fondo"),
                        acento: $("#preview_acento"),
                        area: $("#preview_area"),
                        areaHl: $("#preview_area .hl"),
                    };

                    const HEX_RE = /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/;
                    const RGB_RE = /^rgb\(\s*(?:[01]?\d?\d|2[0-4]\d|25[0-5])\s*,\s*(?:[01]?\d?\d|2[0-4]\d|25[0-5])\s*,\s*(?:[01]?\d?\d|2[0-4]\d|25[0-5])\s*\)$/;

                    const isValidColor = (v) => HEX_RE.test(v) || RGB_RE.test(v);
                    const normalize = (v) => v.trim();

                    // Utilidades de conversión para el selector (que usa #RRGGBB)
                    const toRgbParts = (v) => v.match(/\d+/g)?.map(n => parseInt(n, 10)) ?? null;
                    const rgbToHex = (r, g, b) => '#' + [r, g, b].map(n => n.toString(16).padStart(2, '0')).join('');
                    const hex3To6 = (hex) => '#' + hex.slice(1).split('').map(ch => ch + ch).join('');
                    const anyToHex = (v) => {
                        if (HEX_RE.test(v)) return v.length === 4 ? hex3To6(v) : v.toLowerCase();
                        if (RGB_RE.test(v)) {
                            const p = toRgbParts(v);
                            if (p && p.length === 3) return rgbToHex(p[0], p[1], p[2]);
                        }
                        return null;
                    };

                    function applyPreview() {
                        const t = normalize(inputs.texto.value);
                        const f = normalize(inputs.fondo.value);
                        const a = normalize(inputs.acento.value);

                        if (isValidColor(t)) {
                            previews.texto.style.color = t;
                            previews.texto.style.background = "#ffffff";
                            previews.area.style.color = t;
                        }
                        if (isValidColor(f)) {
                            previews.fondo.style.background = f;
                            previews.fondo.style.color = "#0f172a";
                            previews.area.style.background = f;
                        }
                        if (isValidColor(a)) {
                            previews.acento.style.background = a;
                            previews.acento.style.color = "#ffffff";
                            previews.areaHl.style.background = a;
                            previews.areaHl.style.color = "#ffffff";
                        }
                    }

                    async function loadColors() {
                        try {
                            const res = await fetch('../../controllers/admin_dashboard_controller.php', {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            const json = await res.json();
                            if (json.ok && json.data) {
                                inputs.texto.value = json.data.color_texto;
                                inputs.fondo.value = json.data.color_fondo;
                                inputs.acento.value = json.data.color_acento;
                                applyPreview();
                            } else {
                                showAlert('error', json.error || 'No se pudieron cargar los colores.');
                            }
                        } catch (e) {
                            showAlert('error', 'Error de red al cargar colores.');
                        }
                    }

                    // Vista previa automática al escribir/pegar
                    ['input', 'change'].forEach(ev => {
                        inputs.texto.addEventListener(ev, applyPreview);
                        inputs.fondo.addEventListener(ev, applyPreview);
                        inputs.acento.addEventListener(ev, applyPreview);
                    });

                    // Botón Reestablecer
                    $("#btn-reestablecer").addEventListener('click', async (e) => {
                        e.preventDefault();
                        await loadColors();
                        showAlert('info', 'Valores restablecidos.');
                    });

                    // Guardado
                    $("#form-entorno").addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const payload = {
                            color_texto: normalize(inputs.texto.value),
                            color_fondo: normalize(inputs.fondo.value),
                            color_acento: normalize(inputs.acento.value),
                        };
                        for (const [k, v] of Object.entries(payload)) {
                            if (!isValidColor(v)) {
                                showAlert('error', `Valor inválido para ${k.replace('color_', '').toUpperCase()}. Usá #hex o rgb(r,g,b).`);
                                return;
                            }
                        }
                        try {
                            const res = await fetch('../../controllers/admin_dashboard_controller.php', {

                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(payload)
                            });
                            const json = await res.json();
                            if (json.ok) {
                                showAlert('success', '¡Colores guardados!');
                                applyPreview();
                            } else {
                                showAlert('error', json.error || 'No se pudo guardar.');
                            }
                        } catch {
                            showAlert('error', 'Error de red al guardar.');
                        }
                    });

                    // === Modal selector de color (Pickr) ===
                    const modal = $("#modal-color");
                    const pickerMount = $("#color_picker_mount");
                    let targetInput = null;

                    // Instancia única de Pickr
                    let pickr = null;

                    function ensurePickr() {
                        if (pickr) return pickr;
                        pickr = Pickr.create({
                            el: pickerMount,
                            theme: 'classic',
                            lockOpacity: true, // Sólo colores opacos
                            comparison: false,
                            default: '#000000',
                            position: 'bottom-middle',
                            components: {
                                // Principal
                                preview: true,
                                opacity: false,
                                hue: true,

                                // Interacciones (botonera)
                                interaction: {
                                    hex: true,
                                    rgba: true,
                                    hsla: true,
                                    input: true,
                                    clear: false,
                                    save: false
                                }
                            },
                            i18n: {
                                'btn:toggle': 'Haga clic para alternar las opciones de color (rgb/hsl/hex)',
                                'aria:btn:toggle': 'Alternar opciones',
                                'aria:input': 'Entrada de color',
                                'aria:palette': 'Paleta',
                                'aria:hue': 'Matiz'
                            }
                        });

                        // Cambios en tiempo real -> reflejo en input + preview
                        pickr.on('change', (color) => {
                            if (!targetInput) return;
                            const hex = color.toHEXA().toString(); // #rrggbb
                            targetInput.value = hex;
                            applyPreview();
                        });

                        return pickr;
                    }

                    function openModalFor(inputEl) {
                        targetInput = inputEl;
                        const p = ensurePickr();
                        const hx = anyToHex(normalize(targetInput.value)) || '#000000';
                        p.setColor(hx);
                        modal.classList.remove('hidden');
                        // Enfoco el input interno de pickr para accesibilidad
                        setTimeout(() => {
                            const input = pickerMount.querySelector('.pcr-result');
                            if (input) input.focus();
                        }, 50);
                    }

                    function closeModal() {
                        modal.classList.add('hidden');
                        targetInput = null;
                    }

                    // Botones "paleta" por campo
                    $("#btn-paleta-texto").addEventListener('click', () => openModalFor(inputs.texto));
                    $("#btn-paleta-fondo").addEventListener('click', () => openModalFor(inputs.fondo));
                    $("#btn-paleta-acento").addEventListener('click', () => openModalFor(inputs.acento));

                    // Cerrar modal
                    $("#btn-color-aceptar").addEventListener('click', closeModal);
                    $("#btn-color-cancelar").addEventListener('click', closeModal);
                    modal.addEventListener('click', (e) => {
                        if (e.target === modal) closeModal();
                    });


                    // Carga inicial
                    loadColors();
                })();
            </script>

            <!-- Logica de las categorias y subcategorias -->
            <script type="module">
                (() => {
                    const $ = (sel, ctx = document) => ctx.querySelector(sel);
                    const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
                    const API = '../../controllers/admin_dashboard_controller.php';

                    // Helpers
                    const j = (u, opt = {}) => fetch(u, opt).then(r => r.json());
                    const enc = (obj) => JSON.stringify(obj);
                    const headersJSON = {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    };

                    // --- Estado en memoria ---
                    let categorias = []; // [{id,nombre,estado,created_at,updated_at}]
                    let subcategorias = []; // [{id,nombre,estado,created_at,updated_at}]

                    // --- UI refs ---
                    const tbody = $('#tbody-categorias');
                    const filtroCategorias = $('#filtro_categorias');
                    const tbodySubs = document.querySelector('#tbody-subcategorias');
                    const filtroSubs = document.querySelector('#filtro_subcategorias');

                    // Mapa simple de iconos por nombre de categoría (case-insensitive)
                    const ICONOS_CATEGORIA = {
                        'tragos': 'local_bar',
                        'cocktails': 'local_bar',
                        'vinos': 'wine_bar',
                        'cervezas': 'sports_bar',
                        'comidas': 'restaurant',
                        'platos': 'restaurant',
                        'postres': 'icecream',
                        'cafetería': 'local_cafe',
                        'cafe': 'local_cafe',
                        'promo': 'local_offer',
                        'sin alcohol': 'emoji_food_beverage'
                    };
                    const iconoCategoria = (nombre) => {
                        if (!nombre) return 'category';
                        const key = nombre.trim().toLowerCase();
                        return ICONOS_CATEGORIA[key] || 'category';
                    };


                    // --- API ---
                    async function listarCategorias() {
                        const res = await j(`${API}?r=categories`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!res.ok) throw new Error(res.error || 'No se pudieron listar categorías');
                        categorias = res.data;
                    }
                    async function listarSubcategorias() {
                        const res = await j(`${API}?r=subcategories`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!res.ok) throw new Error(res.error || 'No se pudieron listar subcategorías');
                        subcategorias = res.data;
                    }
                    async function crearCategoria(nombre) {
                        const res = await j(`${API}?r=categories`, {
                            method: 'POST',
                            headers: headersJSON,
                            body: enc({
                                op: 'create',
                                nombre
                            })
                        });
                        if (!res.ok) throw new Error(res.error || 'No se pudo crear la categoría');
                        return res.data;
                    }
                    async function crearSubcategoria(nombre) {
                        const res = await j(`${API}?r=subcategories`, {
                            method: 'POST',
                            headers: headersJSON,
                            body: enc({
                                op: 'create',
                                nombre
                            })
                        });
                        if (!res.ok) throw new Error(res.error || 'No se pudo crear la subcategoría');
                        return res.data;
                    }
                    async function actualizarCategoria(id, payload) {
                        const res = await j(`${API}?r=categories`, {
                            method: 'POST',
                            headers: headersJSON,
                            body: enc({
                                op: 'update',
                                id,
                                ...payload
                            })
                        });
                        if (!res.ok) throw new Error(res.error || 'No se pudo actualizar la categoría');
                        return res.data;
                    }
                    async function eliminarCategoria(id) {
                        const res = await j(`${API}?r=categories`, {
                            method: 'POST',
                            headers: headersJSON,
                            body: enc({
                                op: 'delete',
                                id
                            })
                        });
                        if (!res.ok) throw new Error(res.error || 'No se pudo eliminar la categoría');
                        return res.data;
                    }
                    async function eliminarSubcategoria(id) {
                        const res = await j(`${API}?r=subcategories`, {
                            method: 'POST',
                            headers: headersJSON,
                            body: enc({
                                op: 'delete',
                                id
                            })
                        });
                        if (!res.ok) throw new Error(res.error || 'No se pudo eliminar la subcategoría');
                        return res.data;
                    }
                    async function actualizarSubcategoria(id, payload) {
                        const res = await j(`${API}?r=subcategories`, {
                            method: 'POST',
                            headers: headersJSON,
                            body: enc({
                                op: 'update',
                                id,
                                ...payload
                            })
                        });
                        if (!res.ok) throw new Error(res.error || 'No se pudo actualizar la subcategoría');
                        return res.data;
                    }
                    async function relacionesDeCategoria(category_id) {
                        const res = await j(`${API}?r=relations&category_id=${encodeURIComponent(category_id)}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!res.ok) throw new Error(res.error || 'No se pudieron cargar relaciones');
                        return res.data; // {assigned:[], available:[]}
                    }
                    async function link(category_id, subcategory_id) {
                        const res = await j(`${API}?r=relations`, {
                            method: 'POST',
                            headers: headersJSON,
                            body: enc({
                                op: 'link',
                                category_id,
                                subcategory_id
                            })
                        });
                        if (!res.ok) throw new Error(res.error || 'No se pudo asociar');
                        return res.data;
                    }
                    async function unlink(category_id, subcategory_id) {
                        const res = await j(`${API}?r=relations`, {
                            method: 'POST',
                            headers: headersJSON,
                            body: enc({
                                op: 'unlink',
                                category_id,
                                subcategory_id
                            })
                        });
                        if (!res.ok) throw new Error(res.error || 'No se pudo desasociar');
                        return res.data;
                    }

                    // --- Render ---
                    function filtrar(list, term) {
                        if (!term) return list;
                        const t = term.trim().toLowerCase();
                        return list.filter(c => c.nombre.toLowerCase().includes(t));
                    }

                    function renderCategorias() {
                        const data = filtrar(categorias, filtroCategorias.value);
                        tbody.innerHTML = '';
                        if (!data.length) {
                            tbody.innerHTML = `<tr><td colspan="5">Sin resultados.</td></tr>`;
                            return;
                        }
                        data.forEach((c, idx) => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                    <td>${idx+1}</td>
                    <td>
    <div class="cat-name-wrap">
        <span class="cat-icon" title="Categoría: ${c.nombre}">
            <span class="material-icons">${iconoCategoria(c.nombre)}</span>
        </span>
        <div class="input-group" style="margin:0">
            <label for="cat_${c.id}" class="sr-only">Editar nombre</label>
            <div class="input-icon input-icon-name">
                <input type="text" id="cat_${c.id}" value="${c.nombre}" aria-label="Nombre de categoría ${c.nombre}" />
            </div>
        </div>
    </div>
</td>
                    <td>
                        <span class="badge ${c.estado ? 'success' : 'warning'}">${c.estado ? 'Activa' : 'Inactiva'}</span>
                    </td>
                    <td>
                        <div class="subs-dropdown">
                            <button class="btn btn-info btn-subs" data-id="${c.id}" aria-haspopup="true" aria-expanded="false">Gestionar subcategorías</button>
                            <div class="subs-panel" id="subs_panel_${c.id}" role="menu" aria-label="Subcategorías de ${c.nombre}">
                                <div class="subs-list" data-category="${c.id}">
                                    <!-- items por AJAX -->
                                    <div class="input-group" style="margin:.25rem 0;">
                                        <label for="buscar_subs_${c.id}">Buscar</label>
                                        <div class="input-icon input-icon-name">
                                            <input type="text" id="buscar_subs_${c.id}" placeholder="Filtrar subcategorías" />
                                        </div>
                                    </div>
                                    <div class="subs-contenedor" id="subs_contenedor_${c.id}"></div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
    <div class="icon-stack">
        <button class="icon-btn btn-guardar-cat" data-id="${c.id}" title="Guardar" aria-label="Guardar">
            <span class="material-icons">save</span>
        </button>
        <button class="icon-btn btn-toggle-cat" data-id="${c.id}" title="${c.estado ? 'Desactivar' : 'Activar'}" aria-label="${c.estado ? 'Desactivar' : 'Activar'}">
            <span class="material-icons">${c.estado ? 'toggle_off' : 'toggle_on'}</span>
        </button>
        <button class="icon-btn btn-eliminar-cat" data-id="${c.id}" title="Eliminar" aria-label="Eliminar">
            <span class="material-icons">delete</span>
        </button>
    </div>
</td>
                `;
                            tbody.appendChild(tr);
                        });
                    }

                    // ===== Subcategorías: render + acciones (editar nombre) =====
                    function filtrarSubs(list, term) {
                        if (!term) return list;
                        const t = term.trim().toLowerCase();
                        return list.filter(s => s.nombre.toLowerCase().includes(t));
                    }

                    function renderSubcategorias() {
                        if (!tbodySubs) return;
                        const data = filtrarSubs(subcategorias, filtroSubs?.value || '');
                        tbodySubs.innerHTML = '';
                        if (!data.length) {
                            tbodySubs.innerHTML = `<tr><td colspan="4">Sin resultados.</td></tr>`;
                            return;
                        }
                        data.forEach((s, idx) => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
            <td>${idx + 1}</td>
            <td>
                <div class="input-group" style="margin:0">
                    <label for="sub_${s.id}" class="sr-only">Editar nombre</label>
                    <div class="input-icon input-icon-name">
                        <input type="text" id="sub_${s.id}" value="${s.nombre}" aria-label="Nombre de subcategoría ${s.nombre}" />
                    </div>
                </div>
            </td>
            <td>
                <span class="badge ${s.estado ? 'success' : 'warning'}">${s.estado ? 'Activa' : 'Inactiva'}</span>
            </td>
            <td>
                <div class="icon-stack">
                    <button class="icon-btn btn-guardar-sub" data-id="${s.id}" title="Guardar" aria-label="Guardar">
                        <span class="material-icons">save</span>
                    </button>
                    <button class="icon-btn btn-toggle-sub" data-id="${s.id}" title="${s.estado ? 'Desactivar' : 'Activar'}" aria-label="${s.estado ? 'Desactivar' : 'Activar'}">
                        <span class="material-icons">${s.estado ? 'toggle_off' : 'toggle_on'}</span>
                    </button>
                    <button class="icon-btn btn-eliminar-sub" data-id="${s.id}" title="Eliminar" aria-label="Eliminar">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
            </td>
        `;
                            tbodySubs.appendChild(tr);
                        });
                    }



                    async function abrirPanelSubs(categoryId, btn, panel) {
                        try {
                            panel.classList.add('open'); // pre-animación
                            const data = await relacionesDeCategoria(categoryId);
                            const cont = $(`#subs_contenedor_${categoryId}`);
                            const inputSearch = $(`#buscar_subs_${categoryId}`);
                            // Armo lista unificada con estado checked
                            // Supuesto: muestro TODAS (asignadas + no asignadas) para permitir (des)asignar.
                            const m = new Map();
                            data.available.forEach(s => m.set(s.id, {
                                ...s,
                                checked: false
                            }));
                            data.assigned.forEach(s => m.set(s.id, {
                                ...s,
                                checked: true
                            }));
                            const list = Array.from(m.values()).sort((a, b) => a.nombre.localeCompare(b.nombre, 'es'));

                            const renderList = (term = '') => {
                                const t = term.trim().toLowerCase();
                                cont.innerHTML = '';
                                list.filter(x => !t || x.nombre.toLowerCase().includes(t))
                                    .forEach(s => {
                                        const row = document.createElement('div');
                                        row.className = 'subs-item';
                                        row.innerHTML = `
                                <input type="checkbox" id="sc_${categoryId}_${s.id}" ${s.checked ? 'checked' : ''} data-cat="${categoryId}" data-sub="${s.id}" aria-label="${s.nombre}">
                                <label for="sc_${categoryId}_${s.id}" style="flex:1;">${s.nombre}</label>
                                <span class="chip">${s.checked ? 'Asignada' : 'Disponible'}</span>
                            `;
                                        cont.appendChild(row);
                                    });
                            };
                            renderList();
                            inputSearch.addEventListener('input', (e) => renderList(e.target.value), {
                                once: false
                            });

                            // Marcar/desmarcar
                            cont.addEventListener('change', async (e) => {
                                const el = e.target;
                                if (el && el.matches('input[type="checkbox"]')) {
                                    const cat = parseInt(el.dataset.cat, 10);
                                    const sub = parseInt(el.dataset.sub, 10);
                                    try {
                                        if (el.checked) {
                                            await link(cat, sub);
                                            showAlert('success', 'Subcategoría asignada.');
                                        } else {
                                            await unlink(cat, sub);
                                            showAlert('info', 'Subcategoría desasignada.');
                                        }
                                    } catch (err) {
                                        // revertir UI si falla
                                        el.checked = !el.checked;
                                        showAlert('error', err.message || 'Error actualizando relación.');
                                    }
                                }
                            }, {
                                once: false
                            });

                            // Toggle ARIA
                            btn.setAttribute('aria-expanded', 'true');
                            panel.classList.add('open');
                        } catch (err) {
                            panel.classList.remove('open');
                            showAlert('error', err.message || 'No se pudo cargar subcategorías.');
                        }
                    }

                    function cerrarTodosLosPanels() {
                        $$('.subs-panel.open').forEach(p => {
                            p.classList.remove('open');
                            const btn = p.previousElementSibling;
                            if (btn) btn.setAttribute('aria-expanded', 'false');
                        });
                    }

                    // --- Eventos globales ---
                    document.addEventListener('click', (e) => {
                        const btn = e.target.closest('.btn-subs');
                        if (btn) {
                            const id = parseInt(btn.dataset.id, 10);
                            const panel = $(`#subs_panel_${id}`);
                            const isOpen = panel.classList.contains('open');
                            cerrarTodosLosPanels();
                            if (!isOpen) abrirPanelSubs(id, btn, panel);
                            return;
                        }
                        // Cerrar si clic fuera
                        if (!e.target.closest('.subs-dropdown')) cerrarTodosLosPanels();
                    });

                    // Guardar/activar/eliminar categoría + subcategoría
                    document.addEventListener('click', async (e) => {
                        // --- Categorías ---
                        const bGuardar = e.target.closest('.btn-guardar-cat');
                        const bToggle = e.target.closest('.btn-toggle-cat');
                        const bEliminar = e.target.closest('.btn-eliminar-cat');

                        if (bGuardar) {
                            const id = parseInt(bGuardar.dataset.id, 10);
                            const input = $(`#cat_${id}`);
                            const nombre = input.value.trim();
                            if (!nombre) return showAlert('error', 'El nombre no puede estar vacío.');
                            try {
                                await actualizarCategoria(id, {
                                    nombre
                                });
                                showAlert('success', 'Categoría actualizada.');
                                await listarCategorias();
                                renderCategorias();
                            } catch (err) {
                                showAlert('error', err.message);
                            }
                        }

                        if (bToggle) {
                            const id = parseInt(bToggle.dataset.id, 10);
                            const cat = categorias.find(x => x.id === id);
                            if (!cat) return;
                            try {
                                await actualizarCategoria(id, {
                                    estado: cat.estado ? 0 : 1
                                });
                                showAlert('info', 'Estado de la categoría actualizado.');
                                await listarCategorias();
                                renderCategorias();
                            } catch (err) {
                                showAlert('error', err.message);
                            }
                        }

                        if (bEliminar) {
                            const id = parseInt(bEliminar.dataset.id, 10);
                            if (!confirm('¿Eliminar categoría y sus relaciones?')) return;
                            try {
                                await eliminarCategoria(id);
                                showAlert('info', 'Categoría eliminada.');
                                await Promise.all([listarCategorias(), listarSubcategorias()]);
                                renderCategorias();
                                renderSubcategorias();
                            } catch (err) {
                                showAlert('error', err.message);
                            }
                        }

                        // --- Subcategorías ---
                        const sGuardar = e.target.closest('.btn-guardar-sub');
                        const sToggle = e.target.closest('.btn-toggle-sub');
                        const sEliminar = e.target.closest('.btn-eliminar-sub');

                        if (sGuardar) {
                            const id = parseInt(sGuardar.dataset.id, 10);
                            const input = document.querySelector(`#sub_${id}`);
                            const nombre = (input?.value || '').trim();
                            if (!nombre) return showAlert('error', 'El nombre no puede estar vacío.');
                            try {
                                await actualizarSubcategoria(id, {
                                    nombre
                                });
                                showAlert('success', 'Subcategoría actualizada.');
                                await listarSubcategorias();
                                renderSubcategorias();
                            } catch (err) {
                                showAlert('error', err.message);
                            }
                        }

                        if (sToggle) {
                            const id = parseInt(sToggle.dataset.id, 10);
                            const sub = subcategorias.find(x => x.id === id);
                            if (!sub) return;
                            try {
                                await actualizarSubcategoria(id, {
                                    estado: sub.estado ? 0 : 1
                                });
                                showAlert('info', 'Estado de la subcategoría actualizado.');
                                await listarSubcategorias();
                                renderSubcategorias();
                            } catch (err) {
                                showAlert('error', err.message);
                            }
                        }

                        if (sEliminar) {
                            const id = parseInt(sEliminar.dataset.id, 10);
                            if (!confirm('¿Eliminar subcategoría y sus relaciones?')) return;
                            try {
                                await eliminarSubcategoria(id);
                                showAlert('info', 'Subcategoría eliminada.');
                                await Promise.all([listarSubcategorias(), listarCategorias()]);
                                renderSubcategorias();
                                renderCategorias();
                            } catch (err) {
                                showAlert('error', err.message);
                            }
                        }
                    });


                    // Altas rápidas
                    $('#form-add-categoria').addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const nombre = $('#categoria_nombre').value.trim();
                        if (!nombre) return showAlert('error', 'Ingresá un nombre.');
                        try {
                            await crearCategoria(nombre);
                            $('#categoria_nombre').value = '';
                            await listarCategorias();
                            renderCategorias();
                            showAlert('success', 'Categoría creada.');
                        } catch (err) {
                            showAlert('error', err.message);
                        }
                    });

                    $('#form-add-subcategoria').addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const nombre = $('#subcategoria_nombre').value.trim();
                        if (!nombre) return showAlert('error', 'Ingresá un nombre.');
                        try {
                            await crearSubcategoria(nombre);
                            $('#subcategoria_nombre').value = '';
                            showAlert('success', 'Subcategoría creada.');
                        } catch (err) {
                            showAlert('error', err.message);
                        }
                    });

                    // Filtro y refresco
                    filtroCategorias.addEventListener('input', renderCategorias);
                    $('#btn-refrescar-taxonomia').addEventListener('click', async () => {
                        try {
                            await Promise.all([listarCategorias(), listarSubcategorias()]);
                            renderCategorias();
                            showAlert('info', 'Datos refrescados.');
                        } catch (err) {
                            showAlert('error', err.message);
                        }
                    });

                    // Filtro subcategorías
                    if (filtroSubs) {
                        filtroSubs.addEventListener('input', renderSubcategorias);
                    }

                    // Inicial
                    (async function init() {
                        try {
                            await Promise.all([listarCategorias(), listarSubcategorias()]);
                            renderCategorias();
                            renderSubcategorias();
                        } catch (err) {
                            showAlert('error', err.message);
                        }
                    })();
                })();
            </script>


        </div>
    </div>

    <script src="/views/partial/spinner-global.js" defer></script>

    <script type="module">
        (() => {
            const $ = (s, ctx = document) => ctx.querySelector(s);
            const $$ = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));
            const API = '../../controllers/admin_dashboard_controller.php?r=images';

            const galeria = $('#galeria_media');
            const form = $('#form-upload-imagenes');
            const input = $('#imagenes_input');
            const btnRefrescar = $('#btn-refrescar-media');
            const btnAbrirFile = $('#btn-abrir-file');
            const fileInfo = $('#file-info');

            btnAbrirFile.addEventListener('click', () => input.click());

            const fmtBytes = (n) => {
                if (n < 1024) return `${n} B`;
                if (n < 1024 * 1024) return `${(n/1024).toFixed(1)} KB`;
                return `${(n/1024/1024).toFixed(1)} MB`;
            };

            input.addEventListener('change', () => {
                if (!input.files.length) {
                    fileInfo.textContent = '';
                    return;
                }
                const pills = Array.from(input.files).map(f =>
                    `<span class="file-pill" title="${f.name}">${f.name} • ${fmtBytes(f.size)}</span>`
                ).join('');
                fileInfo.innerHTML = `${input.files.length} archivo(s) seleccionado(s): ${pills}`;
            });

            async function listar() {
                try {
                    const res = await fetch(API, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    if (!json.ok) throw new Error(json.error || 'No se pudieron listar imágenes.');
                    render(json.data);
                } catch (e) {
                    showAlert('error', e.message);
                }
            }

            function render(items) {
                galeria.innerHTML = '';
                if (!items || !items.length) {
                    galeria.innerHTML = `<div class="chip">No hay imágenes aún.</div>`;
                    return;
                }
                items.forEach(it => {
                    const card = document.createElement('div');
                    card.className = 'galeria-item';
                    card.innerHTML = `
                                <a href="${it.url}" target="_blank" rel="noopener">
                                    <img src="${it.url}" alt="Imagen ${it.id}" class="galeria-thumb" loading="lazy">
                                </a>
                                <div class="galeria-footer">
                                    <span class="file-name" title="${it.filename}">${it.filename}</span>
                                    <div class="acciones-grid">
                                        <button class="btn btn-cancelar btn-eliminar-img" data-id="${it.id}">Eliminar</button>
                                    </div>
                                </div>
                            `;
                    galeria.appendChild(card);
                });
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                if (!input.files.length) {
                    showAlert('error', 'Seleccioná al menos una imagen.');
                    return;
                }
                const fd = new FormData();
                fd.append('op', 'upload');
                for (const f of input.files) {
                    fd.append('imagenes[]', f);
                }
                try {
                    const res = await fetch(API, {
                        method: 'POST',
                        body: fd
                    });
                    const json = await res.json();
                    if (!json.ok) throw new Error(json.error || 'No se pudo subir la/s imagen/es.');
                    showAlert('success', `Subida correcta: ${json.data.uploads.length} archivo(s).`);
                    input.value = '';
                    await listar();
                } catch (err) {
                    showAlert('error', err.message);
                }
            });

            btnRefrescar.addEventListener('click', listar);

            document.addEventListener('click', async (e) => {
                const btnDel = e.target.closest('.btn-eliminar-img');
                if (btnDel) {
                    const id = parseInt(btnDel.dataset.id, 10);
                    if (!id) return;
                    if (!confirm('¿Eliminar esta imagen? Esta acción no se puede deshacer.')) return;
                    try {
                        const res = await fetch(API, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                'Accept': 'application/json'
                            },
                            body: new URLSearchParams({
                                op: 'delete',
                                id: String(id)
                            })
                        });
                        const json = await res.json();
                        if (!json.ok) throw new Error(json.error || 'No se pudo eliminar.');
                        showAlert('info', 'Imagen eliminada.');
                        await listar();
                    } catch (err) {
                        showAlert('error', err.message);
                    }
                }
            });

            // Inicio
            listar();
        })();
    </script>

</body>

</html>