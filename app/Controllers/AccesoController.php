<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\View;
use App\Models\{AccesoTributario,Funcionario,Contribuyente};
use App\Services\{AuthService,AccesoRoles,Csrf};

final class AccesoController
{
    public function __construct(private AuthService $auth,private AccesoTributario $access,private Funcionario $users,private Contribuyente $taxpayers,private View $view,private array $config) {}
    public function index(): void
    {
        $user=$this->auth->user(); if (!$user) { \redirect($this->config,'login'); }
        if (!AccesoRoles::socio($user)) { http_response_code(403); echo 'Solo un Socio puede administrar accesos.'; return; }
        $directory=$this->users->activeDirectory(); $assignments=array_column($this->access->rows(),null,'funcionario_id');
        $selected=filter_var(\input($_GET,'funcionario'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]])?:0;
        $target=null; foreach ($directory as $account) { if ((int)$account['_ID']===$selected) { $target=$account; } }
        $record=$assignments[$selected]??['perfil'=>'usuario','contribuyente_id'=>'','activo'=>1,'revision'=>0];
        $errors=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) { http_response_code(419); $errors[]='Formulario vencido. Recarga antes de guardar.'; }
            else {
                $revision=filter_var(\input($_POST,'revision'),FILTER_VALIDATE_INT,['options'=>['min_range'=>0]]);
                $raw=\input($_POST,'contribuyente_id'); $taxpayer=$raw===''?null:filter_var($raw,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
                if (\input($_POST,'perfil')==='socio') { $taxpayer=null; }
                try {
                    if (!$target || $revision===false || $taxpayer===false || !in_array(\input($_POST,'activo'),['0','1'],true)) { throw new \InvalidArgumentException('Revisa la cuenta, contribuyente y revisión seleccionados.'); }
                    $this->access->save((int)$user['_ID'],$target,\input($_POST,'perfil'),$taxpayer,\input($_POST,'activo')==='1',$revision);
                    header('Location: '.\url($this->config,'accesos').'&funcionario='.$selected.'&guardado=1',true,303); return;
                } catch (\InvalidArgumentException $e) { $errors[]=$e->getMessage(); http_response_code(422); }
                catch (\DomainException $e) { $errors[]=$e->getMessage(); http_response_code(409); }
            }
        }
        $taxpayers=$this->taxpayers->listFor((int)$user['_ID']);
        $this->view->render('accesos/index',compact('user','directory','assignments','target','selected','record','taxpayers','errors'));
    }
}
