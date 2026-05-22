<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Session;
use App\Models\LoginAttempt;
use App\Models\User;

final class AuthController extends BaseController
{
    public function loginForm(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('/admin/dashboard');
        }
        $this->render('admin/login', [], 'admin');
    }

    public function login(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/admin/login');
        }

        $db = Database::getInstance();
        $userModel = new User($db);
        $loginAttemptModel = new LoginAttempt($db);

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if ($loginAttemptModel->isRateLimited($ipAddress)) {
            Session::setFlash('error', 'Demasiados intentos. Intenta de nuevo en 15 minutos.');
            $this->redirect('/admin/login');
        }

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Correo y contraseña son obligatorios.');
            $this->redirect('/admin/login');
        }

        $user = $userModel->verifyPassword($email, $password);

        if (!$user) {
            $loginAttemptModel->record($ipAddress, $email, false);
            Session::setFlash('error', 'Credenciales incorrectas.');
            $this->redirect('/admin/login');
        }

        $loginAttemptModel->record($ipAddress, $email, true);
        $loginAttemptModel->clearAttempts($ipAddress);

        Session::regenerate();
        Session::set('user_id', (int)$user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role']);

        $userModel->updateLastLogin((int)$user['id']);

        Session::setFlash('success', 'Bienvenido, ' . $user['name'] . '!');
        $this->redirect('/admin/dashboard');
    }

    public function logout(): void
    {
        Session::destroy();
        $this->redirect('/admin/login');
    }
}
