<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/app/Support/helpers.php';

$paymentRoute='exogena/1001/registro&contribuyente='.$companyId;
[$status,$paymentHtml]=request($client,$base,$paymentRoute);
verify($status===200 && str_contains($paymentHtml,'name="anio" value="2025"')
    && str_contains($paymentHtml,'name="pago_deducible" value=""') && str_contains($paymentHtml,'name="pais" value=""'),
    '1001 abre 2025 vacío y con referencia de versión');
verify((int)$db->query('SELECT COUNT(*) FROM exogena_1001')->fetchColumn()===0, 'Migración 1001 no carga registros');
$paymentPost=App\Services\Exogena1001Data::read(['anio'=>'2025','concepto'=>'5004','documento_tipo'=>'31',
    'identificacion'=>'001234567','razon_social'=>'<b>Entidad ficticia</b>','pais'=>'169',
    'direccion'=>'Dirección ficticia','departamento'=>'05','municipio'=>'001']);
foreach (App\Services\Exogena1001Fields::money() as $field) { $paymentPost[$field]='0'; }
$paymentPost['pago_deducible']='999999999999999999';$paymentPost['_csrf_token']=token($paymentHtml);
verify(request($client,$base,$paymentRoute,array_replace($paymentPost,['_csrf_token'=>'']))[0]===419, '1001 exige CSRF');
verify(request($client,$base,$paymentRoute,array_replace($paymentPost,['anio'=>'2026']))[0]===422, '1001 no guarda otro año con v10 AG2025');
verify(request($client,$base,$paymentRoute,array_replace($paymentPost,['direccion'=>'']))[0]===422, '1001 exige dirección para Colombia');
verify(request($client,$base,$paymentRoute,$paymentPost)[0]===303, '1001 guarda borrador propio');
$paymentId=(int)$db->query('SELECT id FROM exogena_1001')->fetchColumn();
$paymentJson=json_decode($db->query('SELECT datos FROM exogena_1001')->fetchColumn(),true);
verify(count($paymentJson)===20 && $paymentJson['identificacion']==='001234567' && $paymentJson['municipio']==='001',
    '1001 conserva campos y códigos con ceros');
[$status,$paymentHtml]=request($client,$base,'exogena/1001&contribuyente='.$companyId);
verify($status===200 && str_contains($paymentHtml,'999.999.999.999.999.999') && str_contains($paymentHtml,'&lt;b&gt;Entidad ficticia&lt;/b&gt;'),
    '1001 muestra importes de 18 dígitos exactamente y escapa texto');
$paymentPost['pago_deducible']='2000000';
verify(request($client,$base,'exogena/1001/registro&id='.$paymentId,$paymentPost)[0]===303, '1001 permite corregir sin duplicar');
verify((int)$db->query('SELECT COUNT(*) FROM exogena_1001')->fetchColumn()===1, '1001 conserva un solo registro editado');
$foreignPayment=$db->prepare('INSERT INTO exogena_1001 (contribuyente_id,anio,estructura,datos) VALUES (?,2025,?,?)');
$foreignPayment->execute([$otherCompanyId,'1001-v10-ag2025',json_encode($paymentJson)]);$foreignPaymentId=(int)$db->lastInsertId();
verify(request($client,$base,'exogena/1001/registro&id='.$foreignPaymentId)[0]===200, '1001 consulta de Socio restringe el editor ajeno');
verify(request($client,$base,'exogena/1001/registro&id='.$foreignPaymentId,$paymentPost)[0]===403, '1001 no modifica registros ajenos');
verify(request($client,$base,'exogena/1001&contribuyente='.$otherCompanyId)[0]===200, '1001 permite consultar empresas como Socio');
verify(request($client,$base,'exogena/1001/registro&contribuyente='.$taxpayerId,$paymentPost)[0]===404, '1001 no admite informante natural en esta etapa');
verify(request($client,$base,'exogena/1001&anio=2026')[0]===422, '1001 distingue versiones al consultar otro año');
verify(json_decode($db->query('SELECT datos FROM exogena_1007 WHERE id='.$incomeId)->fetchColumn(),true)['ingresos_brutos']==='2000000',
    '1001 no modifica 1007');
