<?php
declare(strict_types=1);
namespace App\Services;

/** Ejercicio explícito con supuestos, sin estado de validación ni persistencia. */
final class RetencionEjemplo
{
    public static function build(array $group,int $year,array $used=[]): array
    {
        $hypothesis=['huella'=>$group['huella'],'parametros'=>ParametrosAnuales::fingerprint($year),'procedimiento'=>'1','dependientes'=>'no','redondeo'=>'peso',
            'vivienda'=>0,'prepagada'=>0,'usado25'=>$used['usado25']??0,'usado1340'=>$used['usado1340']??0];
        $calculation=RetencionLaboral::check($group,$hypothesis,$year);
        return array_intersect_key($calculation,array_flip(['base','deducciones','exencion25','beneficios','esperada','real','diferencia']));
    }
}
