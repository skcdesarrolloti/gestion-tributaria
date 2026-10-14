<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\{Contribuyente, Ingreso};
use App\Services\{AuthService, Csrf, IngresoData, IngresoSummary};

final class IngresoController
{
    public function __construct(private AuthService $auth, private Contribuyente $taxpayers,
        private Ingreso $model, private View $view, private array $config) {}

    public function index(): void
    {
        $user = $this->user();
        $taxpayer = $this->taxpayers->findFor($this->id('contribuyente'), (int) $user['_ID']);
        if (!$taxpayer) { $this->missing(); return; }
        $record = IngresoData::read($_POST, Ingreso::PROFILE_FIELDS);
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->csrfErrors();
            if (!$errors) {
                $errors = IngresoData::profileErrors($record,$taxpayer['tipo']);
                if (!$errors) {
                    try {
                        $id = $this->model->createProfile($record,$taxpayer,(int) $user['_ID']);
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
        $this->view->render('ingresos/index',compact('user','taxpayer','profiles','record','errors'));
    }

    public function detail(): void
    {
        $user = $this->user();
        $profile = $this->model->profileFor($this->id('id'),(int) $user['_ID']);
        if (!$profile) { $this->missing(); return; }
        $concepts = $this->model->concepts($profile);
        $movements = $this->model->movements((int) $profile['id'],(int) $user['_ID']);
        $totals = IngresoSummary::build($concepts,$movements);
        $this->view->render('ingresos/detail',compact('user','profile','concepts','movements','totals'));
    }

    public function movement(): void
    {
        $user = $this->user();
        $profile = $this->model->profileFor($this->id('id'),(int) $user['_ID']);
        if (!$profile) { $this->missing(); return; }
        $concepts = $this->model->concepts($profile);
        $id = array_key_exists('movimiento',$_GET) ? $this->id('movimiento') : null;
        $record = IngresoData::read([],Ingreso::MOVEMENT_FIELDS);
        if ($id !== null) {
            $found = array_filter($this->model->movements((int) $profile['id'],(int) $user['_ID']),
                static fn ($row) => (int) $row['id'] === $id);
            if (!$found) { $this->missing(); return; }
            $record = reset($found);
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
                    $this->model->saveMovement($record,(int) $profile['id'],(int) $user['_ID'],$id);
                    $this->go('ingresos/detalle',(int) $profile['id']);
                }
                http_response_code(422);
            }
        }
        $this->view->render('ingresos/movement',compact('user','profile','concepts','id','record','errors'));
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
