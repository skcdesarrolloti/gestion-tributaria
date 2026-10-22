<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Contribuyente;
use App\Services\{AuthService, ContribuyenteData, Csrf, RutRegistro};
use PDOException;

final class ContribuyenteController
{
    public function __construct(
        private AuthService $auth, private Contribuyente $model, private View $view, private array $config, private RutRegistro $rutRegistration,
    ) {}

    public function index(): void
    {
        $user = $this->requireUser();
        $rows = $this->model->listFor((int) $user['_ID']);
        $notice = $_SESSION['contribuyente_notice'] ?? '';
        unset($_SESSION['contribuyente_notice']);
        $this->view->render('contribuyentes/index', compact('user', 'rows', 'notice'));
    }

    public function form(): void
    {
        $user = $this->requireUser();
        $owner = (int) $user['_ID'];
        $id = null;
        $record = ContribuyenteData::read([]);
        if (array_key_exists('id', $_GET)) {
            $raw = \input($_GET, 'id');
            if (!preg_match('/^[1-9][0-9]{0,9}$/D', $raw)) { $this->notFound(); return; }
            $id = (int) $raw;
            $record = $this->model->findFor($id, $owner);
            if (!$record) { $this->notFound(); return; }
            if ($_SERVER['REQUEST_METHOD']==='GET') {
                $year=\input($_GET,'anio'); $yearQuery=in_array($year,['2025','2026'],true)?'&anio='.$year:'';
                header('Location: '.\url($this->config,'contribuyentes/perfil').'&id='.$id.$yearQuery.'&tab=identificacion',true,303); return;
            }
        }
        $errors = [];
        $rutDraft=$id===null?$this->rutRegistration->draft($owner):null;
        if ($rutDraft && $_SERVER['REQUEST_METHOD']!=='POST') { $record=$rutDraft['seed']; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::valid(\input($_POST, '_csrf_token'))) {
                http_response_code(419);
                $errors[] = 'El formulario venció. Recarga la página antes de guardar.';
            } elseif ($id===null && in_array(\input($_POST,'accion'),['leer_rut','descartar_rut'],true)) {
                try {
                    if (\input($_POST,'accion')==='leer_rut') {
                        $existing=$this->rutRegistration->receive($_FILES['rut_pdf']??[],$owner,\input($_POST,'rut_anio'));
                        if ($existing) { $this->openRut($existing); return; }
                    }
                    else { $this->rutRegistration->discard($owner,\input($_POST,'rut_registro_token')); }
                    \redirect($this->config,'contribuyentes/formulario');
                } catch (\InvalidArgumentException $e) { http_response_code(422); $errors[]=$e->getMessage(); }
                catch (\Throwable $e) { error_log('Lectura RUT en registro: '.get_class($e)); http_response_code(422); $errors[]='No se pudo leer el RUT. Revisa el PDF e inténtalo nuevamente.'; }
                if ($rutDraft) { $record=$rutDraft['seed']; }
            } else {
                $previousSources=$record['fuentes'];
                $record = ContribuyenteData::read($_POST);
                if ($id!==null && !array_key_exists('fuentes',$_POST)) { $record['fuentes']=$previousSources; }
                $errors = ContribuyenteData::errors($record);
                if (!$errors) {
                    try {
                        $rutToken=\input($_POST,'rut_registro_token');
                        if ($id===null && ($rutDraft || $rutToken!=='')) {
                            $created=$this->rutRegistration->create($record,$owner,$rutToken);
                            if (!empty($created['existing'])) { $this->openRut($created); return; }
                            $_SESSION['rut_notice'][$created['id'].':'.$created['year']]='Contribuyente registrado desde el RUT: '.$created['applied'].' campos y renglones guardados. Completa los vacíos. Estado actual DIAN: consulta pendiente.';
                            header('Location: '.\url($this->config,'contribuyentes/perfil').'&id='.$created['id'].'&anio='.$created['year'],true,303); return;
                        }
                        $this->model->saveFor($record, $owner, $id);
                        $_SESSION['contribuyente_notice'] = $id === null ? 'Contribuyente registrado.' : 'Contribuyente actualizado.';
                        \redirect($this->config, 'contribuyentes');
                    } catch (PDOException $error) {
                        if ((int) ($error->errorInfo[1] ?? 0) !== 1062) { throw $error; }
                        $errors[] = 'Ya registraste un contribuyente con ese tipo y número de documento.';
                    } catch (\InvalidArgumentException $e) { $errors[]=$e->getMessage(); }
                }
                http_response_code(422);
            }
        }
        $this->view->render('contribuyentes/form', compact('user', 'id', 'record', 'errors','rutDraft'));
    }

    private function requireUser(): array
    {
        $user = $this->auth->user();
        if (!$user) { \redirect($this->config, 'login'); }
        // Política inicial: todo funcionario activo gestiona exclusivamente sus propios registros.
        return $user;
    }

    private function openRut(array $existing): void
    {
        header('Location: '.\url($this->config,'contribuyentes/perfil').'&id='.$existing['id'].'&anio='.$existing['year'].'&tab=identificacion',true,303);
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo 'Contribuyente no encontrado.';
    }
}
