<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\AuthService;

final class SeguridadSocialController
{
    public function __construct(private AuthService $auth, private View $view, private array $config) {}

    public function index(): void
    {
        $user=$this->auth->user();
        if (!$user) { \redirect($this->config,'login'); }
        $this->view->render('seguridad-social/index',compact('user'));
    }
}
