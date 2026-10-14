<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Ingreso;
use App\Services\{AuthService, ProyeccionSummary};

final class ProyeccionController
{
    public function __construct(private AuthService $auth, private Ingreso $model,
        private View $view, private array $config) {}

    public function index(): void
    {
        $user = $this->auth->user();
        if (!$user) { \redirect($this->config,'login'); }
        $raw = \input($_GET,'id');
        $id = preg_match('/^[1-9][0-9]{0,9}$/D',$raw) ? (int)$raw : 0;
        $profile = $this->model->profileFor($id,(int)$user['_ID']);
        if (!$profile) { http_response_code(404); echo 'Registro no encontrado.'; return; }
        $concepts = $this->model->concepts($profile);
        $movements = $this->model->movements($id,(int)$user['_ID']);
        $today = date('Y-m-d');
        $summary = ProyeccionSummary::build($concepts,$movements,$today);
        $this->view->render('proyeccion/index',compact('user','profile','today','summary'));
    }
}
