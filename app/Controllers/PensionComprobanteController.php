<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\{Contribuyente,Ingreso,ComprobantePension,ExtractoBancario};
use App\Services\{AuthService,AccesoRoles,Csrf,ExtractoUpload,ColpensionesReader,PdfApi};

final class PensionComprobanteController
{
    private ExtractoUpload $files;
    public function __construct(private AuthService $auth,private Contribuyente $taxpayers,private Ingreso $incomes,
        private ComprobantePension $model,private ExtractoBancario $banks,private array $config)
    { $this->files=new ExtractoUpload($config['storage_path']??dirname(__DIR__,2).'/storage'); }
    public function handle(): void
    {
        $user=$this->auth->user();if (!$user) { PdfApi::send(['error'=>'La sesión venció. Inicia sesión y vuelve a abrir Pensiones.'],401);return; }
        $owner=(int)$user['_ID'];$profile=$this->incomes->profileFor((int)\input($_GET,'perfil'),$owner);
        if (!$profile) { PdfApi::send(['error'=>'Perfil no encontrado.'],404);return; }
        $taxpayer=$this->taxpayers->findFor((int)$profile['contribuyente_id'],$owner);
        $file=null;$adopted=false;
        try {
            if ($_SERVER['REQUEST_METHOD']==='POST') {
                if (!Csrf::valid(\input($_POST,'_csrf_token'))) { PdfApi::send(['error'=>'Formulario vencido. Recarga Pensiones antes de continuar.'],419);return; }
                if (!$taxpayer || !AccesoRoles::edit($user,$taxpayer)) { PdfApi::send(['error'=>'No tienes permiso para completar esta ficha.'],403);return; }
                if (\input($_POST,'accion')==='guardar') {
                    $id=(int)\input($_POST,'comprobante');$doc=$this->model->findFor($id,$owner);
                    if (!$doc || (int)$doc['perfil_id']!==(int)$profile['id']) { PdfApi::send(['error'=>'Comprobante no encontrado.'],404);return; }
                    if (\input($_POST,'confirmado')!=='1') { throw new \DomainException('Revisa el titular, los meses y los valores en el comprobante antes de confirmar.'); }
                    if (strlen(\input($_POST,'filas'))>20000 || strlen(\input($_POST,'versiones'))>2000) { throw new \DomainException('La vista previa excede doce meses. Vuelve a abrir el comprobante.'); }
                    $rows=json_decode(\input($_POST,'filas'),true,32,JSON_THROW_ON_ERROR);
                    $expected=json_decode(\input($_POST,'versiones'),true,32,JSON_THROW_ON_ERROR);
                    if (!is_array($rows) || !is_array($expected)) { throw new \DomainException('Revisa la vista previa del comprobante.'); }
                    $this->model->confirm($id,$owner,$rows,$expected,\input($_POST,'reemplazar')==='1');
                    PdfApi::send(['guardado'=>true,'id'=>$id,'impuesto'=>$this->summary($profile,$owner)]);return;
                }
                $file=$this->files->receive(PdfApi::file($_FILES,'comprobante_pdf'),PdfApi::file($_FILES,'comprobante_lectura'));
                $copy=array_intersect_key($file['lectura'],['archivo_consulta'=>true,'sha256_consulta'=>true]);
                $reading=ColpensionesReader::read($this->files->path($copy['archivo_consulta']??$file['archivo']),$taxpayer['documento_numero']);
                foreach ($reading['registros']??[] as $row) {
                    if (substr($row['periodo'],0,4)!==(string)$profile['anio']) { throw new \DomainException('El año leído no coincide con el año que tienes abierto en Pensiones.'); }
                }
                $file['lectura']=$reading+$copy;$saved=$this->model->store((int)$profile['id'],$owner,$file);$adopted=!$saved['duplicado'];
                if ($saved['duplicado']) { $this->files->discard($file);$file=null; }$id=$saved['id'];
            } else { $id=(int)\input($_GET,'comprobante'); }
            $doc=$this->model->findFor($id,$owner);
            if (!$doc || (int)$doc['perfil_id']!==(int)$profile['id']) { PdfApi::send(['error'=>'Comprobante no encontrado.'],404);return; }
            $versions=[];$confirmed=[];foreach ($this->model->rows((int)$profile['id'],$owner) as $row) {
                $versions[$row['mes']]=(int)$row['id'];if ((int)$row['comprobante_id']===$id) { $confirmed[]=$row['datos']; }
            }
            $publicReading=array_diff_key($doc['lectura'],['archivo_consulta'=>true,'sha256_consulta'=>true]);
            if ($confirmed) { $publicReading['registros']=$confirmed; }
            PdfApi::send(['id'=>$id,'titulo'=>$doc['titulo'],'lectura'=>$publicReading,'versiones'=>$versions,
                'pdf'=>\url($this->config,'pensiones/documento').'&comprobante='.$id]);
        } catch (\DomainException|\InvalidArgumentException|\JsonException $e) {
            if ($file && !$adopted) { $this->files->discard($file); }PdfApi::send(['error'=>$e->getMessage()],422);
        } catch (\Throwable $e) {
            if ($file && !$adopted) { $this->files->discard($file); }error_log((string)$e);PdfApi::send(['error'=>'No fue posible completar la carga. Se conservó el diagnóstico privado; no vuelvas a digitar tus ingresos.'],500);
        }
    }
    public function document(): void
    {
        $user=$this->auth->user();if (!$user) { http_response_code(401);return; }
        $doc=$this->model->findFor((int)\input($_GET,'comprobante'),(int)$user['_ID']);
        if (!$doc) { http_response_code(404);return; }
        $path=$this->files->path($doc['lectura']['archivo_consulta']??$doc['archivo']);
        if (!is_file($path)) { http_response_code(404);return; }
        header('Content-Type: application/pdf');header('Content-Disposition: inline; filename="comprobante-colpensiones.pdf"');
        header('X-Frame-Options: SAMEORIGIN');header('Cache-Control: private, no-store');header("Content-Security-Policy: frame-ancestors 'self'");readfile($path);
    }
    private function summary(array $profile,int $owner): array
    {
        return \App\Services\RentaAcumulada::build($profile,$this->incomes->movements((int)$profile['id'],$owner),$this->incomes->payroll((int)$profile['id'],$owner),
            \App\Services\BancoResumen::build($this->banks->listFor((int)$profile['contribuyente_id'],$owner,(int)$profile['anio'])),false,0,$this->model->rows((int)$profile['id'],$owner));
    }
}
