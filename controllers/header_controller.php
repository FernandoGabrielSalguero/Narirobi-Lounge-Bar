<?php

declare(strict_types=1);
// Visibilidad de errores (desactivar en prod)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/header_model.php';

$action = $_GET['action'] ?? 'script';

try {
    switch ($action) {
        case 'script':
            header('Content-Type: application/javascript; charset=utf-8');
            echo <<<'JS'
(function() {
  // --- Elements ---
  const menuToggle = document.getElementById('menuToggle');
  const menuPanel = document.getElementById('menuPanel');
  const openBtn = document.getElementById('openLogin');
  const openReservaBtn = document.getElementById('openReserva');
  const host = document.getElementById('modalHost');

  // Modales cacheados
  let modalEl = null;         // login
  let modalReservaEl = null;  // reservas

  // --- Utils ---
  function safeConsole(level){
    try {
      if (typeof console !== 'undefined' && typeof console[level] === 'function') {
        return console[level].bind(console);
      }
    } catch(_e){}
    return (typeof console !== 'undefined' && console.log) ? console.log.bind(console) : function(){};
  }

  function log(level, msg, extra){
    const fn = safeConsole(level);
    const prefix = `[AuthUI][${level}]`;
    const text = `${prefix} ${String(msg)}`;
    if (typeof extra !== 'undefined') {
      fn(text, extra);
    } else {
      fn(text);
    }
  }

  function toggleMenu(show){
    const shouldOpen = (typeof show === 'boolean') ? show : menuPanel.classList.contains('hidden');
    if (shouldOpen){
      menuPanel.classList.remove('hidden');
      menuToggle.setAttribute('aria-expanded','true');
    } else {
      menuPanel.classList.add('hidden');
      menuToggle.setAttribute('aria-expanded','false');
    }
  }

  document.addEventListener('click', (e)=>{
    if (!menuPanel.contains(e.target) && e.target !== menuToggle){
      toggleMenu(false);
    }
  });

  menuToggle?.addEventListener('click', (e)=>{
    e.stopPropagation();
    toggleMenu();
  });

  // --- LOGIN ---
  async function ensureModalLoadedAndOpen(){
    try{
      if(!modalEl){
        log('info','Cargando modal de login…');
        const resp = await fetch('/core/auth/AuthController.php?action=view_login', {
          credentials:'same-origin',
          cache:'no-store'
        });
        const html = await resp.text();
        host.insertAdjacentHTML('beforeend', html);
        modalEl = document.getElementById('modal');
        if(!modalEl){
          log('error','No se pudo montar #modal (login).');
          return;
        }
        wireUpLoginModal();
        log('info','Modal de login montado.');
      }
      openLoginModal();
    }catch(e){
      log('error','Fallo al cargar la vista del login.', e);
      window.showAlert?.('error','No se pudo cargar el formulario de login.');
    }
  }

  function wireUpLoginModal(){
    const form = document.getElementById('loginForm');
    const cancelar = document.getElementById('btnCancelar');
    const usuario = document.getElementById('usuario');
    const pass = document.getElementById('contrasena');

    window.openLoginModal = function(){
      modalEl.classList.remove('hidden');
      openBtn?.setAttribute('aria-expanded','true');
      setTimeout(()=> usuario?.focus(), 0);
    };

    window.closeLoginModal = function(){
      modalEl.classList.add('hidden');
      openBtn?.setAttribute('aria-expanded','false');
      openBtn?.focus();
    };

    cancelar?.addEventListener('click', (ev)=>{
      ev.preventDefault();
      ev.stopPropagation();
      window.closeLoginModal();
    });

    modalEl.addEventListener('click', (e)=>{
      if(e.target === modalEl) window.closeLoginModal();
    });

    document.addEventListener('keydown', (e)=>{
      if(e.key === 'Escape' && !modalEl.classList.contains('hidden')) window.closeLoginModal();
    });

    form?.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const payload = {
        usuario: (usuario?.value||'').trim(),
        contrasena: pass?.value||''
      };
      if(!payload.usuario || !payload.contrasena){
        window.showAlert?.('info','Completá usuario y contraseña.');
        return;
      }
      try{
        const resp = await fetch('/core/auth/AuthController.php?action=login', {
          method:'POST',
          headers:{ 'Content-Type':'application/json' },
          body: JSON.stringify(payload),
          credentials:'same-origin',
          cache:'no-store'
        });
        let json=null;
        try{ json=await resp.json(); }catch(_e){}
        if(resp.ok && json?.ok){
          window.showAlert?.('success','¡Operación completada con éxito!');
          if(json.data?.redirect) location.href=json.data.redirect;
        } else{
          const msg = json?.error || `Error de autenticación (HTTP ${resp.status}).`;
          window.showAlert?.('error', msg);
        }
      }catch(err){
        window.showAlert?.('error','Error de red o servidor no disponible.');
      }
    });
  }

  function openLoginModal(){
    window.openLoginModal?.();
  }

  // --- RESERVAS ---
  async function ensureReservaLoadedAndOpen(){
    try{
      if(!modalReservaEl){
        log('info','Cargando modal de reservas…');
        const resp = await fetch('/controllers/header_controller.php?action=view_reserva', {
          credentials:'same-origin',
          cache:'no-store'
        });
        const html = await resp.text();
        host.insertAdjacentHTML('beforeend', html);
        modalReservaEl = document.getElementById('modalReserva');
        if(!modalReservaEl){
          log('error','No se pudo montar #modalReserva.');
          return;
        }
        wireUpReservaModal();
        log('info','Modal de reservas montado.');
      }
      openReservaModal();
    }catch(e){
      log('error','Fallo al cargar la vista de reservas.', e);
      window.showAlert?.('error','No se pudo cargar el formulario de reservas.');
    }
  }

  function wireUpReservaModal(){
    const form = document.getElementById('reservaForm');
    const cancelar = document.getElementById('btnCancelarReserva');
    const nombre = document.getElementById('res_nombre');
    const telefono = document.getElementById('res_telefono');
    const fecha = document.getElementById('res_fecha');
    const hora = document.getElementById('res_hora');
    const personas = document.getElementById('res_personas');
    const notas = document.getElementById('res_notas');

    window.openReservaModal = function(){
      modalReservaEl.classList.remove('hidden');
      openReservaBtn?.setAttribute('aria-expanded','true');
      setTimeout(()=> nombre?.focus(), 0);
    };

    window.closeReservaModal = function(){
      modalReservaEl.classList.add('hidden');
      openReservaBtn?.setAttribute('aria-expanded','false');
      openReservaBtn?.focus();
    };

    cancelar?.addEventListener('click', (ev)=>{
      ev.preventDefault();
      ev.stopPropagation();
      window.closeReservaModal();
    });

    modalReservaEl.addEventListener('click', (e)=>{
      if(e.target === modalReservaEl) window.closeReservaModal();
    });

    document.addEventListener('keydown', (e)=>{
      if(e.key === 'Escape' && !modalReservaEl.classList.contains('hidden')) window.closeReservaModal();
    });

    form?.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const payload = {
        nombre: (nombre?.value||'').trim(),
        telefono: (telefono?.value||'').trim(),
        fecha: (fecha?.value||'').trim(),
        hora: (hora?.value||'').trim(),
        personas: parseInt(personas?.value||'0', 10) || 0,
        notas: (notas?.value||'').trim()
      };

      if(!payload.nombre || !payload.telefono || !payload.fecha || !payload.hora || payload.personas<=0){
        window.showAlert?.('info','Completá nombre, teléfono, fecha, hora y cantidad de personas.');
        return;
      }

      try{
        const resp = await fetch('/controllers/header_controller.php?action=crear_reserva', {
          method:'POST',
          headers:{ 'Content-Type':'application/json' },
          body: JSON.stringify(payload),
          credentials:'same-origin',
          cache:'no-store'
        });
        let json=null;
        try{ json=await resp.json(); }catch(_e){}
        if(resp.ok && json?.ok){
          window.showAlert?.('success','Reserva creada. ¡Gracias!');
          window.closeReservaModal();
          form.reset();
        }else{
          const msg = json?.error || `No se pudo crear la reserva (HTTP ${resp.status}).`;
          window.showAlert?.('error', msg);
        }
      }catch(err){
        window.showAlert?.('error','Error de red o servidor no disponible.');
      }
    });
  }

  function openReservaModal(){
    window.openReservaModal?.();
  }

  // --- Wireup ---
  openBtn?.addEventListener('click', (e)=>{
    e.stopPropagation();
    toggleMenu(false);
    ensureModalLoadedAndOpen();
  }, { once:true });
  openBtn?.addEventListener('click', ()=> modalEl && openLoginModal());

  openReservaBtn?.addEventListener('click', (e)=>{
    e.stopPropagation();
    toggleMenu(false);
    ensureReservaLoadedAndOpen();
  }, { once:true });
  openReservaBtn?.addEventListener('click', ()=> modalReservaEl && openReservaModal());
})();
JS;
            exit;

        case 'view_reserva':
            header('Content-Type: text/html; charset=utf-8');
            echo <<<'HTML'
