<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\{Contribuyente, ExogenaRecords};
use App\Services\{AuthService, Csrf, Exogena5247Data, Exogena5247Fields, Exogena1007Data, Exogena1007Fields};
use App\Services\{Exogena1001Data, Exogena1001Fields};

final class ExogenaController
{
    public function __construct(private AuthService $auth, private Contribuyente $taxpayers,
        private ExogenaRecords $model, private View $view, private array $config, private string $formatCode='5247') {}

    public function index(): void
    {
        $user=$this->user();
        $this->view->render('exogena/index',compact('user'));
    }

    public function format(): void
    {
        $user=$this->user();
        $companies=array_filter($this->taxpayers->listFor((int)$user['_ID']),static fn($c)=>$c['tipo']==='juridica');
        $fields=$this->fields();
        $year=array_key_exists('anio',$_GET)?\input($_GET,'anio'):$fields::DEFAULT_YEAR;
        if (!preg_match('/^20[0-9]{2}$/D',$year)) { http_response_code(422); echo 'Año gravable inválido.'; return; }
        if ($this->formatCode==='1001' && $year!=='2025') { http_response_code(422); echo '1001: versión 10 disponible para 2025. Revisar versión para otro año.'; return; }
        $company=null;
        $records=[];
        if (array_key_exists('contribuyente',$_GET)) {
            $company=$this->company($this->id('contribuyente'),(int)$user['_ID']);
            if (!$company) { $this->missing(); return; }
            $records=$this->model->listFor((int)$company['id'],(int)$year,(int)$user['_ID']);
        }
        $notice=$_SESSION['exogena_notice']??'';
        unset($_SESSION['exogena_notice']);
        $this->view->render('exogena/'.$this->formatCode,compact('user','companies','company','records','year','notice'));
    }

    public function record(): void
    {
        $user=$this->user();
        $id=array_key_exists('id',$_GET)?$this->id('id'):null;
        $found=$id!==null?$this->model->findFor($id,(int)$user['_ID']):null;
        $fields=$this->fields();
        $reader=match($this->formatCode) { '1001'=>Exogena1001Data::class, '1007'=>Exogena1007Data::class, default=>Exogena5247Data::class };
        if ($id!==null && (!$found || $found['estructura']!==$fields::STRUCTURE)) { $this->missing(); return; }
        $company=$this->company($found?(int)$found['contribuyente_id']:$this->id('contribuyente'),(int)$user['_ID']);
        if (!$company) { $this->missing(); return; }
        $record=$reader::read($found?array_merge($found['datos'],['anio'=>(string)$found['anio']]):
            ['anio'=>\input($_GET,'anio',$fields::DEFAULT_YEAR)]);
        $errors=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $record=$reader::read($_POST);
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) {
                http_response_code(419); $errors[]='Formulario vencido. Recarga antes de guardar.';
            } else {
                $errors=$reader::errors($record);
                if (!$errors) {
                    $this->model->saveFor($record,(int)$company['id'],(int)$user['_ID'],$id);
                    $_SESSION['exogena_notice']='Registro '.$this->formatCode.' guardado como borrador.';
                    header('Location: '.\url($this->config,'exogena/'.$this->formatCode).'&contribuyente='.$company['id'].'&anio='.$record['anio'],true,303);
                    exit;
                }
                http_response_code(422);
            }
        }
        $template=match($this->formatCode) { '1001'=>'exogena/record-1001','1007'=>'exogena/record-1007',default=>'exogena/record' };
        $this->view->render($template,compact('user','company','record','errors','id'));
    }

    private function fields(): string
    {
        return match($this->formatCode) { '1001'=>Exogena1001Fields::class, '1007'=>Exogena1007Fields::class, '5247'=>Exogena5247Fields::class,
            default=>throw new \InvalidArgumentException('Formato no disponible.') };
    }

    private function company(int $id, int $owner): ?array
    {
        $company=$this->taxpayers->findFor($id,$owner);
        return $company && $company['tipo']==='juridica'?$company:null;
    }

    private function user(): array
    {
        $user=$this->auth->user();
        if (!$user) { \redirect($this->config,'login'); }
        return $user;
    }

    private function id(string $key): int
    {
        $raw=\input($_GET,$key);
        return preg_match('/^[1-9][0-9]{0,9}$/D',$raw)?(int)$raw:0;
    }

    private function missing(): void { http_response_code(404); echo 'Registro no encontrado.'; }
}
