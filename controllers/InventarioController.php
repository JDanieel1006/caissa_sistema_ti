<?php
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/ImagenEquipo.php';
require_once __DIR__ . '/../models/IncidenciaEquipo.php';
require_once __DIR__ . '/../models/Asignacion.php';

class InventarioController {
    private Equipo       $equipoModel;
    private ImagenEquipo $imagenModel;
    private IncidenciaEquipo $incidenciaModel;
    private Asignacion   $asigModel;

    public function __construct() {
        $this->equipoModel      = new Equipo();
        $this->imagenModel      = new ImagenEquipo();
        $this->incidenciaModel  = new IncidenciaEquipo();
        $this->asigModel        = new Asignacion();
    }

    public function index(): void {
        $this->requireAdmin();
        $filters    = ['categoria_id'=>$_GET['categoria_id']??'','estado'=>$_GET['estado']??'','buscar'=>$_GET['buscar']??''];
        $equipos    = $this->equipoModel->getAll($filters);
        $categorias = $this->equipoModel->getCategorias();
        $estados    = Equipo::ESTADOS;
        $stats      = $this->equipoModel->getStats();
        $success    = $_GET['msg'] ?? null;
        require __DIR__ . '/../views/inventario/list.php';
    }

    public function create(): void {
        $this->requireAdmin();
        $categorias = $this->equipoModel->getCategorias();
        $estados    = Equipo::ESTADOS;
        $campos     = [];
        $error      = null;
        $catId      = (int)($_POST['categoria_id'] ?? $_GET['cat'] ?? 0);
        if ($catId) $campos = $this->equipoModel->getCamposCategoria($catId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
            $catId  = (int)($_POST['categoria_id'] ?? 0);
            $codigo = trim($_POST['codigo'] ?? '');
            if (!$catId)  { $error = 'Selecciona una categoría.'; }
            elseif (!$codigo) { $error = 'El código es obligatorio.'; }
            elseif ($this->equipoModel->codigoExiste($codigo)) { $error = "El código \"$codigo\" ya existe."; }
            else {
                $id = $this->equipoModel->create([
                    'categoria_id'  => $catId,
                    'codigo'        => $codigo,
                    'marca'         => $_POST['marca']          ?? '',
                    'modelo'        => $_POST['modelo']         ?? '',
                    'numero_serie'  => $_POST['numero_serie']   ?? '',
                    'direccion_mac' => $_POST['direccion_mac']  ?? '',
                    'direccion_ip'  => $_POST['direccion_ip']   ?? '',
                    'usuario_pc'    => $_POST['usuario_pc']     ?? '',
                    'contrasena_pc' => $_POST['contrasena_pc']  ?? '',
                    'ubicacion'     => $_POST['ubicacion']      ?? '',
                    'estado'        => $_POST['estado']         ?? 'bueno',
                    'notas'         => $_POST['notas']          ?? '',
                    'fecha_compra'  => $_POST['fecha_compra']   ?? '',
                    'creado_por'    => $_SESSION['user_id'],
                ], $_POST['spec'] ?? []);
                header('Location: index.php?c=inventario&a=detail&id='.$id.'&msg=creado'); exit;
            }
            $campos = $this->equipoModel->getCamposCategoria($catId);
        }
        require __DIR__ . '/../views/inventario/create.php';
    }

