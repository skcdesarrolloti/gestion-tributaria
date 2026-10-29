<?php
declare(strict_types=1);
[, $singlePage]=request($client,$base,'extractos&contribuyente='.$colTax.'&anio=2025');
$singleDom=new DOMDocument();@$singleDom->loadHTML($singlePage);$singleXpath=new DOMXPath($singleDom);
verify($singleXpath->query('//input[@data-bank-files]')->length===1 && str_contains($singlePage,'carga única'),'Una sola pantalla recibe los documentos bancarios');
foreach (['registro&contribuyente='.$colTax.'&anio=2025&componente=costos','registro&contribuyente='.$colTax.'&anio=2025&componente=ingresos&cedula=pensiones',
    'registro&contribuyente='.$colTax.'&anio=2025&componente=ingresos&cedula=general&renta=capital','prestamos&contribuyente='.$colTax.'&anio=2025'] as $singleRoute) {
    [, $singleHtml]=request($client,$base,$singleRoute);
    verify(!str_contains($singleHtml,'data-bank-files') && !str_contains($singleHtml,'data-loan-upload') && str_contains($singleHtml,'Ir a extractos · carga única'),'Ingresos, costos y préstamos consultan la carga central sin pedir otro PDF bancario');
}
$singleLoanPath=$temp.'/credito-carga-unica.pdf';file_put_contents($singleLoanPath,str_replace(['5555550001','2025/04/09','2025/04/20','2025/05/10'],['5555550002','2025/05/09','2025/05/20','2025/06/10'],loanFixturePdf()));
$singleLoanPost=['_csrf_token'=>token($singlePage),'extracto_pdf'=>new CURLFile($singleLoanPath,'application/pdf','credito-ficticio-central.pdf'),'mes'=>'auto','banco'=>'','cuenta'=>''];
$singleBefore=(int)$db->query('SELECT COUNT(*) FROM movimientos_ingresos')->fetchColumn();
[$s,$singleCredit]=rutRequest($client,$base,'extractos/lote&contribuyente='.$colTax.'&anio=2025',$singleLoanPost);$singleCredit=json_decode($singleCredit,true);
verify($s===200 && $singleCredit['tipo']==='credito' && $singleCredit['mes']===5,'El mismo selector reconoce el crédito personal y usa la fecha del pago');
verify((int)$db->query('SELECT COUNT(*) FROM extractos_bancarios WHERE contribuyente_id='.$colTax)->fetchColumn()===0
    && (int)$db->query('SELECT COUNT(*) FROM movimientos_ingresos')->fetchColumn()===$singleBefore,'Un PDF de crédito no se guarda como cuenta ni duplica importes fiscales');
[$s,$singleDuplicate]=rutRequest($client,$base,'extractos/lote&contribuyente='.$colTax.'&anio=2025',$singleLoanPost);
verify($s===409 && json_decode($singleDuplicate,true)['duplicado'],'La carga central detecta el crédito repetido');
[, $singleCentral]=request($client,$base,'extractos&contribuyente='.$colTax.'&anio=2025');
verify(str_contains($singleCentral,'credito-ficticio-central.pdf') && str_contains(request($client,$base,'prestamos&contribuyente='.$colTax.'&anio=2025')[1],'$2.420,00'),'Documento central disponible en préstamos sin una segunda carga');
