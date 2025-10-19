<?php

declare(strict_types=1);

// Visibilidad de errores para diagnosticar el 500 (se puede desactivar luego)
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
            // NOTA: usar HEREDOC <<< 'JS' (NOWDOC) evita que PHP intente interpretar ${...} del template string

            echo <<<'JS'
(function() {
  // --- Elements ---
  const menuToggle = document.getElementById('menuToggle');
  const menuPanel  = document.getElementById('menuPanel');
  const openBtn    = document.getElementById('openLogin');
  const openReservaBtn = document.getElementById('openReserva');
  const host = document.getElementById('modalHost');

  // Modales cacheados
  let modalEl = null;        // login
  let modalReservaEl = null; // reservas

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
    if (typeof extra !== 'undefined') { fn(text, extra); } else { fn(text); }
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
  menuToggle?.addEventListener('click', (e)=>{ e.stopPropagation(); toggleMenu(); });

  // --- LOGIN ---
  async function ensureModalLoadedAndOpen(){
    try{
      if(!modalEl){
        log('info','Cargando modal de login…');
        const resp = await fetch('/core/auth/AuthController.php?action=view_login', { credentials:'same-origin', cache:'no-store' });
        const html = await resp.text();
        host.insertAdjacentHTML('beforeend', html);
        modalEl = document.getElementById('modal');
        if(!modalEl){ log('error','No se pudo montar #modal (login).'); return; }
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

    cancelar?.addEventListener('click', (ev)=>{ ev.preventDefault(); ev.stopPropagation(); window.closeLoginModal(); });
    modalEl.addEventListener('click', (e)=>{ if(e.target === modalEl) window.closeLoginModal(); });
    document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape' && !modalEl.classList.contains('hidden')) window.closeLoginModal(); });

    form?.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const payload = { usuario: (usuario?.value||'').trim(), contrasena: pass?.value||'' };
      if(!payload.usuario || !payload.contrasena){ window.showAlert?.('info','Completá usuario y contraseña.'); return; }
      try{
        const resp = await fetch('/core/auth/AuthController.php?action=login', {
          method:'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify(payload),
          credentials:'same-origin', cache:'no-store'
        });
        let json=null; try{ json=await resp.json(); }catch(_e){}
        if(resp.ok && json?.ok){ window.showAlert?.('success','¡Operación completada con éxito!'); if(json.data?.redirect) location.href=json.data.redirect; }
        else{ const msg = json?.error || `Error de autenticación (HTTP ${resp.status}).`; window.showAlert?.('error', msg); }
      }catch(err){ window.showAlert?.('error','Error de red o servidor no disponible.'); }
    });
  }
  function openLoginModal(){ window.openLoginModal?.(); }

  // --- RESERVAS ---
  async function ensureReservaLoadedAndOpen(){
    try{
      if(!modalReservaEl){
        log('info','Cargando modal de reservas…');
        const resp = await fetch('/controllers/body_controller.php?action=view_reserva', { credentials:'same-origin', cache:'no-store' });
        const html = await resp.text();
        host.insertAdjacentHTML('beforeend', html);
        modalReservaEl = document.getElementById('modalReserva');
        if(!modalReservaEl){ log('error','No se pudo montar #modalReserva.'); return; }
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
    const nombre   = document.getElementById('res_nombre');
    const telefono = document.getElementById('res_telefono');
    const fecha    = document.getElementById('res_fecha');
    const hora     = document.getElementById('res_hora');
    const personas = document.getElementById('res_personas');
    const notas    = document.getElementById('res_notas');

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

    cancelar?.addEventListener('click', (ev)=>{ ev.preventDefault(); ev.stopPropagation(); window.closeReservaModal(); });
    modalReservaEl.addEventListener('click', (e)=>{ if(e.target === modalReservaEl) window.closeReservaModal(); });
    document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape' && !modalReservaEl.classList.contains('hidden')) window.closeReservaModal(); });

    form?.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const payload = {
        nombre:   (nombre?.value||'').trim(),
        telefono: (telefono?.value||'').trim(),
        fecha:    (fecha?.value||'').trim(),
        hora:     (hora?.value||'').trim(),
        personas: parseInt(personas?.value||'0', 10) || 0,
        notas:    (notas?.value||'').trim()
      };
      if(!payload.nombre || !payload.telefono || !payload.fecha || !payload.hora || payload.personas<=0){
        window.showAlert?.('info','Completá nombre, teléfono, fecha, hora y cantidad de personas.'); return;
      }
      try{
        const resp = await fetch('/controllers/body_controller.php?action=crear_reserva', {
          method:'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify(payload),
          credentials:'same-origin', cache:'no-store'
        });
        let json=null; try{ json=await resp.json(); }catch(_e){}
        if(resp.ok && json?.ok){
          window.showAlert?.('success','Reserva creada. ¡Gracias!');
          window.closeReservaModal();
          // opcional: limpiar
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
  function openReservaModal(){ window.openReservaModal?.(); }

  // --- Wireup ---
  openBtn?.addEventListener('click', (e)=>{ e.stopPropagation(); toggleMenu(false); ensureModalLoadedAndOpen(); }, { once:true });
  openBtn?.addEventListener('click', ()=> modalEl && openLoginModal());

  openReservaBtn?.addEventListener('click', (e)=>{ e.stopPropagation(); toggleMenu(false); ensureReservaLoadedAndOpen(); }, { once:true });
  openReservaBtn?.addEventListener('click', ()=> modalReservaEl && openReservaModal());

})();
JS;


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
