<?php
declare(strict_types=1);
use App\Services\{ExtractoData,ExtractoUpload};
require_once __DIR__.'/extractos-fixtures.php';
$bankCells=bankFixtureCells();$bankText='BBVA PERIODO DESDE: 01-06-2025 HASTA: 30-06-2025';
$bankReading=App\Services\ExtractoBbva::parse($bankText,$bankCells);
check(count($bankReading['movimientos'])===6 && $bankReading['paginas']===2 && $bankReading['totales']['pension']===100000
    && $bankReading['totales']['rendimientos']===1000 && $bankReading['totales']['gmf']-$bankReading['totales']['gmf_reintegro']===100,
    'Extractos lee todas las páginas y concilia pensión, rendimientos y GMF neto de reintegros.');
check($bankReading['totales']['otros_abonos']===10000 && $bankReading['totales']['otros_debitos']===50000,'Extractos no supone que una transferencia sea pensión ni un pago sea deducible.');
foreach ([[$bankCells[0]],[$bankCells[0],$bankCells[0]]] as $incompleteBank) {
    $rejected=false;try { App\Services\ExtractoBbva::parse($bankText,$incompleteBank); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'Extractos no acepta páginas omitidas o repetidas cuyos saldos no concilian.');
}
$bankMatch=['anio'=>2025,'mes'=>6,'banco'=>'BBVA','cuenta'=>'1234'];
ExtractoData::match($bankReading,$bankMatch,['nombre'=>'Titular banco ficticio']);
foreach ([['anio'=>2026],['mes'=>7],['cuenta'=>'9876'],['banco'=>'OTRO']] as $badMatch) {
    $rejected=false;try { ExtractoData::match($bankReading,array_replace($bankMatch,$badMatch),['nombre'=>'Titular banco ficticio']); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'Extractos contrasta período y cuenta leídos antes de guardar.');
}
$rejected=false;try { ExtractoData::match($bankReading,$bankMatch,['nombre'=>'Otra persona']); } catch (InvalidArgumentException) { $rejected=true; }
check($rejected,'Extractos rechaza PDF leído de otro titular.');
$bankInput=['mes'=>'6','banco'=>' Banco   ficticio ','cuenta'=>'0012','titular_confirmado'=>'1'];
$bankData=ExtractoData::read($bankInput,'2026');
check($bankData['banco']==='BANCO FICTICIO' && $bankData['cuenta']==='0012' && $bankData['mes']===6,'Extractos conserva ceros de cuenta y normaliza banco.');
foreach ([['mes'=>'13'],['mes'=>'0'],['cuenta'=>'123456'],['banco'=>''],['titular_confirmado'=>'0']] as $badBank) {
    $rejected=false;try { ExtractoData::read(array_replace($bankInput,$badBank),'2026'); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'Extractos rechaza período/cuenta/titular sin confirmar.');
}
$bankCalendar=ExtractoData::calendar([['mes'=>6,'banco'=>'BANCO FICTICIO','cuenta'=>'0012'],['mes'=>7,'banco'=>'OTRO','cuenta'=>'0012']],'BANCO FICTICIO','0012');
check(count($bankCalendar)===12 && count($bankCalendar[6])===1 && !$bankCalendar[7],'Extractos muestra doce meses y separa bancos aunque coincidan últimos dígitos.');
$bankFile=tempnam(sys_get_temp_dir(),'bank-unit-');
try {
    file_put_contents($bankFile,'<script>PDF falso</script>');$rejected=false;
    try { ExtractoUpload::validate($bankFile); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'Extractos rechaza contenido no PDF aunque su nombre termine en PDF.');
    $rejected=false;try { (new ExtractoUpload(sys_get_temp_dir()))->path('../config/local.php'); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'Extractos rechaza recorrido de rutas de archivos privados.');
} finally { unlink($bankFile); }
