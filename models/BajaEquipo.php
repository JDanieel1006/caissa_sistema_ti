<?php
require_once __DIR__ . '/../config/database.php';

class BajaEquipo {
    private PDO $db;

    public const MOTIVOS = [
        'obsolescencia'  => ['label' => 'Obsolescencia tecnológica', 'icon' => 'bi-clock-history',   'color' => 'secondary'],
        'dano_irreparable'=> ['label' => 'Daño irreparable',         'icon' => 'bi-exclamation-octagon','color' => 'danger'],
        'robo_extravío'  => ['label' => 'Robo / Extravío',           'icon' => 'bi-shield-x',         'color' => 'dark'],
        'donacion'       => ['label' => 'Donación',                  'icon' => 'bi-gift',             'color' => 'info'],
        'fin_vida_util'  => ['label' => 'Fin de vida útil',          'icon' => 'bi-hourglass-bottom', 'color' => 'warning'],
        'otro'           => ['label' => 'Otro motivo',               'icon' => 'bi-three-dots',       'color' => 'secondary'],
    ];

    public const UPLOAD_DIR = __DIR__ . '/../uploads/bajas/';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    private function generarFolio(): string {
        $y    = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bajas_equipo WHERE YEAR(creado_en) = ?");
        $stmt->execute([$y]);
        return sprintf('BAJA-%d-%04d', $y, (int)$stmt->fetchColumn() + 1);
    }

    public function create(array $d, array $archivos = []): int {
        $stmt = $this->db->prepare("
            INSERT INTO bajas_equipo (folio, equipo_id, motivo, descripcion, estado_anterior, tenia_asignacion, creado_por)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $this->generarFolio(),
            $d['equipo_id'],
            $d['motivo'],
            $d['descripcion'] ?: null,
            $d['estado_anterior'],
            $d['tenia_asignacion'] ? 1 : 0,
            $d['creado_por'],
        ]);
        $id = (int)$this->db->lastInsertId();

        // Subir evidencias
        if (!is_dir(self::UPLOAD_DIR)) mkdir(self::UPLOAD_DIR, 0755, true);
        foreach ($archivos as $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) continue;
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $nd   = sprintf('baja_%d_%s.%s', $id, uniqid('', true), $ext);
            move_uploaded_file($file['tmp_name'], self::UPLOAD_DIR . $nd);
            $this->db->prepare("INSERT INTO imagenes_baja (baja_id, nombre_archivo, nombre_original, tamano) VALUES (?,?,?,?)")
                     ->execute([$id, $nd, $file['name'], $file['size']]);
        }
        return $id;
    }

    public function getAll(array $f = []): array {
        $w = ['1=1']; $p = [];
        if (!empty($f['motivo'])) { $w[] = 'b.motivo = ?'; $p[] = $f['motivo']; }
        if (!empty($f['buscar'])) {
            $w[] = '(b.folio LIKE ? OR e.codigo LIKE ? OR e.marca LIKE ? OR e.modelo LIKE ?)';
            $q   = '%' . $f['buscar'] . '%';
            $p   = array_merge($p, [$q, $q, $q, $q]);
        }
        $stmt = $this->db->prepare("
            SELECT b.*, e.codigo AS equipo_codigo, e.marca AS equipo_marca, e.modelo AS equipo_modelo,
                   c.nombre AS categoria_nombre, c.icono AS categoria_icono,
                   CONCAT(u.nombre,' ',u.apellido) AS nombre_creador
            FROM bajas_equipo b
            JOIN equipos e ON e.id = b.equipo_id
            JOIN categorias_equipo c ON c.id = e.categoria_id
            JOIN usuarios u ON u.id = b.creado_por
            WHERE " . implode(' AND ', $w) . "
            ORDER BY b.creado_en DESC
        ");
        $stmt->execute($p);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("
            SELECT b.*, e.codigo AS equipo_codigo, e.marca AS equipo_marca, e.modelo AS equipo_modelo,
                   e.numero_serie AS equipo_serie, e.ubicacion AS equipo_ubicacion,
                   c.nombre AS categoria_nombre, c.icono AS categoria_icono,
                   CONCAT(u.nombre,' ',u.apellido) AS nombre_creador
            FROM bajas_equipo b
            JOIN equipos e ON e.id = b.equipo_id
            JOIN categorias_equipo c ON c.id = e.categoria_id
            JOIN usuarios u ON u.id = b.creado_por
            WHERE b.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getImagenes(int $bajaId): array {
        $stmt = $this->db->prepare("SELECT * FROM imagenes_baja WHERE baja_id = ? ORDER BY id");
        $stmt->execute([$bajaId]);
        return $stmt->fetchAll();
    }

    public function getStats(): array {
        return [
            'total'     => (int)$this->db->query("SELECT COUNT(*) FROM bajas_equipo")->fetchColumn(),
            'este_anio' => (int)$this->db->query("SELECT COUNT(*) FROM bajas_equipo WHERE YEAR(creado_en) = YEAR(NOW())")->fetchColumn(),
            'por_motivo'=> $this->db->query("SELECT motivo, COUNT(*) as total FROM bajas_equipo GROUP BY motivo ORDER BY total DESC")->fetchAll(),
        ];
    }

    public static function formatBytes(int $b): string {
        if ($b >= 1048576) return round($b/1048576, 1).' MB';
        if ($b >= 1024)    return round($b/1024, 0).' KB';
        return $b.' B';
    }
}
