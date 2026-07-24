<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Registro — Mesa de Ayuda</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{font-family:'DM Sans',sans-serif;background:#0d1b2a;min-height:100vh;display:grid;place-items:center;}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 80% 60% at 70% 20%,rgba(0,119,255,.18) 0%,transparent 60%);pointer-events:none;}
.wrap{background:#fff;border-radius:20px;width:min(520px,95vw);padding:2.5rem 2.8rem;position:relative;z-index:1;box-shadow:0 30px 80px rgba(0,0,0,.45);}
.title{font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;margin:.8rem 0 .2rem;}
.form-control,.form-select{border-radius:9px;border-color:#dde4ef;font-size:.88rem;}
.form-control:focus,.form-select:focus{border-color:#0077ff;box-shadow:0 0 0 3px rgba(0,119,255,.15);}
.form-label{font-size:.82rem;font-weight:600;margin-bottom:.3rem;}
.btn-register{background:#0077ff;color:#fff;border:none;border-radius:10px;padding:.72rem;font-family:'Syne',sans-serif;font-weight:700;font-size:.95rem;width:100%;transition:background .2s;}
.btn-register:hover{background:#005ee0;}
.success-box{text-align:center;padding:2rem 1rem;}
</style></head><body>
<div class="wrap">
  <a href="index.php?c=auth&a=login" style="font-size:.83rem;color:#0077ff;text-decoration:none"><i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión</a>
  <?php if (isset($success) && $success === true): ?>
  <div class="success-box">
    <i class="bi bi-check-circle-fill text-success" style="font-size:3.5rem"></i>
    <h5 class="mt-2 mb-1" style="font-family:'Syne',sans-serif;font-weight:800">¡Registro exitoso!</h5>
    <p style="font-size:.87rem;color:#6b7c93;margin-bottom:1.5rem">Tu cuenta ha sido creada. Ya puedes iniciar sesión.</p>
    <a href="index.php?c=auth&a=login" class="btn btn-primary" style="border-radius:9px;font-weight:700"><i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión</a>
  </div>
  <?php else: ?>
  <div class="title">Crear cuenta</div>
  <div style="font-size:.87rem;color:#6b7c93;margin-bottom:1.8rem">Registro para maestros del plantel</div>
  <?php if ($error): ?><div class="alert alert-danger d-flex gap-2 mb-3"><i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="POST" action="index.php?c=users&a=register">
    <div class="row g-3 mb-3">
      <div class="col-6"><label class="form-label">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($_POST['nombre']??'') ?>" required maxlength="100"></div>
      <div class="col-6"><label class="form-label">Apellido <span class="text-danger">*</span></label><input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($_POST['apellido']??'') ?>" required maxlength="100"></div>
    </div>
    <div class="mb-3"><label class="form-label">Correo <span class="text-danger">*</span></label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email']??'') ?>" required></div>
    <div class="mb-3"><label class="form-label">Materia / Departamento</label><input type="text" name="departamento" class="form-control" value="<?= htmlspecialchars($_POST['departamento']??'') ?>" placeholder="Ej. Matemáticas..."></div>
    <div class="row g-3 mb-4">
      <div class="col-6"><label class="form-label">Contraseña <span class="text-danger">*</span></label><input type="password" name="password" id="pw1" class="form-control" required minlength="6"></div>
      <div class="col-6"><label class="form-label">Confirmar <span class="text-danger">*</span></label><input type="password" name="password2" id="pw2" class="form-control" required></div>
    </div>
    <button type="submit" class="btn-register"><i class="bi bi-person-plus me-2"></i>Crear mi cuenta</button>
    <p class="text-center mt-3 mb-0" style="font-size:.82rem;color:#6b7c93">¿Ya tienes cuenta? <a href="index.php?c=auth&a=login" style="color:#0077ff;font-weight:600">Inicia sesión</a></p>
  </form>
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>const p1=document.getElementById('pw1'),p2=document.getElementById('pw2');if(p2)p2.addEventListener('input',()=>p2.setCustomValidity(p2.value!==p1.value?'Las contraseñas no coinciden':''));</script>
</body></html>