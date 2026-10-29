<?php
declare(strict_types=1);
namespace App\Services;

/** El pago anterior es real; la cuota siguiente sigue pendiente. Importes en centavos. */
final class PrestamoCajaSocial
{
    public static function read(string $path,string $name): array
    {
        require_once dirname(__DIR__,2).'/bootstrap/pdfparser.php';
        $config=new \Smalot\PdfParser\Config();$config->setRetainImageContent(false);$config->setDecodeMemoryLimit(16777216);
        $pdf=(new \Smalot\PdfParser\Parser([],$config))->parseFile($path);$pages=$pdf->getPages();
        if (count($pages)!==1) { throw new \DomainException('Revisa este formato: se espera un extracto mensual de crédito personal de una página.'); }
        return self::parse($pages[0]->getText(),$name);
    }
    public static function parse(string $text,string $name): array
    {
        if (strlen($text)>1000000) { throw new \DomainException('El texto del documento excede el alcance del lector.'); }
        $t=self::normal($text);
        if (!str_contains($t,'CAJA SOCIAL') && !str_contains($t,'BANCOCAJASOCIAL')) { throw new \DomainException('No se identifica Banco Caja Social.'); }
        if (!str_contains($t,'CREDITO DE CONSUMO') || str_contains($t,'CREDITO COMERCIAL')) { throw new \DomainException('Aquí solo se reciben créditos personales de consumo; los de la empresa quedan fuera.'); }
        $compact=static fn($v)=>preg_replace('/[^A-Z0-9]/','',self::normal($v));
        if (!str_contains($compact(substr($t,0,600)),$compact($name))) { throw new \DomainException('El titular no coincide con la persona elegida. No se ha guardado el préstamo.'); }
        if (!preg_match('/NUMERO\s+DE\s+CREDITO\s*([0-9]{6,24})/',$t,$credit)) { throw new \DomainException('No se pudo reconocer el número de crédito.'); }
        $parts=preg_split('/DISTRIBUCION\s+DE\s+LA\s+CUOTA\s+A\s+PAGAR/',$t);
        if (count($parts)!==2 || !preg_match('/DISTRIBUCION\s+DEL\s+PAGO\s+ANTERIOR(.*)/s',$parts[0],$paid)) { throw new \DomainException('Falta separar el pago anterior de la próxima cuota.'); }
        $payment=self::date($paid[1],'FECHA\s+PAGO\s+ANTERIOR');$invoice=self::date($t,'FECHA\s+DE\s+FACTURACION');
        $previous=self::amounts($paid[1],false);$next=self::amounts($parts[1],true);
        return ['banco'=>'BANCO CAJA SOCIAL','credito'=>$credit[1],'factura'=>$invoice,'pago'=>$payment,
            'saldo'=>self::amount($t,'SALDO\s+A\s+CAPITAL\s+EN\s+PESOS',true),'pagado'=>$previous,'proxima'=>$next,
            'vence'=>self::date($t,'PAGUE\s+HASTA')];
    }
    public static function normal(string $s): string
    { return preg_replace('/\s+/u',' ',str_replace(["'",'~'],'',strtoupper(iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$s)?:$s))); }
    private static function date(string $text,string $label): string
    {
        if (!preg_match('/'.$label.'\s*(20[0-9]{2})[\/-]([0-9]{2})[\/-]([0-9]{2})/',$text,$m) || !checkdate((int)$m[2],(int)$m[3],(int)$m[1])) { throw new \DomainException('No se pudo leer una fecha del crédito. Revisa el PDF.'); }
        return "$m[1]-$m[2]-$m[3]";
    }
    private static function amount(string $text,string $label,bool $required=false): ?int
    {
        if (!preg_match('/'.$label.'\s*([0-9][0-9,.]*\.[0-9]{2})(?![0-9])/',$text,$m)) {
            if ($required) { throw new \DomainException('Falta un importe principal del préstamo. No se presumen ceros.'); }return null;
        }
        return PensionImportData::cents($m[1]);
    }
    private static function amounts(string $text,bool $next): array
    {
        $labels=['capital'=>'ABONO\s+A\s+CAPITAL','intereses'=>'INTERESES\s+CORRIENTES','mora'=>'INTERESES(?:\s+DE)?\s+MORA',
            'vida'=>'SEGURO\s+DE\s+VIDA','incendio'=>'SEGURO\s+DE\s+INCENDIO\s+Y\s+TERREMOTO',
            'comision'=>'COMISION\s*\(MICROF\.\s*FNG,\s*ONG\)','cobranza'=>'CARGOS\s+(?:ADTIVOS|ADMINISTRATIVOS)\s+(?:DE\s+)?COBRANZA',
            'pendiente'=>'VALOR\s+PENDIENTE\s+POR\s+APLICAR','total'=>$next?'VALOR\s+A\s+PAGAR':'TOTAL'];
        $row=[];foreach ($labels as $key=>$label) { $row[$key]=self::amount($text,$label,in_array($key,['capital','intereses','total'],true)); }
        $sum=array_sum(array_intersect_key($row,array_flip(['capital','intereses','mora','vida','incendio','comision','cobranza'])));
        if ($next) { $sum-=$row['pendiente']??0; }
        if (abs($sum-$row['total'])>1) { throw new \DomainException('El desglose no coincide con el total del crédito. Conserva el PDF para revisar; no se importan importes parciales.'); }
        return $row;
    }
}
