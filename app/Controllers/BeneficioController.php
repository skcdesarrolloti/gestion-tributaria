<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\{AuthService, BeneficioCatalog};

final class BeneficioController
{
    public function __construct(private AuthService $auth, private View $view, private array $config) {}

    public function index(): void
    {
        $user=$this->auth->user();
        if (!$user) { \redirect($this->config,'login'); }
        $year=\input($_GET,'anio','2026');
        $group=\input($_GET,'grupo');
        $query=trim(\input($_GET,'buscar'));
        $groups=BeneficioCatalog::groups();
        if (!in_array($year,['2025','2026'],true) || ($group!=='' && !isset($groups[$group])) || mb_strlen($query)>100) {
            http_response_code(422); echo 'Revisa el año, el grupo o la búsqueda (máximo 100 caracteres).'; return;
        }
        $parameters=BeneficioCatalog::year($year);
        $items=BeneficioCatalog::filter($group,$query);
        $this->view->render('beneficios/index',compact('user','year','group','query','groups','parameters','items'));
    }
}
