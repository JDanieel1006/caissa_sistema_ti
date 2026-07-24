<?php
$pageTitle='Usuarios';require __DIR__.'/../layouts/header.php';
?>
<?php if($success):?><div class="alert alert-success d-flex gap-2 mb-3"><i class="bi bi-check-circle-fill"></i>Operación completada correctamente.</div><?php endif;?>
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between"><span><i class="bi bi-people me-2 text-primary"></i>Usuarios <span class="badge bg-primary ms-1"><?=count($users)?></span></span><a href="index.php?c=users&a=create" class="btn btn-sm btn-primary"><i class="bi bi-person-plus me-1"></i>Nuevo</a></div>
  <div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Departamento</th><th>Estado</th><th>Alta</th><th style="width:80px"></th></tr></thead>
    <tbody>
    <?php foreach($users as $u):?>
    <tr>
      <td><strong><?=htmlspecialchars($u['nombre'].' '.$u['apellido'])?></strong></td>
      <td style="font-size:.85rem"><?=htmlspecialchars($u['email'])?></td>
      <td><span class="badge bg-<?=['admin'=>'danger','tecnico'=>'warning','maestro'=>'info'][$u['rol']]??"secondary"?>"><?=ucfirst($u['rol'])?></span></td>
      <td style="font-size:.85rem"><?=htmlspecialchars($u['departamento']??'—')?></td>
      <td><span class="badge bg-<?=$u['activo']?'success':'secondary'?>"><?=$u['activo']?'Activo':'Inactivo'?></span></td>
      <td style="font-size:.8rem;color:#6b7c93"><?=date('d/m/Y',strtotime($u['creado_en']))?></td>
      <td>
        <a href="index.php?c=users&a=edit&id=<?=$u['id']?>" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="bi bi-pencil"></i></a>
        <?php if($u['id']!=$_SESSION['user_id']):?>
        <a href="index.php?c=users&a=toggle&id=<?=$u['id']?>" class="btn btn-sm btn-outline-<?=$u['activo']?'danger':'success'?>" title="<?=$u['activo']?'Desactivar':'Activar'?>" onclick="return confirm('¿Confirmas esta acción?')"><i class="bi bi-<?=$u['activo']?'ban':'check-circle'?>"></i></a>
        <?php endif;?>
      </td>
    </tr>
    <?php endforeach;?>
    </tbody>
  </table></div>
</div>
<?php require __DIR__.'/../layouts/footer.php';?>
