<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Mesa de Ayuda' ?> — Centro de Cómputo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <?php if (isset($extraHead)) echo $extraHead; ?>
    <style>
        :root{--sidebar-w:260px;--navy:#0d1b2a;--navy-mid:#1b2e45;--navy-light:#243c58;--accent:#00c2ff;--accent2:#0077ff;--surface:#f0f4f9;--card-bg:#ffffff;--text-main:#0d1b2a;--text-muted:#6b7c93;--border:#dde4ef;--font-head:'Syne',sans-serif;--font-body:'DM Sans',sans-serif;}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:var(--font-body);background:var(--surface);color:var(--text-main);min-height:100vh;}
        #sidebar{position:fixed;top:0;left:0;width:var(--sidebar-w);height:100vh;background:var(--navy);display:flex;flex-direction:column;z-index:1000;transition:transform .3s ease;}
        .sidebar-brand{padding:1.6rem 1.5rem 1.2rem;border-bottom:1px solid var(--navy-light);}
        .sidebar-brand .logo-icon{width:38px;height:38px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:10px;display:grid;place-items:center;font-size:1.1rem;color:#fff;margin-bottom:.6rem;}
        .sidebar-brand .brand-name{font-family:var(--font-head);font-size:.95rem;font-weight:800;color:#fff;line-height:1.2;letter-spacing:-.01em;}
        .sidebar-brand .brand-sub{font-size:.72rem;color:var(--accent);font-weight:500;letter-spacing:.08em;text-transform:uppercase;}
        .sidebar-nav{flex:1;padding:1rem 0;overflow-y:auto;}
        .nav-section-label{font-size:.65rem;font-weight:700;color:#4a6278;letter-spacing:.12em;text-transform:uppercase;padding:.8rem 1.5rem .3rem;}
        .sidebar-link{display:flex;align-items:center;gap:.75rem;padding:.62rem 1.5rem;color:#8ba5be;text-decoration:none;font-size:.88rem;font-weight:500;border-left:3px solid transparent;transition:all .18s;}
        .sidebar-link:hover{color:#fff;background:var(--navy-mid);border-left-color:var(--accent2);}
        .sidebar-link.active{color:#fff;background:var(--navy-light);border-left-color:var(--accent);}
        .sidebar-link i{font-size:1rem;width:20px;text-align:center;}
        .sidebar-user{padding:1rem 1.5rem;border-top:1px solid var(--navy-light);display:flex;align-items:center;gap:.8rem;}
        .user-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:grid;place-items:center;font-size:.85rem;color:#fff;font-weight:700;flex-shrink:0;}
        .user-info .user-name{font-size:.82rem;font-weight:600;color:#fff;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px;}
        .user-info .user-role{font-size:.7rem;color:var(--accent);text-transform:capitalize;font-weight:500;}
        #main-content{margin-left:var(--sidebar-w);min-height:100vh;display:flex;flex-direction:column;}
        .topbar{background:var(--card-bg);border-bottom:1px solid var(--border);padding:.85rem 2rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:500;}
        .topbar-title{font-family:var(--font-head);font-size:1.1rem;font-weight:700;color:var(--text-main);}
        .page-body{padding:2rem;flex:1;}
        .card{border:1px solid var(--border);border-radius:14px;box-shadow:0 2px 12px rgba(13,27,42,.06);}
        .card-header{background:transparent;border-bottom:1px solid var(--border);padding:1rem 1.25rem;font-family:var(--font-head);font-weight:700;}
        .stat-card{background:var(--card-bg);border:1px solid var(--border);border-radius:14px;padding:1.3rem 1.5rem;display:flex;align-items:flex-start;gap:1rem;box-shadow:0 2px 10px rgba(13,27,42,.05);transition:transform .2s,box-shadow .2s;}
        .stat-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(13,27,42,.1);}
        .stat-icon{width:48px;height:48px;border-radius:12px;display:grid;place-items:center;font-size:1.3rem;flex-shrink:0;}
        .stat-value{font-family:var(--font-head);font-size:1.9rem;font-weight:800;line-height:1;color:var(--text-main);}
        .stat-label{font-size:.8rem;color:var(--text-muted);font-weight:500;margin-top:.2rem;}
        .badge{font-size:.72rem;font-weight:600;padding:.3em .7em;border-radius:6px;}
        .table th{font-family:var(--font-head);font-size:.78rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--text-muted);border-bottom:2px solid var(--border);padding:.75rem 1rem;}
        .table td{padding:.85rem 1rem;vertical-align:middle;font-size:.88rem;}
        .table tbody tr:hover{background:#f7faff;}
        .form-control,.form-select{border-radius:9px;border-color:var(--border);font-size:.88rem;transition:border-color .2s,box-shadow .2s;}
        .form-control:focus,.form-select:focus{border-color:var(--accent2);box-shadow:0 0 0 3px rgba(0,119,255,.15);}
        .form-label{font-size:.83rem;font-weight:600;color:var(--text-main);margin-bottom:.35rem;}
        .btn-primary{background:var(--accent2);border-color:var(--accent2);font-weight:600;border-radius:9px;}
        .btn-primary:hover{background:#005ee0;border-color:#005ee0;}
        .btn-outline-primary{border-color:var(--accent2);color:var(--accent2);border-radius:9px;font-weight:600;}
        .alert{border-radius:10px;font-size:.88rem;}
        .timeline{position:relative;padding-left:1.8rem;}
        .timeline::before{content:'';position:absolute;left:.6rem;top:0;bottom:0;width:2px;background:var(--border);}
        .timeline-item{position:relative;padding-bottom:1.2rem;}
        .timeline-item::before{content:'';position:absolute;left:-1.25rem;top:.3rem;width:10px;height:10px;border-radius:50%;background:var(--accent2);border:2px solid #fff;box-shadow:0 0 0 2px var(--accent2);}
        .timeline-date{font-size:.72rem;color:var(--text-muted);font-weight:500;}
        .timeline-text{font-size:.84rem;color:var(--text-main);}
        .comment-bubble{background:#f7faff;border:1px solid var(--border);border-radius:12px;padding:.85rem 1rem;margin-bottom:.75rem;}
        .comment-bubble.internal{background:#fff9e6;border-color:#ffe58a;}
        .comment-meta{font-size:.75rem;color:var(--text-muted);margin-bottom:.35rem;}
        .comment-meta strong{color:var(--text-main);}
        .folio-badge{font-family:var(--font-head);font-size:.8rem;font-weight:700;color:var(--accent2);background:rgba(0,119,255,.08);padding:.2em .6em;border-radius:6px;white-space:nowrap;}
        @media(max-width:768px){#sidebar{transform:translateX(-100%);}#sidebar.show{transform:translateX(0);}#main-content{margin-left:0;}}
    </style>
</head>
<body>
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon"><i class="bi bi-pc-display"></i></div>
        <div class="brand-name">Mesa de Ayuda</div>
        <div class="brand-sub">Centro de Cómputo</div>
    </div>
    <div class="sidebar-nav">
        <?php $c = $_GET['c'] ?? 'dashboard'; $a = $_GET['a'] ?? 'index'; $rol = $_SESSION['user_rol'] ?? ''; ?>
        <span class="nav-section-label">General</span>
        <a href="index.php?c=dashboard" class="sidebar-link <?= $c==='dashboard'?'active':'' ?>"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <span class="nav-section-label">Tickets</span>
        <a href="index.php?c=tickets&a=create" class="sidebar-link <?= $c==='tickets'&&$a==='create'?'active':'' ?>"><i class="bi bi-plus-circle"></i> Nuevo Ticket</a>
        <a href="index.php?c=tickets" class="sidebar-link <?= $c==='tickets'&&$a!=='create'?'active':'' ?>"><i class="bi bi-ticket-detailed"></i> <?= in_array($rol,['admin','tecnico'])?'Todos los Tickets':'Mis Tickets' ?></a>
        <?php if (in_array($rol,['admin','tecnico'])): ?>
        <span class="nav-section-label">Filtros rápidos</span>
        <a href="index.php?c=tickets&estado=abierto" class="sidebar-link"><i class="bi bi-circle text-primary"></i> Abiertos</a>
        <a href="index.php?c=tickets&estado=en_proceso" class="sidebar-link"><i class="bi bi-arrow-repeat text-warning"></i> En Proceso</a>
        <a href="index.php?c=tickets&estado=resuelto" class="sidebar-link"><i class="bi bi-check-circle text-success"></i> Resueltos</a>
        <?php endif; ?>
        <?php if ($rol==='admin'): ?>
        <span class="nav-section-label">Administración</span>
        <a href="index.php?c=inventario" class="sidebar-link <?= $c==='inventario'?'active':'' ?>"><i class="bi bi-box-seam"></i> Inventario</a>
        <a href="index.php?c=asignaciones" class="sidebar-link <?= $c==='asignaciones'?'active':'' ?>"><i class="bi bi-file-earmark-check"></i> Asignaciones</a>
        <a href="index.php?c=mantenimiento" class="sidebar-link <?= $c==='mantenimiento'?'active':'' ?>"><i class="bi bi-tools"></i> Mantenimiento</a>
        <a href="index.php?c=bajas" class="sidebar-link <?= $c==='bajas'?'active':'' ?>"><i class="bi bi-trash3"></i> Bajas</a>
        <a href="index.php?c=users" class="sidebar-link <?= $c==='users'&&$a!=='profile'&&$a!=='register'?'active':'' ?>"><i class="bi bi-people"></i> Usuarios</a>
        <?php endif; ?>
        <span class="nav-section-label">Mi Cuenta</span>
        <a href="index.php?c=users&a=profile" class="sidebar-link <?= $c==='users'&&$a==='profile'?'active':'' ?>"><i class="bi bi-person-circle"></i> Mi Perfil</a>
    </div>
    <div class="sidebar-user">
        <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_name']??'U',0,1)) ?></div>
        <div class="user-info" style="min-width:0">
            <div class="user-name"><?= htmlspecialchars($_SESSION['user_name']??'') ?></div>
            <div class="user-role"><?= htmlspecialchars($rol) ?></div>
        </div>
        <a href="index.php?c=auth&a=logout" class="ms-auto text-decoration-none" title="Cerrar sesión"><i class="bi bi-box-arrow-right text-secondary" style="font-size:1.1rem"></i></a>
    </div>
</nav>
<div id="main-content">
    <div class="topbar">
        <button class="btn btn-sm d-md-none me-2" id="sidebarToggle" style="border:none"><i class="bi bi-list fs-5"></i></button>
        <span class="topbar-title"><?= $pageTitle ?? 'Dashboard' ?></span>
        <div class="d-flex align-items-center gap-3">
            <a href="index.php?c=tickets&a=create" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i>Nuevo Ticket</a>
        </div>
    </div>
    <div class="page-body">
