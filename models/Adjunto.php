<?php
require_once __DIR__ . '/../config/database.php';
class Adjunto {
    private PDO $db;
    public const TIPOS_PERMITIDOS=['image/jpeg','image/jpg','image/png','image/gif','image/webp','application/pdf'];
    public const EXTENSIONES_PERMITIDAS=['jpg','jpeg','png','gif','webp','pdf'];
    public const MAX_BYTES=5*1024*1024;
    public const UPLOAD_DIR=__DIR__.'/../uploads/tickets/';
    public function __construct(){$this->db=Database::getConnection();}
    public function subir(int $tid,int $uid,array $f):int{
        if($f['error']!==UPLOAD_ERR_OK)throw new RuntimeException($this->errMsg($f['error']));
        if($f['size']>self::MAX_BYTES)throw new RuntimeException('Archivo supera 5 MB.');
        $finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($f['tmp_name']);
        if(!in_array($mime,self::TIPOS_PERMITIDOS))throw new RuntimeException('Tipo no permitido.');
        $ext=strtolower(pathinfo($f['name'],PATHINFO_EXTENSION));
        if(!in_array($ext,self::EXTENSIONES_PERMITIDAS))throw new RuntimeException('Extensión no permitida.');
        if(!is_dir(self::UPLOAD_DIR))mkdir(self::UPLOAD_DIR,0755,true);
        $nd=sprintf('%s_%s.%s',uniqid('adj_',true),bin2hex(random_bytes(4)),$ext);
        if(!move_uploaded_file($f['tmp_name'],self::UPLOAD_DIR.$nd))throw new RuntimeException('No se pudo guardar.');
        $s=$this->db->prepare("INSERT INTO adjuntos (ticket_id,usuario_id,nombre_archivo,nombre_original,tipo_mime,tamano) VALUES (?,?,?,?,?,?)");
        $s->execute([$tid,$uid,$nd,$f['name'],$mime,$f['size']]);return (int)$this->db->lastInsertId();
    }
    public function getByTicket(int $tid):array{
        $s=$this->db->prepare("SELECT a.id AS adjunto_id,a.ticket_id,a.usuario_id,a.nombre_archivo,a.nombre_original,a.tipo_mime,a.tamano,a.creado_en,CONCAT(u.nombre,' ',u.apellido) AS nombre_usuario FROM adjuntos a JOIN usuarios u ON u.id=a.usuario_id WHERE a.ticket_id=? ORDER BY a.creado_en ASC");
        $s->execute([$tid]);return $s->fetchAll();
    }
    public function getById(int $id):array|false{$s=$this->db->prepare("SELECT * FROM adjuntos WHERE id=?");$s->execute([$id]);return $s->fetch();}
    public function eliminar(int $id):bool{
        $a=$this->getById($id);if(!$a)return false;
        $r=self::UPLOAD_DIR.$a['nombre_archivo'];if(file_exists($r))unlink($r);
        return $this->db->prepare("DELETE FROM adjuntos WHERE id=?")->execute([$id]);
    }
    public function puedeEliminar(int $id,int $uid,string $rol):bool{
        $a=$this->getById($id);if(!$a)return false;
        return $a['usuario_id']===$uid||in_array($rol,['admin','tecnico']);
    }
    public static function formatBytes(int $b):string{
        if($b>=1048576)return round($b/1048576,1).' MB';if($b>=1024)return round($b/1024,0).' KB';return $b.' B';
    }
    private function errMsg(int $c):string{
        return match($c){UPLOAD_ERR_INI_SIZE,UPLOAD_ERR_FORM_SIZE=>'Archivo demasiado grande.',UPLOAD_ERR_PARTIAL=>'Subida incompleta.',UPLOAD_ERR_NO_FILE=>'Sin archivo.',default=>'Error al subir.'};
    }
}
