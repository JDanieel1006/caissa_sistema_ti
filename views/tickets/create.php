<?php
$pageTitle='Nuevo Ticket';require __DIR__.'/../layouts/header.php';
$success=null;
?>
<div class="row justify-content-center"><div class="col-lg-8">
<?php if($error):?><div class="alert alert-danger d-flex gap-2 mb-4"><i class="bi bi-exclamation-triangle-fill"></i><?=htmlspecialchars($error)?></div><?php endif;?>
<div class="card"><div class="card-header"><i class="bi bi-plus-circle me-2 text-primary"></i>Reportar un Problema</div>
<div class="card-body p-4">
<form method="POST" action="index.php?c=tickets&a=create" id="fCreate">
  <div class="mb-3"><label class="form-label">Título <span class="text-danger">*</span></label><input type="text" name="titulo" class="form-control" placeholder="Ej. Sin conexión a internet en aula 3B" value="<?=htmlspecialchars($_POST['titulo']??'')?>" required maxlength="200"></div>
  <div class="row g-3 mb-3">
    <div class="col-md-6"><label class="form-label">Categoría <span class="text-danger">*</span></label><select name="categoria" class="form-select" required><option value="">— Selecciona —</option><?php foreach($categorias as $k=>$c):?><option value="<?=$k?>" <?=(($_POST['categoria']??'')===$k)?'selected':''?>><?=$c['label']?></option><?php endforeach;?></select></div>
    <div class="col-md-6"><label class="form-label">Prioridad</label><select name="prioridad" class="form-select"><?php foreach($prioridades as $k=>$p):?><option value="<?=$k?>" <?=(($_POST['prioridad']??'media')===$k)?'selected':''?>><?=$p['label']?></option><?php endforeach;?></select></div>
  </div>
  <div class="mb-3"><label class="form-label">Ubicación / Aula</label><input type="text" name="ubicacion" class="form-control" placeholder="Ej. Laboratorio, Aula 2A..." value="<?=htmlspecialchars($_POST['ubicacion']??'')?>" maxlength="150"></div>
  <div class="mb-4"><label class="form-label">Descripción detallada <span class="text-danger">*</span></label><textarea name="descripcion" class="form-control" rows="5" required placeholder="Describe el problema con el mayor detalle posible..."><?=htmlspecialchars($_POST['descripcion']??'')?></textarea></div>
  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary px-4" onclick="this.disabled=true;this.innerHTML='<i class=\'bi bi-hourglass-split me-2\'></i>Enviando...';this.form.submit()"><i class="bi bi-send me-2"></i>Enviar Ticket</button>
    <a href="index.php?c=dashboard" class="btn btn-outline-secondary px-4">Cancelar</a>
  </div>
</form>
</div></div>
</div></div>
<?php require __DIR__.'/../layouts/footer.php';?>
