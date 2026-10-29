<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\View;
use App\Models\{Contribuyente,ExtractoBancario};
use App\Services\{AuthService,AccesoRoles,Csrf,ExtractoData,ExtractoUpload};

final class ExtractoController
{
    private ExtractoUpload $files;
    public function __construct(private AuthService $auth, private Contribuyente $taxpayers,
        private ExtractoBancario $documents, private View $view, private array $config)
    { $this->files=new ExtractoUpload($config['storage_path']??dirname(__DIR__,2).'/storage'); }

    public function index(): void
    {
        $user=$this->user();$owner=(int)$user['_ID'];$year=\input($_GET,'anio','2026');
        if (!in_array($year,['2025','2026'],true)) { http_response_code(422);echo 'Selecciona 2025 o 2026.';return; }
        $rows=$this->taxpayers->listFor($owner);
        $id=filter_var(\input($_GET,'contribuyente'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]])?:0;
        $taxpayer=$id?$this->taxpayers->findFor($id,$owner):null;
        if (isset($_GET['contribuyente']) && !$taxpayer) { http_response_code(404);echo 'Contribuyente no encontrado.';return; }
        if (!$taxpayer && count($rows)===1) { $taxpayer=$rows[0];$id=(int)$taxpayer['id']; }
        $errors=[];$selected=null;
        $form=['banco'=>'BBVA','cuenta'=>'','mes'=>'auto'];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            foreach (array_keys($form) as $key) { $form[$key]=\input($_POST,$key); }
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) { http_response_code(419);$errors[]='Formulario vencido. Recarga antes de subir el PDF.'; }
            elseif (!$taxpayer || !AccesoRoles::edit($user,$taxpayer)) { http_response_code(403);$errors[]='Selecciona un contribuyente autorizado.'; }
            else {
                $file=null;
                try {
                    $posted=$_POST;$automatic=\input($_POST,'mes')==='auto';
                    if ($automatic) { $posted['mes']='1'; }
                    $data=ExtractoData::read($posted,$year);$file=$this->files->receive($_FILES['extracto_pdf']??[],$_FILES['extracto_lectura']??[]);
                    if ($automatic) {
                        if (!isset($file['lectura']['inicio'])) { throw new \InvalidArgumentException('No se pudo leer el período impreso en el PDF. No se exige firma digital. Si el documento abre y el período se ve, selecciona el mes manualmente para guardarlo pendiente de lectura.'); }
                        $data['mes']=(int)substr($file['lectura']['inicio'],5,2);
                    }
                    ExtractoData::match($file['lectura'],$data,$taxpayer);
                    $file['lectura']['titular_confirmado']=$data['titular_confirmado'];
                    $documentId=$this->documents->saveFor($id,$owner,$data,$file);
                    $origin=\input($_GET,'origen');$inRegistration=in_array($origin,['pensiones','capital','costos'],true);
                    $destination=\url($this->config,$inRegistration?'registro':'extractos').'&contribuyente='.$id.'&anio='.$year;
                    if ($inRegistration) { $destination.='&componente='.($origin==='costos'?'costos':'ingresos').'&cedula='.($origin==='pensiones'?'pensiones':'general').'&renta=capital&seccion=financieros'; }
                    header('Location: '.$destination.'&documento='.$documentId.'&guardado=1#detalle',true,303);return;
                } catch (\InvalidArgumentException $e) { http_response_code(422);$errors[]=$e->getMessage(); }
                catch (\DomainException $e) { http_response_code(409);$errors[]=$e->getMessage(); }
                catch (\Throwable $e) { error_log((string)$e);http_response_code(500);$errors[]='No fue posible guardar el extracto. El registro privado conserva el diagnóstico; vuelve a intentar.'; }
                if ($file) { $this->files->discard($file); }
            }
        }
        $documents=$taxpayer?$this->documents->listFor($id,$owner,(int)$year):[];
        $documentId=filter_var(\input($_GET,'documento'),FILTER_VALIDATE_INT)?:0;
        foreach ($documents as $d) { if ((int)$d['id']===$documentId) { $selected=$d;break; } }
        if ($documentId && !$selected) { http_response_code(404);echo 'Extracto no encontrado en este contribuyente y año.';return; }
        $this->view->render('extractos/index',compact('user','rows','taxpayer','year','documents','selected','errors','form'));
    }

    public function document(): void
    {
        $user=$this->user();$id=filter_var(\input($_GET,'documento'),FILTER_VALIDATE_INT)?:0;
        $document=$this->documents->findFor($id,(int)$user['_ID']);
        if (!$document) { http_response_code(404);echo 'Documento no encontrado.';return; }
        $reading=ExtractoData::reading($document);
        $path=$this->files->path($reading['archivo_consulta']??$document['archivo']);
        if (!is_file($path)) { http_response_code(404);echo 'Soporte no disponible.';return; }
        header('Content-Type: application/pdf');header('Content-Disposition: inline; filename="extracto-bancario.pdf"');
        header('X-Frame-Options: SAMEORIGIN');header('X-Content-Type-Options: nosniff');header('Cache-Control: private, no-store');
        header("Content-Security-Policy: frame-ancestors 'self'");readfile($path);
    }

    private function user(): array
    { $user=$this->auth->user();if (!$user) { \redirect($this->config,'login'); }return $user; }
}