    public function detail(): void {
        $this->requireAdmin();
        $id     = (int)($_GET['id'] ?? 0);
        $equipo = $this->equipoModel->getById($id);
        if (!$equipo) { header('Location: index.php?c=inventario'); exit; }

        $specs      = $this->equipoModel->getEspecificaciones($id);
        $imagenes   = $this->imagenModel->getByEquipo($id);
        $incidencias = $this->incidenciaModel->getByEquipo($id);
        $asigActiva = $this->asigModel->getActivaByEquipo($id);
        $estados    = Equipo::ESTADOS;
        $estadosIncidencia = IncidenciaEquipo::ESTADOS;
        $severidadesIncidencia = IncidenciaEquipo::SEVERIDADES;
        $tiposIncidencia = IncidenciaEquipo::TIPOS;
        $success    = null; $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'subir_imagenes' && !empty($_FILES['imagenes'])) {
                $resultados = $this->imagenModel->subir($id, $_FILES['imagenes']);
                $ok  = array_filter($resultados, fn($r) =>  $r['ok']);
                $err = array_filter($resultados, fn($r) => !$r['ok']);
                if ($ok)  $success = count($ok).' imagen(es) subida(s).';
                if ($err) $error   = implode(' | ', array_column($err, 'msg'));
                $imagenes = $this->imagenModel->getByEquipo($id);
            } elseif ($action === 'set_principal') {
                $this->imagenModel->setPrincipal((int)($_POST['imagen_id']??0), $id);
                $success = 'Imagen principal actualizada.'; $imagenes = $this->imagenModel->getByEquipo($id);
            } elseif ($action === 'eliminar_imagen') {
                $this->imagenModel->eliminar((int)($_POST['imagen_id']??0));
                $success = 'Imagen eliminada.'; $imagenes = $this->imagenModel->getByEquipo($id);
            }
        }

        if (!$_POST && isset($_GET['msg'])) $success = match($_GET['msg']) {
            'creado'  => 'Equipo registrado correctamente.',
            'editado' => 'Equipo actualizado correctamente.',
            default   => null,
        };
        require __DIR__ . '/../views/inventario/detail.php';
    }

    public function incidenciaCreate(): void {
        $this->requireAdmin();
        $equipoId = (int)($_GET['equipo_id'] ?? $_POST['equipo_id'] ?? 0);
        $equipo   = $this->equipoModel->getById($equipoId);
        if (!$equipo) { header('Location: index.php?c=inventario'); exit; }

        $tipos       = IncidenciaEquipo::TIPOS;
        $severidades = IncidenciaEquipo::SEVERIDADES;
        $estadosEquipo = Equipo::ESTADOS;
        $error       = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo       = $_POST['tipo'] ?? 'averia';
            $severidad  = $_POST['severidad'] ?? 'media';
            $titulo     = trim($_POST['titulo'] ?? '');
            $descripcion= trim($_POST['descripcion'] ?? '');

            if (!array_key_exists($tipo, $tipos)) {
                $error = 'Tipo de incidencia no válido.';
            } elseif (!array_key_exists($severidad, $severidades)) {
                $error = 'Severidad no válida.';
            } elseif (!$titulo || !$descripcion) {
                $error = 'Título y descripción son obligatorios.';
            } else {
                $id = $this->incidenciaModel->create([
                    'equipo_id'     => $equipoId,
                    'tipo'          => $tipo,
                    'titulo'        => $titulo,
                    'descripcion'   => $descripcion,
                    'severidad'     => $severidad,
                    'reportado_por' => $_SESSION['user_id'],
                ], $_FILES['evidencias'] ?? []);

                $nuevoEstadoEquipo = $_POST['estado_equipo'] ?? '';
                if ($nuevoEstadoEquipo && array_key_exists($nuevoEstadoEquipo, $estadosEquipo) && $nuevoEstadoEquipo !== $equipo['estado']) {
                    $this->equipoModel->update($equipoId, array_merge($equipo, ['estado' => $nuevoEstadoEquipo]));
                }

                header('Location: index.php?c=inventario&a=incidenciaDetail&id=' . $id . '&msg=creada');
                exit;
            }
        }

        require __DIR__ . '/../views/inventario/incidencia_create.php';
    }

    public function incidenciaDetail(): void {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $incidencia = $this->incidenciaModel->getById($id);
        if (!$incidencia) { header('Location: index.php?c=inventario'); exit; }

        $imagenes    = $this->incidenciaModel->getImagenes($id);
        $estados     = IncidenciaEquipo::ESTADOS;
        $severidades = IncidenciaEquipo::SEVERIDADES;
        $tipos       = IncidenciaEquipo::TIPOS;
        $success     = $_GET['msg'] ?? null;
        $error       = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $estado = $_POST['estado'] ?? $incidencia['estado'];
            if (!array_key_exists($estado, $estados)) {
                $error = 'Estado no válido.';
            } else {
                $this->incidenciaModel->updateEstado($id, $estado, trim($_POST['notas_cierre'] ?? '') ?: null);
                header('Location: index.php?c=inventario&a=incidenciaDetail&id=' . $id . '&msg=actualizada');
                exit;
            }
        }

        require __DIR__ . '/../views/inventario/incidencia_detail.php';
    }

    public function incidenciaImagen(): void {
        $this->requireAdmin();
        $id  = (int)($_GET['img_id'] ?? 0);
        $img = $this->incidenciaModel->getImagenById($id);
        if (!$img) { http_response_code(404); die('No encontrada.'); }

        $ruta = IncidenciaEquipo::UPLOAD_DIR . $img['nombre_archivo'];
        if (!file_exists($ruta)) { http_response_code(404); die('Archivo no encontrado.'); }

        header('Content-Type: ' . mime_content_type($ruta));
        header('Content-Length: ' . filesize($ruta));
        header('Cache-Control: private, max-age=86400');
        readfile($ruta);
        exit;
    }

    public function edit(): void {
        $this->requireAdmin();
        $id     = (int)($_GET['id'] ?? 0);
        $equipo = $this->equipoModel->getById($id);
        if (!$equipo) { header('Location: index.php?c=inventario'); exit; }
        $categorias    = $this->equipoModel->getCategorias();
        $estados       = Equipo::ESTADOS;
        $campos        = $this->equipoModel->getCamposCategoria($equipo['categoria_id']);
        $specsActuales = [];
        foreach ($this->equipoModel->getEspecificaciones($id) as $s) $specsActuales[$s['nombre_campo']] = $s['valor'];
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $catId = (int)($_POST['categoria_id'] ?? $equipo['categoria_id']);
            if ($catId !== $equipo['categoria_id']) { $campos = $this->equipoModel->getCamposCategoria($catId); $specsActuales = []; }
            $this->equipoModel->update($id, [
                'categoria_id'  => $catId,
                'marca'         => $_POST['marca']         ?? '',
                'modelo'        => $_POST['modelo']        ?? '',
                'numero_serie'  => $_POST['numero_serie']  ?? '',
                'direccion_mac' => $_POST['direccion_mac'] ?? '',
                'direccion_ip'  => $_POST['direccion_ip']  ?? '',
                'usuario_pc'    => $_POST['usuario_pc']    ?? '',
                'contrasena_pc' => $_POST['contrasena_pc'] ?? '',
                'ubicacion'     => $_POST['ubicacion']     ?? '',
                'estado'        => $_POST['estado']        ?? 'bueno',
                'notas'         => $_POST['notas']         ?? '',
                'fecha_compra'  => $_POST['fecha_compra']  ?? '',
            ], $_POST['spec'] ?? []);
            header('Location: index.php?c=inventario&a=detail&id='.$id.'&msg=editado'); exit;
        }
        require __DIR__ . '/../views/inventario/edit.php';
    }

    public function delete(): void {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            try {
                $this->equipoModel->delete($id);
                header('Location: index.php?c=inventario&msg=eliminado');
            } catch (RuntimeException $e) {
                header('Location: index.php?c=inventario&a=edit&id='.$id.'&error='.urlencode($e->getMessage()));
            }
        }
        exit;
    }

    public function apiCategoria(): void {
        $this->requireAdmin();
        header('Content-Type: application/json');
        $catId  = (int)($_GET['cat_id'] ?? 0);
        echo json_encode(['campos' => $this->equipoModel->getCamposCategoria($catId), 'codigo' => $this->equipoModel->generateCodigo($catId)]);
        exit;
    }

    public function imagen(): void {
        $this->requireAdmin();
        $id  = (int)($_GET['img_id'] ?? 0);
        $img = $this->imagenModel->getById($id);
        if (!$img) { http_response_code(404); die('No encontrada.'); }
        $ruta = ImagenEquipo::UPLOAD_DIR . $img['nombre_archivo'];
        if (!file_exists($ruta)) { http_response_code(404); die('Archivo no encontrado.'); }

        $mime = mime_content_type($ruta);

        if (isset($_GET['thumb']) && function_exists('imagecreatefromjpeg')) {
            $cacheDir  = ImagenEquipo::UPLOAD_DIR . 'thumbs/';
            $thumbPath = $cacheDir . 'th_' . $img['nombre_archivo'];
            if (!is_dir($cacheDir)) mkdir($cacheDir, 0755, true);
            if (!file_exists($thumbPath)) {
                $src = match($mime) {
                    'image/jpeg','image/jpg' => imagecreatefromjpeg($ruta),
                    'image/png'              => imagecreatefrompng($ruta),
                    'image/gif'              => imagecreatefromgif($ruta),
                    'image/webp'             => imagecreatefromwebp($ruta),
                    default => null,
                };
                if ($src) {
                    $w = imagesx($src); $h = imagesy($src);
                    $tw = 300; $th = (int)($h * $tw / $w);
                    $thumb = imagecreatetruecolor($tw, $th);
                    imagecopyresampled($thumb, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
                    imagejpeg($thumb, $thumbPath, 85);
                    imagedestroy($src); imagedestroy($thumb);
                }
            }
            if (file_exists($thumbPath)) {
                header('Content-Type: image/jpeg');
                header('Cache-Control: private, max-age=86400');
                readfile($thumbPath); exit;
            }
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($ruta));
        header('Cache-Control: private, max-age=86400');
        readfile($ruta); exit;
    }

    private function requireAdmin(): void {
        if (empty($_SESSION['user_id'])) { header('Location: index.php?c=auth&a=login'); exit; }
        if ($_SESSION['user_rol'] !== 'admin') { header('Location: index.php?c=dashboard'); exit; }
    }
}
