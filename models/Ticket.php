<?php
require_once __DIR__ . '/../config/database.php';
class Ticket {
    private PDO $db;
    public const ESTADOS=['abierto'=>['label'=>'Abierto','color'=>'primary'],'en_proceso'=>['label'=>'En Proceso','color'=>'warning'],'en_espera'=>['label'=>'En Espera','color'=>'secondary'],'resuelto'=>['label'=>'Resuelto','color'=>'success'],'cerrado'=>['label'=>'Cerrado','color'=>'dark']];
    public const PRIORIDADES=['baja'=>['label'=>'Baja','color'=>'secondary'],'media'=>['label'=>'Media','color'=>'info'],'alta'=>['label'=>'Alta','color'=>'warning'],'critica'=>['label'=>'Crítica','color'=>'danger']];
    public const CATEGORIAS=['internet'=>['label'=>'Internet','icon'=>'bi-wifi'],'computadora'=>['label'=>'Computadora','icon'=>'bi-pc-display'],'impresora'=>['label'=>'Impresora','icon'=>'bi-printer'],'proyector'=>['label'=>'Proyector','icon'=>'bi-projector'],'red'=>['label'=>'Red','icon'=>'bi-hdd-network'],'software'=>['label'=>'Software','icon'=>'bi-window'],'hardware'=>['label'=>'Hardware','icon'=>'bi-cpu'],'otro'=>['label'=>'Otro','icon'=>'bi-tools']];
    public function __construct(){$this->db=Database::getConnection();}
    private function generateFolio():string{
        $y=date('Y');$s=$this->db->prepare("SELECT COUNT(*) FROM tickets WHERE YEAR(creado_en)=?");$s->execute([$y]);
        return sprintf('TKT-%d-%03d',$y,(int)$s->fetchColumn()+1);
    }
    public function create(array $d):int{
        $s=$this->db->prepare("INSERT INTO tickets (folio,titulo,descripcion,categoria,prioridad,ubicacion,usuario_id) VALUES (?,?,?,?,?,?,?)");
        $s->execute([$this->generateFolio(),$d['titulo'],$d['descripcion'],$d['categoria'],$d['prioridad']??'media',$d['ubicacion']??null,$d['usuario_id']]);
        $id=(int)$this->db->lastInsertId();$this->historial($id,$d['usuario_id'],null,'abierto','Ticket creado');return $id;
    }
    public function getById(int $id):array|false{
        $s=$this->db->prepare("SELECT t.*,CONCAT(u.nombre,' ',u.apellido) AS nombre_usuario,u.email AS email_usuario,u.departamento AS dept_usuario,CONCAT(te.nombre,' ',te.apellido) AS nombre_tecnico FROM tickets t JOIN usuarios u ON u.id=t.usuario_id LEFT JOIN usuarios te ON te.id=t.tecnico_id WHERE t.id=?");
        $s->execute([$id]);return $s->fetch();
    }
    public function getAll(array $f=[]):array{
        $w=['1=1'];$p=[];
        if(!empty($f['estado'])){$w[]='t.estado=?';$p[]=$f['estado'];}
        if(!empty($f['categoria'])){$w[]='t.categoria=?';$p[]=$f['categoria'];}
        if(!empty($f['prioridad'])){$w[]='t.prioridad=?';$p[]=$f['prioridad'];}
        if(!empty($f['buscar'])){$w[]='(t.titulo LIKE ? OR t.folio LIKE ? OR CONCAT(u.nombre," ",u.apellido) LIKE ?)';$q='%'.$f['buscar'].'%';$p=array_merge($p,[$q,$q,$q]);}
        $sql="SELECT t.id,t.folio,t.titulo,t.categoria,t.prioridad,t.estado,t.ubicacion,t.creado_en,t.actualizado_en,CONCAT(u.nombre,' ',u.apellido) AS nombre_usuario,CONCAT(te.nombre,' ',te.apellido) AS nombre_tecnico FROM tickets t JOIN usuarios u ON u.id=t.usuario_id LEFT JOIN usuarios te ON te.id=t.tecnico_id WHERE ".implode(' AND ',$w)." ORDER BY FIELD(t.prioridad,'critica','alta','media','baja'),FIELD(t.estado,'abierto','en_proceso','en_espera','resuelto','cerrado'),t.creado_en DESC";
        $s=$this->db->prepare($sql);$s->execute($p);return $s->fetchAll();
    }
    public function getByUser(int $uid,array $f=[]):array{
        $w=['t.usuario_id=?'];$p=[$uid];
        if(!empty($f['estado'])){$w[]='t.estado=?';$p[]=$f['estado'];}
        if(!empty($f['categoria'])){$w[]='t.categoria=?';$p[]=$f['categoria'];}
        $sql="SELECT t.id,t.folio,t.titulo,t.categoria,t.prioridad,t.estado,t.ubicacion,t.creado_en,t.actualizado_en,CONCAT(te.nombre,' ',te.apellido) AS nombre_tecnico FROM tickets t LEFT JOIN usuarios te ON te.id=t.tecnico_id WHERE ".implode(' AND ',$w)." ORDER BY t.creado_en DESC";
        $s=$this->db->prepare($sql);$s->execute($p);return $s->fetchAll();
    }
    public function updateEstado(int $id,string $est,int $uid,?string $nota=null,?int $tid=null,?string $res=null):bool{
        $t=$this->getById($id);if(!$t)return false;
        $ex='';$p=[$est];
        if($tid!==null){$ex.=',tecnico_id=?';$p[]=$tid;}
        if($res!==null){$ex.=',resolucion=?';$p[]=$res;}
        if(in_array($est,['cerrado','resuelto'])){$ex.=',cerrado_en=NOW()';}
        $p[]=$id;$s=$this->db->prepare("UPDATE tickets SET estado=? $ex WHERE id=?");$ok=$s->execute($p);
        if($ok)$this->historial($id,$uid,$t['estado'],$est,$nota);return $ok;
    }
    public function asignarTecnico(int $tid,int $techId,int $aid):bool{
        $ok=$this->db->prepare("UPDATE tickets SET tecnico_id=?,estado='en_proceso' WHERE id=?")->execute([$techId,$tid]);
        if($ok){$t=$this->getById($tid);$this->historial($tid,$aid,$t['estado']??'abierto','en_proceso','Técnico asignado');}
        return $ok;
    }
    public function getComentarios(int $tid,bool $admin=false):array{
        $sql="SELECT c.*,CONCAT(u.nombre,' ',u.apellido) AS nombre_usuario,u.rol FROM comentarios c JOIN usuarios u ON u.id=c.usuario_id WHERE c.ticket_id=?";
        if(!$admin)$sql.=" AND c.es_interno=0";$sql.=" ORDER BY c.creado_en ASC";
        $s=$this->db->prepare($sql);$s->execute([$tid]);return $s->fetchAll();
    }
    public function addComentario(int $tid,int $uid,string $msg,bool $interno=false):int{
        $this->db->prepare("INSERT INTO comentarios (ticket_id,usuario_id,mensaje,es_interno) VALUES (?,?,?,?)")->execute([$tid,$uid,$msg,$interno?1:0]);
        $this->db->prepare("UPDATE tickets SET actualizado_en=NOW() WHERE id=?")->execute([$tid]);
        return (int)$this->db->lastInsertId();
    }
    public function getHistorial(int $tid):array{
        $s=$this->db->prepare("SELECT h.*,CONCAT(u.nombre,' ',u.apellido) AS nombre_usuario FROM historial_estados h JOIN usuarios u ON u.id=h.usuario_id WHERE h.ticket_id=? ORDER BY h.creado_en ASC");
        $s->execute([$tid]);return $s->fetchAll();
    }
    private function historial(int $tid,int $uid,?string $ant,string $nuevo,?string $nota):void{
        $this->db->prepare("INSERT INTO historial_estados (ticket_id,usuario_id,estado_anterior,estado_nuevo,nota) VALUES (?,?,?,?,?)")->execute([$tid,$uid,$ant,$nuevo,$nota]);
    }
    public function getStats():array{
        $rows=$this->db->query("SELECT estado,COUNT(*) as total FROM tickets GROUP BY estado")->fetchAll();
        $s=['por_estado'=>array_column($rows,'total','estado')];
        $s['por_categoria']=$this->db->query("SELECT categoria,COUNT(*) as total FROM tickets GROUP BY categoria ORDER BY total DESC")->fetchAll();
        $rows=$this->db->query("SELECT prioridad,COUNT(*) as total FROM tickets GROUP BY prioridad")->fetchAll();
        $s['por_prioridad']=array_column($rows,'total','prioridad');
        $s['total']=(int)$this->db->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
        $s['semana']=(int)$this->db->query("SELECT COUNT(*) FROM tickets WHERE creado_en>=DATE_SUB(NOW(),INTERVAL 7 DAY)")->fetchColumn();
        return $s;
    }
    public function getStatsByUser(int $uid):array{
        $s=$this->db->prepare("SELECT estado,COUNT(*) as total FROM tickets WHERE usuario_id=? GROUP BY estado");
        $s->execute([$uid]);return array_column($s->fetchAll(),'total','estado');
    }
}
