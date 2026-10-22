<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\View;
use App\Models\{Contribuyente,Caracterizacion,RutDocumento};
use App\Services\{AuthService,Csrf,CaracterizacionSchema,CaracterizacionData,ContribuyenteData,PerfilContribuyente};

final class CaracterizacionController
{
    public function __construct(private AuthService $auth, private Contribuyente $taxpayers, private Caracterizacion $model,
        private View $view, private array $config, private RutDocumento $documents) {}

    public function index(): void
    {
        $user=$this->auth->user(); if (!$user) { \redirect($this->config,'login'); }
        $owner=(int)$user['_ID']; $raw=\input($_GET,'id'); $year=\input($_GET,'anio','2026');
        $taxpayer=preg_match('/^[1-9][0-9]{0,9}$/D',$raw)?$this->taxpayers->findFor((int)$raw,$owner):null;
        if (!$taxpayer) { http_response_code(404); echo 'Contribuyente no encontrado.'; return; }
        if (!array_key_exists('anio',$_GET)) { $year=(string)$this->model->latestYearFor((int)$raw,$owner); }
        $schemas=CaracterizacionSchema::all($this->model->catalogs()); $tabs=PerfilContribuyente::tabs($taxpayer['tipo']);
        $tab=\input($_GET,'tab'); $legacy=\input($_GET,'bloque');
        if ($tab==='') { $tab=$legacy!==''?$legacy:'identificacion'; }
        $definition=$tabs[$tab]??null; $block=$definition[1]??$legacy; $roles=$definition[2]??[];
        if (!in_array($year,['2025','2026'],true) || (!$definition && !isset($schemas[$block]))) {
            http_response_code(422); echo 'Revisa el año y la pestaña seleccionados.'; return;
        }
        $blocks=$this->model->blocksFor((int)$raw,$owner,(int)$year); $errors=[]; $revision=0; $record=[]; $saved=[];
        $notice=$_SESSION['rut_notice'][$raw.':'.$year]??''; unset($_SESSION['rut_notice'][$raw.':'.$year]);
        $identity=$taxpayer;
        if ($block!=='') {
            $schema=$schemas[$block]; $saved=CaracterizacionData::read($blocks[$block]['datos']??[],$schema);
            $record=PerfilContribuyente::subset($saved,$roles); $schemas[$block]=PerfilContribuyente::schema($schema,$roles);
            if ($tab==='personal') { $schemas[$block]['guide']='Situación laboral, pensión y afiliaciones del titular. Completa lo que no figura en el RUT; aquí no se calculan aportes ni impuestos.'; }
            if ($tab==='socios' && $taxpayer['tipo']==='natural') { $schemas[$block]['guide']='Registra las sociedades en las que participa el titular, su identificación y porcentaje. El RUT personal puede no incluir este detalle: complétalo con los certificados correspondientes.'; }
            $revision=(int)($blocks[$block]['revision']??0);
        }
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if ($block==='') { http_response_code(422); echo 'Selecciona una pestaña para guardar.'; return; }
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) { $errors[]='El formulario venció. Recarga antes de guardar.'; http_response_code(419); }
            else {
                try {
                    if (!is_array($_POST['campos']??[]) || !is_array($_POST['filas']??[])) { throw new \InvalidArgumentException('Formato de captura inválido.'); }
                    $record=CaracterizacionData::read($_POST,$schemas[$block]);
                    $merged=PerfilContribuyente::combine($saved,$record,$roles);
                    $errors=CaracterizacionData::errors($merged,$schema,$taxpayer['tipo']);
                    if ($block==='identificacion' && isset($_POST['identidad'])) {
                        if (!is_array($_POST['identidad'])) { throw new \InvalidArgumentException('Formato de identificación inválido.'); }
                        $identity=ContribuyenteData::read($_POST['identidad']); $identity['fuentes']=$taxpayer['fuentes'];
                        $identityCheck=$identity; $identityCheck['fuentes']=[];
                        $errors=array_merge(CaracterizacionData::errors($merged,$schema,$identity['tipo']),ContribuyenteData::errors($identityCheck));
                    }
                    $sent=filter_var(\input($_POST,'revision'),FILTER_VALIDATE_INT,['options'=>['min_range'=>0]]);
                    if ($sent===false) { $errors[]='Revisión de guardado inválida. Recarga la página.'; } else { $revision=$sent; }
                    if (!$errors) {
                        $ok=$block==='identificacion' && isset($_POST['identidad'])
                            ?$this->model->saveIdentityFor((int)$raw,$owner,(int)$year,$merged,$sent,$identity)
                            :$this->model->saveFor((int)$raw,$owner,(int)$year,$block,$merged,$sent);
                        if (!$ok) { http_response_code(409); $errors[]='Esta información cambió en otra ventana. Recarga para comparar antes de guardar.'; }
                        else {
                            $selection=$definition?'&tab='.rawurlencode($tab):'&bloque='.rawurlencode($block);
                            header('Location: '.\url($this->config,'contribuyentes/perfil').'&id='.$raw.'&anio='.$year.$selection.'&guardado=1',true,303); return;
                        }
                    } else { http_response_code(422); }
                } catch (\InvalidArgumentException $e) { $errors[]=$e->getMessage(); http_response_code(422); }
                catch (\PDOException $e) { if ((int)($e->errorInfo[1]??0)!==1062) { throw $e; } $errors[]='Ya tienes otro contribuyente con esa identificación.'; http_response_code(422); }
            }
        } elseif (\input($_GET,'guardado')==='1') { $notice='Información guardada.'; }
        $documents=$this->documents->listFor((int)$raw,$owner,(int)$year);
        $this->view->render('caracterizacion/index',compact('user','taxpayer','year','tab','tabs','block','roles','schemas','blocks','record','identity','revision','errors','notice','documents'));
    }
}
