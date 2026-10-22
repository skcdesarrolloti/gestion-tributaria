<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\View;
use App\Models\{Contribuyente,Caracterizacion,RutDocumento};
use App\Services\{AuthService,Csrf,RutUpload,RutImportPlan,CaracterizacionSchema,RutAplicacion};

final class RutController
{
    private RutUpload $files;
    public function __construct(private AuthService $auth, private Contribuyente $taxpayers, private Caracterizacion $profiles,
        private RutDocumento $documents, private View $view, private array $config)
    { $this->files=new RutUpload($config['storage_path']??dirname(__DIR__,2).'/storage'); }

    public function index(): void
    {
        $user=$this->user(); $owner=(int)$user['_ID']; $raw=\input($_GET,'id'); $year=\input($_GET,'anio','2026');
        $taxpayer=preg_match('/^[1-9][0-9]{0,9}$/D',$raw)?$this->taxpayers->findFor((int)$raw,$owner):null;
        if (!$taxpayer) { http_response_code(404); echo 'Contribuyente no encontrado.'; return; }
        if (!in_array($year,['2025','2026'],true)) { http_response_code(422); echo 'Revisa el año seleccionado.'; return; }
        $schemas=CaracterizacionSchema::all($this->profiles->catalogs());
        $blocks=$this->profiles->blocksFor((int)$raw,$owner,(int)$year); $errors=[];
        $this->files->cleanup(); $preview=$this->preview($owner,(int)$raw,$year);
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) { http_response_code(419); $errors[]='Formulario vencido. Recarga la página.'; }
            else {
                try {
                    $action=\input($_POST,'accion');
                    if ($action==='leer') {
                        $received=$this->files->receive($_FILES['rut_pdf']??[]);
                        try { $plan=RutImportPlan::build($received['parsed'],$taxpayer,$schemas,$year); }
                        catch (\Throwable $e) { $this->files->remove($received['key']); throw $e; }
                        if (isset($_SESSION['rut_import']['file']['key'])) { $this->files->remove($_SESSION['rut_import']['file']['key']); }
                        $preview=['owner'=>$owner,'id'=>(int)$raw,'year'=>$year,'time'=>time(),'token'=>bin2hex(random_bytes(24)),
                            'file'=>$received,'plan'=>$plan,'revisions'=>array_map(static fn($b)=>(int)$b['revision'],$blocks)];
                        $_SESSION['rut_import']=$preview;
                        if (\input($_POST,'aplicar_vacios')==='1') {
                            $result=$this->apply($preview,array_keys($plan['items']),false,$schemas);
                            unset($_SESSION['rut_import']);
                            $_SESSION['rut_notice'][$raw.':'.$year]='RUT importado: '.$result['applied'].' campos y renglones completados; '.$result['skipped'].' datos existentes conservados. Completa solo los vacíos en tus pestañas.';
                            header('Location: '.\url($this->config,'contribuyentes/perfil').'&id='.$raw.'&anio='.$year.'&tab=identificacion',true,303); return;
                        }
                    } elseif ($action==='aplicar' || $action==='cancelar') {
                        if (!$preview || !hash_equals($preview['token'],\input($_POST,'preview_token'))) { throw new \InvalidArgumentException('La vista previa venció o corresponde a otro contribuyente. Vuelve a subir el PDF.'); }
                        if ($action==='cancelar') {
                            $this->files->remove($preview['file']['key']); unset($_SESSION['rut_import']); $preview=null;
                        } else {
                            $selected=$_POST['seleccion']??[];
                            if (!is_array($selected) || !$selected || count($selected)>200) { throw new \InvalidArgumentException('Selecciona al menos un campo o grupo de códigos para aplicar.'); }
                            foreach ($selected as $key) { if (!is_string($key) || !isset($preview['plan']['items'][$key])) { throw new \InvalidArgumentException('Selección de campos inválida.'); } }
                            $result=$this->apply($preview,array_values(array_unique($selected)),\input($_POST,'reemplazar')==='1',$schemas);
                            unset($_SESSION['rut_import']);
                            $_SESSION['rut_notice'][$raw.':'.$year]='RUT guardado: '.$result['applied'].' campos/grupos aplicados; '.$result['skipped'].' conservados por tener datos diferentes. Revisa los bloques. Estado actual DIAN: consulta pendiente.';
                            header('Location: '.\url($this->config,'contribuyentes/perfil').'&id='.$raw.'&anio='.$year,true,303); return;
                        }
                    } else { throw new \InvalidArgumentException('Selecciona una acción válida.'); }
                } catch (\DomainException $e) { http_response_code(409); $errors[]=$e->getMessage(); }
                catch (\InvalidArgumentException $e) { http_response_code(422); $errors[]=$e->getMessage(); }
                catch (\Throwable $e) { error_log('Importación RUT: '.get_class($e)); http_response_code(422); $errors[]='No se pudo completar la carga. Intenta con un PDF descargado de la DIAN y revisa los datos antes de aplicar.'; }
            }
        }
        if ($errors && \input($_POST,'aplicar_vacios')==='1') {
            $_SESSION['rut_notice'][$raw.':'.$year]=implode(' ',$errors);
            header('Location: '.\url($this->config,'contribuyentes/perfil').'&id='.$raw.'&anio='.$year.'&tab=documentos',true,303); return;
        }
        $documents=$this->documents->listFor((int)$raw,$owner,(int)$year);
        $this->view->render('rut/index',compact('user','taxpayer','year','schemas','blocks','preview','errors','documents'));
    }

    private function preview(int $owner, int $id, string $year): ?array
    {
        $p=$_SESSION['rut_import']??null;
        if ($p && $p['time']<time()-1800) { $this->files->remove($p['file']['key']); unset($_SESSION['rut_import']); return null; }
        return $p && $p['owner']===$owner && $p['id']===$id && $p['year']===$year?$p:null;
    }

    private function apply(array $preview, array $selected, bool $replace, array $schemas): array
    {
        return (new RutAplicacion($this->documents,$this->files))->apply($preview,$selected,$replace,$schemas);
    }

    public function download(): void
    {
        $user=$this->user(); $raw=\input($_GET,'documento');
        $document=preg_match('/^[1-9][0-9]{0,9}$/D',$raw)?$this->documents->findFor((int)$raw,(int)$user['_ID']):null;
        if (!$document || !is_file($this->files->path($document['archivo']))) { http_response_code(404); echo 'Documento no encontrado.'; return; }
        header('Content-Type: application/pdf'); header('Content-Disposition: attachment; filename="RUT-soporte.pdf"');
        header('X-Content-Type-Options: nosniff'); header('Cache-Control: private, no-store');
        readfile($this->files->path($document['archivo']));
    }

    private function user(): array
    {
        $user=$this->auth->user(); if (!$user) { \redirect($this->config,'login'); } return $user;
    }
}
