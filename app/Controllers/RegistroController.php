<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\View;
use App\Models\{Contribuyente,Ingreso};
use App\Services\{AuthService,RegistroNavegacion,IngresoSummary};

final class RegistroController
{
    public function __construct(private AuthService $auth,private Contribuyente $taxpayers,private Ingreso $incomes,private View $view,private array $config) {}
    public function index(): void { $this->show('registro/index'); }
    public function analysis(): void { $this->show('registro/analysis'); }
    private function show(string $template): void
    {
        $user=$this->auth->user();if (!$user) { \redirect($this->config,'login'); }
        try { $state=RegistroNavegacion::state($_GET); }
        catch (\InvalidArgumentException $e) { http_response_code(422);echo \e($e->getMessage());return; }
        $owner=(int)$user['_ID'];$rows=$this->taxpayers->listFor($owner);
        $id=filter_var(\input($_GET,'contribuyente'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]])?:0;
        $taxpayer=$id?$this->taxpayers->findFor($id,$owner):null;
        if (array_key_exists('contribuyente',$_GET) && !$taxpayer) { http_response_code(404);echo 'Contribuyente no encontrado.';return; }
        if (!$taxpayer && count($rows)===1) { $taxpayer=$rows[0]; }
        $profile=null;$concepts=[];$movements=[];$totals=[];
        if ($taxpayer) {
            foreach ($this->incomes->profiles((int)$taxpayer['id'],$owner) as $p) { if ((string)$p['anio']===$state['anio']) { $profile=$this->incomes->profileFor((int)$p['id'],$owner);break; } }
            if ($profile) {
                $all=$this->incomes->concepts($profile);$allMovements=$this->incomes->movements((int)$profile['id'],$owner);
                $totals=IngresoSummary::build($all,$allMovements);
                $concepts=RegistroNavegacion::concepts($all,$profile,$state);$ids=array_column($concepts,'id');
                $movements=array_values(array_filter($allMovements,static fn($m)=>in_array($m['concepto_id'],$ids)));
            }
        }
        $this->view->render($template,compact('user','rows','taxpayer','profile','state','concepts','movements','totals'));
    }
}
