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
$annulledBank=App\Services\ExtractoBbva::parse($bankText,bankFixtureCells('ANU. COM DECRETO'));
check($annulledBank['totales']===$bankReading['totales'],'Extractos concilia la anulación BBVA del GMF sin tratarla como ingreso.');
$unknownRefund=false;try { App\Services\ExtractoBbva::parse($bankText,bankFixtureCells('ABONO DESCONOCIDO')); } catch (InvalidArgumentException) { $unknownRefund=true; }
check($unknownRefund,'Extractos no supone que todo abono desconocido sea un reintegro del GMF.');
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
check(ExtractoData::read(array_replace($bankInput,['titular_confirmado'=>'0']),'2026')['titular_confirmado']===false,'Extractos permite guardar sin declarar revisión visual.');
foreach ([['mes'=>'13'],['mes'=>'0'],['cuenta'=>'123456'],['banco'=>'']] as $badBank) {
    $rejected=false;try { ExtractoData::read(array_replace($bankInput,$badBank),'2026'); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'Extractos rechaza período o cuenta inválidos.');
}
$bankCalendar=ExtractoData::calendar([['mes'=>6,'banco'=>'BANCO FICTICIO','cuenta'=>'0012'],['mes'=>7,'banco'=>'OTRO','cuenta'=>'0012']],'BANCO FICTICIO','0012');
check(count($bankCalendar)===12 && count($bankCalendar[6])===1 && !$bankCalendar[7],'Extractos muestra doce meses y separa bancos aunque coincidan últimos dígitos.');
(static function(): void {
    $config=['base_path'=>'/public'];$year='2025';$extractQuery='&anio=2025';
    $record=static fn($id,$month,$account,$state,$pension,$interest)=>['id'=>$id,'mes'=>$month,'banco'=>'BANCO FICTICIO','cuenta'=>$account,
        'lectura'=>json_encode(['estado'=>$state,'paginas'=>1,'totales'=>['pension'=>$pension,'rendimientos'=>$interest,'gmf'=>0,'gmf_reintegro'=>0]])];
    $documents=[$record(3,3,'1111','leido',300000,3000),$record(1,1,'1111','leido',100000,1000),
        $record(2,2,'1111','pendiente',800000,8000),$record(4,1,'2222','leido',900000,9000)];
    ob_start();require dirname(__DIR__).'/app/Views/extractos/months.php';$html=ob_get_clean();
    $dom=new DOMDocument();@$dom->loadHTML('<?xml encoding="UTF-8">'.$html);$xpath=new DOMXPath($dom);
    $rows='//details[contains(summary,"1111")]//tbody/tr';
    check(trim($xpath->query($rows.'[th="Marzo"]/td[3]')->item(0)->textContent)==='$4.000,00'
        && trim($xpath->query($rows.'[th="Marzo"]/td[5]')->item(0)->textContent)==='$40,00',
        'Extractos acumula cronológicamente pensión y rendimientos sin sumar lecturas pendientes ni otra cuenta.');
    check(trim($xpath->query($rows.'[th="Febrero"]/td[3]')->item(0)->textContent)==='—',
        'Mes pendiente no presenta un acumulado como si su ingreso fuera cero.');
})();
$bankFile=tempnam(sys_get_temp_dir(),'bank-unit-');
try {
    file_put_contents($bankFile,str_replace('(1.00)','(2.00)',bankFixturePdf()));
    $pendingBank=App\Services\ExtractoPdfReader::read($bankFile);
    check($pendingBank['estado']==='pendiente' && $pendingBank['inicio']==='2025-06-01' && !isset($pendingBank['totales'],$pendingBank['movimientos']),
        'Extractos conserva mes e identidad reconocidos sin publicar importes si no concilia.');
    $rejected=false;try { ExtractoData::match($pendingBank,array_replace($bankMatch,['mes'=>7]),['nombre'=>'Titular banco ficticio']); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'Extractos pendiente también impide guardar en un mes distinto del impreso.');
    file_put_contents($bankFile,bankFixturePdf('ANU. COM DECRETO'));
    check(App\Services\ExtractoPdfReader::read($bankFile)['estado']==='leido','Extractos sin contraseña reconoce período y concilia reintegros del banco.');
    file_put_contents($bankFile,'<script>PDF falso</script>');$rejected=false;
    try { ExtractoUpload::validate($bankFile); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'Extractos rechaza contenido no PDF aunque su nombre termine en PDF.');
    $rejected=false;try { (new ExtractoUpload(sys_get_temp_dir()))->path('../config/local.php'); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'Extractos rechaza recorrido de rutas de archivos privados.');
} finally { unlink($bankFile); }
