<?php
require_once __DIR__ . '/../config/database.php';
class Asignacion {
    private PDO $db;
    public const ESTADOS=['activa'=>['label'=>'Activa','color'=>'success'],'devuelta'=>['label'=>'Devuelta','color'=>'secondary'],'cancelada'=>['label'=>'Cancelada','color'=>'danger']];
    public const CONDICIONES=['bueno'=>['label'=>'Bueno','color'=>'success'],'dañado'=>['label'=>'Dañado','color'=>'danger'],'en_reparacion'=>['label'=>'En Reparación','color'=>'warning']];
    public function __construct(){$this->db=Database::getConnection();}
    public function getAll(array $f=[]):array{
        $w=['1=1'];$p=[];
        if(!empty($f['estado'])){$w[]='a.estado=?';$p[]=$f['estado'];}
        if(!empty($f['usuario_id'])){$w[]='a.usuario_id=?';$p[]=$f['usuario_id'];}
        if(!empty($f['buscar'])){$w[]='(a.folio LIKE ? OR CONCAT(u.nombre," ",u.apellido) LIKE ? OR e.codigo LIKE ? OR a.nombre_obra LIKE ? OR a.numero_contrato LIKE ?)';$q='%'.$f['buscar'].'%';$p=array_merge($p,[$q,$q,$q,$q,$q]);}
        $s=$this->db->prepare("SELECT a.*,CONCAT(u.nombre,' ',u.apellido) AS nombre_usuario,u.email AS email_usuario,u.departamento AS dept_usuario,u.rol AS rol_usuario,CONCAT(ad.nombre,' ',ad.apellido) AS nombre_admin,e.codigo AS equipo_codigo,e.marca AS equipo_marca,e.modelo AS equipo_modelo,c.nombre AS categoria_nombre,c.icono AS categoria_icono,ie.id AS img_id FROM asignaciones a JOIN usuarios u ON u.id=a.usuario_id JOIN usuarios ad ON ad.id=a.entregado_por JOIN equipos e ON e.id=a.equipo_id JOIN categorias_equipo c ON c.id=e.categoria_id LEFT JOIN imagenes_equipo ie ON ie.equipo_id=e.id AND ie.es_principal=1 WHERE ".implode(' AND ',$w)." ORDER BY a.creado_en DESC");
        $s->execute($p);return $s->fetchAll();
    }
    public function getById(int $id):array|false{
        $s=$this->db->prepare("SELECT a.*,CONCAT(u.nombre,' ',u.apellido) AS nombre_usuario,u.email AS email_usuario,u.departamento AS dept_usuario,u.rol AS rol_usuario,CONCAT(ad.nombre,' ',ad.apellido) AS nombre_admin,ad.email AS email_admin,e.codigo AS equipo_codigo,e.marca AS equipo_marca,e.modelo AS equipo_modelo,e.numero_serie AS equipo_serie,e.ubicacion AS equipo_ubicacion,e.notas AS equipo_notas,c.nombre AS categoria_nombre,c.icono AS categoria_icono,ie.id AS img_id FROM asignaciones a JOIN usuarios u ON u.id=a.usuario_id JOIN usuarios ad ON ad.id=a.entregado_por JOIN equipos e ON e.id=a.equipo_id JOIN categorias_equipo c ON c.id=e.categoria_id LEFT JOIN imagenes_equipo ie ON ie.equipo_id=e.id AND ie.es_principal=1 WHERE a.id=?");
        $s->execute([$id]);return $s->fetch();
    }
    public function getByEquipo(int $eid):array{$s=$this->db->prepare("SELECT a.*,CONCAT(u.nombre,' ',u.apellido) AS nombre_usuario FROM asignaciones a JOIN usuarios u ON u.id=a.usuario_id WHERE a.equipo_id=? ORDER BY a.creado_en DESC");$s->execute([$eid]);return $s->fetchAll();}
    public function getActivaByEquipo(int $eid):array|false{$s=$this->db->prepare("SELECT a.*,CONCAT(u.nombre,' ',u.apellido) AS nombre_usuario FROM asignaciones a JOIN usuarios u ON u.id=a.usuario_id WHERE a.equipo_id=? AND a.estado='activa' LIMIT 1");$s->execute([$eid]);return $s->fetch();}
    public function equipoDisponible(int $eid):bool{return $this->getActivaByEquipo($eid)===false;}
    public function create(array $d):int{
        $folio=$this->folio();
        $s=$this->db->prepare("INSERT INTO asignaciones (equipo_id,usuario_id,entregado_por,folio,condicion_entrega,fecha_asignacion,fecha_devolucion_esperada,notas_entrega,nombre_obra,numero_contrato) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $s->execute([$d['equipo_id'],$d['usuario_id'],$d['entregado_por'],$folio,$d['condicion_entrega'],$d['fecha_asignacion'],$d['fecha_devolucion_esperada']?:null,$d['notas_entrega']?:null,$d['nombre_obra']?:null,$d['numero_contrato']?:null]);
        return(int)$this->db->lastInsertId();
    }
    public function registrarDevolucion(int $id,array $d):bool{
        return $this->db->prepare("UPDATE asignaciones SET estado='devuelta',condicion_devolucion=?,fecha_devolucion_real=?,notas_devolucion=? WHERE id=?")->execute([$d['condicion_devolucion'],$d['fecha_devolucion_real'],$d['notas_devolucion']?:null,$id]);
    }
    public function cancelar(int $id):bool{return $this->db->prepare("UPDATE asignaciones SET estado='cancelada' WHERE id=?")->execute([$id]);}
    public function getStats():array{
        return['total'=>(int)$this->db->query("SELECT COUNT(*) FROM asignaciones")->fetchColumn(),'activas'=>(int)$this->db->query("SELECT COUNT(*) FROM asignaciones WHERE estado='activa'")->fetchColumn(),'devueltas'=>(int)$this->db->query("SELECT COUNT(*) FROM asignaciones WHERE estado='devuelta'")->fetchColumn(),'vencidas'=>(int)$this->db->query("SELECT COUNT(*) FROM asignaciones WHERE estado='activa' AND fecha_devolucion_esperada IS NOT NULL AND fecha_devolucion_esperada<CURDATE()")->fetchColumn()];
    }
    private function folio():string{$y=date('Y');$s=$this->db->prepare("SELECT COUNT(*) FROM asignaciones WHERE YEAR(creado_en)=?");$s->execute([$y]);return sprintf('ASIG-%d-%04d',$y,(int)$s->fetchColumn()+1);}
}
