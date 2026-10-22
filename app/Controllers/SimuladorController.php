<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\{Ingreso, EscenarioRenta};
use App\Services\{AuthService, Csrf, SimuladorFields, RentaSimulador};

final class SimuladorController
{
    public function __construct(private AuthService $auth, private Ingreso $incomes, private EscenarioRenta $model,
        private View $view, private array $config) {}

    public function index(): void
    {
        $user=$this->user(); $owner=(int)$user['_ID']; $profiles=$this->model->profiles($owner);
        $id=$this->id('id'); $saved=$this->id('escenario')?$this->model->findFor($this->id('escenario'),$owner):null;
        if (array_key_exists('escenario',$_GET) && !$saved) { $this->missing(); return; }
        if ($saved) { $id=(int)$saved['perfil_id']; }
        $profile=$id?$this->incomes->profileFor($id,$owner):null;
        if (array_key_exists('id',$_GET) && !$profile) { $this->missing(); return; }
        if ($profile && ($profile['tipo']!=='natural' || $profile['residencia']!=='residente' || $profile['regimen']!=='ordinario')) {
            http_response_code(422); echo 'Simulador disponible para personas naturales residentes en régimen ordinario.'; return;
        }
        $record=SimuladorFields::read($saved?$saved['entradas']:['servicios'=>$profile['servicios']??'',
            'pensionado'=>'','declaraciones'=>'','metodo_anticipo'=>'']);
        $errors=[]; $result=null;
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (!$profile) { $this->missing(); return; }
            if (!\App\Services\AccesoRoles::writable($user,$profile)) { return; }
            $record=SimuladorFields::read($_POST);
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) { http_response_code(419); $errors[]='Formulario vencido; recarga antes de guardar.'; }
            else {
                $errors=SimuladorFields::storageErrors($record);
                if (!$errors) {
                    $result=RentaSimulador::calculate($record,$profile);
                    $new=$this->model->save($record,$result,$profile,$owner);
                    header('Location: '.\url($this->config,'simulador').'&escenario='.$new,true,303); return;
                }
                http_response_code(422);
            }
        }
        $versions=$profile?$this->model->listFor((int)$profile['id'],$owner):[];
        $base=$this->id('base')?$this->model->findFor($this->id('base'),$owner):null;
        if (array_key_exists('base',$_GET) && (!$base || !$profile || (int)$base['perfil_id']!==(int)$profile['id'])) { $this->missing(); return; }
        if ($base && $base['version']!==SimuladorFields::VERSION) { http_response_code(422); echo 'La versión de comparación requiere revisión de reglas.'; return; }
        if ($saved && $saved['version']!==SimuladorFields::VERSION) { http_response_code(422); echo 'Reglas históricas distintas: no recalcular silenciosamente.'; return; }
        if ($profile && !$result) { $result=RentaSimulador::calculate($record,$profile); }
        $this->view->render('simulador/index',compact('user','profiles','profile','record','result','saved','base','versions','errors'));
    }

    public function calculate(): void
    {
        $user=$this->user(); $profile=$this->incomes->profileFor($this->id('id'),(int)$user['_ID']);
        if (!$profile) { $this->missing(); return; }
        header('Content-Type: application/json; charset=UTF-8');
        if (!Csrf::valid(\input($_POST,'_csrf_token'))) { http_response_code(419); echo json_encode(['estado'=>'pendiente','errores'=>['Sesión vencida; recarga.']]); return; }
        echo json_encode(RentaSimulador::calculate(SimuladorFields::read($_POST),$profile),JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE);
    }

    private function user(): array
    {
        $user=$this->auth->user(); if (!$user) { \redirect($this->config,'login'); } return $user;
    }
    private function id(string $key): int { $raw=\input($_GET,$key); return preg_match('/^[1-9][0-9]{0,9}$/D',$raw)?(int)$raw:0; }
    private function missing(): void { http_response_code(404); echo 'Registro no encontrado.'; }
}
