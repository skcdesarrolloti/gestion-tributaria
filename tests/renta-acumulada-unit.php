<?php
declare(strict_types=1);
use App\Services\{BancoResumen,RentaAcumulada,NominaData};

$bankFixture=static fn($id,$bank,$account,$month,$state,$totals)=>['id'=>$id,'banco'=>$bank,'cuenta'=>$account,'mes'=>$month,
    'lectura'=>json_encode(['estado'=>$state,'totales'=>$totals])];
$bankTotals=['rendimientos'=>5000,'gmf'=>12000000,'gmf_reintegro'=>2000000,'pension'=>396041000];
$bankRows=[$bankFixture(1,'BBVA','0011',1,'leido',$bankTotals),$bankFixture(2,'OTRO','0022',1,'pendiente',$bankTotals),
    $bankFixture(3,'BBVA','0011',2,'leido',array_replace($bankTotals,['pension'=>0]))];
$banks=BancoResumen::build([...$bankRows,$bankRows[0]]);
check(count($banks['cuentas'])===2 && $banks['pendientes']===1 && $banks['totales']['rendimientos']===10000,
    'Bancos separa cuentas, excluye pendientes y no duplica un mismo mes.');
check($banks['totales']['gmf']-$banks['totales']['gmf_reintegro']===20000000,'GMF descuenta reintegros una sola vez.');
$emptyBanks=BancoResumen::build([]);
$taxProfile=['tipo'=>'natural','residencia'=>'residente','regimen'=>'ordinario','anio'=>'2026'];
$taxMovement=static fn($code,$value,$date='2026-01-15',$state='real',$country='Colombia')=>[
    'casilla'=>$code,'valor_cop'=>$value,'fecha'=>$date,'estado'=>$state,'pais'=>$country];
$taxValues=array_fill_keys(NominaData::FIELDS,0);$taxValues['retencion']=10000000;
$taxPay=[['datos'=>['valores'=>$taxValues]]];
$taxMovements=[$taxMovement(32,100000000),$taxMovement(33,8000000)];
$salary=RentaAcumulada::build($taxProfile,$taxMovements,$taxPay,$emptyBanks);
check($salary['estado']==='calculado' && $salary['bruto_laboral']===100000000 && $salary['impuesto_neto']===2263000,
    'Análisis usa 32 menos 33, exención 25% y tarifa anual: 69 millones de base, impuesto 2.263.000.');
check($salary['retenciones']===100000 && $salary['por_cubrir']===2163000,'Retenciones son abonos y no duplican ni reducen la base salarial.');
$partial=RentaAcumulada::build($taxProfile,$taxMovements,$taxPay,$banks);
check($partial['pension_bruta']===0 && $partial['pension_gravada']===0 && count($partial['pendientes'])>=2,
    'Pensión neta bancaria queda pendiente de bruto y no se incorpora como mesada bruta.');
check($partial['ahorro_gmf']>0 && $partial['impuesto_neto']===$salary['impuesto_neto'],'GMF es comparación condicional, sin alterar el impuesto base.');
$pensionRows=[...$taxMovements,$taxMovement(99,40000000),$taxMovement(99,40000000),$taxMovement(100,1000000)];
$mixed=RentaAcumulada::build($taxProfile,$pensionRows,$taxPay,$emptyBanks);
check($mixed['pension_exenta']===52374000 && $mixed['pension_gravada']===26626000 && $mixed['impuesto_neto']>$salary['impuesto_neto'],
    'Dos pensiones del mismo mes comparten un solo límite mensual de 1.000 UVT.');
check(RentaAcumulada::build($taxProfile,[...$taxMovements,$taxMovement(99,4000000,'2026-01-15','real','Otro país')],$taxPay,$emptyBanks)['estado']==='pendiente',
    'Pensión extranjera no obtiene exención automática en el resumen simplificado.');
$future=[...$taxMovements,$taxMovement(32,10000000,'2026-12-15','proyectado')];
check(RentaAcumulada::build($taxProfile,$future,$taxPay,$emptyBanks)['bruto_laboral']===100000000
    && RentaAcumulada::build($taxProfile,$future,$taxPay,$emptyBanks,true)['bruto_laboral']===110000000,
    'Proyección se suma solo en su escenario y no cambia el acumulado real.');
$voluntary=RentaAcumulada::build($taxProfile,$taxMovements,$taxPay,$emptyBanks,false,50000000);
check($voluntary['aporte_limitado']===30000000 && $voluntary['ahorro_aporte']===2263000,
    'Aporte hipotético respeta el límite 30%, el conjunto y recalcula la exención, sin restarlo peso por peso al impuesto.');
check(RentaAcumulada::build($taxProfile,[],$taxPay,$emptyBanks)['estado']==='pendiente','Ausencia de ingresos no se presenta como impuesto confirmado de cero.');
