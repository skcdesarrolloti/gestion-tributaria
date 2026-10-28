<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\View;
use App\Models\{Contribuyente,Ingreso,Caracterizacion};
use App\Services\{AuthService,RegistroNavegacion,IngresoSummary};

final class RegistroController
{
    public function __construct(private AuthService $auth,private Contribuyente $taxpayers,private Ingreso $incomes,private View $view,private array $config,private Caracterizacion $characterization) {}
    public function index(): void { $this->show('registro/index'); }
    public function analysis(): void { $this->show('registro/analysis'); }
    public function sources(): void
    {
        $user=$this->auth->user();if (!$user) { \redirect($this->config,'login'); }
        if (!\App\Services\Csrf::valid(\input($_POST,'_csrf_token'))) { http_response_code(419);echo 'Formulario vencido. Recarga antes de guardar.';return; }
        $id=filter_var(\input($_GET,'contribuyente'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]])?:0;
        $owner=(int)$user['_ID'];$taxpayer=$this->taxpayers->findFor($id,$owner);
        if (!$taxpayer) { http_response_code(404);echo 'Contribuyente no encontrado.';return; }
        if (!\App\Services\AccesoRoles::writable($user,$taxpayer)) { return; }
        try {
            $state=RegistroNavegacion::state($_GET);
            $blocks=$this->characterization->blocksFor($id,$owner,(int)$state['anio']);
            $posted=$_POST['respuestas']??null;$revision=filter_var(\input($_POST,'revision'),FILTER_VALIDATE_INT,['options'=>['min_range'=>0]]);
            if (!is_array($posted) || $revision===false) { throw new \InvalidArgumentException('Revisa el formato de las respuestas.'); }
            $record=\App\Services\IngresoPreguntas::merge($blocks['ingresos']['datos']??[],$taxpayer['tipo'],\input($_POST,'grupo'),$posted);
            if (!$this->characterization->saveFor($id,$owner,(int)$state['anio'],'ingresos',$record,$revision)) { http_response_code(409);echo 'Las respuestas cambiaron en otra ventana. Recarga para conservar los cambios.';return; }
            header('Location: '.\url($this->config,'registro').RegistroNavegacion::query($state,$id).'&fuentes_guardadas=1#fuentes',true,303);
        } catch (\InvalidArgumentException $e) { http_response_code(422);echo \e($e->getMessage()); }
    }
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
        $profile=null;$concepts=[];$movements=[];$totals=[];$preparation=null;$questions=null;$payroll=[];$retentionData=[];
        if ($taxpayer) {
            $blocks=$this->characterization->blocksFor((int)$taxpayer['id'],$owner,(int)$state['anio']);
            $retentionData=$blocks['retenciones_laborales']['datos']??[];
            $questions=\App\Services\IngresoPreguntas::prepare($blocks['ingresos']??[],$taxpayer['tipo']);
            foreach ($this->incomes->profiles((int)$taxpayer['id'],$owner) as $p) { if ((string)$p['anio']===$state['anio']) { $profile=$this->incomes->profileFor((int)$p['id'],$owner);break; } }
            if ($profile) {
                $all=$this->incomes->concepts($profile);$allMovements=$this->incomes->movements((int)$profile['id'],$owner);
                $totals=IngresoSummary::build($all,$allMovements);
                $payroll=$this->incomes->payroll((int)$profile['id'],$owner);
                $concepts=RegistroNavegacion::concepts($all,$profile,$state);$ids=array_column($concepts,'id');
                $movements=array_values(array_filter($allMovements,static fn($m)=>in_array($m['concepto_id'],$ids)));
            } else {
                $preparation=\App\Services\PerfilAnualSugerencias::stored($this->characterization,$taxpayer,$owner,$state['anio']);
            }
        }
        $this->view->render($template,compact('user','rows','taxpayer','profile','state','concepts','movements','totals','preparation','questions','payroll','retentionData'));
    }
}
