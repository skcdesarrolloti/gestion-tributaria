<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\AuthService;

final class HomeController
{
    public function __construct(private AuthService $auth, private View $view, private array $config) {}

    public function index(): void
    {
        $this->show('home/index');
    }

    public function profile(): void
    {
        $this->show('home/profile');
    }

    private function show(string $template): void
    {
        $user = $this->auth->user();
        if (!$user) {
            redirect($this->config, 'login');
        }
        $this->view->render($template, compact('user'));
    }
}
