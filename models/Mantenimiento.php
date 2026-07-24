<?php
require_once __DIR__ . '/../config/database.php';

class Mantenimiento {
    private PDO $db;

    public const ESTADOS = [
        'pendiente'  => ['label' => 'Pendiente',  'color' => 'warning'],
        'en_proceso' => ['label' => 'En Proceso', 'color' => 'info'],
        'completado' => ['label' => 'Completado', 'color' => 'success'],
        'cancelado'  => ['label' => 'Cancelado',  'color' => 'danger'],
    ];

    public const TIPOS = [
        'preventivo' => ['label' => 'Preventivo', 'color' => 'primary', 'icon' => 'bi-shield-check'],
        'correctivo' => ['label' => 'Correctivo', 'color' => 'danger',  'icon' => 'bi-wrench'],
    ];

    public function __construct() {
        $this->db = Database::getConnection();
    }

    private function generarFolio(): string {
        $y    = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM mantenimientos WHERE YEAR(creado_en) = ?");
        $stmt->execute([$y]);
        return sprintf('MANT-%d-%04d', $y, (int)$stmt->fetchColumn() + 1);
    }

    private function generarToken(): string {
        return bin2hex(random_bytes(32));
    }

    public function create(array $d): int {
        $stmt = $this->db->prepare("
            INSERT INTO mantenimientos
                (folio, equipo_id, tipo, descripcion, fecha_programada, tecnico_id, creado_por, token_acceso)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $this->generarFolio(),
            $d['equipo_id'],
            $d['tipo'],
            $d['descripcion'] ?: null,
            $d['fecha_programada'],
            $d['tecnico_id'] ?: null,
            $d['creado_por'],
            $this->generarToken(),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getAll(array $f = []): array {
        $w = ['1=1']; $p = [];
        if (!empty($f['estado']))    { $w[] = 'm.estado = ?';    $p[] = $f['estado']; }
        if (!empty($f['tipo']))      { $w[] = 'm.tipo = ?';      $p[] = $f['tipo']; }
        if (!empty($f['equipo_id'])) { $w[] = 'm.equipo_id = ?'; $p[] = $f['equipo_id']; }
        if (!empty($f['buscar'])) {
            $w[] = '(m.folio LIKE ? OR e.codigo LIKE ? OR e.marca LIKE ? OR e.modelo LIKE ?)';
            $q   = '%' . $f['buscar'] . '%';
            $p   = array_merge($p, [$q, $q, $q, $q]);
        }
        $stmt = $this->db->prepare("
            SELECT m.*, e.codigo AS equipo_codigo, e.marca AS equipo_marca, e.modelo AS equipo_modelo,
                   c.nombre AS categoria_nombre, c.icono AS categoria_icono,
                   CONCAT(u.nombre,' ',u.apellido) AS nombre_creador,
                   CONCAT(t.nombre,' ',t.apellido) AS nombre_tecnico
            FROM mantenimientos m
            JOIN equipos e ON e.id = m.equipo_id
            JOIN categorias_equipo c ON c.id = e.categoria_id
            JOIN usuarios u ON u.id = m.creado_por
            LEFT JOIN usuarios t ON t.id = m.tecnico_id
            WHERE " . implode(' AND ', $w) . "
            ORDER BY FIELD(m.estado,'pendiente','en_proceso','completado','cancelado'),
                     m.fecha_programada ASC
        ");
        $stmt->execute($p);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("
            SELECT m.*, e.codigo AS equipo_codigo, e.marca AS equipo_marca, e.modelo AS equipo_modelo,
                   e.numero_serie AS equipo_serie, e.ubicacion AS equipo_ubicacion,
                   c.nombre AS categoria_nombre, c.icono AS categoria_icono,
                   CONCAT(u.nombre,' ',u.apellido) AS nombre_creador, u.email AS email_creador,
                   CONCAT(t.nombre,' ',t.apellido) AS nombre_tecnico, t.email AS email_tecnico
            FROM mantenimientos m
            JOIN equipos e ON e.id = m.equipo_id
            JOIN categorias_equipo c ON c.id = e.categoria_id
            JOIN usuarios u ON u.id = m.creado_por
            LEFT JOIN usuarios t ON t.id = m.tecnico_id
            WHERE m.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByToken(string $token): array|false {
        $stmt = $this->db->prepare("
            SELECT m.*, e.codigo AS equipo_codigo, e.marca AS equipo_marca, e.modelo AS equipo_modelo,
                   e.numero_serie AS equipo_serie, e.ubicacion AS equipo_ubicacion,
                   c.nombre AS categoria_nombre, c.icono AS categoria_icono,
                   CONCAT(u.nombre,' ',u.apellido) AS nombre_creador
            FROM mantenimientos m
            JOIN equipos e ON e.id = m.equipo_id
            JOIN categorias_equipo c ON c.id = e.categoria_id
            JOIN usuarios u ON u.id = m.creado_por
            WHERE m.token_acceso = ?
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function getByEquipo(int $equipoId): array {
        $stmt = $this->db->prepare("
            SELECT m.*, CONCAT(t.nombre,' ',t.apellido) AS nombre_tecnico
            FROM mantenimientos m
            LEFT JOIN usuarios t ON t.id = m.tecnico_id
            WHERE m.equipo_id = ? ORDER BY m.creado_en DESC
        ");
        $stmt->execute([$equipoId]);
        return $stmt->fetchAll();
    }

    public function updateEstado(int $id, string $estado, ?string $notas = null, ?string $fechaRealizada = null): bool {
        $stmt = $this->db->prepare("
            UPDATE mantenimientos SET estado = ?, notas = ?, fecha_realizada = ? WHERE id = ?
        ");
        return $stmt->execute([$estado, $notas, $fechaRealizada ?: null, $id]);
    }

    public function getStats(): array {
        return [
            'total'      => (int)$this->db->query("SELECT COUNT(*) FROM mantenimientos")->fetchColumn(),
            'pendientes' => (int)$this->db->query("SELECT COUNT(*) FROM mantenimientos WHERE estado='pendiente'")->fetchColumn(),
            'en_proceso' => (int)$this->db->query("SELECT COUNT(*) FROM mantenimientos WHERE estado='en_proceso'")->fetchColumn(),
            'completados'=> (int)$this->db->query("SELECT COUNT(*) FROM mantenimientos WHERE estado='completado'")->fetchColumn(),
            'vencidos'   => (int)$this->db->query("SELECT COUNT(*) FROM mantenimientos WHERE estado IN ('pendiente','en_proceso') AND fecha_programada < CURDATE()")->fetchColumn(),
        ];
    }
}
