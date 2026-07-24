<?php
require_once __DIR__ . '/../models/Mantenimiento.php';
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/Asignacion.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/EmailService.php';

class MantenimientoController {
    private Mantenimiento $mantModel;
    private Equipo        $equipoModel;
    private Asignacion    $asigModel;
    private User          $userModel;
    private EmailService  $emailService;

    public function __construct() {
        $this->mantModel   = new Mantenimiento();
        $this->equipoModel = new Equipo();
        $this->asigModel   = new Asignacion();
        $this->userModel   = new User();
        $this->emailService= new EmailService();
    }

    public function index(): void {
        $this->requireAdmin();
        $filters = ['estado'=>$_GET['estado']??'','tipo'=>$_GET['tipo']??'','buscar'=>$_GET['buscar']??''];
        $mantenimientos = $this->mantModel->getAll($filters);
        $stats   = $this->mantModel->getStats();
        $estados = Mantenimiento::ESTADOS;
        $tipos   = Mantenimiento::TIPOS;
        $success = $_GET['msg'] ?? null;
        require __DIR__ . '/../views/mantenimiento/list.php';
    }

    public function create(): void {
        $this->requireAdmin();
        $usuarios       = $this->userModel->getAll();
        $tipos          = Mantenimiento::TIPOS;
        $error          = null;
        $equiposUsuario = [];

        if (!empty($_POST['usuario_id'])) {
            $equiposUsuario = $this->getEquiposActivosPorUsuario((int)$_POST['usuario_id']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioId = (int)($_POST['usuario_id'] ?? 0);
            $equipoId  = (int)($_POST['equipo_id']  ?? 0);
            $fecha     = $_POST['fecha_programada'] ?? '';

            if (!$usuarioId || !$equipoId || !$fecha) {
                $error = 'Usuario, equipo y fecha son obligatorios.';
            } else {
                $id = $this->mantModel->create([
                    'equipo_id'        => $equipoId,
                    'tipo'             => $_POST['tipo']        ?? 'preventivo',
                    'descripcion'      => $_POST['descripcion'] ?? '',
                    'fecha_programada' => $fecha,
                    'tecnico_id'       => null,
                    'creado_por'       => $_SESSION['user_id'],
                ]);

                $mant  = $this->mantModel->getById($id);
                $specs = $this->equipoModel->getEspecificaciones($equipoId);

                // Enviar correo al dueño del equipo
                $usuario = $this->userModel->findById($usuarioId);
                if ($usuario && !empty($usuario['email'])) {
                    $urlPublica = APP_URL . '/index.php?c=mantenimiento&a=publico&token=' . $mant['token_acceso'];
                    $mant['email_tecnico']  = $usuario['email'];
                    $mant['nombre_tecnico'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
                    $mant['url_publica']    = $urlPublica;
                    $this->emailService->notificarMantenimiento($mant, $specs);
                }

                header('Location: index.php?c=mantenimiento&a=detail&id=' . $id . '&msg=creado');
                exit;
            }
        }
        require __DIR__ . '/../views/mantenimiento/create.php';
    }

    public function detail(): void {
        $this->requireAdmin();
        $id   = (int)($_GET['id'] ?? 0);
        $mant = $this->mantModel->getById($id);
        if (!$mant) { header('Location: index.php?c=mantenimiento'); exit; }

        $estados  = Mantenimiento::ESTADOS;
        $tipos    = Mantenimiento::TIPOS;
        $success  = $_GET['msg'] ?? null;
        $error    = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'cambiar_estado') {
                $nuevoEstado = $_POST['estado'] ?? $mant['estado'];
                $this->mantModel->updateEstado(
                    $id,
                    $nuevoEstado,
                    $_POST['notas']           ?? null,
                    $_POST['fecha_realizada'] ?? null
                );

                // Enviar correo al dueño del equipo si se completa o cancela
                if (in_array($nuevoEstado, ['completado', 'cancelado'])) {
                    $mantActualizado = $this->mantModel->getById($id);
                    $dueno = $this->getDuenoEquipo($mant['equipo_id']);
                    if ($dueno && !empty($dueno['email'])) {
                        $specs      = $this->equipoModel->getEspecificaciones($mant['equipo_id']);
                        $urlPublica = APP_URL . '/index.php?c=mantenimiento&a=publico&token=' . $mantActualizado['token_acceso'];
                        $mantActualizado['email_tecnico']  = $dueno['email'];
                        $mantActualizado['nombre_tecnico'] = $dueno['nombre'] . ' ' . $dueno['apellido'];
                        $mantActualizado['url_publica']    = $urlPublica;
                        $this->emailService->notificarCambioEstadoMantenimiento($mantActualizado, $specs, $nuevoEstado);
                    }
                }

                header('Location: index.php?c=mantenimiento&a=detail&id=' . $id . '&msg=actualizado');
                exit;
            }
        }

        $mant = $this->mantModel->getById($id);
        require __DIR__ . '/../views/mantenimiento/detail.php';
    }

    // Página pública — acceso por token, sin login
    public function publico(): void {
        $token = trim($_GET['token'] ?? '');
        if (!$token) { http_response_code(404); die('Enlace no válido.'); }

        $mant = $this->mantModel->getByToken($token);
        if (!$mant) { http_response_code(404); die('Orden no encontrada o enlace expirado.'); }

        $specs = $this->equipoModel->getEspecificaciones($mant['equipo_id']);
        require __DIR__ . '/../views/mantenimiento/publico.php';
    }

    public function vale(): void {
        $this->requireAdmin();
        $id   = (int)($_GET['id'] ?? 0);
        $mant = $this->mantModel->getById($id);
        if (!$mant) { http_response_code(404); die('No encontrado.'); }
        $specs = $this->equipoModel->getEspecificaciones($mant['equipo_id']);
        require __DIR__ . '/../views/mantenimiento/vale_pdf.php';
    }

    public function equiposPorUsuario(): void {
        $this->requireAdmin();
        header('Content-Type: application/json');
        $uid     = (int)($_GET['usuario_id'] ?? 0);
        $equipos = $uid ? $this->getEquiposActivosPorUsuario($uid) : [];
        echo json_encode($equipos);
        exit;
    }

    // Obtener equipos con asignación activa de un usuario
    private function getEquiposActivosPorUsuario(int $userId): array {
        $asignaciones = $this->asigModel->getAll(['estado' => 'activa', 'usuario_id' => $userId]);
        $equipos = [];
        foreach ($asignaciones as $a) {
            $equipos[] = [
                'equipo_id'        => $a['equipo_id'],
                'equipo_codigo'    => $a['equipo_codigo'],
                'equipo_marca'     => $a['equipo_marca']     ?? '',
                'equipo_modelo'    => $a['equipo_modelo']    ?? '',
                'categoria_nombre' => $a['categoria_nombre'],
                'nombre'           => trim(($a['equipo_marca'] ?? '') . ' ' . ($a['equipo_modelo'] ?? '')),
                'categoria'        => $a['categoria_nombre'],
            ];
        }
        return $equipos;
    }

    // Obtener el dueño actual del equipo desde la asignación activa
    private function getDuenoEquipo(int $equipoId): array|false {
        $asig = $this->asigModel->getActivaByEquipo($equipoId);
        if (!$asig) return false;
        return $this->userModel->findById($asig['usuario_id']);
    }

    private function requireAdmin(): void {
        if (empty($_SESSION['user_id']))       { header('Location: index.php?c=auth&a=login'); exit; }
        if ($_SESSION['user_rol'] !== 'admin') { header('Location: index.php?c=dashboard');   exit; }
    }
}
