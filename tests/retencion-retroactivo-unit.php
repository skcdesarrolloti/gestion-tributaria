<?php
use App\Services\{NominaData,RetencionLaboral,RetencionRetroactivo,RetencionSimulacion};
// Reajuste ficticio: 2 a 2,4 millones mensuales; cinco quincenas pendientes de diferencia.
$retroRows=[];
for ($m=1;$m<=4;$m++) { foreach ([1,16] as $day) {
    $new=$m>3 || ($m===3 && $day===16);$salary=$new?120000000:100000000;
    $v=array_fill_keys(NominaData::FIELDS,0);$v['salario']=$salary;$v['retroactivos']=$m===3 && $day===16?100000000:0;
    $v['devengado']=$salary+$v['retroactivos'];$v['salud']=intdiv($salary,25);$v['pension']=intdiv($salary,25);
    $retroRows[]=['id'=>count($retroRows)+1,'huella'=>str_repeat('f',64),'inicio'=>sprintf('2026-%02d-%02d',$m,$day),'fin'=>sprintf('2026-%02d-%02d',$m,$day===1?15:cal_days_in_month(CAL_GREGORIAN,$m,2026)),
        'empleador_documento'=>'999777111','datos'=>['empleador'=>'Pagador ficticio de reajuste','valores'=>$v],'revision'=>1];
} }
$retroGroups=RetencionLaboral::groups($retroRows);$retroTrial=RetencionSimulacion::build($retroGroups,2026,'999777111');
check($retroTrial['calculados']===4 && $retroTrial['meses'][2]['simulada']===0 && $retroTrial['meses'][2]['retroactivo']['total']===100000000,
    'Reajuste exacto desde enero con P1 cero comprobado permite continuar, sin mover el pago de marzo.');
check($retroGroups['3-999777111']['valores']['retroactivos']===100000000,'Hipótesis de retroactivo no modifica el origen.');
$retroWrong=$retroGroups['3-999777111'];$retroWrong['valores']['retroactivos']++;
check(RetencionRetroactivo::zeroExample($retroWrong,$retroGroups,2026,[])===[],'No infiere períodos si el importe del retroactivo no concilia exactamente.');
$retroHigh=$retroGroups;$retroHigh['2-999777111']['valores']['devengado']+=1000000000;
check(RetencionRetroactivo::zeroExample($retroGroups['3-999777111'],$retroHigh,2026,[])===[],'No declara retención cero si un mes reajustado cambia de rango.');
check(RetencionRetroactivo::zeroExample($retroGroups['3-999777111'],$retroGroups,2026,['usado25'=>790*52374*100])===[],
    'No ignora cupo anual agotado para desbloquear el retroactivo.');
