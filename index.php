<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nairobi Lounge | Inicio</title>
  <link rel="preload" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css" as="style" />
  <link rel="stylesheet" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css" />
  <script defer src="https://www.fernandosalguero.com/cdn/assets/javascript/framework.js"></script>
  <style>
    /* Fondo negro y centro vertical/horizontal sin FOUC */
    html, body { height: 100%; }
    body {
      margin: 0;
      background: #000;
      color: #fff;
      display: grid;
      place-items: center;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, "Noto Sans", "Helvetica Neue", sans-serif;
    }
    .hero {
      text-align: center;
      padding: 2rem;
      animation: fadeIn 300ms ease-out;
    }
    .btn-primary {
      background: #fff;
      color: #000;
      border: 0;
      padding: 0.875rem 1.25rem;
      border-radius: 999px;
      cursor: pointer;
      font-weight: 600;
      transition: transform .15s ease, opacity .15s ease;
    }
    .btn-primary:focus { outline: 2px solid #9ecbff; outline-offset: 2px; }
    .btn-primary:hover { transform: translateY(-1px); opacity: .95; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px);} to { opacity: 1; transform: translateY(0);} }

    /* Modal: respeta estructura estándar + pequeños ajustes de centrado */
    .modal.hidden { display: none; }
    .modal {
      position: fixed; inset: 0; display: grid; place-items: center;
      background: rgba(0,0,0,.5);
      z-index: 1000;
    }
    .modal-content {
      width: min(92vw, 400px);
      background: #111;
      color: #fff;
      border-radius: 12px;
      padding: 1.25rem;
      box-shadow: 0 10px 40px rgba(0,0,0,.6);
      animation: fadeIn 180ms ease-out;
    }
    .modal-content h3 { margin-top: 0; }
    .form-buttons { display: flex; gap: .5rem; justify-content: flex-end; }
    /* Inputs con tu patrón */
    .input-icon { background: #fff; border-radius: 8px; }
    .input-icon input {
      background: transparent;
      color: #111;
    }
    label { color: #ddd; }
  </style>
</head>
<body>
  <main class="hero" role="main">
    <h1 style="margin-bottom:1rem;">Nairobi Lounge</h1>
    <button id="openLogin" class="btn btn-aceptar btn-primary" aria-haspopup="dialog" aria-controls="modal" aria-expanded="false">
      Iniciar sesión
    </button>
  </main>

  <!-- Modal: Estructura estándar -->
  <div id="modal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalDesc">
    <div class="modal-content">
      <h3 id="modalTitle">Iniciar sesión</h3>
      <p id="modalDesc" class="sr-only">Ingresá tu usuario y contraseña para continuar.</p>

      <form id="loginForm" novalidate>
        <div class="input-group">
          <label for="usuario">Usuario</label>
          <div class="input-icon input-icon-name">
            <input type="text" id="usuario" name="usuario" placeholder="tu_usuario" autocomplete="username" required />
          </div>
        </div>
        <div class="input-group">
          <label for="contrasena">Contraseña</label>
          <div class="input-icon input-icon-password">
            <input type="password" id="contrasena" name="contrasena" placeholder="********" autocomplete="current-password" required />
          </div>
        </div>

        <div class="form-buttons" style="margin-top:1rem;">
          <button type="button" class="btn btn-cancelar" id="btnCancelar">Cancelar</button>
          <button type="submit" class="btn btn-aceptar">Ingresar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    (function(){
      const openBtn = document.getElementById('openLogin');
      const modal   = document.getElementById('modal');
      const form    = document.getElementById('loginForm');
      const cancelar= document.getElementById('btnCancelar');
      const usuario = document.getElementById('usuario');
      const pass    = document.getElementById('contrasena');

      function openModal() {
        modal.classList.remove('hidden');
        openBtn.setAttribute('aria-expanded', 'true');
        // focus seguro
        setTimeout(()=> usuario.focus(), 0);
      }
      function closeModal() {
        modal.classList.add('hidden');
        openBtn.setAttribute('aria-expanded', 'false');
        openBtn.focus();
      }
      openBtn.addEventListener('click', openModal);
      cancelar.addEventListener('click', closeModal);
      modal.addEventListener('click', (e)=> {
        if (e.target === modal) closeModal();
      });
      document.addEventListener('keydown', (e)=> {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
      });

      form.addEventListener('submit', async (e)=>{
        e.preventDefault();
        const data = {
          usuario: usuario.value.trim(),
          contrasena: pass.value
        };
        if (!data.usuario || !data.contrasena) {
          if (typeof showAlert === 'function') showAlert('info','Completá usuario y contraseña.');
          return;
        }
        try {
          const resp = await fetch('Core/auth/AuthController.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
            credentials: 'same-origin',
          });
          const json = await resp.json();
          if (json.ok) {
            if (typeof showAlert === 'function') showAlert('success','¡Operación completada con éxito!');
            // Redirección por rol definida por el backend
            window.location.href = json.data.redirect;
          } else {
            if (typeof showAlert === 'function') showAlert('error', json.error || 'Ha ocurrido un error inesperado.');
          }
        } catch (err) {
          if (typeof showAlert === 'function') showAlert('error','Error de red o servidor no disponible.');
        }
      });
      // Exponer cierre para compatibilidad con tu CDN si lo llama
      window.closeModal = closeModal;
    })();
  </script>
</body>
</html>
