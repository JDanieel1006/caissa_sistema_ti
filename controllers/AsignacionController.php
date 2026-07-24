<?php
require_once __DIR__ . '/../models/Asignacion.php';
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/User.php';

class AsignacionController {
    private Asignacion $asigModel;
    private Equipo     $equipoModel;
    private User       $userModel;

    public function __construct() {
        $this->asigModel   = new Asignacion();
        $this->equipoModel = new Equipo();
        $this->userModel   = new User();
    }

    public function index(): void {
        $this->requireAdmin();
        $filters      = ['estado'=>$_GET['estado']??'','usuario_id'=>$_GET['usuario_id']??'','buscar'=>$_GET['buscar']??''];
        $asignaciones = $this->asigModel->getAll($filters);
        $usuarios     = $this->userModel->getAll();
        $estados      = Asignacion::ESTADOS;
        $condiciones  = Asignacion::CONDICIONES;
        $success      = $_GET['msg'] ?? null;
        $asigModel    = $this->asigModel;
        require __DIR__ . '/../views/asignaciones/list.php';
    }

    public function create(): void {
        $this->requireAdmin();
        $usuarios     = $this->userModel->getAll();
        $condiciones  = Asignacion::CONDICIONES;
        $equipoPresel = null;
        $error        = null;
        if (!empty($_GET['equipo_id'])) $equipoPresel = $this->equipoModel->getById((int)$_GET['equipo_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipoId  = (int)($_POST['equipo_id']  ?? 0);
            $usuarioId = (int)($_POST['usuario_id'] ?? 0);
            if (!$equipoId || !$usuarioId) {
                $error = 'Selecciona el equipo y el usuario.';
            } elseif (!$this->asigModel->equipoDisponible($equipoId)) {
                $error = 'Este equipo ya tiene una asignación activa.';
            } else {
                $id = $this->asigModel->create([
                    'equipo_id'                => $equipoId,
                    'usuario_id'               => $usuarioId,
                    'entregado_por'            => $_SESSION['user_id'],
                    'condicion_entrega'        => $_POST['condicion_entrega'] ?? 'bueno',
                    'fecha_asignacion'         => $_POST['fecha_asignacion']  ?? date('Y-m-d'),
                    'fecha_devolucion_esperada'=> $_POST['fecha_devolucion_esperada'] ?? '',
                    'notas_entrega'            => $_POST['notas_entrega']     ?? '',
                    'nombre_obra'              => $_POST['nombre_obra']       ?? '',
                    'numero_contrato'          => $_POST['numero_contrato']   ?? '',
                ]);
                header('Location: index.php?c=asignaciones&a=detail&id='.$id.'&msg=creada');
                exit;
            }
            if ($equipoId) $equipoPresel = $this->equipoModel->getById($equipoId);
        }
        require __DIR__ . '/../views/asignaciones/create.php';
    }

    public function detail(): void {
        $this->requireAdmin();
        $id   = (int)($_GET['id'] ?? 0);
        $asig = $this->asigModel->getById($id);
        if (!$asig) { header('Location: index.php?c=asignaciones'); exit; }
        $condiciones = Asignacion::CONDICIONES;
        $estados     = Asignacion::ESTADOS;
        $success     = $_GET['msg'] ?? null;
        $error       = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'devolver') {
                $this->asigModel->registrarDevolucion($id, [
                    'condicion_devolucion' => $_POST['condicion_devolucion'] ?? 'bueno',
                    'fecha_devolucion_real'=> $_POST['fecha_devolucion_real'] ?? date('Y-m-d'),
                    'notas_devolucion'     => $_POST['notas_devolucion'] ?? '',
                ]);
                header('Location: index.php?c=asignaciones&a=detail&id='.$id.'&msg=devuelta'); exit;
            } elseif ($action === 'cancelar') {
                $this->asigModel->cancelar($id);
                header('Location: index.php?c=asignaciones&a=detail&id='.$id.'&msg=cancelada'); exit;
            }
        }
        $asig = $this->asigModel->getById($id);
        require __DIR__ . '/../views/asignaciones/detail.php';
    }

    public function buscarEquipos(): void {
        $this->requireAdmin();
        header('Content-Type: application/json');
        $q       = trim($_GET['q'] ?? '');
        $equipos = [];
        if (strlen($q) >= 2) {
            foreach ($this->equipoModel->getAll(['buscar' => $q]) as $eq) {
                $equipos[] = [
                    'id'         => $eq['id'],
                    'codigo'     => $eq['codigo'],
                    'nombre'     => trim(($eq['marca'] ?? '') . ' ' . ($eq['modelo'] ?? '')),
                    'categoria'  => $eq['categoria_nombre'],
                    'disponible' => $this->asigModel->equipoDisponible($eq['id']),
                ];
            }
        }
        echo json_encode($equipos); exit;
    }

    public function acta(): void {
        $this->requireAdmin();
        $id   = (int)($_GET['id'] ?? 0);
        $asig = $this->asigModel->getById($id);
        if (!$asig) { http_response_code(404); die('No encontrada.'); }
        $specs = $this->equipoModel->getEspecificaciones($asig['equipo_id']);
        require __DIR__ . '/../views/asignaciones/acta_pdf.php';
    }

    private function requireAdmin(): void {
        if (empty($_SESSION['user_id'])) { header('Location: index.php?c=auth&a=login'); exit; }
        if ($_SESSION['user_rol'] !== 'admin') { header('Location: index.php?c=dashboard'); exit; }
    }

    public function contrato(): void {
        $this->requireAdmin();
        $id   = (int)($_GET['id'] ?? 0);
        $asig = $this->asigModel->getById($id);
        if (!$asig) { http_response_code(404); die('No encontrada.'); }
        $specs = $this->equipoModel->getEspecificaciones($asig['equipo_id']);
        require __DIR__ . '/../views/asignaciones/contrato_pdf.php';
    }
}
