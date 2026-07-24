<?php
require_once __DIR__ . '/../models/BajaEquipo.php';
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/Asignacion.php';

class BajaEquipoController {
    private BajaEquipo $bajaModel;
    private Equipo     $equipoModel;
    private Asignacion $asigModel;

    public function __construct() {
        $this->bajaModel   = new BajaEquipo();
        $this->equipoModel = new Equipo();
        $this->asigModel   = new Asignacion();
    }

    public function index(): void {
        $this->requireAdmin();
        $filters = ['motivo' => $_GET['motivo'] ?? '', 'buscar' => $_GET['buscar'] ?? ''];
        $bajas   = $this->bajaModel->getAll($filters);
        $stats   = $this->bajaModel->getStats();
        $motivos = BajaEquipo::MOTIVOS;
        $success = $_GET['msg'] ?? null;
        require __DIR__ . '/../views/bajas/list.php';
    }

    public function create(): void {
        $this->requireAdmin();
        $motivos      = BajaEquipo::MOTIVOS;
        $error        = null;
        $equipoPresel = !empty($_GET['equipo_id']) ? $this->equipoModel->getById((int)$_GET['equipo_id']) : null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipoId = (int)($_POST['equipo_id'] ?? 0);
            $motivo   = $_POST['motivo'] ?? '';

            if (!$equipoId || !$motivo) {
                $error = 'El equipo y el motivo son obligatorios.';
            } elseif (!array_key_exists($motivo, BajaEquipo::MOTIVOS)) {
                $error = 'Motivo no válido.';
            } else {
                $equipo        = $this->equipoModel->getById($equipoId);
                $asigActiva    = $this->asigModel->getActivaByEquipo($equipoId);
                $tenaAsignacion = $asigActiva !== false;

                // Cancelar asignación activa si existe
                if ($tenaAsignacion) {
                    $this->asigModel->cancelar($asigActiva['id']);
                }

                // Cambiar estado del equipo a dado_de_baja
                $estadoAnterior = $equipo['estado'];
                $this->equipoModel->update($equipoId, array_merge((array)$equipo, ['estado' => 'dado_de_baja']));

                // Procesar hasta 3 imágenes
                $archivos = [];
                if (!empty($_FILES['evidencias']['name'][0])) {
                    foreach ($_FILES['evidencias']['name'] as $i => $nombre) {
                        if ($_FILES['evidencias']['error'][$i] !== UPLOAD_ERR_OK) continue;
                        $mime = mime_content_type($_FILES['evidencias']['tmp_name'][$i]);
                        if (!in_array($mime, ['image/jpeg','image/jpg','image/png','image/webp'])) continue;
                        $archivos[] = [
                            'name'     => $nombre,
                            'tmp_name' => $_FILES['evidencias']['tmp_name'][$i],
                            'error'    => $_FILES['evidencias']['error'][$i],
                            'size'     => $_FILES['evidencias']['size'][$i],
                        ];
                        if (count($archivos) >= 3) break;
                    }
                }

                $id = $this->bajaModel->create([
                    'equipo_id'        => $equipoId,
                    'motivo'           => $motivo,
                    'descripcion'      => $_POST['descripcion'] ?? '',
                    'estado_anterior'  => $estadoAnterior,
                    'tenia_asignacion' => $tenaAsignacion,
                    'creado_por'       => $_SESSION['user_id'],
                ], $archivos);

                header('Location: index.php?c=bajas&a=detail&id=' . $id . '&msg=creada');
                exit;
            }
        }
        require __DIR__ . '/../views/bajas/create.php';
    }

    public function detail(): void {
        $this->requireAdmin();
        $id   = (int)($_GET['id'] ?? 0);
        $baja = $this->bajaModel->getById($id);
        if (!$baja) { header('Location: index.php?c=bajas'); exit; }
        $imagenes = $this->bajaModel->getImagenes($id);
        $motivos  = BajaEquipo::MOTIVOS;
        $success  = $_GET['msg'] ?? null;
        require __DIR__ . '/../views/bajas/detail.php';
    }

    public function imagen(): void {
        $this->requireAdmin();
        $id  = (int)($_GET['img_id'] ?? 0);
        $stmt = Database::getConnection()->prepare("SELECT * FROM imagenes_baja WHERE id = ?");
        $stmt->execute([$id]);
        $img = $stmt->fetch();
        if (!$img) { http_response_code(404); die('No encontrada.'); }
        $ruta = BajaEquipo::UPLOAD_DIR . $img['nombre_archivo'];
        if (!file_exists($ruta)) { http_response_code(404); die('Archivo no encontrado.'); }
        header('Content-Type: ' . mime_content_type($ruta));
        header('Content-Length: ' . filesize($ruta));
        header('Cache-Control: private, max-age=86400');
        readfile($ruta); exit;
    }

    public function buscarEquipos(): void {
        $this->requireAdmin();
        header('Content-Type: application/json');
        $q       = trim($_GET['q'] ?? '');
        $equipos = [];
        if (strlen($q) >= 2) {
            foreach ($this->equipoModel->getAll(['buscar' => $q]) as $eq) {
                if ($eq['estado'] === 'dado_de_baja') continue; // ya dados de baja no aparecen
                $equipos[] = [
                    'id'       => $eq['id'],
                    'codigo'   => $eq['codigo'],
                    'nombre'   => trim(($eq['equipo_marca'] ?? $eq['marca'] ?? '') . ' ' . ($eq['equipo_modelo'] ?? $eq['modelo'] ?? '')),
                    'categoria'=> $eq['categoria_nombre'],
                    'estado'   => $eq['estado'],
                ];
            }
        }
        echo json_encode($equipos); exit;
    }

    private function requireAdmin(): void {
        if (empty($_SESSION['user_id']))       { header('Location: index.php?c=auth&a=login'); exit; }
        if ($_SESSION['user_rol'] !== 'admin') { header('Location: index.php?c=dashboard');   exit; }
    }
}
