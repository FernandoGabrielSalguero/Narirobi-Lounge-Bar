<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nairobi Lounge | Carta</title>
  <link rel="preload" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css" as="style" />
  <link rel="stylesheet" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css" />
  <style>
    :root {
      color-scheme: dark;
    }

    * {
      box-sizing: border-box;
    }

    html,
    body {
      height: 100%;
    }

    body {
      margin: 0;
      background: #000;
      color: #fff;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, "Noto Sans", "Helvetica Neue", sans-serif;
    }

    header {
      position: sticky;
      top: 0;
      background: #000;
      border-bottom: 1px solid rgba(255, 255, 255, .08);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: .75rem 1rem;
    }

    .brand {
      font-weight: 700;
      letter-spacing: .3px;
    }

    .btn {
      appearance: none;
      border: 0;
      background: #fff;
      color: #000;
      padding: .6rem .95rem;
      border-radius: 999px;
      font-weight: 600;
      cursor: pointer;
      transition: transform .15s ease, opacity .15s ease;
    }

    .btn:focus {
      outline: 2px solid #9ecbff;
      outline-offset: 2px;
    }

    .btn:hover {
      transform: translateY(-1px);
      opacity: .95;
    }

    main {
      padding: 2rem 1rem;
    }

    /* contenedor del modal inyectado */
    #modalHost {
      position: relative;
      z-index: 5;
    }
  </style>
</head>

<body>
  <header>
    <div class="brand">Nairobi Lounge Bar</div>
    <button id="openLogin" class="btn" aria-haspopup="dialog" aria-controls="modal" aria-expanded="false" data-testid="btn-open-login">Ingresar</button>
  </header>

  <main>
    <!-- Página vacía; sólo header. El modal se carga acá -->
    <div id="modalHost"></div>
  </main>

  <script>
    (function() {
      const openBtn = document.getElementById('openLogin');
      const host = document.getElementById('modalHost');
      let modalEl = null;

      function log(level, msg, extra) {
        const prefix = `[AuthUI][${level}]`;
        if (extra) console[level](`${prefix} ${msg}`, extra);
        else console[level](`${prefix} ${msg}`);
      }

      async function ensureModalLoadedAndOpen() {
        try {
          if (!modalEl) {
            log('info', 'Cargando modal de login…');
            const resp = await fetch('/core/auth/AuthController.php?action=view_login', {
              credentials: 'same-origin',
              cache: 'no-store'
            });
            const html = await resp.text();
            host.innerHTML = html;
            modalEl = document.getElementById('modal');
            if (!modalEl) {
              log('error', 'No se pudo montar el modal: #modal no encontrado en HTML recibido.');
              return;
            }
            wireUpModal();
            log('info', 'Modal de login montado.');
          }
          openModal();
        } catch (e) {
          log('error', 'Fallo al cargar la vista del login.', e);
        }
      }

      function wireUpModal() {
        const form = document.getElementById('loginForm');
        const cancelar = document.getElementById('btnCancelar');
        const usuario = document.getElementById('usuario');
        const pass = document.getElementById('contrasena');

        window.openModal = function() {
          modalEl.classList.remove('hidden');
          openBtn.setAttribute('aria-expanded', 'true');
          setTimeout(() => usuario?.focus(), 0);
        };
        window.closeModal = function() {
          modalEl.classList.add('hidden');
          openBtn.setAttribute('aria-expanded', 'false');
          openBtn.focus();
        };

        // Defensa: si por algún motivo no existe el botón, lo logueamos
        if (cancelar) {
          cancelar.addEventListener('click', (ev) => {
            ev.preventDefault();
            ev.stopPropagation();
            log('info', 'Cancelar presionado, cerrando modal.');
            window.closeModal();
          });
        } else {
          log('error', 'Botón Cancelar (#btnCancelar) no encontrado.');
        }

        cancelar.addEventListener('click', (ev) => {
          ev.preventDefault();
          ev.stopPropagation();
          window.closeModal();
        });

        // Click fuera del contenido => cerrar
        modalEl.addEventListener('click', (e) => {
          if (e.target === modalEl) {
            log('info', 'Overlay clickeado, cerrando modal.');
            window.closeModal();
          }
        });

        // Escape => cerrar
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape' && !modalEl.classList.contains('hidden')) {
            log('info', 'Escape presionado, cerrando modal.');
            window.closeModal();
          }
        });

        form.addEventListener('submit', async (e) => {
          e.preventDefault();
          const payload = {
            usuario: usuario.value.trim(),
            contrasena: pass.value
          };

          if (!payload.usuario || !payload.contrasena) {
            log('warn', 'Campos incompletos.', payload);
            window.showAlert?.('info', 'Completá usuario y contraseña.');
            return;
          }

          try {
            log('info', 'Enviando login…', {
              endpoint: '/core/auth/AuthController.php?action=login'
            });
            const resp = await fetch('/core/auth/AuthController.php?action=login', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(payload),
              credentials: 'same-origin',
              cache: 'no-store'
            });

            let json = null;
            try {
              json = await resp.json();
            } catch (parseErr) {
              log('error', 'No se pudo parsear JSON de la respuesta.', parseErr);
            }

            log('info', 'Respuesta login recibida.', {
              status: resp.status,
              body: json
            });

            if (resp.ok && json?.ok) {
              window.showAlert?.('success', '¡Operación completada con éxito!');
              window.location.href = json.data.redirect;
            } else {
              const msg = json?.error || `Error de autenticación (HTTP ${resp.status}).`;
              window.showAlert?.('error', msg);
            }
          } catch (err) {
            log('error', 'Error de red al enviar login.', err);
            window.showAlert?.('error', 'Error de red o servidor no disponible.');
          }
        });
      }

      function openModal() {
        window.openModal?.();
      }

      // Primer click: carga y abre
      openBtn.addEventListener('click', ensureModalLoadedAndOpen, {
        once: true
      });
      // Clicks siguientes: abrir directamente
      openBtn.addEventListener('click', () => modalEl && window.openModal());
    })();
  </script>


</body>

</html>