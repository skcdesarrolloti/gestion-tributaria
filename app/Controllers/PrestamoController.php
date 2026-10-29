<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Services\{AuthService,AccesoRoles,Csrf,ExtractoUpload,PdfApi,PrestamoCajaSocial,RegistroNavegacion};
use App\Models\{Contribuyente,PrestamoPersonal,Ingreso,ComprobantePension,ExtractoBancario};
use App\Core\View;

final class PrestamoController
{
    private ExtractoUpload $files;
    public function __construct(private AuthService $auth,private Contribuyente $taxpayers,private PrestamoPersonal $loans,
        private Ingreso $incomes,private ComprobantePension $pensions,private ExtractoBancario $banks,private View $view,private array $config)
    { $this->files=new ExtractoUpload($config['storage_path']??dirname(__DIR__,2).'/storage'); }
    public function index(): void
    {
        $user=$this->auth->user();if (!$user) { \redirect($this->config,'login'); }$owner=(int)$user['_ID'];
        try { $state=RegistroNavegacion::state($_GET+['componente'=>'costos']); }catch (\InvalidArgumentException $e) { http_response_code(422);echo \e($e->getMessage());return; }
        $rows=array_values(array_filter($this->taxpayers->listFor($owner),static fn($r)=>$r['tipo']==='natural'));
        $taxpayer=$this->taxpayers->findFor((int)\input($_GET,'contribuyente'),$owner);$profile=null;$documents=[];$calendar=[];$selected=null;
        if (isset($_GET['contribuyente']) && (!$taxpayer || $taxpayer['tipo']!=='natural')) { http_response_code(404);echo 'Selecciona una ficha personal autorizada.';return; }
        if ($taxpayer) {
            $documents=array_values(array_filter($this->loans->listFor((int)$taxpayer['id'],$owner),static fn($d)=>substr($d['fecha_pago'],0,4)===$state['anio'] || substr($d['fecha_factura'],0,4)===$state['anio']));$pensions=[];
            foreach ($this->incomes->profiles((int)$taxpayer['id'],$owner) as $p) { if ((string)$p['anio']===$state['anio']) { $pensions=$this->pensions->rows((int)$p['id'],$owner);break; } }
            $calendar=\App\Services\PrestamoCruce::months($documents,$pensions,$this->banks->listFor((int)$taxpayer['id'],$owner,(int)$state['anio']),$state['anio']);
            foreach ($documents as $doc) { if ((int)$doc['id']===(int)\input($_GET,'documento')) { $selected=$doc; } }
            if (isset($_GET['documento']) && !$selected) { http_response_code(404);echo 'Documento no encontrado.';return; }
        }
        $this->view->render('prestamos/index',compact('user','rows','taxpayer','profile','state','documents','calendar','selected'));
    }
    public function upload(): void
    {
        $user=$this->auth->user();if (!$user) { PdfApi::send(['error'=>'Inicia sesión antes de subir el crédito.'],401);return; }
        if (!Csrf::valid(\input($_POST,'_csrf_token'))) { PdfApi::send(['error'=>'Recarga el formulario antes de subir.'],419);return; }
        $owner=(int)$user['_ID'];$taxpayer=$this->taxpayers->findFor((int)\input($_GET,'contribuyente'),$owner);
        if (!$taxpayer || $taxpayer['tipo']!=='natural') { PdfApi::send(['error'=>'Selecciona una ficha personal autorizada.'],404);return; }
        if (!AccesoRoles::edit($user,$taxpayer)) { PdfApi::send(['error'=>'No puedes modificar esta ficha.'],403);return; }
        $file=null;$stored=false;
        try {
            $file=$this->files->receive(PdfApi::file($_FILES,'prestamo_pdf'),PdfApi::file($_FILES,'prestamo_lectura'));
            $copy=array_intersect_key($file['lectura'],['archivo_consulta'=>true,'sha256_consulta'=>true]);
            $data=PrestamoCajaSocial::read($this->files->path($copy['archivo_consulta']??$file['archivo']),$taxpayer['nombre'])+$copy;
            if (max($data['pago'],$data['factura'])>date('Y-m-d')) { throw new \DomainException('Las fechas del extracto no pueden ser futuras.'); }
            if (substr($data['pago'],0,4)!==\input($_GET,'anio')) { throw new \DomainException('Abre el año del pago anterior: '.substr($data['pago'],0,4).'. El mes del pago puede ser anterior al de facturación.'); }
            $id=$this->loans->save((int)$taxpayer['id'],$owner,$file,$data);$stored=true;
            PdfApi::send(['guardado'=>true,'id'=>$id,'mensaje'=>'Extracto guardado. El pago se asignó a '.$data['pago'].'. La próxima cuota no se sumó como pagada.']);
        } catch (\DomainException|\InvalidArgumentException $e) { if ($file && !$stored) { $this->files->discard($file); }PdfApi::send(['error'=>$e->getMessage()],422); }
        catch (\Throwable $e) { if ($file && !$stored) { $this->files->discard($file); }error_log((string)$e);PdfApi::send(['error'=>'No se pudo leer o guardar el crédito. Se conservó el diagnóstico privado.'],500); }
    }
    public function document(): void
    {
        $user=$this->auth->user();if (!$user) { http_response_code(401);return; }
        $d=$this->loans->findFor((int)\input($_GET,'documento'),(int)$user['_ID']);if (!$d) { http_response_code(404);return; }
        $path=$this->files->path($d['datos']['archivo_consulta']??$d['archivo']);if (!is_file($path)) { http_response_code(404);return; }
        header('Content-Type: application/pdf');header('Content-Disposition: inline; filename="credito-personal.pdf"');
        header('X-Content-Type-Options: nosniff');header('Cache-Control: private, no-store');header("Content-Security-Policy: frame-ancestors 'self'");readfile($path);
    }
}
