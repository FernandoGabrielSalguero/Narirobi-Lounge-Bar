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


        </div>
    </div>

    <script src="/views/partial/spinner-global.js" defer></script>

</body>

</html>