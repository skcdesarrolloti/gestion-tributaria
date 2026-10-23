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
        $page=filter_var(\input($_GET,'pagina','1'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>1000]]);
        if ($page===false) { http_response_code(422); echo 'Revisa el número de página.'; return; }
        $parameters=BeneficioCatalog::year($year);
        $showItems=$group!=='' || $query!=='';
        $found=$showItems?BeneficioCatalog::filter($group,$query):[];
        $total=count($found); $pages=max(1,(int)ceil($total/9)); $page=min($page,$pages);
        $items=array_slice($found,($page-1)*9,9);
        $counts=array_count_values(array_column(BeneficioCatalog::all(),'group'));
        $this->view->render('beneficios/index',compact('user','year','group','query','groups','parameters','items','showItems','total','pages','page','counts'));
    }
    public function article(): void
    {
        if (!$this->auth->user()) { \redirect($this->config,'login'); }
        header('Content-Type: application/json; charset=UTF-8');
        $article=\App\Services\NormaCatalog::read(\input($_GET,'articulo'));
        if (!$article) { http_response_code(404);echo json_encode(['error'=>'Artículo no disponible en la colección de consulta.']);return; }
        echo json_encode($article,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
    }
}
