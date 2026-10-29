<?php
declare(strict_types=1);
require_once __DIR__.'/prestamos-fixtures.php';
$loanRoute='prestamos&contribuyente='.$colTax.'&anio=2025';[$s,$loanPage]=request($client,$base,$loanRoute);
verify($s===200 && str_contains($loanPage,'Control de préstamos personales') && str_contains($loanPage,'Cruce mensual de Colpensiones'),'Préstamo personal accesible desde Costos con cruce pendiente en la misma página');
$loanCompany=(int)$db->query('SELECT id FROM contribuyentes WHERE tipo<>"natural" LIMIT 1')->fetchColumn();
verify(request($client,$base,'prestamos&contribuyente='.$loanCompany.'&anio=2025')[0]===404,'Control personal no abre créditos de la empresa');
$loanPath=$temp.'/credito-ficticio.pdf';file_put_contents($loanPath,loanFixturePdf());
$loanPost=['_csrf_token'=>token($loanPage),'prestamo_pdf'=>new CURLFile($loanPath,'application/pdf','credito-ficticio.pdf')];$loanUpload='prestamos/subir&contribuyente='.$colTax.'&anio=2025';
verify(rutRequest($client,$base,$loanUpload,array_replace($loanPost,['_csrf_token'=>'']))[0]===419,'Crédito exige CSRF antes de almacenar');
$loanBefore=$db->query('SELECT id,valor_cop FROM movimientos_ingresos ORDER BY id')->fetchAll();
[$s,$loanSaved]=rutRequest($client,$base,$loanUpload,$loanPost);$loanSaved=json_decode($loanSaved,true);
verify($s===200 && $loanSaved['guardado'],'Crédito sintético se lee y guarda por carga HTTP');$loanId=$loanSaved['id'];
verify(rutRequest($client,$base,$loanUpload,$loanPost)[0]===422,'PDF repetido no duplica el pago del crédito');
verify($loanBefore===$db->query('SELECT id,valor_cop FROM movimientos_ingresos ORDER BY id')->fetchAll(),'Guardar crédito no altera ingresos ni crea deducciones automáticas');
verify(str_contains(request($client,$base,$loanRoute)[1],'$1.210,00') && !str_contains(request($client,$base,str_replace('2025','2026',$loanRoute))[1],'$1.210,00'),'Crédito se acumula por año de pago y no se copia al siguiente');
verify(request($client,$base,'prestamos/documento&documento='.$loanId)[0]===200,'PDF del crédito disponible con sesión autorizada');
file_put_contents($loanPath,loanFixturePdf('Pensionado ficticio','CREDITO COMERCIAL'));verify(rutRequest($client,$base,$loanUpload,$loanPost)[0]===422,'PDF comercial no se importa en el préstamo personal');
file_put_contents($loanPath,loanFixturePdf('Otra persona'));verify(rutRequest($client,$base,$loanUpload,$loanPost)[0]===422,'Crédito de otro titular se rechaza');
$classDoc=$db->query('SELECT id,lectura FROM extractos_bancarios WHERE contribuyente_id='.$readBankTax.' AND mes=6')->fetch();$classReading=json_decode($classDoc['lectura'],true);$classIndex=null;
foreach ($classReading['movimientos'] as $i=>$m) { if ($m['categoria']==='otros_debitos') { $classIndex=$i;break; } }
$classRoute='extractos/clasificar&documento='.$classDoc['id'];[$s,$classInitial]=request($client,$base,$classRoute);$classInitial=json_decode($classInitial,true);
$classPost=['_csrf_token'=>token($loanPage),'revision'=>(string)$classInitial['revision'],'clasificaciones'=>json_encode([$classIndex=>'personal'])];
verify(request($client,$base,$classRoute,array_replace($classPost,['_csrf_token'=>'']))[0]===419,'Clasificación de movimientos exige CSRF');
verify(request($client,$base,$classRoute,$classPost)[0]===200,'Finalidad personal de un cargo se conserva sin alterar su importe');
verify(request($client,$base,$classRoute,$classPost)[0]===409,'Una ventana antigua no pisa una clasificación posterior');
$loanOther=curl_init();curl_setopt($loanOther,CURLOPT_COOKIEFILE,'');
try { [, $loanLogin]=request($loanOther,$base,'login');request($loanOther,$base,'login',['_csrf_token'=>token($loanLogin),'username'=>'hash-user','password'=>'Otra-clave-segura']);
    verify(request($loanOther,$base,$loanRoute)[0]===404 && request($loanOther,$base,'prestamos/documento&documento='.$loanId)[0]===404 && request($loanOther,$base,$classRoute)[0]===404,'Tercero no consulta créditos, PDF ni clasificaciones ajenas');
} finally { curl_close($loanOther); }
