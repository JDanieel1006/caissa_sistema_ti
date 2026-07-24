<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private User $userModel;
    public function __construct() { $this->userModel = new User(); }

    public function register(): void {
        if (!empty($_SESSION['user_id'])) { header('Location: index.php?c=dashboard'); exit; }
        $error = null; $success = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre    = trim($_POST['nombre']       ?? '');
            $apellido  = trim($_POST['apellido']     ?? '');
            $email     = trim($_POST['email']        ?? '');
            $password  = $_POST['password']          ?? '';
            $password2 = $_POST['password2']         ?? '';
            $depto     = trim($_POST['departamento'] ?? '');
            if (!$nombre || !$apellido || !$email || !$password) { $error = 'Todos los campos marcados son obligatorios.'; }
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))  { $error = 'Correo no válido.'; }
            elseif (strlen($password) < 6)                       { $error = 'Contraseña mínimo 6 caracteres.'; }
            elseif ($password !== $password2)                    { $error = 'Las contraseñas no coinciden.'; }
            elseif ($this->userModel->findByEmail($email))       { $error = 'Ya existe una cuenta con ese correo.'; }
            else {
                $this->userModel->create(['nombre'=>$nombre,'apellido'=>$apellido,'email'=>$email,'password'=>$password,'rol'=>'auxiliar_administrativo','departamento'=>$depto ?: null]);
                $success = true;
            }
        }
        require __DIR__ . '/../views/auth/register.php';
    }

    public function profile(): void {
        $this->requireAuth();
        $user = $this->userModel->findById($_SESSION['user_id']);
        $error = null; $success = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'update_info') {
                $nombre = trim($_POST['nombre'] ?? ''); $apellido = trim($_POST['apellido'] ?? '');
                if (!$nombre || !$apellido) { $error = 'Nombre y apellido son obligatorios.'; }
                else {
                    $this->userModel->update($_SESSION['user_id'], ['nombre'=>$nombre,'apellido'=>$apellido,'departamento'=>trim($_POST['departamento']??'') ?: null]);
                    $_SESSION['user_name'] = $nombre.' '.$apellido;
                    $success = 'Información actualizada.';
                    $user = $this->userModel->findById($_SESSION['user_id']);
                }
            } elseif ($action === 'change_password') {
                $actual = $_POST['password_actual'] ?? ''; $nueva = $_POST['password_nueva'] ?? ''; $nueva2 = $_POST['password_nueva2'] ?? '';
                if (!password_verify($actual, $user['password'])) { $error = 'Contraseña actual incorrecta.'; }
                elseif (strlen($nueva) < 6)  { $error = 'Nueva contraseña mínimo 6 caracteres.'; }
                elseif ($nueva !== $nueva2)   { $error = 'Las contraseñas no coinciden.'; }
                else { $this->userModel->changePassword($_SESSION['user_id'], $nueva); $success = 'Contraseña cambiada.'; }
            }
        }
        require __DIR__ . '/../views/users/profile.php';
    }

    public function index(): void {
        $this->requireAdmin();
        $users   = $this->userModel->getAll();
        $success = $_GET['msg'] ?? null;
        require __DIR__ . '/../views/users/list.php';
    }

    public function create(): void {
        $this->requireAdmin();
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre   = trim($_POST['nombre']       ?? '');
            $apellido = trim($_POST['apellido']     ?? '');
            $email    = trim($_POST['email']        ?? '');
            $rol      = $_POST['rol']               ?? 'auxiliar_administrativo';
            $depto    = trim($_POST['departamento'] ?? '');

            if (!$nombre || !$apellido || !$email) {
                $error = 'Nombre, apellido y correo son obligatorios.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Correo no válido.';
            } elseif ($this->userModel->findByEmail($email)) {
                $error = 'Ya existe un usuario con ese correo.';
            } else {
                // Contraseña generada internamente, el usuario no tiene acceso al sistema
                $password = bin2hex(random_bytes(16));
                $this->userModel->create([
                    'nombre'       => $nombre,
                    'apellido'     => $apellido,
                    'email'        => $email,
                    'password'     => $password,
                    'rol'          => $rol,
                    'departamento' => $depto ?: null,
                ]);
                header('Location: index.php?c=users&msg=creado');
                exit;
            }
        }
        require __DIR__ . '/../views/users/create.php';
    }

    public function edit(): void {
        $this->requireAdmin();
        $id   = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);
        if (!$user) { header('Location: index.php?c=users'); exit; }
        $error = null; $success = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre   = trim($_POST['nombre']   ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $email    = trim($_POST['email']    ?? '');
            $rol      = $_POST['rol']           ?? $user['rol'];
            if (!$nombre || !$apellido || !$email) { $error = 'Nombre, apellido y correo son obligatorios.'; }
            else {
                $this->userModel->update($id, ['nombre'=>$nombre,'apellido'=>$apellido,'email'=>$email,'rol'=>$rol,'departamento'=>trim($_POST['departamento']??'') ?: null,'activo'=>isset($_POST['activo'])?1:0]);
                $success = 'Usuario actualizado.';
                $user = $this->userModel->findById($id);
            }
        }
        require __DIR__ . '/../views/users/edit.php';
    }

    public function toggle(): void {
        $this->requireAdmin();
        $id   = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);
        if ($user && $user['id'] != $_SESSION['user_id'])
            $this->userModel->update($id, ['activo' => $user['activo'] ? 0 : 1]);
        header('Location: index.php?c=users');
        exit;
    }

    private function requireAuth(): void {
        if (empty($_SESSION['user_id'])) { header('Location: index.php?c=auth&a=login'); exit; }
    }
    private function requireAdmin(): void {
        $this->requireAuth();
        if ($_SESSION['user_rol'] !== 'admin') { header('Location: index.php?c=dashboard'); exit; }
    }
}
