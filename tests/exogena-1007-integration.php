<?php
declare(strict_types=1);

$incomeRoute='exogena/1007/registro&contribuyente='.$companyId;
[$status,$incomeHtml]=request($client,$base,$incomeRoute);
verify($status===200 && str_contains($incomeHtml,'name="anio" value="2025"')
    && str_contains($incomeHtml,'name="ingresos_brutos" value=""') && str_contains($incomeHtml,'name="pais" value=""'),
    '1007 abre captura 2025 vacía sin asumir país ni importes');
verify((int)$db->query('SELECT COUNT(*) FROM exogena_1007')->fetchColumn()===0, 'Migración 1007 no importa datos reales');
$incomePost=['_csrf_token'=>token($incomeHtml),'anio'=>'2025','concepto'=>'4001','documento_tipo'=>'31',
    'identificacion'=>'001234567','razon_social'=>'<b>Entidad ficticia</b>','pais'=>'169',
    'ingresos_brutos'=>'1000000','devoluciones'=>'0'];
verify(request($client,$base,$incomeRoute,array_replace($incomePost,['_csrf_token'=>'']))[0]===419, '1007 exige CSRF');
verify(request($client,$base,$incomeRoute,array_replace($incomePost,['devoluciones'=>'']))[0]===422, '1007 rechaza importe desconocido');
verify(request($client,$base,$incomeRoute,$incomePost)[0]===303, '1007 guarda borrador independiente');
$incomeId=(int)$db->query('SELECT id FROM exogena_1007')->fetchColumn();
$incomeJson=json_decode($db->query('SELECT datos FROM exogena_1007')->fetchColumn(),true);
verify(count($incomeJson)===11 && $incomeJson['identificacion']==='001234567', '1007 persiste once campos y ceros');
[$status,$incomeHtml]=request($client,$base,'exogena/1007&contribuyente='.$companyId);
verify($status===200 && str_contains($incomeHtml,'&lt;b&gt;Entidad ficticia&lt;/b&gt;') && str_contains($incomeHtml,'1.000.000'), '1007 lista y escapa registros propios');
$incomePost['ingresos_brutos']='2000000';
verify(request($client,$base,'exogena/1007/registro&id='.$incomeId,$incomePost)[0]===303, '1007 permite edición propia');
verify((int)$db->query('SELECT COUNT(*) FROM exogena_1007')->fetchColumn()===1
    && json_decode($db->query('SELECT datos FROM exogena_1007')->fetchColumn(),true)['ingresos_brutos']==='2000000', '1007 corrige sin duplicar');
verify(json_decode($db->query('SELECT datos FROM exogena_5247 WHERE id='.$exogenaId)->fetchColumn(),true)['pago']==='2000000', '1007 no altera registros 5247');
$foreignIncome=$db->prepare('INSERT INTO exogena_1007 (contribuyente_id,anio,estructura,datos) VALUES (?,2025,?,?)');
$foreignIncome->execute([$otherCompanyId,'1007-v9',json_encode($incomeJson)]);$foreignIncomeId=(int)$db->lastInsertId();
verify(request($client,$base,'exogena/1007/registro&id='.$foreignIncomeId)[0]===404, '1007 no lee registros ajenos');
verify(request($client,$base,'exogena/1007/registro&id='.$foreignIncomeId,$incomePost)[0]===404, '1007 no modifica registros ajenos');
verify(request($client,$base,'exogena/1007&contribuyente='.$otherCompanyId)[0]===404, '1007 oculta empresas ajenas');
verify(request($client,$base,'exogena/1007/registro&contribuyente='.$taxpayerId,$incomePost)[0]===404, '1007 no admite informante natural en esta etapa');
verify(request($client,$base,'exogena/1007/registro&id=999999',$incomePost)[0]===404, '1007 no crea por edición inexistente');
[$status,$formatsHtml]=request($client,$base,'exogena');
verify($status===200 && str_contains($formatsHtml,'Ingresos Propios') && str_contains($formatsHtml,'Pagos o Abonos en cuenta'), 'Menú y tarjetas muestran nombres solicitados');
