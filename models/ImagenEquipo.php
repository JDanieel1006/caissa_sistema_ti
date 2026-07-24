<?php
require_once __DIR__ . '/../config/database.php';
class ImagenEquipo {
    private PDO $db;
    public const TIPOS_PERMITIDOS=['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
    public const EXTENSIONES_OK=['jpg','jpeg','png','gif','webp'];
    public const MAX_BYTES=5*1024*1024;
    public const MAX_IMAGENES=8;
    public const UPLOAD_DIR=__DIR__.'/../uploads/inventario/';
    public function __construct(){$this->db=Database::getConnection();}
    public function subir(int $eid,array $files):array{
        $res=[];$lista=[];
        if(is_array($files['name'])){foreach($files['name'] as $i=>$n){if($files['error'][$i]===UPLOAD_ERR_NO_FILE)continue;$lista[]=['name'=>$n,'tmp_name'=>$files['tmp_name'][$i],'error'=>$files['error'][$i],'size'=>$files['size'][$i]];}}
        else{if($files['error']!==UPLOAD_ERR_NO_FILE)$lista[]=$files;}
        $cnt=$this->contarImagenes($eid);
        foreach($lista as $f){
            if($cnt>=self::MAX_IMAGENES){$res[]=['ok'=>false,'msg'=>'Límite alcanzado.'];break;}
            try{$id=$this->subirUna($eid,$f,$cnt===0);$cnt++;$res[]=['ok'=>true,'id'=>$id];}
            catch(RuntimeException $e){$res[]=['ok'=>false,'msg'=>$e->getMessage()];}
        }
        return $res;
    }
    private function subirUna(int $eid,array $f,bool $principal):int{
        if($f['error']!==UPLOAD_ERR_OK)throw new RuntimeException('Error al subir.');
        if($f['size']>self::MAX_BYTES)throw new RuntimeException('Imagen supera 5MB.');
        $finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($f['tmp_name']);
        if(!in_array($mime,self::TIPOS_PERMITIDOS))throw new RuntimeException('Solo imágenes.');
        $ext=strtolower(pathinfo($f['name'],PATHINFO_EXTENSION));
        if(!in_array($ext,self::EXTENSIONES_OK))throw new RuntimeException('Extensión no permitida.');
        if(!is_dir(self::UPLOAD_DIR))mkdir(self::UPLOAD_DIR,0755,true);
        $nd=sprintf('img_%s_%s.%s',$eid,uniqid('',true),$ext);
        if(!move_uploaded_file($f['tmp_name'],self::UPLOAD_DIR.$nd))throw new RuntimeException('No se pudo guardar.');
        $s=$this->db->prepare("INSERT INTO imagenes_equipo (equipo_id,nombre_archivo,nombre_original,tamano,es_principal) VALUES (?,?,?,?,?)");
        $s->execute([$eid,$nd,$f['name'],$f['size'],$principal?1:0]);return(int)$this->db->lastInsertId();
    }
    public function getByEquipo(int $eid):array{$s=$this->db->prepare("SELECT * FROM imagenes_equipo WHERE equipo_id=? ORDER BY es_principal DESC,creado_en ASC");$s->execute([$eid]);return $s->fetchAll();}
    public function getById(int $id):array|false{$s=$this->db->prepare("SELECT * FROM imagenes_equipo WHERE id=?");$s->execute([$id]);return $s->fetch();}
    public function setPrincipal(int $id,int $eid):bool{
        $this->db->prepare("UPDATE imagenes_equipo SET es_principal=0 WHERE equipo_id=?")->execute([$eid]);
        return $this->db->prepare("UPDATE imagenes_equipo SET es_principal=1 WHERE id=? AND equipo_id=?")->execute([$id,$eid]);
    }
    public function eliminar(int $id):bool{
        $img=$this->getById($id);if(!$img)return false;
        $r=self::UPLOAD_DIR.$img['nombre_archivo'];if(file_exists($r))unlink($r);
        $this->db->prepare("DELETE FROM imagenes_equipo WHERE id=?")->execute([$id]);
        if($img['es_principal']){$s=$this->db->prepare("SELECT id FROM imagenes_equipo WHERE equipo_id=? LIMIT 1");$s->execute([$img['equipo_id']]);$n=$s->fetch();if($n)$this->setPrincipal($n['id'],$img['equipo_id']);}
        return true;
    }
    public function contarImagenes(int $eid):int{$s=$this->db->prepare("SELECT COUNT(*) FROM imagenes_equipo WHERE equipo_id=?");$s->execute([$eid]);return(int)$s->fetchColumn();}
    public static function formatBytes(int $b):string{if($b>=1048576)return round($b/1048576,1).' MB';if($b>=1024)return round($b/1024,0).' KB';return $b.' B';}
}
