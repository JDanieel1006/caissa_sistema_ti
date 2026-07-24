<?php
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/Asignacion.php';

class DashboardController {
    private Ticket     $ticketModel;
    private User       $userModel;
    private Equipo     $equipoModel;
    private Asignacion $asigModel;

    public function __construct() {
        $this->ticketModel = new Ticket();
        $this->userModel   = new User();
        $this->equipoModel = new Equipo();
        $this->asigModel   = new Asignacion();
    }

    public function index(): void {
        if (empty($_SESSION['user_id'])) { header('Location: index.php?c=auth&a=login'); exit; }
        if (in_array($_SESSION['user_rol'], ['admin','tecnico'])) $this->adminDashboard();
        else $this->teacherDashboard();
    }

    private function adminDashboard(): void {
        $stats               = $this->ticketModel->getStats();
        $ticketsRecientes    = array_slice($this->ticketModel->getAll([]), 0, 6);
        $ticketsPorDia       = $this->getTicketsPorDia(14);
        $statsEquipos        = $this->equipoModel->getStats();
        $statsAsig           = $this->asigModel->getStats();
        $asignacionesActivas = array_slice($this->asigModel->getAll(['estado' => 'activa']), 0, 5);
        $statsUsuarios       = $this->userModel->countByRole();
        $estados             = Ticket::ESTADOS;
        $prioridades         = Ticket::PRIORIDADES;
        $categorias          = Ticket::CATEGORIAS;
        require __DIR__ . '/../views/dashboard/admin.php';
    }

    private function getTicketsPorDia(int $dias): array {
        $db   = Database::getConnection();
        $stmt = $db->prepare("SELECT DATE(creado_en) as fecha, COUNT(*) as total FROM tickets WHERE creado_en >= DATE_SUB(CURDATE(), INTERVAL ? DAY) GROUP BY DATE(creado_en) ORDER BY fecha ASC");
        $stmt->execute([$dias]);
        $rows = $stmt->fetchAll();
        $resultado = [];
        for ($i = $dias - 1; $i >= 0; $i--) { $resultado[date('Y-m-d', strtotime("-$i days"))] = 0; }
        foreach ($rows as $row) $resultado[$row['fecha']] = (int)$row['total'];
        return $resultado;
    }

    private function teacherDashboard(): void {
        $userId      = $_SESSION['user_id'];
        $stats       = $this->ticketModel->getStatsByUser($userId);
        $tickets     = array_slice($this->ticketModel->getByUser($userId), 0, 5);
        $estados     = Ticket::ESTADOS;
        $prioridades = Ticket::PRIORIDADES;
        $categorias  = Ticket::CATEGORIAS;
        require __DIR__ . '/../views/dashboard/teacher.php';
    }
}
