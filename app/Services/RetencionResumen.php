<?php
declare(strict_types=1);
namespace App\Services;

/** Compara meses del mismo pagador; nunca convierte un pendiente en cero esperado. */
final class RetencionResumen
{
    /** Una revisión vigente tiene prioridad sobre la hipótesis P1, sin cambiar la nómina. */
    public static function controls(array $groups,array $reviews,int $year): array
    {
        $trials=[];$months=[];
        foreach ($groups as $key=>$group) {
            $document=(string)$group['documento'];
            if (!isset($trials[$document])) { $trials[$document]=RetencionSimulacion::build($groups,$year,$document); }
            $review=$reviews[$key]??[];$result=RetencionLaboral::check($group,$review,$year);$provisional=false;
            if (!isset($result['esperada'])) {
                $trial=$trials[$document]['meses'][$group['mes']-1];
                if ($trial['simulada']!==null) {
                    $provisional=true;
                    $result=['estado'=>'Provisional P1','base'=>$trial['base'],'beneficios'=>$trial['beneficios'],
                        'esperada'=>$trial['simulada'],'real'=>$trial['real'],'diferencia'=>$trial['diferencia']];
                } else { $result['motivo']=$trial['motivo']; }
            }
            $months[$group['mes']][$key]=['grupo'=>$group,'revision'=>$review,'resultado'=>$result,'provisional'=>$provisional];
        }
        return ['meses'=>$months,'simulaciones'=>$trials];
    }

    public static function build(array $groups,array $reviews,int $year,string $document): array
    {
        $selected=array_filter($groups,static fn($g)=>(string)$g['documento']===$document);
        $cut=$selected?max(array_column($selected,'mes')):0;
        $summary=['meses'=>[],'corte'=>$cut,'real'=>0,'esperada'=>0,'real_comparable'=>0,'revisados'=>0,'pendientes'=>0];
        for ($month=1;$month<=$cut;$month++) {
            $key=$month.'-'.$document;$group=$selected[$key]??null;
            $review=$reviews[$key]??[];
            $result=$group?RetencionLaboral::check($group,$review,$year):
                ['estado'=>'Sin nómina','motivo'=>'No hay pagos importados de este mes; no se supone que el ingreso o la retención sean cero.'];
            $actual=$group?$group['valores']['retencion']:null;
            $expected=$result['esperada']??null;
            $summary['real']+=$actual??0;
            if ($expected!==null) {
                $summary['esperada']+=$expected;$summary['real_comparable']+=$actual;$summary['revisados']++;
            } else { $summary['pendientes']++; }
            $summary['meses'][]=['mes'=>$month,'key'=>$key,'real'=>$actual,'esperada'=>$expected,
                'base'=>$result['base']??null,'procedimiento'=>isset($result['base'])?$review['procedimiento']:null,
                'diferencia'=>$result['diferencia']??null,'estado'=>$result['estado'],'motivo'=>$result['motivo']];
        }
        $summary['diferencia']=$summary['revisados']?$summary['real_comparable']-$summary['esperada']:null;
        return $summary;
    }
}
