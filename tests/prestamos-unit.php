<?php
declare(strict_types=1);
require_once __DIR__.'/prestamos-fixtures.php';
use App\Services\{PrestamoCajaSocial,PrestamoCruce,PensionImportData,ColpensionesReader};
$loan=PrestamoCajaSocial::parse(loanFixtureText(),'Pensionado ficticio');
check($loan['pago']==='2025-04-09' && $loan['pagado']['total']===121000 && $loan['proxima']['total']===119500,'Préstamo separa pago anterior y cuota por pagar con ajuste pendiente.');
check($loan['pagado']['incendio']===null,'Una casilla sin importe no se etiqueta como cero leído.');
foreach ([loanFixtureText('Persona diferente'),loanFixtureText('Pensionado ficticio','CREDITO COMERCIAL'),str_replace('1,210.00','1,211.00',loanFixtureText())] as $bad) {
    $failed=false;try { PrestamoCajaSocial::parse($bad,'Pensionado ficticio'); }catch(DomainException) { $failed=true; }check($failed,'Crédito rechaza titular distinto, empresa o sumas no conciliadas.');
}
$loanMonths=PrestamoCruce::months([['fecha_pago'=>'2025-04-09','datos'=>$loan]],[],[],'2025');
check($loanMonths[4]['pago']===121000 && $loanMonths[5]['pago']===null && $loanMonths[4]['libranza']===null,'Cruce no convierte próxima cuota en pago ni inventa una libranza ausente.');
$reading=ColpensionesReader::parse(["COLPENSIONES PERIODO: ENERO DE 2025\nPENSION BRUTA: 5000.00\nAPORTE SALUD: 500.00\nLIBRANZA: 1000.00\nNETO A PAGAR: 3500.00"],'12345678');
$rows=PensionImportData::rows([['mes'=>'1','bruto'=>'5000','salud'=>'500','libranza'=>'1000','neto'=>'3500']],'2025',$reading);
check($rows[0]['salud']===50000 && $rows[0]['libranza']===100000 && $rows[0]['neto']===350000,'Libranza se conserva separada de salud y del neto del comprobante.');
$failed=false;try { PensionImportData::rows([['mes'=>'1','bruto'=>'5000','salud'=>'500','libranza'=>'1000','neto'=>'4000']],'2025',$reading); }catch(DomainException) { $failed=true; }
check($failed,'Comprobante rechaza bruto, descuentos y neto incompatibles.');
