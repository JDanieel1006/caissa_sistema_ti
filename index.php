<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Ticket.php';
require_once __DIR__ . '/models/Adjunto.php';
require_once __DIR__ . '/models/Equipo.php';
require_once __DIR__ . '/models/ImagenEquipo.php';
require_once __DIR__ . '/models/IncidenciaEquipo.php';
require_once __DIR__ . '/models/Asignacion.php';
require_once __DIR__ . '/models/Mantenimiento.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/TicketController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/InventarioController.php';
require_once __DIR__ . '/controllers/AsignacionController.php';
require_once __DIR__ . '/controllers/ReportController.php';
require_once __DIR__ . '/controllers/MantenimientoController.php';
require_once __DIR__ . '/controllers/BajaEquipoController.php';

$controller = $_GET['c'] ?? 'dashboard';
$action     = $_GET['a'] ?? 'index';

$publicRoutes = [
    ['c' => 'auth',  'a' => 'login'],
    ['c' => 'users', 'a' => 'register'],
];
$isPublic = array_filter($publicRoutes, fn($r) => $r['c'] === $controller && $r['a'] === $action);

if (!$isPublic && empty($_SESSION['user_id'])) {
    header('Location: index.php?c=auth&a=login');
    exit;
}

switch ($controller) {
    case 'auth':
        $ctrl = new AuthController();
        match ($action) {
            'login'  => $ctrl->login(),
            'logout' => $ctrl->logout(),
            default  => $ctrl->login(),
        };
        break;

    case 'dashboard':
        (new DashboardController())->index();
        break;

    case 'tickets':
        $ctrl = new TicketController();
        match ($action) {
            'index'   => $ctrl->index(),
            'create'  => $ctrl->create(),
            'detail'  => $ctrl->detail(),
            'stats'   => $ctrl->statsJson(),
            'adjunto' => $ctrl->adjunto(),
            default   => $ctrl->index(),
        };
        break;

    case 'users':
        $ctrl = new UserController();
        match ($action) {
            'register' => $ctrl->register(),
            'profile'  => $ctrl->profile(),
            'index'    => $ctrl->index(),
            'create'   => $ctrl->create(),
            'edit'     => $ctrl->edit(),
            'toggle'   => $ctrl->toggle(),
            default    => $ctrl->index(),
        };
        break;

    case 'inventario':
        $ctrl = new InventarioController();
        match ($action) {
            'index'        => $ctrl->index(),
            'create'       => $ctrl->create(),
            'detail'       => $ctrl->detail(),
            'edit'         => $ctrl->edit(),
            'delete'       => $ctrl->delete(),
            'apiCategoria' => $ctrl->apiCategoria(),
            'imagen'       => $ctrl->imagen(),
            'incidenciaCreate'  => $ctrl->incidenciaCreate(),
            'incidenciaDetail'  => $ctrl->incidenciaDetail(),
            'incidenciaImagen'  => $ctrl->incidenciaImagen(),
            default        => $ctrl->index(),
        };
        break;

    case 'asignaciones':
        $ctrl = new AsignacionController();
        match ($action) {
            'index'         => $ctrl->index(),
            'create'        => $ctrl->create(),
            'detail'        => $ctrl->detail(),
            'acta'          => $ctrl->acta(),
            'buscarEquipos' => $ctrl->buscarEquipos(),
            'buscarUsuarios'=> $ctrl->buscarUsuarios(),
            'contrato'      => $ctrl->contrato(),
            'reenviarEmail' => $ctrl->reenviarEmail(),
            default         => $ctrl->index(),
        };
        break;

    case 'reports':
        $ctrl = new ReportController();
        match ($action) {
            'ticket' => $ctrl->ticketPdf(),
            'lista'  => $ctrl->listaPdf(),
            default  => $ctrl->listaPdf(),
        };
        break;
    case 'mantenimiento':
        $ctrl = new MantenimientoController();
        match ($action) {
            'index'              => $ctrl->index(),
            'create'             => $ctrl->create(),
            'detail'             => $ctrl->detail(),
            'vale'               => $ctrl->vale(),
            'equiposPorUsuario'  => $ctrl->equiposPorUsuario(),
            'publico'            => $ctrl->publico(),
            default              => $ctrl->index(),
        };
    break;

    case 'bajas':
        $ctrl = new BajaEquipoController();
        match($action) {
            'create'        => $ctrl->create(),
            'detail'        => $ctrl->detail(),
            'imagen'        => $ctrl->imagen(),
            'buscarEquipos' => $ctrl->buscarEquipos(),
            default         => $ctrl->index(),
        };
    break;

    default:
        header('Location: index.php?c=dashboard');
        exit;
}
