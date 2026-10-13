<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Installation;
use App\Services\{Csrf, LoginLimiter};

final class SetupController
{
    public function __construct(
        private Installation $installation,
        private View $view,
        private LoginLimiter $limiter,
        private array $config,
    ) {}

    public function index(): void
    {
        if (!$this->installation->needed()) {
            http_response_code(404);
            echo 'La instalación inicial ya se completó.';
            return;
        }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = $this->validate();
            if ($error === '') {
                $this->installation->createAdministrator(
                    trim(input($_POST, 'name')), trim(input($_POST, 'username')), input($_POST, 'password')
                );
                redirect($this->config, 'login');
            }
        }
        $this->view->render('auth/setup', compact('error'));
    }

    private function validate(): string
    {
        if (!Csrf::valid(input($_POST, '_csrf_token'))) {
            http_response_code(419);
            return 'Formulario vencido. Intenta nuevamente.';
        }
        $retry = $this->limiter->consume('setup|' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 10);
        if ($retry > 0) {
            http_response_code(429);
            header('Retry-After: ' . $retry);
            return 'Demasiados intentos. Intenta en 15 minutos.';
        }
        $key = (string) $this->config['setup_key'];
        if (strlen($key) < 32 || !hash_equals($key, input($_POST, 'setup_key'))) {
            http_response_code(403);
            return 'Clave de instalación incorrecta o pendiente de configurar.';
        }
        $username = trim(input($_POST, 'username'));
        $name = trim(input($_POST, 'name'));
        $password = input($_POST, 'password');
        if (!preg_match('/^[a-zA-Z0-9._-]{3,100}$/D', $username) || $name === '' || mb_strlen($name) > 150) {
            http_response_code(422);
            return 'Revisa el nombre y el usuario (3 a 100 letras simples, números, punto, guion o guion bajo).';
        }
        if (strlen($password) < 12 || strlen($password) > 72 || $password !== input($_POST, 'confirmation')) {
            http_response_code(422);
            return 'Usa una contraseña de 12 a 72 bytes y repítela exactamente.';
        }
        return '';
    }
}
