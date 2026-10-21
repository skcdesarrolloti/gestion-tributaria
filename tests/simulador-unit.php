<?php
declare(strict_types=1);

use App\Services\{SimuladorFields,RentaSimulador,RentaTarifa};
require_once __DIR__.'/simulador-fixtures.php';
$sheet=simulatorFixture(); $profile=simulatorProfile();
$calc=static fn($data)=>RentaSimulador::calculate(array_replace($sheet,$data),$profile);
check(count(SimuladorFields::coverage())===52,'Las 52 fichas tienen estado de cobertura explícito.');
check($calc([])['estado']==='calculado' && $calc([])['saldo_pagar']===0,'Cero confirmado permite un escenario vacío sin impuesto inventado.');
$pending=$calc(['ceros_confirmados'=>'']);
check($pending['estado']==='pendiente' && !array_key_exists('saldo_pagar',$pending),'Importes desconocidos no producen saldo final.');
check($calc(['salario_1'=>'1e9'])['estado']==='pendiente','Rechaza notación científica.');
check($calc(['b_dependientes72'=>'5'])['estado']==='pendiente','Rechaza quinto dependiente adicional.');
check($calc(['declaraciones'=>''])['estado']==='pendiente','No presume que corresponde el anticipo de tercer año.');
check($calc(['declaraciones'=>'1','metodo_anticipo'=>'promedio'])['estado']==='pendiente','Primer declarante no promedia un año inexistente.');
check($calc(['honorarios'=>'80000000','servicios'=>'exencion','c_honorarios_servicios'=>'1000'])['estado']==='pendiente',
    'Rechaza acumular costos y exención en honorarios.');
check($calc(['intereses'=>'1000','c_mantenimiento_capital'=>'1001'])['estado']==='pendiente','Pérdida requiere revisión en lugar de compensación silenciosa.');
check($calc(['r_energia'=>'1000'])['estado']==='pendiente','Importe especial no desaparece del saldo final.');
check($calc(['ganancias_ocasionales'=>'1'])['estado']==='pendiente','Bloquea un supuesto de ganancia ocasional no resuelto.');
check($calc(['pension_1'=>'1000000'])['estado']==='pendiente','Pensión obliga a revisar el estado del contribuyente.');
$pension=$calc(['pensionado'=>'si','pension_1'=>'80000000','salud_pension_1'=>'1000000']);
check($pension['pension_exenta']===52374000 && $pension['pension_gravada']===26626000,'Exención pensional usa el mes efectivamente pagado, no doce límites.');
$base=$calc(['intereses'=>'60000000']);
$mixed=$calc(['intereses'=>'60000000','pensionado'=>'si','pension_1'=>'10000000','salud_pension_1'=>'1000000']);
check($base['impuesto_neto']===$mixed['impuesto_neto'],'Pensión exenta no exonera intereses ni eleva su impuesto.');
$month=$calc(['salario_1'=>'100000000','dep387_1'=>'1','prepagada_1'=>'1000000']);
check($month['deducciones']['partidas']['dependientes387']===32*52374,'Dependiente en un mes no recibe doce topes.');
check($month['deducciones']['partidas']['salud']===16*52374,'Prepagada usa tope mensual y no anual completo.');
$savings=$calc(['honorarios'=>'100000000','servicios'=>'exencion','b_afc'=>'20000000','b_fpv'=>'20000000','base_aportes'=>'100000000']);
check($savings['deducciones']['partidas']['afc_fpv']===30000000 && $savings['beneficios_limitados']===40000000,'FPV/AFC comparte 30% y se aplica además el 40% global.');
$rais=$calc(['salario_1'=>'100000000','b_rais'=>'10000000','base_rais'=>'100000000']);
check($rais['estado']==='calculado','RAIS puede imputarse a trabajo laboral sin honorarios ficticios.');
$tax=$calc(['no_laborales'=>'100000000']);
$prepaid=$calc(['no_laborales'=>'100000000','retenciones'=>'20000000']);
check($tax['impuesto_neto']===$prepaid['impuesto_neto'] && $prepaid['saldo_pagar']<$tax['saldo_pagar'],
    'Retención reduce saldo, no impuesto causado.');
check($calc(['patrimonio'=>'900000000','deudas'=>'100000000'])['impuesto_neto']===0,'Patrimonio y deudas no se restan como gasto de renta.');
$uvt=52374;
foreach ([[1090,19,0],[1700,28,116],[4100,33,788],[8670,35,2296],[18970,37,5901],[31000,39,10352]] as [$edge,$rate,$fixed]) {
    $r=RentaTarifa::calculate($edge*$uvt+10000,$uvt);
    check($r['tarifa']===$rate && $r['impuesto']===intdiv(10000*$rate+50,100)+$fixed*$uvt,'Tramo marginal correcto sobre el exceso del umbral.');
}
check(RentaTarifa::calculate(1090*$uvt,$uvt)['impuesto']===0,'Umbral 1090 UVT sin impuesto.');
check(RentaTarifa::thousand(1499)===1000 && RentaTarifa::thousand(1500)===2000,'Redondeo a miles separado de elegir tramo.');
