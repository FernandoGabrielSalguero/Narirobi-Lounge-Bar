<?php
declare(strict_types=1);

ini_set('display_errors', '1'); // puedes desactivar luego
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/body_model.php';

$action = $_GET['action'] ?? 'ping';

try {
    switch ($action) {
        case 'ping':
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok'=>true,'data'=>['pong'=>true]], JSON_UNESCAPED_UNICODE);
            exit;

        // --- Vista del modal de reservas ---
        case 'view_reserva':
            header('Content-Type: text/html; charset=utf-8');
            echo <<<'HTML'
<div id="modalReserva" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="reservaTitulo">
  <div class="modal-content">
    <h3 id="reservaTitulo">Reservas</h3>
    <form id="reservaForm" class="form-grid grid-1" autocomplete="off" novalidate>
      <div class="input-group">
        <label for="res_nombre">Nombre</label>
        <div class="input-icon input-icon-name">
          <input type="text" id="res_nombre" name="nombre" placeholder="Tu nombre" required />
        </div>
      </div>

      <div class="input-group">
        <label for="res_telefono">Teléfono</label>
        <div class="input-icon input-icon-phone">
          <input type="tel" id="res_telefono" name="telefono" placeholder="+54 9 ..." required />
        </div>
      </div>

      <div class="form-grid grid-2">
        <div class="input-group">
          <label for="res_fecha">Fecha</label>
          <div class="input-icon input-icon-calendar">
            <input type="date" id="res_fecha" name="fecha" required />
          </div>
        </div>

        <div class="input-group">
          <label for="res_hora">Hora</label>
          <div class="input-icon input-icon-time">
            <input type="time" id="res_hora" name="hora" required />
          </div>
        </div>
      </div>

      <div class="input-group">
        <label for="res_personas">Personas</label>
        <div class="input-icon input-icon-users">
          <input type="number" id="res_personas" name="personas" min="1" step="1" value="2" required />
        </div>
      </div>

      <div class="input-group">
        <label for="res_notas">Notas</label>
        <div class="input-icon input-icon-edit">
          <textarea id="res_notas" name="notas" rows="3" placeholder="Preferencias, alergias, ocasión, etc."></textarea>
        </div>
      </div>

      <div class="form-buttons">
        <button type="submit" class="btn btn-aceptar">Confirmar</button>
        <button id="btnCancelarReserva" class="btn btn-cancelar">Cancelar</button>
      </div>
    </form>
  </div>
</div>
<style>
  /* Responsive sin layout shift, usa estilos del framework y ajusta minimos */
  #modalReserva .modal-content{ max-width: 640px; width: 92vw; }
  @media (min-width: 768px){
    #modalReserva .modal-content{ width: 560px; }
  }
</style>
HTML;
            exit;

        // --- Alta de reserva ---
        case 'crear_reserva':
            header('Content-Type: application/json; charset=utf-8');

            // leer JSON
            $raw = file_get_contents('php://input') ?: '';
            $data = json_decode($raw, true) ?: [];

            // sanitización básica
            $nombre   = trim((string)($data['nombre']   ?? ''));
            $telefono = trim((string)($data['telefono'] ?? ''));
            $fecha    = trim((string)($data['fecha']    ?? ''));
            $hora     = trim((string)($data['hora']     ?? ''));
            $personas = (int)($data['personas'] ?? 0);
            $notas    = trim((string)($data['notas']    ?? ''));

            if ($nombre === '' || $telefono === '' || $fecha === '' || $hora === '' || $personas < 1) {
                http_response_code(422);
                echo json_encode(['ok'=>false,'error'=>'Datos incompletos.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Insert con PDO (estado por defecto 'pendiente')
            $pdo = $pdo ?? null; // asumimos $pdo del config.php
            if (!$pdo instanceof \PDO) {
                http_response_code(500);
                echo json_encode(['ok'=>false,'error'=>'Conexión de base de datos no disponible.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $model = new BodyModel($pdo);
            $id = $model->crearReserva($nombre, $telefono, $fecha, $hora, $personas, $notas);

            echo json_encode(['ok'=>true,'data'=>['id'=>$id]], JSON_UNESCAPED_UNICODE);
            exit;

        default:
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok'=>false,'error'=>'Acción no soportada'], JSON_UNESCAPED_UNICODE);
            exit;
    }
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>false,'error'=>'Error interno: '.$e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
