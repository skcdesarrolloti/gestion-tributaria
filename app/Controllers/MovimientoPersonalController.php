<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Services\{AuthService,AccesoRoles,Csrf,ExtractoData,PdfApi};
use App\Models\{ExtractoBancario,Contribuyente,Caracterizacion};

final class MovimientoPersonalController
{
    public const CATEGORIES=['pendiente','personal','actividad','traslado','prestamo','devolucion'];
    public function __construct(private AuthService $auth,private ExtractoBancario $banks,private Contribuyente $taxpayers,private Caracterizacion $characterization) {}
    public function handle(): void
    {
        $user=$this->auth->user();if (!$user) { PdfApi::send(['error'=>'Inicia sesión antes de clasificar.'],401);return; }
        $owner=(int)$user['_ID'];$doc=$this->banks->findFor((int)\input($_GET,'documento'),$owner);
        if (!$doc) { PdfApi::send(['error'=>'Extracto no encontrado.'],404);return; }
        $taxpayer=$this->taxpayers->findFor((int)$doc['contribuyente_id'],$owner);
        if (!$taxpayer || $taxpayer['tipo']!=='natural') { PdfApi::send(['error'=>'Este control corresponde a una ficha personal.'],404);return; }
        $block=$this->characterization->blocksFor((int)$taxpayer['id'],$owner,(int)$doc['anio'])['movimientos_bancarios']??[];
        $data=$block['datos']??[];$revision=(int)($block['revision']??0);$prefix=$doc['id'].'-';
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (!Csrf::valid(\input($_POST,'_csrf_token'))) { PdfApi::send(['error'=>'Recarga antes de guardar.'],419);return; }
            if (!AccesoRoles::edit($user,$taxpayer)) { PdfApi::send(['error'=>'No puedes modificar esta ficha.'],403);return; }
            try {
                $raw=\input($_POST,'clasificaciones');if (strlen($raw)>30000) { throw new \InvalidArgumentException('Guarda hasta 200 movimientos por vez.'); }
                $posted=json_decode($raw,true,16,JSON_THROW_ON_ERROR);$reading=ExtractoData::reading($doc);
                if (!is_array($posted) || count($posted)>200 || ($reading['estado']??'')!=='leido') { throw new \InvalidArgumentException('Revisa los movimientos legibles del extracto.'); }
                if ((string)$revision!==\input($_POST,'revision')) { PdfApi::send(['error'=>'La clasificación cambió en otra ventana. Recarga para conservarla.'],409);return; }
                foreach ($posted as $index=>$category) {
                    if (!ctype_digit((string)$index) || !in_array($category,self::CATEGORIES,true) || !in_array($reading['movimientos'][(int)$index]['categoria']??'', ['otros_abonos','otros_debitos'],true)) { throw new \InvalidArgumentException('Solo se clasifican otros abonos y cargos de este documento.'); }
                    $data[$prefix.$index]=$category;
                }
                if (!$this->characterization->saveFor((int)$taxpayer['id'],$owner,(int)$doc['anio'],'movimientos_bancarios',$data,$revision)) { PdfApi::send(['error'=>'Cambió la clasificación. Recarga y revisa antes de guardar.'],409);return; }$revision++;
            } catch (\InvalidArgumentException|\JsonException $e) { PdfApi::send(['error'=>$e->getMessage()],422);return; }
        }
        $selected=[];foreach ($data as $key=>$value) { if (str_starts_with((string)$key,$prefix)) { $selected[substr((string)$key,strlen($prefix))]=$value; } }
        PdfApi::send(['revision'=>$revision,'clasificaciones'=>$selected]);
    }
}
