<?php
declare(strict_types=1);
namespace App\Services;

/** Hipótesis limitada: reajuste desde enero cuya retención P1 resulta cero. No cambia pagos. */
final class RetencionRetroactivo
{
    public static function zeroExample(array $group,array $groups,int $year,array $used): array
    {
        $month=$group['mes'];$rows=$group['rows'];
        if ($month<2 || !$group['completo'] || count($rows)!==2) { return []; }
        $old=$rows[0]['datos']['valores']['salario'];$new=$rows[1]['datos']['valores']['salario'];$delta=$new-$old;
        if ($delta<=0 || $rows[0]['inicio']!==sprintf('%04d-%02d-01',$year,$month)
            || substr($rows[0]['fin'],8)!=='15' || substr($rows[1]['inicio'],8)!=='16'
            || $group['valores']['retroactivos']!==$delta*(2*($month-1)+1)) { return []; }
        $rules=ParametrosAnuales::year($year);$limit=$rules['tramos'][0][1]*$rules['uvt']*100;
        // Comprueba meses anteriores con su porción del reajuste y sin inventar aportes adicionales.
        $benefits=0;
        for ($m=1;$m<$month;$m++) {
            $prior=$groups[$m.'-'.$group['documento']]??null;
            if (!$prior || !$prior['completo'] || count($prior['rows'])!==2 || $prior['valores']['retroactivos']!==0) { return []; }
            foreach ($prior['rows'] as $r) { if ($r['datos']['valores']['salario']!==$old) { return []; } }
            $net=max(0,$prior['valores']['devengado']+2*$delta-$prior['valores']['aportes']);
            $ex=intdiv($net*$rules['exencion_pct'],100);$benefits+=$ex;
            if ($net-$ex>$limit) { return []; }
        }
        $net=max(0,$group['valores']['devengado']-$group['valores']['aportes']);$ex=intdiv($net*$rules['exencion_pct'],100);
        // Cota conservadora: suma beneficios anteriores recalculados y actuales, sin liberar cupos.
        $upper=$benefits+$ex;
        if ($upper>$rules['exencion_uvt']*$rules['uvt']*100 || $upper>$rules['conjunto_uvt']*$rules['uvt']*100
            || ($used['usado25']??0)+$ex>$rules['exencion_uvt']*$rules['uvt']*100
            || ($used['usado1340']??0)+$ex>$rules['conjunto_uvt']*$rules['uvt']*100 || $net-$ex>$limit) { return []; }
        $trial=$group;$trial['valores']['retroactivos']=0;
        $result=RetencionEjemplo::build($trial,$year,$used);
        if (($result['esperada']??null)!==0 || ($result['beneficios']??null)!==$ex) { return []; }
        return $result+['retroactivo'=>['mes'=>$month,'anterior'=>$old*2,'nuevo'=>$new*2,'diferencia_mensual'=>$delta*2,
            'meses_previos'=>$month-1,'primera_quincena'=>$delta,'total'=>$group['valores']['retroactivos']]];
    }
}
