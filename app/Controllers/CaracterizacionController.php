<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\View;
use App\Models\{Contribuyente,Caracterizacion};
use App\Services\{AuthService,Csrf,CaracterizacionSchema,CaracterizacionData};

final class CaracterizacionController
{
    public function __construct(private AuthService $auth, private Contribuyente $taxpayers, private Caracterizacion $model,
        private View $view, private array $config) {}

    public function index(): void
    {
        $user=$this->auth->user(); if (!$user) { \redirect($this->config,'login'); }
        $raw=\input($_GET,'id'); $year=\input($_GET,'anio','2026'); $block=\input($_GET,'bloque');
        $taxpayer=preg_match('/^[1-9][0-9]{0,9}$/D',$raw)?$this->taxpayers->findFor((int)$raw,(int)$user['_ID']):null;
        if (!$taxpayer) { http_response_code(404); echo 'Contribuyente no encontrado.'; return; }
        $catalogs=$this->model->catalogs(); $schemas=CaracterizacionSchema::all($catalogs);
        if (!in_array($year,['2025','2026'],true) || ($block!=='' && !isset($schemas[$block]))) {
            http_response_code(422); echo 'Revisa el año y el bloque seleccionados.'; return;
        }
        $blocks=$this->model->blocksFor((int)$raw,(int)$user['_ID'],(int)$year); $errors=[]; $notice=''; $record=[]; $revision=0;
        if ($block!=='') {
            $schema=$schemas[$block]; $record=CaracterizacionData::read($blocks[$block]['datos']??[],$schema);
            $revision=(int)($blocks[$block]['revision']??0);
        }
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if ($block==='') { http_response_code(422); echo 'Selecciona un bloque para guardar.'; return; }
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) { $errors[]='Formulario vencido. Recarga antes de guardar.'; http_response_code(419); }
            else {
                try {
                    if (!is_array($_POST['campos']??[]) || !is_array($_POST['filas']??[])) { throw new \InvalidArgumentException('Formato de captura inválido.'); }
                    $record=CaracterizacionData::read($_POST,$schema); $errors=CaracterizacionData::errors($record,$schema,$taxpayer['tipo']);
                    $sent=filter_var(\input($_POST,'revision'),FILTER_VALIDATE_INT,['options'=>['min_range'=>0]]);
                    if ($sent===false) { $errors[]='Revisión de guardado inválida. Recarga la página.'; }
                    else { $revision=$sent; }
                    if (!$errors) {
                        if (!$this->model->saveFor((int)$raw,(int)$user['_ID'],(int)$year,$block,$record,$sent)) {
                            http_response_code(409); $errors[]='Este bloque cambió en otra ventana. Conserva tus datos y recarga para comparar antes de guardar.';
                        } else {
                            $destination='contribuyentes/perfil&id='.$raw.'&anio='.$year.'&bloque='.$block.'&guardado=1';
                            header('Location: '.$this->config['base_path'].'/index.php?route='.$destination,true,303); return;
                        }
                    } else { http_response_code(422); }
                } catch (\InvalidArgumentException $e) { $errors[]=$e->getMessage(); http_response_code(422); }
            }
        } elseif (\input($_GET,'guardado')==='1') { $notice='Bloque guardado. Puedes salir y continuar después.'; }
        $annual=$this->model->incomeProfileFor((int)$raw,(int)$user['_ID'],(int)$year);
        $this->view->render('caracterizacion/index',compact('user','taxpayer','year','block','schemas','blocks','record','revision','errors','notice','annual'));
    }
}
