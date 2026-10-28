<?php
declare(strict_types=1);
namespace App\Services;

/** Compara meses del mismo pagador; nunca convierte un pendiente en cero esperado. */
final class RetencionResumen
{
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
