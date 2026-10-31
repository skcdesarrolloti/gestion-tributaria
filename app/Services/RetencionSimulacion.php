<?php
declare(strict_types=1);
namespace App\Services;

/** Hipótesis P1 cronológica; sin validaciones definitivas ni escrituras. Importes en centavos. */
final class RetencionSimulacion
{
    public static function build(array $groups,int $year,string $document): array
    {
        $selected=array_filter($groups,static fn($g)=>(string)$g['documento']===$document);
        $cut=$selected?max(array_column($selected,'mes')):0;
        $summary=['meses'=>[],'corte'=>$cut,'calculados'=>0,'excluidos'=>0,'real'=>0,'simulada'=>null,'diferencia'=>null];
        $used=['usado25'=>0,'usado1340'=>0];$continuous=true;$cumulative=0;
        for ($month=1;$month<=$cut;$month++) {
            $group=$selected[$month.'-'.$document]??null;
            $retro=$continuous && $group && $group['valores']['retroactivos']>0?RetencionRetroactivo::zeroExample($group,$selected,$year,$used):[];
            $reason=match(true) {
                !$group=>'No hay nómina importada de este mes.',
                !$group['completo']=>'Faltan períodos del mes: no extrapolamos una quincena.',
                $group['valores']['retroactivos']>0 && !$retro=>'Hay retroactivos: requieren una liquidación particular.',
                !$continuous=>'Falta resolver un mes anterior para conocer el consumo de los topes anuales.',
                default=>'',
            };
            $example=$reason===''?($retro?:RetencionEjemplo::build($group,$year,$used)):[];
            if (!$example) { $continuous=false;$summary['excluidos']++; }
            else {
                // Sin deducciones adicionales, el beneficio aplicado corresponde a la exención laboral.
                $used['usado25']+=$example['beneficios'];$used['usado1340']+=$example['beneficios'];
                $summary['real']+=$example['real'];$cumulative+=$example['esperada'];$summary['calculados']++;
                $summary['simulada']=$cumulative;$summary['diferencia']=$summary['real']-$cumulative;
            }
            $summary['meses'][]=['mes'=>$month,'real'=>$group['valores']['retencion']??null,'motivo'=>$reason,
                'simulada'=>$example['esperada']??null,'diferencia'=>$example['diferencia']??null,
                'acumulado'=>$example?$cumulative:null,'devengado'=>$group['valores']['devengado']??null,
                'aportes'=>$group['valores']['aportes']??null,'beneficios'=>$example['beneficios']??null,'base'=>$example['base']??null,
                'conceptos'=>$group['valores']??[],'retroactivo'=>$example['retroactivo']??null];
        }
        return $summary;
    }
}
