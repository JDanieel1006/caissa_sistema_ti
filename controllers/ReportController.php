<?php
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../models/User.php';

class ReportController {
    private Ticket $ticketModel;
    private User   $userModel;

    public function __construct() { $this->ticketModel = new Ticket(); $this->userModel = new User(); }

    public function ticketPdf(): void {
        $this->requireAuth();
        $id     = (int)($_GET['id'] ?? 0);
        $ticket = $this->ticketModel->getById($id);
        if (!$ticket) { http_response_code(404); die('No encontrado.'); }
        $rol = $_SESSION['user_rol'];
        if ($rol === 'maestro' && $ticket['usuario_id'] != $_SESSION['user_id']) { header('Location: index.php?c=tickets'); exit; }
        $esAdmin     = in_array($rol, ['admin','tecnico']);
        $comentarios = $this->ticketModel->getComentarios($id, $esAdmin);
        $historial   = $this->ticketModel->getHistorial($id);
        $estados     = Ticket::ESTADOS; $prioridades = Ticket::PRIORIDADES; $categorias = Ticket::CATEGORIAS;
        require __DIR__ . '/../views/reports/ticket_pdf.php';
    }

    public function listaPdf(): void {
        $this->requireAuth();
        $rol     = $_SESSION['user_rol'];
        $filters = ['estado'=>$_GET['estado']??'','categoria'=>$_GET['categoria']??'','prioridad'=>$_GET['prioridad']??'','buscar'=>$_GET['buscar']??''];
        if (in_array($rol, ['admin','tecnico'])) { $tickets = $this->ticketModel->getAll($filters); $stats = $this->ticketModel->getStats(); }
        else { $tickets = $this->ticketModel->getByUser($_SESSION['user_id'], $filters); $stats = $this->ticketModel->getStatsByUser($_SESSION['user_id']); }
        $estados = Ticket::ESTADOS; $prioridades = Ticket::PRIORIDADES; $categorias = Ticket::CATEGORIAS;
        require __DIR__ . '/../views/reports/lista_pdf.php';
    }

    private function requireAuth(): void {
        if (empty($_SESSION['user_id'])) { header('Location: index.php?c=auth&a=login'); exit; }
    }
}
