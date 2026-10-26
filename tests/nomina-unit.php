<?php
declare(strict_types=1);
use App\Services\NominaData;
$payValues=array_fill_keys(NominaData::FIELDS,0);
$payValues=array_replace($payValues,['salario'=>1000000000,'comisiones'=>100000000,'devengado'=>1100000000,
    'salud'=>40000000,'pension'=>40000000,'retencion'=>50000000,'deducido'=>130000000,'neto'=>970000000]);
$payInput=['documento'=>'999111222','empleador_documento'=>'999888777','empleador'=>'Empresa ficticia',
    'inicio'=>'2025-01-01','fin'=>'2025-01-15','archivo'=>'nomina-ficticia.xls','hoja'=>'Enero 1',
    'archivo_sha'=>str_repeat('a',64),'valores'=>$payValues];
$payTax=['id'=>9,'tipo'=>'natural','documento_numero'=>'999111222'];
$payProfile=['id'=>7,'anio'=>'2025','formulario'=>'210','regimen'=>'ordinario'];
$payRead=NominaData::read($payInput,$payTax,$payProfile);
$payAmount=NominaData::amounts($payRead['valores']);
check($payAmount['aportes']===80000000 && $payAmount['neto_calculado']===970000000 && $payAmount['liquida']===1020000000,'Nómina: retención es pago anticipado y no reduce 34.');
$renamed=array_replace($payRead,['archivo'=>'copia.xls','hoja'=>'Copia']);
check(NominaData::fingerprint($renamed)===$payRead['huella'],'Nómina: renombrar el archivo no crea otro ingreso.');
$month=NominaData::monthly([['inicio'=>'2025-01-01','datos'=>$payRead],['inicio'=>'2025-01-16','datos'=>$payRead]]);
check($month[1]['registros']===2 && $month[1]['devengado']===2200000000 && $month[2]===[],'Nómina: suma quincenas sin convertir un mes ausente en cero.');
foreach ([['documento'=>'999111223'],['fin'=>'2025-02-15'],['inicio'=>'2025-02-30'],['valores'=>array_replace($payValues,['neto'=>1])],['valores'=>array_replace($payValues,['salud'=>'40000000'])],['valores'=>array_replace($payValues,['pension'=>-1])]] as $bad) {
    $caught=false;try { NominaData::read(array_replace($payInput,$bad),$payTax,$payProfile); } catch (DomainException) { $caught=true; }
    check($caught,'Nómina rechaza identificación, período, importe o conciliación inválidos.');
}
check(!isset($month[1]['basico']),'Nómina: básico mensual no se suma como pago de quincena.');
$visual=array_replace($payRead,['nombre'=>'Persona ficticia','estado'=>'Nueva']);
$visualRows=[array_replace($visual,['inicio'=>'2025-02-01','fin'=>'2025-02-15']),
    array_replace($visual,['inicio'=>'2025-01-16','fin'=>'2025-01-31']),$visual];
$groups=\App\Services\NominaPreview::groups($visualRows);
check(count($groups)===1 && array_keys($groups[0]['meses'])===['2025-01','2025-02'],
    'Vista previa: una persona/pagador y meses cronológicos aunque el origen venga desordenado.');
$january=$groups[0]['meses']['2025-01'];
check(array_column($january['filas'],'inicio')===['2025-01-01','2025-01-16']
    && $january['totales']['devengado']===2200000000 && count($visualRows)===3,
    'Vista previa: dos quincenas, subtotal correcto y filas de importación intactas.');
$other=array_replace($visual,['empleador_documento'=>'999777666']);
check(count(\App\Services\NominaPreview::groups([$visual,$other]))===2,'Vista previa: no mezcla pagadores.');
$corrected=NominaData::read(array_replace($payInput,['inicio'=>'2025-06-01','fin'=>'2025-06-15','periodo_origen'=>'2025-06-01 a 2025-06-16']),$payTax,$payProfile);
check($corrected['fin']==='2025-06-15' && $corrected['periodo_origen']==='2025-06-01 a 2025-06-16'
    && $corrected['valores']===$payValues,'Corrección de encabezado conserva fechas originales e importes sin trasladarlos.');
$storageError=new PDOException('SQL y credenciales privadas que no deben publicarse');$storageError->errorInfo=['HY000',1364,'detalle privado'];
$safeMessage=\App\Services\NominaFailure::message($storageError);
check(str_contains($safeMessage,'NOM-DB-1364') && !str_contains($safeMessage,'credenciales') && !str_contains($safeMessage,'detalle privado'),
    'Error controlado de nómina muestra solo código, nunca SQL ni datos privados.');
