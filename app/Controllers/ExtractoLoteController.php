<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\{Contribuyente,ExtractoBancario,PrestamoPersonal};
use App\Services\{AuthService,AccesoRoles,Csrf,ExtractoUpload,ExtractoData,PdfApi,CreditoCarga};

/** Recibe cada PDF por separado para respetar el límite de carga del servidor. */
final class ExtractoLoteController
{
    public function __construct(private AuthService $auth,private Contribuyente $taxpayers,private ExtractoBancario $model,private array $config,private PrestamoPersonal $loans) {}
    public function upload(): void
    {
        $user=$this->auth->user();if (!$user) { PdfApi::send(['error'=>'La sesión venció. Inicia sesión y vuelve a cargar los archivos pendientes.'],401);return; }
        if (!Csrf::valid(\input($_POST,'_csrf_token'))) { PdfApi::send(['error'=>'Formulario vencido. Recarga antes de continuar.'],419);return; }
        $owner=(int)$user['_ID'];$taxpayer=$this->taxpayers->findFor((int)\input($_GET,'contribuyente'),$owner);
        if (!$taxpayer) { PdfApi::send(['error'=>'Contribuyente no encontrado.'],404);return; }
        if (!AccesoRoles::edit($user,$taxpayer)) { PdfApi::send(['error'=>'No tienes permiso para cargar en esta ficha.'],403);return; }
        $files=new ExtractoUpload($this->config['storage_path']??dirname(__DIR__,2).'/storage');$file=null;
        try {
            $year=\input($_GET,'anio');if (!in_array($year,['2025','2026'],true)) { throw new \InvalidArgumentException('Selecciona el año del extracto.'); }
            $file=$files->receive(PdfApi::file($_FILES,'extracto_pdf'),PdfApi::file($_FILES,'extracto_lectura'));
            $credit=CreditoCarga::read($file,$files,$taxpayer,$year);
            if ($credit) {
                $id=$this->loans->save((int)$taxpayer['id'],$owner,$file,$credit);$file=null;
                PdfApi::send(['guardado'=>true,'id'=>$id,'tipo'=>'credito','banco'=>$credit['banco'],'cuenta'=>substr($credit['credito'],-4),'mes'=>(int)substr($credit['pago'],5,2),'leido'=>true]);return;
            }
            $reading=$file['lectura'];$posted=$_POST;
            if (isset($reading['inicio'],$reading['cuenta'])) {
                $posted['banco']=$reading['banco']??'BBVA';$posted['cuenta']=$reading['cuenta'];$posted['mes']=(string)(int)substr($reading['inicio'],5,2);
            } elseif (\input($posted,'mes','auto')==='auto') {
                throw new \InvalidArgumentException('No se detectó el período. Indica el mes, el banco y la cuenta de este PDF para conservarlo pendiente de lectura.');
            }
            $data=ExtractoData::read($posted,$year);ExtractoData::match($reading,$data,$taxpayer);
            $file['lectura']['titular_confirmado']=\input($_POST,'titular_confirmado')==='1';
            $id=$this->model->saveFor((int)$taxpayer['id'],$owner,$data,$file);$file=null;
            PdfApi::send(['guardado'=>true,'id'=>$id,'banco'=>$data['banco'],'cuenta'=>$data['cuenta'],'mes'=>$data['mes'],
                'meses'=>$reading['meses_cubiertos']??[$data['mes']],'leido'=>($reading['estado']??'')==='leido']);
        } catch (\DomainException $e) {
            $duplicate=false;
            if ($file) {
                foreach ($this->model->listFor((int)$taxpayer['id'],$owner,(int)$year) as $existing) { if (hash_equals($existing['sha256'],$file['sha256'])) { $duplicate=true;break; } }
                if (($file['lectura']['tipo_documento']??'')==='credito') { foreach ($this->loans->listFor((int)$taxpayer['id'],$owner) as $existing) { if (hash_equals($existing['sha256'],$file['sha256'])) { $duplicate=true;break; } } }
                $files->discard($file);
            }
            PdfApi::send(['error'=>$duplicate?'Este PDF ya estaba guardado.':$e->getMessage(),'duplicado'=>$duplicate],409);
        } catch (\InvalidArgumentException $e) {
            if ($file) { $files->discard($file); }PdfApi::send(['error'=>$e->getMessage()],422);
        } catch (\Throwable $e) {
            if ($file) { $files->discard($file); }error_log((string)$e);PdfApi::send(['error'=>'No fue posible guardar este PDF. Se conservó el diagnóstico privado.'],500);
        }
    }
}
