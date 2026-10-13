<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\{AuthService, Csrf, LoginLimiter};

final class AuthController
{
    public function __construct(
        private AuthService $auth,
        private View $view,
        private LoginLimiter $limiter,
        private array $config,
        private \App\Models\Installation $installation,
    ) {}

    public function login(): void
    {
        if ($this->installation->needed()) {
            redirect($this->config, 'instalacion');
        }
        if ($this->auth->user()) {
            redirect($this->config, 'inicio');
        }
        $error = '';
        $username = trim(input($_POST, 'username'));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::valid(input($_POST, '_csrf_token'))) {
                http_response_code(419);
                $error = 'La sesión del formulario venció. Intenta nuevamente.';
            } else {
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                $retry = $this->limiter->consume('ip|' . $ip, 30);
                if ($retry === 0) {
                    $retry = $this->limiter->consume('user|' . $ip . '|' . strtolower($username), 10);
                }
                if ($retry > 0) {
                    http_response_code(429);
                    header('Retry-After: ' . $retry);
                    $error = 'Demasiados intentos. Espera ' . (int) ceil($retry / 60) . ' minutos.';
                } elseif ($this->auth->attempt($username, input($_POST, 'password'))) {
                    redirect($this->config, 'inicio');
                } else {
                    http_response_code(401);
                    $error = 'Usuario o contraseña incorrectos.';
                }
            }
        }
        $this->view->render('auth/login', compact('error', 'username'));
    }

    public function logout(): void
    {
        if (!Csrf::valid(input($_POST, '_csrf_token'))) {
            http_response_code(419);
            echo 'Token de seguridad inválido. Recarga la página.';
            return;
        }
        $this->auth->logout();
        redirect($this->config, 'login');
    }
}
