<?php
declare(strict_types=1);

namespace App\Services;

final class RentaDeducciones
{
    public static function calculate(array $v, int $uvt, int $work, int $generalGross): array
    {
        $health=0; $dependents=0;
        foreach (range(1,12) as $m) {
            $health+=min($v['prepagada_'.$m],16*$uvt);
            if ($v['dep387_'.$m]===1) { $dependents+=min(intdiv($v['salario_'.$m],10),32*$uvt); }
        }
        $fpv=min($v['b_fpv']+$v['b_afc'],intdiv($v['base_aportes']*30,100),3800*$uvt);
        $cesInd=min($v['b_cesantia_ind'],2500*$uvt,intdiv($v['ingreso_gravable_cesantia_ind'],12));
        $list=['vivienda'=>min($v['b_vivienda'],1200*$uvt),'icetex'=>min($v['b_icetex'],100*$uvt),
            'salud'=>$health,'dependientes387'=>$dependents,'gmf'=>intdiv($v['b_gmf'],2),'afc_fpv'=>$fpv,'cesantia_ind'=>$cesInd];
        $sum=array_sum($list);
        $ratio=$v['promedio_cesantias'];
        $exCes=0;
        foreach ([[350,100],[410,90],[470,80],[530,60],[590,40],[650,20],[PHP_INT_MAX,0]] as [$cap,$rate]) {
            if ($cap===PHP_INT_MAX || $ratio<=$cap*$uvt) { $exCes=RentaTarifa::percent($v['cesantias'],$rate); break; }
        }
        // Las cesantías, aun en su porción gravada, no generan la exención del 25%.
        $eligible=$v['salarios']+$v['otros_trabajo']-$v['incr_laboral']-$v['rais_laboral'];
        if ($v['servicios']==='exencion') { $eligible+=$v['honorarios']-$v['incr_honorarios']-$v['rais_honorarios']; }
        $ex25=min(intdiv(max(0,$eligible-$sum),4),790*$uvt);
        $requested=$sum+$exCes+$ex25;
        $cap=min(intdiv($generalGross*40,100),1340*$uvt);
        return ['partidas'=>$list,'cesantias_exentas'=>$exCes,'exencion25'=>$ex25,'solicitado'=>$requested,
            'limite'=>$cap,'admitido'=>min($requested,$cap),'dependientes72'=>$work>0?$v['b_dependientes72']*72*$uvt:0,
            'compras1'=>min(intdiv($v['b_facturas'],100),240*$uvt)];
    }
}
