<?php
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Adjunto.php';
require_once __DIR__ . '/../services/EmailService.php';

class TicketController {
    private Ticket $ticketModel;
    private User $userModel;
    private EmailService $emailService;
    private Adjunto $adjuntoModel;

    public function __construct() {
        $this->ticketModel  = new Ticket();
        $this->userModel    = new User();
        $this->emailService = new EmailService();
        $this->adjuntoModel = new Adjunto();
    }

    public function index(): void {
        $this->requireAuth();
        $filters = [
            'estado'    => $_GET['estado']    ?? '',
            'categoria' => $_GET['categoria'] ?? '',
            'prioridad' => $_GET['prioridad'] ?? '',
            'buscar'    => $_GET['buscar']    ?? '',
        ];
        $rol = $_SESSION['user_rol'];
        $tickets = in_array($rol, ['admin','tecnico'])
            ? $this->ticketModel->getAll($filters)
            : $this->ticketModel->getByUser($_SESSION['user_id'], $filters);
        $estados     = Ticket::ESTADOS;
        $categorias  = Ticket::CATEGORIAS;
        $prioridades = Ticket::PRIORIDADES;
        require __DIR__ . '/../views/tickets/list.php';
    }

    public function create(): void {
        $this->requireAuth();
        $categorias  = Ticket::CATEGORIAS;
        $prioridades = Ticket::PRIORIDADES;
        $error       = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titulo'      => trim($_POST['titulo']      ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'categoria'   => $_POST['categoria']        ?? '',
                'prioridad'   => $_POST['prioridad']        ?? 'media',
                'ubicacion'   => trim($_POST['ubicacion']   ?? ''),
                'usuario_id'  => $_SESSION['user_id'],
            ];
            if (empty($data['titulo']) || empty($data['descripcion']) || empty($data['categoria'])) {
                $error = 'Título, descripción y categoría son obligatorios.';
            } else {
                $id     = $this->ticketModel->create($data);
                $ticket = $this->ticketModel->getById($id);
                $usuarioData = $this->userModel->findById($_SESSION['user_id']);
                $this->emailService->notificarTicketCreado($ticket, $usuarioData);
                $admins = $this->userModel->getAdminsYTecnicos();
                $this->emailService->notificarAdmins($ticket, $usuarioData, $admins);
                // Redirigir al listado con mensaje — evita duplicados por doble clic
                header('Location: index.php?c=tickets&creado=' . urlencode($ticket['folio']));
                exit;
            }
        }
        require __DIR__ . '/../views/tickets/create.php';
    }

    public function detail(): void {
        $this->requireAuth();
        $id     = (int)($_GET['id'] ?? 0);
        $ticket = $this->ticketModel->getById($id);
        if (!$ticket) { $this->notFound(); return; }

        $rol = $_SESSION['user_rol'];
        if ($rol === 'maestro' && $ticket['usuario_id'] != $_SESSION['user_id']) {
            header('Location: index.php?c=tickets'); exit;
        }

        $esAdmin     = in_array($rol, ['admin','tecnico']);
        $estados     = Ticket::ESTADOS;
        $prioridades = Ticket::PRIORIDADES;
        $categorias  = Ticket::CATEGORIAS;
        $tecnicos    = $esAdmin ? $this->userModel->getTechnicians() : [];
        $msgError    = null;
        $msgSuccess  = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];

            if ($action === 'comentar') {
                $msg     = trim($_POST['mensaje'] ?? '');
                $interno = isset($_POST['es_interno']) && $esAdmin;
                if ($msg) {
                    $this->ticketModel->addComentario($id, $_SESSION['user_id'], $msg, $interno);
                    header('Location: index.php?c=tickets&a=detail&id='.$id.'&ok=comentario'); exit;
                } else { $msgError = 'El mensaje no puede estar vacío.'; }

            } elseif ($action === 'subir_adjunto') {
                if (isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] !== UPLOAD_ERR_NO_FILE) {
                    try {
                        $this->adjuntoModel->subir($id, $_SESSION['user_id'], $_FILES['adjunto']);
                        header('Location: index.php?c=tickets&a=detail&id='.$id.'&ok=adjunto'); exit;
                    } catch (RuntimeException $e) { $msgError = $e->getMessage(); }
                } else { $msgError = 'Selecciona un archivo.'; }

            } elseif ($action === 'eliminar_adjunto') {
                $adjId = (int)($_POST['adjunto_id'] ?? 0);
                if ($this->adjuntoModel->puedeEliminar($adjId, $_SESSION['user_id'], $rol)) {
                    $this->adjuntoModel->eliminar($adjId);
                    header('Location: index.php?c=tickets&a=detail&id='.$id.'&ok=adjunto_eliminado'); exit;
                } else { $msgError = 'Sin permiso para eliminar.'; }

            } elseif ($action === 'cambiar_estado' && $esAdmin) {
                $nuevoEstado = $_POST['estado']          ?? '';
                $nota        = trim($_POST['nota']       ?? '');
                $resolucion  = trim($_POST['resolucion'] ?? '');
                $tecnicoId   = !empty($_POST['tecnico_id']) ? (int)$_POST['tecnico_id'] : null;
                if (array_key_exists($nuevoEstado, Ticket::ESTADOS)) {
                    $estadoAnterior = $ticket['estado'];
                    $this->ticketModel->updateEstado($id, $nuevoEstado, $_SESSION['user_id'], $nota ?: null, $tecnicoId, $resolucion ?: null);
                    $solicitante = $this->userModel->findById($ticket['usuario_id']);
                    $ticketActualizado = $this->ticketModel->getById($id);
                    $this->emailService->notificarCambioEstado($ticketActualizado, $solicitante, $estadoAnterior, $nuevoEstado);
                    header('Location: index.php?c=tickets&a=detail&id='.$id.'&ok=estado'); exit;
                }

            } elseif ($action === 'asignar' && $esAdmin) {
                $tecnicoId = (int)($_POST['tecnico_id'] ?? 0);
                if ($tecnicoId) {
                    $this->ticketModel->asignarTecnico($id, $tecnicoId, $_SESSION['user_id']);
                    header('Location: index.php?c=tickets&a=detail&id='.$id.'&ok=asignado'); exit;
                }
            }
        }

        // Mensajes tras redirección GET
        if (isset($_GET['ok'])) {
            $msgSuccess = match($_GET['ok']) {
                'estado'            => 'Estado actualizado correctamente.',
                'comentario'        => 'Comentario agregado.',
                'adjunto'           => 'Archivo adjuntado correctamente.',
                'adjunto_eliminado' => 'Archivo eliminado.',
                'asignado'          => 'Técnico asignado.',
                default             => null,
            };
        }

        // Recargar datos frescos desde BD
        $ticket      = $this->ticketModel->getById($id);
        $comentarios = $this->ticketModel->getComentarios($id, $esAdmin);
        $historial   = $this->ticketModel->getHistorial($id);
        $adjuntos    = $this->adjuntoModel->getByTicket($id);

        require __DIR__ . '/../views/tickets/detail.php';
    }

    public function statsJson(): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        echo json_encode($this->ticketModel->getStats());
        exit;
    }

    public function adjunto(): void {
        $this->requireAuth();
        $adjId = (int)($_GET['adj_id'] ?? 0);
        $adj   = $this->adjuntoModel->getById($adjId);
        if (!$adj) { http_response_code(404); die('No encontrado.'); }
        $ticket = $this->ticketModel->getById($adj['ticket_id']);
        if ($_SESSION['user_rol'] === 'maestro' && $ticket['usuario_id'] != $_SESSION['user_id']) {
            http_response_code(403); die('Sin acceso.');
        }
        $ruta = Adjunto::UPLOAD_DIR . $adj['nombre_archivo'];
        if (!file_exists($ruta)) { http_response_code(404); die('Archivo no encontrado.'); }
        $inline = str_starts_with($adj['tipo_mime'], 'image/');
        header('Content-Type: '        . $adj['tipo_mime']);
        header('Content-Length: '      . $adj['tamano']);
        header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . addslashes($adj['nombre_original']) . '"');
        header('Cache-Control: private, max-age=3600');
        readfile($ruta); exit;
    }

    private function requireAuth(): void {
        if (empty($_SESSION['user_id'])) { header('Location: index.php?c=auth&a=login'); exit; }
    }
    private function notFound(): void { http_response_code(404); echo '<h1>Ticket no encontrado</h1>'; }
}
