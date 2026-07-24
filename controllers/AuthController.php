<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private User $userModel;
    public function __construct() { $this->userModel = new User(); }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email']    ?? '');
            $password = trim($_POST['password'] ?? '');
            $error    = null;
            if (empty($email) || empty($password)) {
                $error = 'Por favor completa todos los campos.';
            } else {
                $user = $this->userModel->findByEmail($email);
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id']    = $user['id'];
                    $_SESSION['user_name']  = $user['nombre'].' '.$user['apellido'];
                    $_SESSION['user_rol']   = $user['rol'];
                    $_SESSION['user_email'] = $user['email'];
                    header('Location: index.php?c=dashboard'); exit;
                } else {
                    $error = 'Correo o contraseña incorrectos.';
                }
            }
            require __DIR__ . '/../views/auth/login.php';
        } else {
            $error = null;
            require __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout(): void {
        session_destroy();
        header('Location: index.php?c=auth&a=login');
        exit;
    }
}
