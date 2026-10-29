<?php
declare(strict_types=1);
namespace App\Services;

final class PensionImportData
{
    public static function cents(string $value): int
    {
        $value=trim($value);$digits='';
        if (preg_match('/^(?:[0-9]{1,3}(?:\.[0-9]{3})+|[0-9]+)(?:,[0-9]{2})?$/D',$value)) {
            $parts=explode(',',$value);$digits=str_replace('.','',$parts[0]).($parts[1]??'00');
        } elseif (preg_match('/^(?:[0-9]{1,3}(?:,[0-9]{3})+|[0-9]+)\.[0-9]{2}$/D',$value)) { $digits=str_replace([',','.'],'',$value); }
        elseif (preg_match('/^[0-9]{1,3}(?:,[0-9]{3})+$/D',$value)) { $digits=str_replace(',','',$value).'00'; }
        if (!$digits || strlen(ltrim($digits,'0'))>13) { throw new \InvalidArgumentException('Revisa el importe; máximo once dígitos en pesos y dos decimales.'); }
        return (int)$digits;
    }
    public static function rows(array $posted,string $year,array $reading): array
    {
        if (!empty($reading['anual_sin_desglose'])) { throw new \DomainException('El certificado anual no permite distribuir valores por mes. Sube comprobantes mensuales.'); }
        if (!empty($reading['revision_particular']) || array_filter($reading['registros']??[],static fn($r)=>!empty($r['especial']))) { throw new \DomainException('Hay ajustes, retroactivos u otros aportes que requieren revisión particular. Se conserva el comprobante sin omitir esas partidas ni importarlo como mesada ordinaria.'); }
        if (!$posted || count($posted)>12 || !array_is_list($posted)) { throw new \DomainException('Revisa los meses del comprobante.'); }
        $rows=[];$seen=[];
        foreach ($posted as $row) {
            if (!is_array($row)) { throw new \DomainException('Revisa el formato de los meses.'); }
            $month=filter_var(\input($row,'mes'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>12]]);
            if (!$month || isset($seen[$month])) { throw new \DomainException('Selecciona meses válidos, sin repetirlos.'); }$seen[$month]=true;
            $period=$year.'-'.str_pad((string)$month,2,'0',STR_PAD_LEFT);
            if ($period>date('Y-m')) { throw new \DomainException('Un comprobante real no puede pertenecer a un mes futuro.'); }
            $gross=self::cents(\input($row,'bruto'));$health=self::cents(\input($row,'salud'));
            if ($gross<=0 || $health>$gross) { throw new \DomainException('La pensión bruta debe ser positiva y la salud no puede superarla.'); }
            $item=['mes'=>$month,'periodo'=>$period,'bruto'=>$gross,'salud'=>$health];
            foreach (['libranza','neto'] as $key) { if (\input($row,$key)!=='') { $item[$key]=self::cents(\input($row,$key));if ($item[$key]>$gross) { throw new \DomainException('La libranza y el neto no pueden superar la pensión bruta. Revisa el comprobante.'); } } }
            if (isset($item['libranza'],$item['neto']) && $health+$item['libranza']+$item['neto']>$gross+1) { throw new \DomainException('Bruto, salud, libranza y neto no concilian. Revisa los importes del comprobante.'); }
            $rows[]=$item;
        }
        $detected=array_column($reading['registros']??[],'periodo');
        if (!$detected && count($rows)!==1) { throw new \DomainException('Un comprobante sin períodos mensuales legibles solo se asocia a un mes tras revisarlo; no se reparte en varios meses.'); }
        if ($detected) {
            $selected=array_column($rows,'periodo');sort($selected);sort($detected);
            if ($selected!==$detected) { throw new \DomainException('Los períodos deben coincidir con el comprobante. Revisa el año seleccionado.'); }
        }
        return $rows;
    }
}
