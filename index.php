<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nairobi Lounge | Inicio</title>
  <link rel="preload" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css" as="style" />
  <link rel="stylesheet" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css" />
  <style>
    :root { color-scheme: dark; }
    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body { margin: 0; background:#000; color:#fff; font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, "Noto Sans", "Helvetica Neue", sans-serif; }
    header {
      position: sticky; top: 0; background:#000; border-bottom:1px solid rgba(255,255,255,.08);
      display:flex; align-items:center; justify-content:space-between; padding:.75rem 1rem;
    }
    .brand { font-weight:700; letter-spacing:.3px; }
    .btn {
      appearance: none; border:0; background:#fff; color:#000; padding:.6rem .95rem; border-radius:999px; font-weight:600; cursor:pointer;
      transition: transform .15s ease, opacity .15s ease;
    }
    .btn:focus { outline:2px solid #9ecbff; outline-offset:2px; }
    .btn:hover { transform: translateY(-1px); opacity:.95; }
    main { padding: 2rem 1rem; }
    /* contenedor del modal inyectado */
    #modalHost { position: relative; z-index: 5; }
  </style>
</head>
<body>
  <header>
    <div class="brand">Nairobi Lounge</div>
    <button id="openLogin" class="btn" aria-haspopup="dialog" aria-controls="modal" aria-expanded="false">Ingresar</button>
  </header>

  <main>
    <!-- Página vacía; sólo header. El modal se carga acá -->
    <div id="modalHost"></div>
  </main>

  <script>
    (function () {
      const openBtn = document.getElementById('openLogin');
      const host = document.getElementById('modalHost');
      let modalEl = null;

      async function ensureModalLoaded() {
        if (modalEl) return;
        const resp = await fetch('core/auth/AuthController.php?action=view_login', { credentials: 'same-origin' });
        const html = await resp.text();
        host.innerHTML = html;
        modalEl = document.getElementById('modal');
        wireUpModal();
      }

      function wireUpModal() {
        const form = document.getElementById('loginForm');
        const cancelar = document.getElementById('btnCancelar');
        const usuario = document.getElementById('usuario');
        const pass = document.getElementById('contrasena');

        function openModal() {
          modalEl.classList.remove('hidden');
          openBtn.setAttribute('aria-expanded', 'true');
          setTimeout(() => usuario.focus(), 0);
        }
        function closeModal() {
          modalEl.classList.add('hidden');
          openBtn.setAttribute('aria-expanded', 'false');
          openBtn.focus();
        }

        openBtn.addEventListener('click', openModal);
        cancelar.addEventListener('click', closeModal);
        modalEl.addEventListener('click', (e) => { if (e.target === modalEl) closeModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modalEl.classList.contains('hidden')) closeModal(); });

        form.addEventListener('submit', async (e) => {
          e.preventDefault();
          const data = { usuario: usuario.value.trim(), contrasena: pass.value };
          if (!data.usuario || !data.contrasena) {
            if (typeof showAlert === 'function') showAlert('info', 'Completá usuario y contraseña.');
            return;
          }
          try {
            const resp = await fetch('core/auth/AuthController.php?action=login', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(data),
              credentials: 'same-origin'
            });
            const json = await resp.json();
            if (json.ok) {
              if (typeof showAlert === 'function') showAlert('success', '¡Operación completada con éxito!');
              window.location.href = json.data.redirect;
            } else {
              if (typeof showAlert === 'function') showAlert('error', json.error || 'Ha ocurrido un error inesperado.');
            }
          } catch (err) {
            if (typeof showAlert === 'function') showAlert('error', 'Error de red o servidor no disponible.');
          }
        });

        // expone para compatibilidad
        window.closeModal = closeModal;
      }

      openBtn.addEventListener('click', ensureModalLoaded, { once: true });
    })();
  </script>
</body>
</html>
