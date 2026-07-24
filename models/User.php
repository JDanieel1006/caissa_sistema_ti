<?php
require_once __DIR__ . '/../config/database.php';
class User {
    private PDO $db;
    public function __construct() { $this->db = Database::getConnection(); }
    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]); return $stmt->fetch();
    }
    public function findById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT id,nombre,apellido,email,password,rol,departamento,activo,creado_en FROM usuarios WHERE id = ?");
        $stmt->execute([$id]); return $stmt->fetch();
    }
    public function getAll(): array {
        return $this->db->query("SELECT id,nombre,apellido,email,rol,departamento,activo,creado_en FROM usuarios ORDER BY nombre")->fetchAll();
    }
    public function getTechnicians(): array {
        $stmt = $this->db->prepare("SELECT id,nombre,apellido FROM usuarios WHERE rol IN ('tecnico','admin') AND activo=1 ORDER BY nombre");
        $stmt->execute(); return $stmt->fetchAll();
    }
    public function getAdminsYTecnicos(): array {
        $stmt = $this->db->prepare("SELECT id,nombre,apellido,email FROM usuarios WHERE rol IN ('admin','tecnico') AND activo=1");
        $stmt->execute(); return $stmt->fetchAll();
    }
    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO usuarios (nombre,apellido,email,password,rol,departamento) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$data['nombre'],$data['apellido'],$data['email'],password_hash($data['password'],PASSWORD_BCRYPT),$data['rol'],$data['departamento']??null]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $data): bool {
        $fields=[]; $values=[];
        foreach(['nombre','apellido','email','rol','departamento','activo'] as $f) { if(isset($data[$f])){$fields[]="$f=?";$values[]=$data[$f];} }
        if(!$fields) return false; $values[]=$id;
        return $this->db->prepare("UPDATE usuarios SET ".implode(',',$fields)." WHERE id=?")->execute($values);
    }
    public function changePassword(int $id, string $pw): bool {
        return $this->db->prepare("UPDATE usuarios SET password=? WHERE id=?")->execute([password_hash($pw,PASSWORD_BCRYPT),$id]);
    }
    public function countByRole(): array {
        return $this->db->query("SELECT rol,COUNT(*) as total FROM usuarios WHERE activo=1 GROUP BY rol")->fetchAll();
    }
}
