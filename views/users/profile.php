<?php
$pageTitle='Mi Perfil';require __DIR__.'/../layouts/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-7">
<?php if($error):?><div class="alert alert-danger d-flex gap-2 mb-3"><i class="bi bi-exclamation-triangle-fill"></i><?=htmlspecialchars($error)?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex gap-2 mb-3"><i class="bi bi-check-circle-fill"></i><?=htmlspecialchars($success)?></div><?php endif;?>
<!-- Info -->
<div class="card mb-4"><div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Información Personal</div>
<div class="card-body p-4">
<form method="POST">
  <input type="hidden" name="action" value="update_info">
  <div class="row g-3 mb-3">
    <div class="col-6"><label class="form-label">Nombre *</label><input type="text" name="nombre" class="form-control" value="<?=htmlspecialchars($user['nombre'])?>" required></div>
    <div class="col-6"><label class="form-label">Apellido *</label><input type="text" name="apellido" class="form-control" value="<?=htmlspecialchars($user['apellido'])?>" required></div>
  </div>
  <div class="mb-3"><label class="form-label">Correo</label><input type="email" class="form-control" value="<?=htmlspecialchars($user['email'])?>" disabled><div class="form-text">El correo no se puede cambiar desde el perfil.</div></div>
  <div class="mb-4"><label class="form-label">Departamento</label><input type="text" name="departamento" class="form-control" value="<?=htmlspecialchars($user['departamento']??'')?>"></div>
  <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar</button>
</form></div></div>
<!-- Contraseña -->
<div class="card"><div class="card-header"><i class="bi bi-lock me-2 text-warning"></i>Cambiar Contraseña</div>
<div class="card-body p-4">
<form method="POST">
  <input type="hidden" name="action" value="change_password">
  <div class="mb-3"><label class="form-label">Contraseña actual *</label><input type="password" name="password_actual" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Nueva contraseña *</label><input type="password" name="password_nueva" id="pn" class="form-control" required minlength="6"></div>
  <div class="mb-4"><label class="form-label">Confirmar nueva *</label><input type="password" name="password_nueva2" id="pn2" class="form-control" required></div>
  <button type="submit" class="btn btn-warning" style="color:#000"><i class="bi bi-lock me-1"></i>Cambiar Contraseña</button>
</form></div></div>
</div></div>
<script>const p1=document.getElementById('pn'),p2=document.getElementById('pn2');p2?.addEventListener('input',()=>p2.setCustomValidity(p2.value!==p1.value?'No coinciden':''));</script>
<?php require __DIR__.'/../layouts/footer.php';?>
