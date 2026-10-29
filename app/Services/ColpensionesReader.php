<?php
declare(strict_types=1);
namespace App\Services;

/** Lectura prudente de comprobantes mensuales; nunca distribuye un certificado anual. */
final class ColpensionesReader
{
    public static function read(string $path,string $document): array
    {
        try {
            require_once dirname(__DIR__,2).'/bootstrap/pdfparser.php';
            $config=new \Smalot\PdfParser\Config();$config->setRetainImageContent(false);
            $config->setDecodeMemoryLimit(16777216);$config->setMaxNestingDepth(50);
            $pdf=(new \Smalot\PdfParser\Parser([],$config))->parseFile($path);$pages=$pdf->getPages();
            if (!$pages || count($pages)>24) { throw new \InvalidArgumentException('Se admiten hasta 24 páginas por comprobante.'); }
            $texts=[];foreach ($pages as $page) { $texts[]=$page->getText(); }
            return self::parse($texts,$document);
        } catch (\DomainException $e) { throw $e; }
        catch (\Throwable) { return ['estado'=>'pendiente','motivo'=>'No se pudo leer el texto. Revisa el PDF aquí y completa únicamente los datos faltantes.','registros'=>[]]; }
    }
    public static function parse(array $pages,string $document): array
    {
        $text=self::normalize(implode("\n",$pages));
        if (strlen($text)>1500000) { throw new \DomainException('El documento excede el alcance de lectura.'); }
        if (trim($text)==='') { return ['estado'=>'pendiente','motivo'=>'PDF sin texto legible; revisa el comprobante en el visor.','registros'=>[]]; }
        if (!str_contains($text,'COLPENSIONES')) { throw new \DomainException('El PDF no se identifica como comprobante de Colpensiones. No subas aquí el extracto del banco.'); }
        preg_match_all('/(?:CEDULA(?:\s+DE\s+CIUDADANIA)?|C\.?\s*C\.?|DOCUMENTO(?:\s+DE\s+IDENTIFICACION)?)\s*(?:N[O°º.]*)?\s*[:.#-]?\s*([0-9][0-9. ]{4,24})/u',$text,$ids);
        $ids=array_map(static fn($v)=>preg_replace('/\D/','',$v),$ids[1]);
        if ($ids && !in_array($document,$ids,true)) { throw new \DomainException('La identificación del comprobante no coincide con el contribuyente elegido.'); }
        $records=[];$blocked=false;
        foreach ($pages as $page) {
            $p=self::normalize($page);$period=self::period($p);
            if (!$period) { continue; }
            if ($period['desde']!==$period['hasta']) { $blocked=true;continue; }
            $gross=self::amount($p,'(?:TOTAL\s+DEVENGAD[OA]S?|PENSION\s+BRUTA|MESADA\s+BRUTA)');
            $health=self::amount($p,'(?:APORTES?(?:\s+OBLIGATORIOS?)?(?:\s+A)?\s+(?:LA\s+)?SALUD|DESCUENTO(?:\s+DE)?\s+SALUD)');
            if ($health===null && preg_match('/(?:DEDUCIDOS|DEDUCCIONES)(.+?)(?:TOTAL\s+DEDUCID[OA]S?|TOTAL\s+DEDUCCIONES|NETO)/s',$p,$deductions)) { $health=self::amount($deductions[1],'SALUD'); }
            $special=(bool)preg_match('/RETROACTIV|AJUSTE|REINTEGRO|INDEMNIZACION/',$p);
            $solidarity=self::amount($p,'(?:FONDO\s+(?:DE\s+)?SOLIDARIDAD(?:\s+PENSIONAL)?|SOLIDARIDAD)');
            if ($solidarity!==null && $solidarity>0) { $special=true; }
            $key=$period['desde'];
            if (isset($records[$key])) { throw new \DomainException('Se detectó más de un bloque del mismo mes. Revisa los comprobantes por separado para evitar duplicados.'); }
            $records[$key]=['periodo'=>$key,'bruto'=>$gross,'salud'=>$health,'especial'=>$special,
                'libranza'=>self::amount($p,'(?:TOTAL\s+LIBRANZAS?|DESCUENTO(?:\s+DE)?\s+LIBRANZA|LIBRANZA)'),
                'neto'=>self::amount($p,'(?:NETO\s+A\s+PAGAR|TOTAL\s+NETO|NETO)')];
        }
        if ($blocked || (!$records && preg_match('/ANUAL|A[ÑN]O\s+GRAVABLE/u',$text))) { return ['estado'=>'pendiente','anual_sin_desglose'=>true,'motivo'=>'El documento abarca varios meses sin un desglose mensual inequívoco. Sube comprobantes mensuales; no se divide el total anual.','registros'=>[]]; }
        $particular=(bool)preg_match('/RETROACTIV|AJUSTE|REINTEGRO|INDEMNIZACION/',$text);
        $solidarity=self::amount($text,'(?:FONDO\s+(?:DE\s+)?SOLIDARIDAD(?:\s+PENSIONAL)?|SOLIDARIDAD)');
        return ['estado'=>$records?'revisar':'pendiente','identificacion_leida'=>(bool)$ids,'revision_particular'=>$particular || ($solidarity!==null && $solidarity>0),
            'campos_sin_periodo'=>count($pages)===1?['bruto'=>self::amount($text,'(?:TOTAL\s+DEVENGAD[OA]S?|PENSION\s+BRUTA|MESADA\s+BRUTA)'),
                'salud'=>self::amount($text,'(?:APORTES?(?:\s+OBLIGATORIOS?)?(?:\s+A)?\s+(?:LA\s+)?SALUD|DESCUENTO(?:\s+DE)?\s+SALUD)')]:[],
            'motivo'=>$records?'Revisa titular, período, bruto y salud antes de guardar.':'No se identificaron todos los campos mensuales. Revisa el PDF y completa lo que falta.',
            'registros'=>array_values($records)];
    }
    public static function normalize(string $text): string
    { return strtoupper(strtr($text,['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U',"\xc2\xa0"=>' '])); }
    private static function period(string $text): ?array
    {
        if (preg_match('/PERIODO\s*[^\n]{0,100}?((?:20)[0-9]{2})[-\/]([01]?[0-9])\s*(?:A|AL|HASTA|-)\s*((?:20)[0-9]{2})[-\/]([01]?[0-9])/',$text,$m)) {
            return ['desde'=>$m[1].'-'.str_pad($m[2],2,'0',STR_PAD_LEFT),'hasta'=>$m[3].'-'.str_pad($m[4],2,'0',STR_PAD_LEFT)];
        }
        $months=implode('|',array_map([self::class,'normalize'],ExtractoData::MONTHS));
        if (preg_match('/(?:PERIODO|MES\s+DE\s+PAGO|MESADA(?:\s+DE)?)\s*[^\n]{0,35}?('.$months.')\s*(?:DE\s*)?(20[0-9]{2})/',$text,$m)) {
            $month=array_search($m[1],array_map([self::class,'normalize'],ExtractoData::MONTHS),true);
            $period=$m[2].'-'.str_pad((string)$month,2,'0',STR_PAD_LEFT);return ['desde'=>$period,'hasta'=>$period];
        }
        if (preg_match('/PERIODO\s*[:=-]?\s*(20[0-9]{2})[-\/]([01]?[0-9])(?:\s|$)/',$text,$m)) {
            $period=$m[1].'-'.str_pad($m[2],2,'0',STR_PAD_LEFT);return ['desde'=>$period,'hasta'=>$period];
        }
        return null;
    }
    private static function amount(string $text,string $label): ?int
    {
        if (!preg_match('/'.$label.'\s*[:=|]?\s*\$?\s*([0-9][0-9.,]*)(?![0-9])/',$text,$m)) { return null; }
        try { return PensionImportData::cents($m[1]); }catch (\InvalidArgumentException) { return null; }
    }
}
