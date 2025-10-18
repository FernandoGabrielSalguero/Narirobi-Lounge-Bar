<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/admin_reservas_model.php';

class AdminReservasController
{
    private AdminReservasModel $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new AdminReservasModel($pdo);
        header('Content-Type: application/json; charset=utf-8');
    }

    /** Router simple por método/acción */
    public function handle(): void
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $action = $_GET['action'] ?? 'list';

            switch ($method) {
                case 'GET':
                    if ($action === 'list') {
                        $this->list();
                    } elseif ($action === 'detail') {
                        $this->detail();
                    } elseif ($action === 'ocupacion') {
                        $this->ocupacion();
                    } elseif ($action === 'historial') {
                        $this->historial();
                    } else {
                        $this->json(false, null, 'Acción GET desconocida');
                    }
                    break;

                case 'POST':
                    $this->create();
                    break;

                case 'PUT':
                    if ($action === 'estado') {
                        $this->estado();
                    } else {
                        $this->update();
                    }
                    break;

                default:
                    $this->json(false, null, 'Método no soportado');
            }
        } catch (Throwable $e) {
            $this->json(false, null, $e->getMessage());
        }
    }

    private function list(): void
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $filtros = [
            'q' => $_GET['q'] ?? null,
            'estado' => $_GET['estado'] ?? null,
            'fecha_desde' => $_GET['fecha_desde'] ?? null,
            'fecha_hasta' => $_GET['fecha_hasta'] ?? null,
            'hora' => $_GET['hora'] ?? null,
        ];
        $data = $this->model->listar($filtros, $page, 12);
        $this->json(true, $data);
    }

    private function detail(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $row = $this->model->obtener($id);
        if (!$row) {
            $this->json(false, null, 'No encontrado');
            return;
        }
        $this->json(true, $row);
    }

    private function create(): void
    {
        $input = $this->readJson();
        $id = $this->model->crear($input);
        $this->json(true, ['id' => $id]);
    }

    private function update(): void
    {
        parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
        $id = (int)($qs['id'] ?? 0);
        $input = $this->readJson();
        $ok = $this->model->editar($id, $input);
        $this->json(true, ['ok' => $ok]);
    }

    private function estado(): void
    {
        parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
        $id = (int)($qs['id'] ?? 0);
        $input = $this->readJson();
        $ok = $this->model->cambiarEstado($id, $input['estado'] ?? 'pendiente', $input['motivo'] ?? null);
        $this->json(true, ['ok' => $ok]);
    }

    private function ocupacion(): void
    {
        $from = $_GET['from'] ?? date('Y-m-01');
        $to   = $_GET['to'] ?? date('Y-m-t');
        $data = $this->model->ocupacion($from, $to);
        $this->json(true, $data);
    }

    private function historial(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $data = $this->model->historial($id);
        $this->json(true, $data);
    }

    // ===== Helpers =====
    private function readJson(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private function json(bool $ok, $data = null, string $error = ''): void
    {
        echo json_encode(
            $ok ? ['ok' => true, 'data' => $data] : ['ok' => false, 'error' => $error],
            JSON_UNESCAPED_UNICODE
        );
    }
}

// Bootstrap mínimo si se llama directamente a este controlador:
if (php_sapi_name() !== 'cli') {
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        require_once __DIR__ . '/../config.php';
        // Se espera que config.php exponga $pdo (PDO) o función para obtenerlo.
    }
    (new AdminReservasController($pdo))->handle();
}
