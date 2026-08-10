<?php
$pageTitle = 'Editar Usuario';
require __DIR__ . '/../layouts/header.php';

$roles = [
    'auxiliar_administrativo' => 'Auxiliar Administrativo',
    'coordinador'             => 'Coordinador',
    'operario'                => 'Operario',
    'ayudante'                => 'Ayudante',
    'residente_becario'       => 'Residente/Becario',
    'control_de_obra'         => 'Control de Obra',
    'supervisor_seguridad'    => 'Supervisor de Seguridad',
    'contra_incendios'        => 'Contra Incendios',
    'tecnico_instrumentista'  => 'Técnico Instrumentista',
    'admin'                   => 'Administrador',
];
?>
<div class="row justify-content-center"><div class="col-lg-7">
<?php if ($error): ?>
<div class="alert alert-danger d-flex gap-2 mb-3">
  <i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>
<?php if ($success): ?>
<div class="alert alert-success d-flex gap-2 mb-3">
  <i class="bi bi-check-circle-fill"></i><?= htmlspecialchars($success) ?>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <i class="bi bi-pencil me-2 text-primary"></i>Editar: <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>
  </div>
  <div class="card-body p-4">
    <form method="POST" action="index.php?c=users&a=edit&id=<?= $user['id'] ?>">
      <div class="row g-3 mb-3">
        <div class="col-6">
          <label class="form-label">Nombre *</label>
          <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($user['nombre']) ?>" required>
        </div>
        <div class="col-6">
          <label class="form-label">Apellido *</label>
          <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($user['apellido']) ?>" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Correo *</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-6">
          <label class="form-label">Rol</label>
          <select name="rol" class="form-select">
            <?php foreach ($roles as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= $user['rol'] === $val ? 'selected' : '' ?>>
              <?= $lbl ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6">
          <label class="form-label">Departamento</label>
          <input type="text" name="departamento" class="form-control" value="<?= htmlspecialchars($user['departamento'] ?? '') ?>">
        </div>
      </div>

      <div class="form-check mb-3">
        <input type="checkbox" name="activo" class="form-check-input" id="chkActivo" <?= $user['activo'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="chkActivo">Usuario activo</label>
      </div>

      <hr>
      <div class="mb-4">
        <label class="form-label">Nueva contraseña <span class="text-muted" style="font-weight:400">(dejar vacío para no cambiar)</span></label>
        <input type="password" name="password_nueva" class="form-control" minlength="6">
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
        <a href="index.php?c=users" class="btn btn-outline-secondary px-4">Cancelar</a>
      </div>
    </form>
  </div>
</div>
</div></div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
