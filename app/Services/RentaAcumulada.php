<?php
declare(strict_types=1);
namespace App\Services;

/** Escenario al corte, sin convertir abonos bancarios netos en pensión bruta. */
final class RentaAcumulada
{
    public static function build(array $profile,array $movements,array $payroll,array $banks,bool $projected=false,int $saving=0): array
    {
        if ($profile['tipo']!=='natural' || $profile['residencia']!=='residente' || $profile['regimen']!=='ordinario') {
            return ['estado'=>'pendiente','errores'=>['Este resumen corresponde a personas naturales residentes en régimen ordinario. Las sociedades y otros regímenes requieren su cálculo propio.']];
        }
        $raw=SimuladorFields::read(array_fill_keys(SimuladorFields::numeric(),'0'));
        $raw=array_replace($raw,['ceros_confirmados'=>'1','elegibilidad'=>'1','pensionado'=>'si',
            'servicios'=>'no_aplica','declaraciones'=>'3','metodo_anticipo'=>'actual']);
        $gross=0;$contributions=0;$pension=0;$health=0;$cut='';$foreign=false;$other=0;
        foreach ($movements as $movement) {
            if ($movement['estado']!=='real' && !$projected) { continue; }
            $code=(int)$movement['casilla'];$value=(int)$movement['valor_cop'];
            if (!in_array($code,[32,33,99,100],true)) { if (in_array($code,[43,58,74,104,107,108,109,112],true)) { $other+=$value; }continue; }
            $month=(int)substr($movement['fecha'],5,2);$cut=max($cut,$movement['fecha']);
            if ($code===32) { $gross+=$value;$raw['salario_'.$month]=(string)((int)$raw['salario_'.$month]+$value); }
            elseif ($code===33) { $contributions+=$value; }
            elseif ($code===99) {
                $pension+=$value;$raw['pension_'.$month]=(string)((int)$raw['pension_'.$month]+$value);
                if (!in_array(mb_strtolower(trim($movement['pais'])),['colombia','co','col'],true)) { $foreign=true; }
            } else { $health+=$value;$raw['salud_pension_'.$month]=(string)((int)$raw['salud_pension_'.$month]+$value); }
        }
        $raw['incr_laboral']=(string)$contributions;$withheld=0;$special=false;
        foreach ($payroll as $row) {
            $values=NominaData::amounts($row['datos']['valores']);$withheld+=$values['retencion'];
            if ($values['retroactivos']>0) { $special=true; }
        }
        $raw['retenciones']=(string)(int)round($withheld/100);
        $pending=[];
        if ($banks['pendientes']>0) { $pending[]='Hay extractos pendientes de lectura. Sus importes no se presumen cero ni están incorporados en esta estimación.'; }
        if ($banks['totales']['pension']>0 && $pension===0) { $pending[]='Falta la pensión bruta y la salud del comprobante del pagador: el neto bancario no permite completar el impuesto de pensión.'; }
        if ($pension>0 && $health===0) { $pending[]='Confirma la salud descontada de la pensión. En este escenario se ha usado 0; corrígelo si hubo aportes.'; }
        if ($pension>0) { $pending[]='La exención pensional simulada supone reconocimiento conforme a la Ley 100. Confirma el derecho y todas las mesadas del mes, incluidas otras pensiones.'; }
        $missingMonths=[];
        foreach ($banks['cuentas'] as $account) {
            foreach ($account['meses'] as $month=>$entry) {
                if ($entry['leido'] && $entry['valores']['pension']>0 && (int)$raw['pension_'.$month]===0) { $missingMonths[$month]=ExtractoData::MONTHS[$month]; }
            }
        }
        if ($missingMonths) { ksort($missingMonths);$pending[]='Meses con pensión bancaria y sin bruto registrado: '.implode(', ',$missingMonths).'.'; }
        if ($other>0 || $banks['totales']['rendimientos']>0) { $pending[]='Este cálculo cubre salarios y pensiones; los demás ingresos y rendimientos deben incorporarse en el escenario completo.'; }
        if ($foreign || $special) {
            return ['estado'=>'pendiente','errores'=>[$foreign?'Hay pensión del exterior: revisa su tratamiento particular en el escenario completo.':'Hay retroactivos de nómina: requieren revisión particular.']];
        }
        if ($gross+$pension===0) { return ['estado'=>'pendiente','errores'=>['Registra salarios o la pensión bruta antes de estimar su impuesto.']]; }
        $result=RentaSimulador::calculate($raw,$profile);
        if ($result['estado']!=='calculado') { return $result; }
        $gmf=max(0,$banks['totales']['gmf']-$banks['totales']['gmf_reintegro']);
        $withGmf=$raw;$withGmf['b_gmf']=(string)(int)round($gmf/100);
        $alternative=RentaSimulador::calculate($withGmf,$profile);
        $withSaving=$raw;$withSaving['b_fpv']=(string)$saving;$withSaving['base_aportes']=(string)$gross;
        $savingResult=RentaSimulador::calculate($withSaving,$profile);
        return $result+['bruto_laboral'=>$gross,'aportes'=>$contributions,'corte'=>$cut,'pendientes'=>$pending,
            'meses_pension_pendientes'=>array_values($missingMonths),
            'neto_bancario_pension'=>$banks['totales']['pension'],'otros_ingresos'=>$other,
            'por_cubrir'=>max(0,$result['impuesto_neto']-$result['retenciones']),
            'gmf_pagado'=>$gmf,'ahorro_gmf'=>$alternative['estado']==='calculado'?max(0,$result['impuesto_neto']-$alternative['impuesto_neto']):null,
            'aporte_simulado'=>$saving,'aporte_limitado'=>$savingResult['deducciones']['partidas']['afc_fpv']??null,
            'aporte_motivo'=>$savingResult['estado']==='calculado'?'':implode(' ',$savingResult['errores']),
            'impuesto_con_aporte'=>$savingResult['impuesto_neto']??null,
            'ahorro_aporte'=>$savingResult['estado']==='calculado'?max(0,$result['impuesto_neto']-$savingResult['impuesto_neto']):null];
    }
}
