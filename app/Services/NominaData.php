<?php
declare(strict_types=1);
namespace App\Services;

final class NominaData
{
    public const FIELDS=['salario','retroactivos','sobresueldo','comisiones','incapacidad','auxilio','devengado',
        'pension','salud','solidaridad','retencion','otros_descuentos','deducido','neto'];
    public const LABELS=[
        'salario'=>['Salario liquidado','32'],'retroactivos'=>['Retroactivos','32'],
        'sobresueldo'=>['Sobresueldo (nombre de origen)','32'],'comisiones'=>['Comisiones expresas','32'],
        'incapacidad'=>['Incapacidad pagada','32'],'auxilio'=>['Auxilio de transporte','32'],
        'devengado'=>['Total devengado del listado','32'],'salud'=>['Salud del trabajador','33'],
        'pension'=>['Pensión del trabajador','33'],'solidaridad'=>['Solidaridad pensional','33'],
        'aportes'=>['Aportes obligatorios registrados','33'],'retencion'=>['Retención por renta','132'],
        'otros_descuentos'=>['Otros descuentos (sin beneficio automático)','Control'],
        'deducido'=>['Total descontado del listado','Control'],'neto_calculado'=>['Neto calculado','Control'],
        'neto'=>['Neto del listado (no prueba de consignación)','Control'],
        'diferencia'=>['Diferencia neto listado - calculado','Control'],
        'liquida'=>['Renta líquida antes de beneficios','34'],
    ];
    public static function read(array $raw,array $taxpayer,array $profile): array
    {
        if ($taxpayer['tipo']!=='natural' || $profile['formulario']!=='210' || $profile['regimen']!=='ordinario') {
            throw new \DomainException('La nómina requiere persona natural residente en régimen ordinario. Prepara ese año en Ingresos.');
        }
        $r=[];
        foreach (['documento'=>20,'empleador_documento'=>20,'empleador'=>150,'inicio'=>10,'fin'=>10,'archivo'=>180,'hoja'=>80,'archivo_sha'=>64] as $key=>$max) {
            $v=$raw[$key]??'';
            if (!is_string($v) || trim($v)==='' || mb_strlen($v)>$max || preg_match('/[\x00-\x1f]/',$v)) { throw new \DomainException('Revisa el campo '.$key.' del archivo.'); }
            $r[$key]=trim($v);
        }
        if (!preg_match('/^[0-9]{5,20}$/D',$r['documento']) || $r['documento']!==(string)$taxpayer['documento_numero']) {
            throw new \DomainException('La identificación de la nómina no coincide exactamente con el contribuyente. No se vincula por nombre.');
        }
        if (!preg_match('/^[0-9]{5,20}$/D',$r['empleador_documento']) || !preg_match('/^[a-f0-9]{64}$/D',$r['archivo_sha'])) { throw new \DomainException('Revisa NIT del empleador y huella del archivo.'); }
        foreach (['inicio','fin'] as $k) {
            $d=\DateTimeImmutable::createFromFormat('!Y-m-d',$r[$k]);
            if (!$d || $d->format('Y-m-d')!==$r[$k] || $d->format('Y')!==(string)$profile['anio'] || !in_array($d->format('Y'),['2025','2026'],true)) { throw new \DomainException('El período no corresponde al año seleccionado.'); }
        }
        if ($r['inicio']>$r['fin'] || substr($r['inicio'],0,7)!==substr($r['fin'],0,7) || $r['fin']>date('Y-m-d')) { throw new \DomainException('Selecciona un período cerrado, dentro de un mismo mes.'); }
        if (isset($raw['periodo_origen'])) {
            if (!is_string($raw['periodo_origen']) || !preg_match('/^\d{4}-\d{2}-\d{2} a \d{4}-\d{2}-\d{2}$/D',$raw['periodo_origen'])) {
                throw new \DomainException('La referencia del período original no es válida.');
            }
            $r['periodo_origen']=$raw['periodo_origen'];
        }
        if (!isset($raw['valores']) || !is_array($raw['valores'])) { throw new \DomainException('Falta el detalle de importes de la nómina.'); }
        foreach (self::FIELDS as $k) {
            $v=$raw['valores'][$k]??null;
            if (!is_int($v) || $v<0 || $v>9999999999999) { throw new \DomainException('Importe inválido en '.$k.'. Se esperan centavos enteros no negativos.'); }
            $r['valores'][$k]=$v;
        }
        $v=$r['valores'];$gross=array_sum(array_intersect_key($v,array_flip(['salario','retroactivos','sobresueldo','comisiones','incapacidad','auxilio'])));
        $ded=$v['pension']+$v['salud']+$v['solidaridad']+$v['retencion']+$v['otros_descuentos'];
        if ($v['devengado']<=0 || abs($gross-$v['devengado'])>100 || abs($ded-$v['deducido'])>100 || abs($v['neto']-($v['devengado']-$ded))>100 || $ded>$v['devengado']) {
            throw new \DomainException('La fila no concilia devengados, descuentos y neto (tolerancia $1). Revisa sus conceptos y fórmulas antes de importar.');
        }
        $r['perfil_id']=(int)$profile['id'];$r['contribuyente_id']=(int)$taxpayer['id'];
        $r['huella']=self::fingerprint($r);
        return $r;
    }
    public static function fingerprint(array $r): string
    {
        return hash('sha256',json_encode([$r['perfil_id'],$r['documento'],$r['empleador_documento'],$r['inicio'],$r['fin'],$r['valores']],JSON_THROW_ON_ERROR));
    }
    public static function amounts(array $v): array
    {
        $v['aportes']=$v['salud']+$v['pension']+$v['solidaridad'];
        $v['neto_calculado']=$v['devengado']-$v['aportes']-$v['retencion']-$v['otros_descuentos'];
        $v['diferencia']=$v['neto']-$v['neto_calculado'];$v['liquida']=$v['devengado']-$v['aportes'];return $v;
    }
    public static function monthly(array $rows): array
    {
        $months=array_fill(1,12,[]);
        foreach ($rows as $row) {
            $m=(int)substr($row['inicio'],5,2);
            foreach (self::amounts($row['datos']['valores']) as $key=>$value) { $months[$m][$key]=($months[$m][$key]??0)+$value; }
            $months[$m]['registros']=($months[$m]['registros']??0)+1;
        }
        return $months;
    }
}
