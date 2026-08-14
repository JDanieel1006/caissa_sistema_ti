<?php
$pageTitle = 'Nuevo Usuario';
require __DIR__ . '/../layouts/header.php';
$roles = [
    'auxiliar_administrativo' => 'Auxiliar Administrativo',
    'coordinador'             => 'Coordinador',
    'operario'                => 'Operario',
    'ayudante'                => 'Ayudante',
    'residente_becario'       => 'Residente/Becario',
    'auxiliar_seguridad'      => 'Auxiliar de Seguridad',
    'auxiliar_oficina'        => 'Auxiliar de Oficina',
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
<div class="card">
    <div class="card-header"><i class="bi bi-person-plus me-2 text-primary"></i>Registrar Usuario</div>
    <div class="card-body p-4">
    <form method="POST" action="index.php?c=users&a=create">
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
            </div>
            <div class="col-6">
                <label class="form-label">Apellido *</label>
                <input type="text" name="apellido" class="form-control"
                       value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo *</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-6">
                <label class="form-label">Puesto / Rol *</label>
                <select name="rol" class="form-select">
                    <?php foreach ($roles as $val => $lbl): ?>
                    <option value="<?= $val ?>" <?= (($_POST['rol'] ?? '') === $val) ? 'selected' : '' ?>>
                        <?= $lbl ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6">
                <label class="form-label">Departamento</label>
                <input type="text" name="departamento" class="form-control"
                       value="<?= htmlspecialchars($_POST['departamento'] ?? '') ?>">
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-person-plus me-1"></i>Registrar
            </button>
            <a href="index.php?c=users" class="btn btn-outline-secondary px-4">Cancelar</a>
        </div>
    </form>
    </div>
</div>
</div></div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
