<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\View;
use App\Models\{Ingreso,Caracterizacion};
use App\Services\{AuthService,AccesoRoles,Csrf,RetencionDatos,RetencionLaboral,NominaReserva};

final class RetencionController
{
    public function __construct(private AuthService $auth,private Ingreso $incomes,private Caracterizacion $blocks,private View $view,private array $config) {}
    public function index(): void
    {
        $user=$this->auth->user();if (!$user) { \redirect($this->config,'login'); }
        $owner=(int)$user['_ID'];$id=(int)\input($_GET,'id');$profile=$this->incomes->profileFor($id,$owner);
        if (!$profile) { http_response_code(404);echo 'Perfil no encontrado.';return; }
        if ($profile['tipo']!=='natural' || $profile['formulario']!=='210' || $profile['regimen']!=='ordinario' || !in_array((int)$profile['anio'],[2025,2026],true)) {
            http_response_code(422);echo 'Este control requiere persona natural residente en régimen ordinario de 2025 o 2026.';return;
        }
        $payroll=$this->incomes->payroll($id,$owner);$groups=RetencionLaboral::groups($payroll);
        $records=$this->blocks->blocksFor((int)$profile['contribuyente_id'],$owner,(int)$profile['anio']);
        $block=$records['retenciones_laborales']??[];$settings=$block['datos']??[];$revision=(int)($block['revision']??0);
        $key=\input($_GET,'mes').'-'.\input($_GET,'empleador');$group=$groups[$key]??null;
        $projection=\input($_GET,'vista')==='reserva';
        if (!$projection && !$group) { http_response_code(404);echo 'Mes y empleador no encontrados en la nómina de este perfil.';return; }
        $errors=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) { http_response_code(419);echo 'Formulario vencido. Recarga antes de guardar.';return; }
            if (!AccesoRoles::writable($user,$profile)) { return; }
            try {
                $posted=filter_var(\input($_POST,'revision'),FILTER_VALIDATE_INT,['options'=>['min_range'=>0]]);
                if ($posted===false || $posted!==$revision) { http_response_code(409);echo 'La revisión cambió en otra ventana. Recarga antes de guardar.';return; }
                $hash=$projection?NominaReserva::fingerprint($payroll):$group['huella'];
                if (!hash_equals($hash,\input($_POST,'huella'))) { http_response_code(409);echo 'La nómina cambió. Recarga y revisa sus valores.';return; }
                if ($projection) { $value=NominaReserva::read($_POST); }
                else {
                    if (!$group['completo'] || $group['valores']['retroactivos']>0) { throw new \InvalidArgumentException('Completa los períodos y revisa los casos especiales antes de validar.'); }
                    $value=RetencionDatos::read($_POST);
                }
                $value+=['huella'=>$hash,'revisado_por'=>$owner,'fecha'=>date('c')];
                if ($projection) { $settings['reserva']=$value; } else { $settings['meses'][$key]=$value; }
                if (!$this->blocks->saveFor((int)$profile['contribuyente_id'],$owner,(int)$profile['anio'],'retenciones_laborales',$settings,$revision)) {
                    http_response_code(409);echo 'La información cambió en otra ventana. Recarga para conservar los cambios.';return;
                }
                header('Location: '.\url($this->config,'retenciones').'&id='.$id.($projection?'&vista=reserva':'&mes='.$group['mes'].'&empleador='.rawurlencode($group['documento'])).'&guardado=1',true,303);return;
            } catch (\InvalidArgumentException $e) { http_response_code(422);$errors[]=$e->getMessage(); }
        }
        $review=$group?($settings['meses'][$key]??[]):[];
        $result=$projection?NominaReserva::calculate($payroll,$settings['reserva']??[],$profile):RetencionLaboral::check($group,$review,(int)$profile['anio']);
        $this->view->render('retenciones/index',compact('user','profile','payroll','group','projection','settings','review','revision','result','errors'));
    }
}
