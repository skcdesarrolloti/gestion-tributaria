<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\View;
use App\Models\ParametroAnual;
use App\Services\{AuthService,AccesoRoles,Csrf,ParametrosAnuales,ParametroAnualDatos};

final class ParametroAnualController
{
    public function __construct(private AuthService $auth,private ParametroAnual $model,private View $view,private array $config) {}
    public function index(): void
    {
        $user=$this->auth->user();if (!$user) { \redirect($this->config,'login'); }
        if (!AccesoRoles::socio($user)) { http_response_code(403);echo 'Solo un Socio activo puede administrar los parámetros anuales.';return; }
        $year=filter_var(\input($_POST,'anio',\input($_GET,'anio',date('Y'))),FILTER_VALIDATE_INT,['options'=>['min_range'=>2025,'max_range'=>2100]]);
        if ($year===false) { http_response_code(422);echo 'Selecciona un año entre 2025 y 2100.';return; }
        $exists=in_array($year,ParametrosAnuales::years(),true);$current=$exists?ParametrosAnuales::year($year):ParametrosAnuales::defaults(2026);
        $revision=$exists?(int)$current['revision']:0;$locked=$year<(int)date('Y');$errors=[];
        $values=$current;
        if (!$exists) { $values['uvt']='';$values['source']='';$values['norma']=''; }
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) { http_response_code(419);echo 'Formulario vencido. Recarga antes de guardar.';return; }
            if ($locked) { http_response_code(403);echo 'Los parámetros de años anteriores se conservan para proteger su historia.';return; }
            $values=$_POST;
            try {
                if (\input($_POST,'revision')!==(string)$revision) { throw new \DomainException('Otra persona cambió los parámetros. Recarga y revisa antes de publicar.'); }
                $data=ParametroAnualDatos::read($_POST);
                if (!$this->model->save($year,$data,$revision,(int)$user['_ID'])) { throw new \DomainException('Otra persona cambió los parámetros. Recarga antes de publicar.'); }
                header('Location: '.\url($this->config,'configuracion').'&anio='.$year.'&guardado=1',true,303);return;
            } catch (\InvalidArgumentException $e) { http_response_code(422);$errors[]=$e->getMessage(); }
            catch (\DomainException $e) { http_response_code(409);$errors[]=$e->getMessage(); }
        }
        if ($_SERVER['REQUEST_METHOD']!=='POST') {
            $values['tramos']=array_map(static fn($r)=>['hasta'=>$r[1],'tarifa'=>$r[2],'fijo'=>$r[3]],$current['tramos']);
        }
        $history=$this->model->history($year);$years=ParametrosAnuales::years();
        $this->view->render('configuracion/index',compact('user','year','exists','current','revision','locked','values','errors','history','years'));
    }
}
