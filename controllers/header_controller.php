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
  const openBtn = document.getElementById('openLogin');
  const host = document.getElementById('modalHost');
  let modalEl = null;

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
    if (typeof extra !== 'undefined') { fn(text, extra); }
    else { fn(text); }
  }

  async function ensureModalLoadedAndOpen(){
    try{
      if(!modalEl){
        log('info','Cargando modal de login…');
        const resp = await fetch('/core/auth/AuthController.php?action=view_login', {
          credentials:'same-origin', cache:'no-store'
        });
        const html = await resp.text();
        host.innerHTML = html;

        modalEl = document.getElementById('modal');
        if(!modalEl){
          log('error','No se pudo montar el modal: #modal no encontrado en HTML recibido.');
          return;
        }
        wireUpModal();
        log('info','Modal de login montado.');
      }
      openModal();
    }catch(e){
      log('error','Fallo al cargar la vista del login.', e);
      window.showAlert?.('error','No se pudo cargar el formulario de login.');
    }
  }

  function wireUpModal(){
    const form = document.getElementById('loginForm');
    const cancelar = document.getElementById('btnCancelar');
    const usuario = document.getElementById('usuario');
    const pass = document.getElementById('contrasena');

    window.openModal = function(){
      modalEl.classList.remove('hidden');
      openBtn?.setAttribute('aria-expanded','true');
      setTimeout(()=> usuario?.focus(), 0);
    };
    window.closeModal = function(){
      modalEl.classList.add('hidden');
      openBtn?.setAttribute('aria-expanded','false');
      openBtn?.focus();
    };

    if(cancelar){
      cancelar.addEventListener('click', (ev)=>{
        ev.preventDefault(); ev.stopPropagation();
        log('info','Cancelar presionado, cerrando modal.');
        window.closeModal();
      });
    } else {
      log('warn','Botón Cancelar (#btnCancelar) no encontrado.');
    }

    modalEl.addEventListener('click', (e)=>{
      if(e.target === modalEl){
        log('info','Overlay clickeado, cerrando modal.');
        window.closeModal();
      }
    });

    document.addEventListener('keydown', (e)=>{
      if(e.key === 'Escape' && !modalEl.classList.contains('hidden')){
        log('info','Escape presionado, cerrando modal.');
        window.closeModal();
      }
    });

    form?.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const payload = {
        usuario: usuario?.value?.trim() || '',
        contrasena: pass?.value || ''
      };
      if(!payload.usuario || !payload.contrasena){
        log('warn','Campos incompletos.', payload);
        window.showAlert?.('info','Completá usuario y contraseña.');
        return;
      }
      try{
        log('info','Enviando login…', { endpoint:'/core/auth/AuthController.php?action=login' });
        const resp = await fetch('/core/auth/AuthController.php?action=login', {
          method:'POST',
          headers:{ 'Content-Type':'application/json' },
          body: JSON.stringify(payload),
          credentials:'same-origin',
          cache:'no-store'
        });
        let json = null;
        try{ json = await resp.json(); }catch(parseErr){
          log('error','No se pudo parsear JSON de la respuesta.', parseErr);
        }
        log('info','Respuesta login recibida.', { status: resp.status, body: json });
        if(resp.ok && json?.ok){
          window.showAlert?.('success','¡Operación completada con éxito!');
          if(json.data?.redirect){ window.location.href = json.data.redirect; }
        }else{
          const msg = (json && json.error) ? json.error : `Error de autenticación (HTTP ${resp.status}).`;
          window.showAlert?.('error', msg);
        }
      }catch(err){
        log('error','Error de red al enviar login.', err);
        window.showAlert?.('error','Error de red o servidor no disponible.');
      }
    });
  }

  function openModal(){ window.openModal?.(); }

  if(openBtn){
    openBtn.addEventListener('click', ensureModalLoadedAndOpen, { once:true });
    openBtn.addEventListener('click', ()=> modalEl && window.openModal());
  }else{
    log('error','No se encontró el botón #openLogin.');
  }
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
