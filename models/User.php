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
    public function searchActive(string $q='',int $page=1,int $perPage=20):array {
        $page=max(1,$page);$perPage=max(1,min(50,$perPage));$offset=($page-1)*$perPage;$limit=$perPage+1;
        $p=[];$w=['activo=1'];
        if($q!==''){$w[]='(nombre LIKE ? OR apellido LIKE ? OR email LIKE ? OR departamento LIKE ? OR rol LIKE ?)';$like='%'.$q.'%';$p=[$like,$like,$like,$like,$like];}
        $sql="SELECT id,nombre,apellido,email,rol,departamento,activo,creado_en FROM usuarios WHERE ".implode(' AND ',$w)." ORDER BY nombre,apellido LIMIT $limit OFFSET $offset";
        $stmt=$this->db->prepare($sql);$stmt->execute($p);$rows=$stmt->fetchAll();
        return ['items'=>array_slice($rows,0,$perPage),'more'=>count($rows)>$perPage];
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
