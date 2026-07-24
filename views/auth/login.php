<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Iniciar Sesión — Mesa de Ayuda</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root{--navy:#0d1b2a;--acc2:#0077ff;}
body{font-family:'DM Sans',sans-serif;background:var(--navy);min-height:100vh;display:grid;place-items:center;}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 80% 60% at 70% 20%,rgba(0,119,255,.18) 0%,transparent 60%),radial-gradient(ellipse 60% 80% at 10% 80%,rgba(0,194,255,.12) 0%,transparent 60%);pointer-events:none;}
.login-wrap{display:grid;grid-template-columns:1fr 1fr;width:min(900px,95vw);min-height:520px;border-radius:20px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.5);position:relative;z-index:1;}
.panel-left{background:linear-gradient(145deg,var(--acc2) 0%,#0044bb 60%,#1b2e45 100%);padding:3rem 2.5rem;display:flex;flex-direction:column;justify-content:space-between;color:#fff;}
.panel-left .title{font-family:'Syne',sans-serif;font-size:1.9rem;font-weight:800;line-height:1.2;letter-spacing:-.02em;}
.panel-left .title span{color:#00c2ff;}
.features li{display:flex;align-items:center;gap:.7rem;font-size:.84rem;opacity:.85;margin-bottom:.5rem;list-style:none;}
.features li i{color:#00c2ff;}
.panel-right{background:#fff;padding:3rem 2.5rem;display:flex;flex-direction:column;justify-content:center;}
.login-title{font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;margin-bottom:.25rem;}
.login-sub{font-size:.88rem;color:#6b7c93;margin-bottom:2rem;}
.form-floating .form-control{border-radius:10px;border-color:#dde4ef;font-size:.9rem;}
.form-floating .form-control:focus{border-color:var(--acc2);box-shadow:0 0 0 3px rgba(0,119,255,.15);}
.btn-login{background:var(--acc2);color:#fff;border:none;border-radius:10px;padding:.75rem;font-family:'Syne',sans-serif;font-weight:700;font-size:.95rem;width:100%;transition:background .2s;}
.btn-login:hover{background:#005ee0;}
.demo-creds{background:#f0f4f9;border-radius:10px;padding:.75rem 1rem;margin-top:1.5rem;font-size:.78rem;color:#6b7c93;}
.demo-creds strong{color:#0d1b2a;display:block;margin-bottom:.25rem;}
.alert{border-radius:10px;font-size:.85rem;}
@media(max-width:640px){.login-wrap{grid-template-columns:1fr;}.panel-left{display:none;}}
</style>
</head>
<body>
<div class="login-wrap">
  <div class="panel-left">
    <div>
      <div style="background:rgba(255,255,255,.2);display:inline-block;padding:8px 14px;border-radius:8px;margin-bottom:1rem"><i class="bi bi-pc-display" style="font-size:1.3rem"></i></div>
      <div style="font-size:.7rem;opacity:.75;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.3rem">Mesa de Ayuda</div>
      <div style="font-size:.85rem;font-weight:700;margin-bottom:1.5rem">Centro de Cómputo</div>
    </div>
    <div>
      <div class="title mb-3">Soporte técnico<br><span>rápido y</span><br>organizado.</div>
      <p style="font-size:.83rem;opacity:.75;margin-bottom:1.2rem">Reporta problemas de tecnología y da seguimiento en tiempo real.</p>
      <ul class="features ps-0">
        <li><i class="bi bi-check-circle-fill"></i> Tickets con seguimiento de estado</li>
        <li><i class="bi bi-check-circle-fill"></i> Asignación a técnicos</li>
        <li><i class="bi bi-check-circle-fill"></i> Historial y comentarios</li>
        <li><i class="bi bi-check-circle-fill"></i> Inventario de equipos</li>
      </ul>
    </div>
    <div style="font-size:.72rem;opacity:.5">© <?php echo date('Y') ?> TI - CAISSA</div>
  </div>
  <div class="panel-right">
    <div class="login-title">Bienvenido</div>
    <div class="login-sub">Ingresa tus credenciales para continuar</div>
    <?php if (!empty($error)): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-3"><i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="index.php?c=auth&a=login">
      <div class="form-floating mb-3">
        <input type="email" name="email" id="email" class="form-control" placeholder="correo" value="<?= htmlspecialchars($_POST['email']??'') ?>" required>
        <label for="email"><i class="bi bi-envelope me-1"></i>Correo electrónico</label>
      </div>
      <div class="form-floating mb-4">
        <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
        <label for="password"><i class="bi bi-lock me-1"></i>Contraseña</label>
      </div>
      <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión</button>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body></html>