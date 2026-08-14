<?php
require_once __DIR__ . '/../models/Asignacion.php';
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/EmailService.php';

class AsignacionController {
    private Asignacion $asigModel;
    private Equipo     $equipoModel;
    private User       $userModel;
    private EmailService $emailService;

    public function __construct() {
        $this->asigModel   = new Asignacion();
        $this->equipoModel = new Equipo();
        $this->userModel   = new User();
        $this->emailService= new EmailService();
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
                $asignacion = $this->asigModel->getById($id);
                if ($asignacion) {
                    $specs = $this->equipoModel->getEspecificaciones($equipoId);
                    $this->emailService->notificarAsignacionEquipo($asignacion, $specs);
                }
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
            } elseif ($action === 'comentar') {
                $comentario = trim($_POST['comentario'] ?? '');
                if ($comentario === '') {
                    $error = 'Escribe un comentario antes de guardarlo.';
                } else {
                    $this->asigModel->addComentario($id, (int)$_SESSION['user_id'], $comentario);
                    header('Location: index.php?c=asignaciones&a=detail&id='.$id.'&msg=comentario'); exit;
                }
            }
        }
        $asig = $this->asigModel->getById($id);
        $comentarios = $this->asigModel->getComentarios($id);
        require __DIR__ . '/../views/asignaciones/detail.php';
    }

    public function reenviarEmail(): void {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=asignaciones');
            exit;
        }

        $id   = (int)($_POST['id'] ?? 0);
        $asig = $this->asigModel->getById($id);
        if (!$asig) {
            header('Location: index.php?c=asignaciones');
            exit;
        }

        $specs = $this->equipoModel->getEspecificaciones((int)$asig['equipo_id']);
        $ok = $this->emailService->notificarAsignacionEquipo($asig, $specs);
        header('Location: index.php?c=asignaciones&a=detail&id=' . $id . '&msg=' . ($ok ? 'email_reenviado' : 'email_error'));
        exit;
    }

    public function buscarEquipos(): void {
        $this->requireAdmin();
        header('Content-Type: application/json');
        $q       = trim($_GET['q'] ?? $_GET['term'] ?? '');
        $page    = (int)($_GET['page'] ?? 1);
        $result  = $this->equipoModel->searchForAsignacion($q, $page, 20);
        $equipos = array_map(function ($eq) {
            $nombre = trim(($eq['marca'] ?? '') . ' ' . ($eq['modelo'] ?? ''));
            $serie  = $eq['numero_serie'] ? ' | Serie: ' . $eq['numero_serie'] : '';
            $asig   = $eq['asignacion_activa_id'] ? ' | Asignado: ' . $eq['asignacion_activa_folio'] . ' - ' . $eq['asignacion_activa_usuario'] : '';
            return [
                'id'         => $eq['id'],
                'text'       => $eq['codigo'] . ' - ' . ($nombre ?: $eq['categoria_nombre']) . $serie . $asig,
                'codigo'     => $eq['codigo'],
                'nombre'     => $nombre,
                'categoria'  => $eq['categoria_nombre'],
                'serie'      => $eq['numero_serie'],
                'disponible' => empty($eq['asignacion_activa_id']),
                'disabled'   => !empty($eq['asignacion_activa_id']),
            ];
        }, $result['items']);
        echo json_encode(['results' => $equipos, 'pagination' => ['more' => $result['more']]]); exit;
    }

    public function buscarUsuarios(): void {
        $this->requireAdmin();
        header('Content-Type: application/json');
        $q      = trim($_GET['q'] ?? $_GET['term'] ?? '');
        $page   = (int)($_GET['page'] ?? 1);
        $result = $this->userModel->searchActive($q, $page, 20);
        $roles  = [
            'auxiliar_administrativo' => 'Auxiliar Administrativo',
            'coordinador'             => 'Coordinador',
            'operario'                => 'Operario',
            'ayudante'                => 'Ayudante',
            'residente_becario'       => 'Residente/Becario',
            'auxiliar_seguridad'      => 'Auxiliar de Seguridad',
            'auxiliar_oficina'        => 'Auxiliar de Oficina',
            'control_de_obra'         => 'Control de Obra',
            'supervisor_seguridad'    => 'Supervisor de Seguridad',
            'contra_incendios'        => 'Contra Incendios',
            'tecnico_instrumentista'  => 'Tecnico Instrumentista',
            'admin'                   => 'Administrador',
            'tecnico'                 => 'Tecnico',
            'maestro'                 => 'Maestro',
        ];
        $usuarios = array_map(function ($u) use ($roles) {
            $nombre = trim($u['nombre'] . ' ' . $u['apellido']);
            $rol = $roles[$u['rol']] ?? ucwords(str_replace('_', ' ', $u['rol']));
            return [
                'id'           => $u['id'],
                'text'         => $nombre . ' - ' . $rol,
                'nombre'       => $nombre,
                'departamento' => $u['departamento'] ?? '',
                'rol'          => $rol,
            ];
        }, $result['items']);
        echo json_encode(['results' => $usuarios, 'pagination' => ['more' => $result['more']]]); exit;
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
