<?php

declare(strict_types=1);

namespace Core\Auth;

final class AuthView
{
    /**
     * Respuesta JSON consistente {ok, data|error}
     */
    public static function json(array $payload): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function success(array $data = []): void
    {
        self::json(['ok' => true, 'data' => $data]);
    }

    public static function error(string $message, int $status = 400): void
    {
        http_response_code($status);
        self::json(['ok' => false, 'error' => $message]);
    }

    /**
     * Vista: Modal de login (HTML) para ser inyectado en index.php
     */
    public static function loginModal(): string
    {
        return <<<HTML
<div id="modal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalDesc" style="position:fixed;inset:0;/* display:grid;  <-- lo sacamos para que .hidden gane */place-items:center;background:rgba(0,0,0,.5);z-index:1000;display:flex;align-items:center;justify-content:center;">
  <div class="modal-content" style="width:min(92vw,400px);background:#111;color:#fff;border-radius:12px;padding:1.25rem;box-shadow:0 10px 40px rgba(0,0,0,.6);">
    <h3 id="modalTitle" style="margin-top:0;">Iniciar sesión</h3>
    <p id="modalDesc" class="sr-only" style="position:absolute;left:-9999px;">Ingresá tu usuario y contraseña para continuar.</p>

    <form id="loginForm" novalidate>
      <div class="input-group" style="margin-bottom:.75rem;">
        <label for="usuario" style="color:#ddd;display:block;margin-bottom:.25rem;">Usuario</label>
        <div class="input-icon input-icon-name" style="background:#fff;border-radius:8px;padding:.25rem .5rem;">
          <input type="text" id="usuario" name="usuario" placeholder="tu_usuario" autocomplete="username" required style="width:100%;background:transparent;color:#111;border:0;outline:0;padding:.5rem 0;" />
        </div>
      </div>

      <div class="input-group" style="margin-bottom:.75rem;">
        <label for="contrasena" style="color:#ddd;display:block;margin-bottom:.25rem;">Contraseña</label>
        <div class="input-icon input-icon-password" style="background:#fff;border-radius:8px;padding:.25rem .5rem;">
          <input type="password" id="contrasena" name="contrasena" placeholder="********" autocomplete="current-password" required style="width:100%;background:transparent;color:#111;border:0;outline:0;padding:.5rem 0;" />
        </div>
      </div>

      <div class="form-buttons" style="display:flex;gap:.5rem;justify-content:flex-end;margin-top:1rem;">
        <button type="button" class="btn btn-cancelar" id="btnCancelar" style="background:transparent;border:1px solid #666;color:#fff;padding:.5rem .9rem;border-radius:999px;">Cancelar</button>
        <button type="submit" class="btn btn-aceptar" style="background:#fff;color:#000;border:0;padding:.6rem .95rem;border-radius:999px;font-weight:600;">Ingresar</button>
      </div>
    </form>
  </div>
</div>
<style>
  .hidden{display:none !important;}
</style>
HTML;
    }
}
