<?php
require_once __DIR__ . '/../config/database.php';

class IncidenciaEquipo {
    private PDO $db;

    public const ESTADOS = [
        'abierta'     => ['label' => 'Abierta',     'color' => 'danger'],
        'en_revision' => ['label' => 'En Revisión', 'color' => 'warning'],
        'resuelta'    => ['label' => 'Resuelta',    'color' => 'success'],
        'descartada'  => ['label' => 'Descartada',  'color' => 'secondary'],
    ];

    public const SEVERIDADES = [
        'baja'    => ['label' => 'Baja',    'color' => 'secondary'],
        'media'   => ['label' => 'Media',   'color' => 'info'],
        'alta'    => ['label' => 'Alta',    'color' => 'warning'],
        'critica' => ['label' => 'Crítica', 'color' => 'danger'],
    ];

    public const TIPOS = [
        'averia'       => ['label' => 'Avería',       'icon' => 'bi-exclamation-triangle'],
        'dano_fisico'  => ['label' => 'Daño físico',  'icon' => 'bi-tools'],
        'software'     => ['label' => 'Software',     'icon' => 'bi-window'],
        'red'          => ['label' => 'Red',          'icon' => 'bi-hdd-network'],
        'rendimiento'  => ['label' => 'Rendimiento',  'icon' => 'bi-speedometer2'],
        'otro'         => ['label' => 'Otro',         'icon' => 'bi-three-dots'],
    ];

    public const TIPOS_PERMITIDOS = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    public const EXTENSIONES_OK   = ['jpg', 'jpeg', 'png', 'webp'];
    public const MAX_BYTES        = 5 * 1024 * 1024;
    public const MAX_IMAGENES     = 5;
    public const UPLOAD_DIR       = __DIR__ . '/../uploads/incidencias/';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    private function generarFolio(): string {
        $y = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM incidencias_equipo WHERE YEAR(creado_en) = ?");
        $stmt->execute([$y]);
        return sprintf('INC-%d-%04d', $y, (int)$stmt->fetchColumn() + 1);
    }

    public function create(array $d, array $files = []): int {
        $stmt = $this->db->prepare("
            INSERT INTO incidencias_equipo
                (folio, equipo_id, tipo, titulo, descripcion, severidad, estado, reportado_por)
            VALUES (?, ?, ?, ?, ?, ?, 'abierta', ?)
        ");
        $stmt->execute([
            $this->generarFolio(),
            $d['equipo_id'],
            $d['tipo'],
            $d['titulo'],
            $d['descripcion'],
            $d['severidad'],
            $d['reportado_por'],
        ]);

        $id = (int)$this->db->lastInsertId();
        $this->subirImagenes($id, $files);
        return $id;
    }

    public function getByEquipo(int $equipoId): array {
        $stmt = $this->db->prepare("
            SELECT i.*, CONCAT(u.nombre,' ',u.apellido) AS nombre_reporta,
                   (SELECT COUNT(*) FROM imagenes_incidencia img WHERE img.incidencia_id = i.id) AS total_imagenes
            FROM incidencias_equipo i
            JOIN usuarios u ON u.id = i.reportado_por
            WHERE i.equipo_id = ?
            ORDER BY FIELD(i.estado,'abierta','en_revision','resuelta','descartada'),
                     i.creado_en DESC
        ");
        $stmt->execute([$equipoId]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("
            SELECT i.*, CONCAT(u.nombre,' ',u.apellido) AS nombre_reporta,
                   e.codigo AS equipo_codigo, e.marca AS equipo_marca, e.modelo AS equipo_modelo,
                   e.numero_serie AS equipo_serie, c.nombre AS categoria_nombre, c.icono AS categoria_icono
            FROM incidencias_equipo i
            JOIN usuarios u ON u.id = i.reportado_por
            JOIN equipos e ON e.id = i.equipo_id
            JOIN categorias_equipo c ON c.id = e.categoria_id
            WHERE i.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getImagenes(int $incidenciaId): array {
        $stmt = $this->db->prepare("SELECT * FROM imagenes_incidencia WHERE incidencia_id = ? ORDER BY id");
        $stmt->execute([$incidenciaId]);
        return $stmt->fetchAll();
    }

    public function getImagenById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM imagenes_incidencia WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateEstado(int $id, string $estado, ?string $notasCierre = null): bool {
        $cerradoEn = in_array($estado, ['resuelta', 'descartada'], true) ? date('Y-m-d H:i:s') : null;
        $stmt = $this->db->prepare("
            UPDATE incidencias_equipo
            SET estado = ?, notas_cierre = ?, cerrado_en = ?
            WHERE id = ?
        ");
        return $stmt->execute([$estado, $notasCierre ?: null, $cerradoEn, $id]);
    }

    private function subirImagenes(int $incidenciaId, array $files): void {
        if (empty($files) || empty($files['name'])) return;
        if (!is_dir(self::UPLOAD_DIR)) mkdir(self::UPLOAD_DIR, 0755, true);

        $lista = [];
        if (is_array($files['name'])) {
            foreach ($files['name'] as $i => $nombre) {
                if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
                $lista[] = [
                    'name'     => $nombre,
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
            }
        } elseif (($files['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $lista[] = $files;
        }

        $lista = array_slice($lista, 0, self::MAX_IMAGENES);
        foreach ($lista as $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) continue;
            if ($file['size'] > self::MAX_BYTES) continue;

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($file['tmp_name']);
            $ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($mime, self::TIPOS_PERMITIDOS, true)) continue;
            if (!in_array($ext, self::EXTENSIONES_OK, true)) continue;

            $nombreArchivo = sprintf('inc_%d_%s.%s', $incidenciaId, uniqid('', true), $ext);
            if (!move_uploaded_file($file['tmp_name'], self::UPLOAD_DIR . $nombreArchivo)) continue;

            $stmt = $this->db->prepare("
                INSERT INTO imagenes_incidencia
                    (incidencia_id, nombre_archivo, nombre_original, tipo_mime, tamano)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$incidenciaId, $nombreArchivo, $file['name'], $mime, $file['size']]);
        }
    }

    public static function formatBytes(int $b): string {
        if ($b >= 1048576) return round($b / 1048576, 1) . ' MB';
        if ($b >= 1024) return round($b / 1024, 0) . ' KB';
        return $b . ' B';
    }
}
