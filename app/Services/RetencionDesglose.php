<?php
declare(strict_types=1);
namespace App\Services;

/** Descripción de la misma cuenta usada por el motor, con enteros en centavos. */
final class RetencionDesglose
{
    public static function build(array $values,array $result,int $year): array
    {
        $rules=ParametrosAnuales::year($year);$uvt=$rules['uvt']*100;$base=$result['base'];
        foreach ($rules['tramos'] as $row) { if ($row[1]===null || $base<=$row[1]*$uvt) { $bracket=$row;break; } }
        [$lower,$upper,$rate,$fixed]=$bracket;
        $origin=$values['conceptos']??$values;$income=[];$contributions=[];
        foreach (['salario','sobresueldo','comisiones','incapacidad','auxilio','retroactivos'] as $key) {
            if (($origin[$key]??0)>0) { $income[NominaData::LABELS[$key][0]]=$origin[$key]; }
        }
        foreach (['salud','pension','solidaridad'] as $key) {
            if (isset($origin[$key])) { $contributions[NominaData::LABELS[$key][0]]=$origin[$key]; }
        }
        return ['bruto'=>$values['devengado'],'aportes'=>$values['aportes'],
            'ingresos_detalle'=>$income,'aportes_detalle'=>$contributions,
            'neto'=>max(0,$values['devengado']-$values['aportes']),'beneficios'=>$result['beneficios'],
            'deducciones'=>$result['deducciones']??0,'exencion'=>$result['exencion25']??$result['beneficios'],
            'base'=>$base,'uvt'=>$uvt,'inferior'=>$lower*$uvt,'superior'=>$upper===null?null:$upper*$uvt,
            'tarifa'=>$rate,'fijo'=>$fixed*$uvt,'excedente'=>max(0,$base-$lower*$uvt),
            'sin_redondear'=>RetencionTarifa::calculate($base,$rules['uvt'],$rules['tramos']),
            'esperada'=>$result['esperada'],'real'=>$result['real'],'year'=>$year,'rules'=>$rules];
    }
}
