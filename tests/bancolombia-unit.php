<?php
require_once __DIR__.'/bancolombia-fixtures.php';
use App\Services\{ExtractoBancolombia,ExtractoData,BancoResumen};
$bancoText='BANCOLOMBIA ESTADO DE CUENTA CUENTA DE AHORROS DESDE: 2025/03/31 HASTA: 2025/06/30';
$bancoReading=ExtractoBancolombia::parse($bancoText,bancolombiaFixtureCells());
check($bancoReading['inicio']==='2025-04-01' && $bancoReading['meses_cubiertos']===[4,5,6],'Bancolombia: saldo al 31 de marzo no marca marzo como extracto recibido.');
check($bancoReading['totales']['pension']===30000 && $bancoReading['totales']['rendimientos']===90 && $bancoReading['totales']['gmf']===400
    && $bancoReading['totales']['otros_abonos']===300,'Bancolombia: distingue pensión, rendimientos, GMF y abono CXC sin presumir ingreso.');
$bancoDoc=['id'=>901,'banco'=>'BANCOLOMBIA','cuenta'=>'8765','anio'=>2025,'mes'=>4,'lectura'=>json_encode($bancoReading)];
$bancoCalendar=ExtractoData::calendar([$bancoDoc],'BANCOLOMBIA','8765');
check(!$bancoCalendar[3] && ExtractoData::reading($bancoCalendar[5][0])['totales']['rendimientos']===30,'Bancolombia: meses consultan su importe real sin dividir totales trimestrales.');
check(BancoResumen::build([$bancoDoc,$bancoDoc])['totales']['pension']===30000,'Bancolombia: repeticiones no duplican la pensión del trimestre.');
$bancoBad=bancolombiaFixtureCells();$bancoBad[1][count($bancoBad[1])-1]['text']='1,249.91';$bancoFailed=false;
try { ExtractoBancolombia::parse($bancoText,$bancoBad); }catch(InvalidArgumentException){$bancoFailed=true;}
check($bancoFailed,'Bancolombia: un centavo de descuadre impide aceptar la lectura.');
