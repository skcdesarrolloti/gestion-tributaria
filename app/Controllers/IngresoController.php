<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\{Contribuyente, Ingreso,Caracterizacion};
use App\Services\{AuthService, Csrf, IngresoData, IngresoSummary};

final class IngresoController
{
    public function __construct(private AuthService $auth, private Contribuyente $taxpayers,
        private Ingreso $model, private View $view, private array $config,private Caracterizacion $characterization) {}

    public function index(): void
    {
        $user = $this->user();
        $registration=null;
        if (\input($_GET,'origen')==='registro') {
            try { $registration=\App\Services\RegistroNavegacion::state($_GET); }
            catch (\InvalidArgumentException $e) { http_response_code(422);echo \e($e->getMessage());return; }
        }
        $taxpayer = $this->taxpayers->findFor($this->id('contribuyente'), (int) $user['_ID']);
        if (!$taxpayer) { $this->missing(); return; }
        if ($_SERVER['REQUEST_METHOD']==='POST' && !\App\Services\AccesoRoles::writable($user,$taxpayer)) { return; }
        $record = IngresoData::read($_POST, Ingreso::PROFILE_FIELDS);
        $profileOrigins=[];
        if ($_SERVER['REQUEST_METHOD']==='GET') {
            $year=in_array(\input($_GET,'anio'),['2025','2026'],true)?\input($_GET,'anio'):'2026';
            $preparation=\App\Services\PerfilAnualSugerencias::stored($this->characterization,$taxpayer,(int)$user['_ID'],$year);
            $record=$preparation['record'];$profileOrigins=$preparation['origins'];
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->csrfErrors();
            if (!$errors) {
                $errors = IngresoData::profileErrors($record,$taxpayer['tipo']);
                if (!$errors) { $blocks=$this->characterization->blocksFor((int)$taxpayer['id'],(int)$user['_ID'],(int)$record['anio']);$errors=\App\Services\IngresoPreguntas::profileErrors($record,$blocks['ingresos']['datos']['campos']??[],$taxpayer['tipo']); }
                if (!$errors) {
                    try {
                        $id = $this->model->createProfile($record,$taxpayer,(int) $user['_ID']);
                        if ($registration) { $registration['anio']=$record['anio'];header('Location: '.\url($this->config,'registro').\App\Services\RegistroNavegacion::query($registration,(int)$taxpayer['id']),true,303);return; }
                        $_SESSION['perfil_notice']='Perfil anual guardado. Estos son los datos registrados y sus ingresos.';
                        $this->go('ingresos/detalle',$id);
                    } catch (\PDOException $error) {
                        if ((int) ($error->errorInfo[1] ?? 0) !== 1062) { throw $error; }
                        $errors[] = 'Ya existe un perfil para este contribuyente y año. Ábrelo desde el listado.';
                    }
                }
                http_response_code(422);
            }
        }
        $profiles = $this->model->profiles((int) $taxpayer['id'],(int) $user['_ID']);
        $this->view->render('ingresos/index',compact('user','taxpayer','profiles','record','errors','registration','profileOrigins'));
    }

    public function detail(): void
    {
        $user = $this->user();
        $profile = $this->model->profileFor($this->id('id'),(int) $user['_ID']);
        if (!$profile) { $this->missing(); return; }
        $concepts = $this->model->concepts($profile);
        $movements = $this->model->movements((int) $profile['id'],(int) $user['_ID']);
        $totals = IngresoSummary::build($concepts,$movements);
        $notice=$_SESSION['perfil_notice']??'';
        unset($_SESSION['perfil_notice']);
        $this->view->render('ingresos/detail',compact('user','profile','concepts','movements','totals','notice'));
    }

