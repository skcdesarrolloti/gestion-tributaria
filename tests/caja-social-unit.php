<?php
declare(strict_types=1);
require_once __DIR__.'/caja-social-fixtures.php';
use App\Services\{ExtractoCajaSocial,ExtractoData,BancoResumen};
$cajaText='BANCO CAJA SOCIAL Cuenta de Ahorros Periodo del Informe 1 de Enero a 31 de Marzo de 2025';
$caja=ExtractoCajaSocial::parse($cajaText,cajaFixtureCells());
check($caja['meses_cubiertos']===[1,2,3] && $caja['meses'][1]['totales']['rendimientos']===100 && $caja['meses'][2]['totales']['rendimientos']===200 && $caja['meses'][3]['totales']['rendimientos']===300,'Trimestre se agrupa por fecha, sin dividir el total entre tres.');
check($caja['totales']['pension']===0 && $caja['totales']['otros_abonos']===10500,'Abono nómina y traslado no se presumen pensión, arriendo propio ni nuevo ingreso fiscal.');
$cajaDoc=['id'=>900,'banco'=>'BANCO CAJA SOCIAL','cuenta'=>'4321','anio'=>2025,'mes'=>1,'lectura'=>json_encode($caja)];
$cajaSummary=BancoResumen::build([$cajaDoc,$cajaDoc]);
check($cajaSummary['totales']['rendimientos']===600 && count($cajaSummary['cuentas'][0]['meses'])===3,'Un mismo PDF respalda tres meses sin duplicar el trimestre ni el documento repetido.');
$cajaCalendar=ExtractoData::calendar([$cajaDoc],'BANCO CAJA SOCIAL','4321');
check(count(array_filter($cajaCalendar))===3 && $cajaCalendar[2][0]['id']===900 && ExtractoData::reading($cajaCalendar[2][0])['totales']['rendimientos']===200,'Febrero consulta el mismo ID con sus importes mensuales.');
$badCells=cajaFixtureCells();$badCells[1][count($badCells[1])-1]['text']='1,086.01';$failed=false;
try { ExtractoCajaSocial::parse($cajaText,$badCells); }catch(InvalidArgumentException){$failed=true;}
check($failed,'Un descuadre del trimestre impide aceptar importes parciales.');