<div id="modalReserva" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="reservaTitulo">
  <div class="modal-content">
    <div class="reserva-header">
      <h3 id="reservaTitulo">Reservas</h3>
      <p class="reserva-subtitle">Completá tus datos y confirmá tu lugar.</p>
    </div>

    <form id="reservaForm" class="res-grid" autocomplete="off" novalidate>
      <!-- Fila 1: Nombre / Teléfono -->
      <div class="input-group">
        <label for="res_nombre" class="color_label">Nombre</label>
        <div class="input-icon input-icon-name">
          <input type="text" id="res_nombre" name="nombre" placeholder="Tu nombre" required />
        </div>
      </div>

      <div class="input-group">
        <label for="res_telefono" class="color_label">Teléfono</label>
        <div class="input-icon input-icon-phone">
          <input type="tel" id="res_telefono" name="telefono" placeholder="2616686062" required />
        </div>
      </div>

      <!-- Fila 2: Fecha / Hora -->
      <div class="input-group">
        <label for="res_fecha" class="color_label">Fecha</label>
        <div class="input-icon input-icon-calendar">
          <input type="date" id="res_fecha" name="fecha" required />
        </div>
      </div>

      <div class="input-group">
        <label for="res_hora" class="color_label">Hora</label>
        <div class="input-icon input-icon-time">
          <input type="time" id="res_hora" name="hora" required />
        </div>
      </div>

      <!-- Fila 3: Personas / Notas -->
      <div class="input-group">
        <label for="res_personas" class="color_label">Personas</label>
        <div class="input-icon input-icon-users">
          <input type="number" id="res_personas" name="personas" min="1" step="1" value="2" required />
        </div>
      </div>

      <div class="input-group">
        <label for="res_notas" class="color_label">Notas</label>
        <div class="input-icon input-icon-edit ">
          <textarea id="res_notas" name="notas" rows="3" placeholder="Preferencias, alergias, ocasión, etc."></textarea>
        </div>
      </div>

      <!-- Botones -->
      <div class="form-buttons res-buttons">
        <button type="submit" class="btn btn-aceptar border_botons ">Confirmar</button>
        <button id="btnCancelarReserva" class="btn btn-cancelar border_botons ">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<style>
  /* Contenedor del modal */
  #modalReserva .modal-content{ max-width: 720px; width: 92vw; }
  .reserva-header h3{ margin:0 0 .25rem 0; color:#000000ff; }
  .reserva-header .reserva-subtitle{ margin:0 0 1rem 0; opacity:.8; font-size:.95rem; color:#000000ff; }
  /* Grid responsive: mobile 1 columna, desktop 2 columnas */
  .res-grid{ display:grid; grid-template-columns:1fr; gap:1rem 1rem; }
  /* Botonera separada del grid para mantener alineación */
  .res-buttons{ margin-top:.5rem; display:flex; gap:.75rem; }
  @media (min-width: 768px){
    .res-grid{ grid-template-columns:1fr 1fr; align-items:start; }
    .res-buttons{ grid-column:1 / -1; }
  }

  .color_label {
    color: #000000ff;
  }

  .border_botons{
    border: 1px solid black;
  }
</style>
HTML;
            exit;

        case 'crear_reserva':
            header('Content-Type: application/json; charset=utf-8');

            // leer JSON
            $raw = file_get_contents('php://input') ?: '';
            $data = json_decode($raw, true) ?: [];

            // sanitización básica
            $nombre   = trim((string)($data['nombre'] ?? ''));
            $telefono = trim((string)($data['telefono'] ?? ''));
            $fecha    = trim((string)($data['fecha'] ?? ''));
            $hora     = trim((string)($data['hora'] ?? ''));
            $personas = (int)($data['personas'] ?? 0);
            $notas    = trim((string)($data['notas'] ?? ''));

            if ($nombre === '' || $telefono === '' || $fecha === '' || $hora === '' || $personas < 1) {
                http_response_code(422);
                echo json_encode(['ok' => false, 'error' => 'Datos incompletos.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // conexión PDO desde config.php
            $pdo = $pdo ?? null;
            if (!$pdo instanceof \PDO) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'error' => 'Conexión de base de datos no disponible.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $model = new HeaderModel($pdo);
            $id = $model->crearReserva($nombre, $telefono, $fecha, $hora, $personas, $notas);

            echo json_encode(['ok' => true, 'data' => ['id' => $id]], JSON_UNESCAPED_UNICODE);
            exit;

        default:
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'Acción no soportada'], JSON_UNESCAPED_UNICODE);
            exit;
    }
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'Error interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
