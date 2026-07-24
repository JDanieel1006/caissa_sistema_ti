<?php
require_once __DIR__ . '/../config/database.php';
class Equipo {
    private PDO $db;
    public const ESTADOS = [
        'bueno'         => ['label' => 'Bueno',         'color' => 'success'],
        'usado'         => ['label' => 'Usado',         'color' => 'info'],
        'dañado'        => ['label' => 'Dañado',        'color' => 'danger'],
        'en_reparacion' => ['label' => 'En Reparación', 'color' => 'warning'],
        'dado_de_baja'  => ['label' => 'Dado de Baja',  'color' => 'dark'],
    ];
    public function __construct(){$this->db=Database::getConnection();}
    public function getCategorias():array{return $this->db->query("SELECT * FROM categorias_equipo WHERE activa=1 ORDER BY nombre")->fetchAll();}
    public function getCategoriaById(int $id):array|false{$s=$this->db->prepare("SELECT * FROM categorias_equipo WHERE id=?");$s->execute([$id]);return $s->fetch();}
    public function getCamposCategoria(int $cid):array{$s=$this->db->prepare("SELECT * FROM campos_categoria WHERE categoria_id=? ORDER BY orden");$s->execute([$cid]);return $s->fetchAll();}
    public function getAll(array $f=[]):array{
        $w=['1=1'];$p=[];
        if(!empty($f['categoria_id'])){$w[]='e.categoria_id=?';$p[]=$f['categoria_id'];}
        if(!empty($f['estado'])){$w[]='e.estado=?';$p[]=$f['estado'];}
        if(!empty($f['buscar'])){$w[]='(e.codigo LIKE ? OR e.marca LIKE ? OR e.modelo LIKE ? OR e.numero_serie LIKE ? OR e.ubicacion LIKE ? OR e.direccion_ip LIKE ? OR e.direccion_mac LIKE ?)';$q='%'.$f['buscar'].'%';$p=array_merge($p,[$q,$q,$q,$q,$q,$q,$q]);}
        $s=$this->db->prepare("SELECT e.id,e.categoria_id,e.codigo,e.marca,e.modelo,e.numero_serie,e.direccion_mac,e.direccion_ip,e.usuario_pc,e.contrasena_pc,e.ubicacion,e.estado,e.notas,e.fecha_compra,e.creado_por,e.creado_en,e.actualizado_en,c.nombre AS categoria_nombre,c.icono AS categoria_icono,ie.id AS img_principal_id,ie.nombre_archivo AS img_principal FROM equipos e JOIN categorias_equipo c ON c.id=e.categoria_id LEFT JOIN imagenes_equipo ie ON ie.equipo_id=e.id AND ie.es_principal=1 WHERE ".implode(' AND ',$w)." ORDER BY c.nombre,e.codigo");
        $s->execute($p);return $s->fetchAll();
    }
    public function getById(int $id):array|false{
        $s=$this->db->prepare("SELECT e.id,e.categoria_id,e.codigo,e.marca,e.modelo,e.numero_serie,e.direccion_mac,e.direccion_ip,e.usuario_pc,e.contrasena_pc,e.ubicacion,e.estado,e.notas,e.fecha_compra,e.creado_por,e.creado_en,e.actualizado_en,c.nombre AS categoria_nombre,c.icono AS categoria_icono FROM equipos e JOIN categorias_equipo c ON c.id=e.categoria_id WHERE e.id=?");
        $s->execute([$id]);return $s->fetch();
    }
    public function getEspecificaciones(int $eid):array{
        $s=$this->db->prepare("SELECT cc.nombre_campo,cc.etiqueta,ee.valor FROM especificaciones_equipo ee JOIN campos_categoria cc ON cc.id=ee.campo_id WHERE ee.equipo_id=? ORDER BY cc.orden");
        $s->execute([$eid]);return $s->fetchAll();
    }
    public function create(array $d,array $sp=[]):int{
        $s=$this->db->prepare("INSERT INTO equipos (categoria_id,codigo,marca,modelo,numero_serie,direccion_mac,direccion_ip,usuario_pc,contrasena_pc,ubicacion,estado,notas,fecha_compra,creado_por) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $s->execute([$d['categoria_id'],$d['codigo'],$d['marca']?:null,$d['modelo']?:null,$d['numero_serie']?:null,$d['direccion_mac']?:null,$d['direccion_ip']?:null,$d['usuario_pc']?:null,$d['contrasena_pc']?:null,$d['ubicacion']?:null,$d['estado'],$d['notas']?:null,$d['fecha_compra']?:null,$d['creado_por']]);
        $id=(int)$this->db->lastInsertId();$this->saveSpecs($id,$sp);return $id;
    }
    public function update(int $id,array $d,array $sp=[]):bool{
        $ok=$this->db->prepare("UPDATE equipos SET categoria_id=?,marca=?,modelo=?,numero_serie=?,direccion_mac=?,direccion_ip=?,usuario_pc=?,contrasena_pc=?,ubicacion=?,estado=?,notas=?,fecha_compra=? WHERE id=?")->execute([$d['categoria_id'],$d['marca']?:null,$d['modelo']?:null,$d['numero_serie']?:null,$d['direccion_mac']?:null,$d['direccion_ip']?:null,$d['usuario_pc']?:null,$d['contrasena_pc']?:null,$d['ubicacion']?:null,$d['estado'],$d['notas']?:null,$d['fecha_compra']?:null,$id]);
        if($ok)$this->saveSpecs($id,$sp);return $ok;
    }
    public function delete(int $id):bool{
        $stmt=$this->db->prepare("SELECT COUNT(*) FROM asignaciones WHERE equipo_id=?");
        $stmt->execute([$id]);
        if((int)$stmt->fetchColumn()>0) throw new RuntimeException('Este equipo tiene asignaciones registradas y no puede eliminarse.');
        return $this->db->prepare("DELETE FROM equipos WHERE id=?")->execute([$id]);
    }
    private function saveSpecs(int $eid,array $sp):void{
        if(empty($sp))return;
        $s=$this->db->prepare("INSERT INTO especificaciones_equipo (equipo_id,campo_id,valor) VALUES (?,?,?) ON DUPLICATE KEY UPDATE valor=VALUES(valor)");
        foreach($sp as $cid=>$v){if($v!==null&&$v!=='')$s->execute([$eid,(int)$cid,$v]);}
    }
    public function generateCodigo(int $cid):string{
        $cat=$this->getCategoriaById($cid);$pre=strtoupper(substr(preg_replace('/[^a-zA-Z]/','', $cat['nombre']??'EQ'),0,3));
        $s=$this->db->prepare("SELECT COUNT(*) FROM equipos WHERE categoria_id=?");$s->execute([$cid]);
        return sprintf('%s-%04d',$pre,(int)$s->fetchColumn()+1);
    }
    public function getStats():array{
        return['total'=>(int)$this->db->query("SELECT COUNT(*) FROM equipos")->fetchColumn(),'buenos'=>(int)$this->db->query("SELECT COUNT(*) FROM equipos WHERE estado='bueno'")->fetchColumn(),'dañados'=>(int)$this->db->query("SELECT COUNT(*) FROM equipos WHERE estado='dañado'")->fetchColumn(),'baja'=>(int)$this->db->query("SELECT COUNT(*) FROM equipos WHERE estado='dado_de_baja'")->fetchColumn(),'reparacion'=>(int)$this->db->query("SELECT COUNT(*) FROM equipos WHERE estado='en_reparacion'")->fetchColumn(),'por_categoria'=>$this->db->query("SELECT c.nombre,c.icono,COUNT(e.id) as total FROM categorias_equipo c LEFT JOIN equipos e ON e.categoria_id=c.id WHERE c.activa=1 GROUP BY c.id ORDER BY total DESC")->fetchAll()];
    }
    public function codigoExiste(string $c,int $ex=0):bool{$s=$this->db->prepare("SELECT COUNT(*) FROM equipos WHERE codigo=? AND id!=?");$s->execute([$c,$ex]);return(int)$s->fetchColumn()>0;}
}