    public function profile(): void
    {
        $user=$this->user();
        $profile=$this->model->profileFor($this->id('id'),(int)$user['_ID']);
        if (!$profile) { $this->missing(); return; }
        if ($_SERVER['REQUEST_METHOD']==='POST' && !\App\Services\AccesoRoles::writable($user,$profile)) { return; }
        $taxpayer=['id'=>$profile['contribuyente_id'],'tipo'=>$profile['tipo'],'nombre'=>$profile['nombre']];
        $record=[];
        foreach (Ingreso::PROFILE_FIELDS as $field) { $record[$field]=(string)$profile[$field]; }
        $errors=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $errors=$this->csrfErrors();
            if (!$errors) {
                $record=IngresoData::read($_POST,Ingreso::PROFILE_FIELDS);
                $errors=IngresoData::profileErrors($record,$profile['tipo']);
                if (!$errors) { $blocks=$this->characterization->blocksFor((int)$profile['contribuyente_id'],(int)$user['_ID'],(int)$record['anio']);$errors=\App\Services\IngresoPreguntas::profileErrors($record,$blocks['ingresos']['datos']['campos']??[],$profile['tipo']); }
                if (!$errors) {
                    try {
                        $this->model->updateProfile($record,(int)$profile['id'],(int)$user['_ID']);
                        $_SESSION['perfil_notice']='Perfil actualizado y guardado.';
                        $this->go('ingresos/detalle',(int)$profile['id']);
                    } catch (\DomainException $error) { $errors[]=$error->getMessage(); }
                    catch (\PDOException $error) {
                        if ((int)($error->errorInfo[1]??0)!==1062) { throw $error; }
                        $errors[]='Ya existe otro perfil para ese año. No se modificó el registro.';
                    }
                }
                http_response_code(422);
            }
        }
        $hasMovements=(bool)$this->model->movements((int)$profile['id'],(int)$user['_ID']);
        $this->view->render('ingresos/profile',compact('user','taxpayer','profile','record','errors','hasMovements'));
    }

    public function movement(): void
    {
        $user = $this->user();
        $profile = $this->model->profileFor($this->id('id'),(int) $user['_ID']);
        if (!$profile) { $this->missing(); return; }
        if ($_SERVER['REQUEST_METHOD']==='POST' && !\App\Services\AccesoRoles::writable($user,$profile)) { return; }
        $concepts = $this->model->concepts($profile);
        $id = array_key_exists('movimiento',$_GET) ? $this->id('movimiento') : null;
        $record = IngresoData::read([],Ingreso::MOVEMENT_FIELDS);
        $registration=null;
        if (\input($_GET,'origen')==='registro') {
            try { $registration=\App\Services\RegistroNavegacion::state($_GET);$registration['anio']=(string)$profile['anio']; }
            catch (\InvalidArgumentException $e) { http_response_code(422);echo \e($e->getMessage());return; }
            $concepts=\App\Services\RegistroNavegacion::concepts($concepts,$profile,$registration);
        }
        foreach ($concepts as $concept) { if ((int)$concept['id']===$this->id('concepto') && IngresoData::allowed($concept,$profile)) { $record['concepto_id']=(string)$concept['id']; } }
        if ($id !== null) {
            $found = array_filter($this->model->movements((int) $profile['id'],(int) $user['_ID']),
                static fn ($row) => (int) $row['id'] === $id);
            if (!$found) { $this->missing(); return; }
            $record = reset($found);
            if ($registration && !in_array((int)$record['concepto_id'],array_map('intval',array_column($concepts,'id')),true)) { $this->missing();return; }
            $record['valor_original'] = $record['moneda'] === 'COP' ? (string) $record['valor_cop'] : $record['valor_original'];
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->csrfErrors();
            if (!$errors) {
                $record = IngresoData::read($_POST,Ingreso::MOVEMENT_FIELDS);
                $record['moneda'] = strtoupper($record['moneda']);
                $found = array_filter($concepts,static fn ($c) => (string) $c['id'] === $record['concepto_id']);
                $concept = $found ? reset($found) : null;
                $errors = IngresoData::movementErrors($record,$profile,$concept);
                if (!$errors) {
                    try {
                        $this->model->saveMovement($record,(int) $profile['id'],(int) $user['_ID'],$id);
                        if ($registration) { header('Location: '.\url($this->config,'registro').\App\Services\RegistroNavegacion::query($registration,(int)$profile['contribuyente_id']),true,303);return; }
                        $this->go('ingresos/detalle',(int) $profile['id']);
                    } catch (\DomainException $error) { $errors[]=$error->getMessage(); }
                }
                http_response_code(422);
            }
        }
        $this->view->render('ingresos/movement',compact('user','profile','concepts','id','record','errors','registration'));
    }

    private function user(): array
    {
        $user = $this->auth->user();
        if (!$user) { \redirect($this->config,'login'); }
        return $user;
    }

    private function id(string $field): int
    {
        $raw = \input($_GET,$field);
        return preg_match('/^[1-9][0-9]{0,9}$/D',$raw) ? (int) $raw : 0;
    }

    private function csrfErrors(): array
    {
        if (Csrf::valid(\input($_POST,'_csrf_token'))) { return []; }
        http_response_code(419);
        return ['Formulario vencido. Recarga la página antes de guardar.'];
    }

    private function missing(): void { http_response_code(404); echo 'Registro no encontrado.'; }

    private function go(string $route, int $id): never
    {
        header('Location: '.\url($this->config,$route).'&id='.$id,true,303);
        exit;
    }
}
