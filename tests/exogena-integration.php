<?php
declare(strict_types=1);

$companyId=(int)$db->query("SELECT id FROM contribuyentes WHERE tipo='juridica' AND funcionario_id=100 LIMIT 1")->fetchColumn();
[$status,$blankExogenaHtml]=request($client,$base,'exogena/5247/registro&contribuyente='.$companyId);
verify($status===200 && str_contains($blankExogenaHtml,'name="anio" value="2025"')
    && str_contains($blankExogenaHtml,'name="pago" value=""'), 'Captura inicial 2025 sin valores precargados');
[$status,$exogenaHtml]=request($client,$base,'exogena');
verify($status===200 && str_contains($exogenaHtml,'Abrir 5247'), 'Exógena aparece como módulo independiente');
$exogenaList='exogena/5247&contribuyente='.$companyId.'&anio=2024';
[$status,$exogenaHtml]=request($client,$base,$exogenaList);
verify($status===200 && str_contains($exogenaHtml,'Nuevo registro 5247'), 'Selecciona empresa propia para 5247');
verify(request($client,$base,'exogena/5247&contribuyente='.$taxpayerId)[0]===404, 'Persona natural no se admite como informante en esta etapa');
verify(request($client,$base,'exogena/5247&contribuyente='.$otherId)[0]===404, 'No consulta empresas ajenas');
verify(request($client,$base,'exogena/5247&anio[]=2024')[0]===422, 'Rechaza año malformado');
$exogenaForm='exogena/5247/registro&contribuyente='.$companyId.'&anio=2024';
[$status,$exogenaHtml]=request($client,$base,$exogenaForm);
verify($status===200 && str_contains($exogenaHtml,'name="retencion_no_domiciliados"'), 'Captura incluye las retenciones de la plantilla');
$exogenaRecord=['_csrf_token'=>token($exogenaHtml),'anio'=>'2024','tipo_contrato'=>'1','concepto'=>'5005',
    'documento_tipo'=>'13','identificacion'=>'001234567','primer_apellido'=>'<b>Ejemplo</b>',
    'primer_nombre'=>'Persona','departamento'=>'05','municipio'=>'001','pais'=>'169',
    'pago'=>'1000000','iva_costo'=>'0','retencion_renta'=>'35000','retencion_renta_asumida'=>'0',
    'retencion_iva'=>'0','retencion_no_domiciliados'=>'0','participante_tipo'=>'31','participante_identificacion'=>'900000001'];
verify(request($client,$base,$exogenaForm,array_replace($exogenaRecord,['_csrf_token'=>'']))[0]===419, '5247 exige CSRF al guardar');
verify(request($client,$base,$exogenaForm,array_replace($exogenaRecord,['retencion_renta'=>'']))[0]===422, '5247 conserva vacíos como pendientes y no los asume cero');
verify(request($client,$base,$exogenaForm,$exogenaRecord)[0]===303, '5247 guarda borrador con 22 campos');
$exogenaId=(int)$db->query('SELECT id FROM exogena_5247')->fetchColumn();
$saved=json_decode($db->query('SELECT datos FROM exogena_5247')->fetchColumn(),true);
verify(count($saved)===22 && $saved['identificacion']==='001234567' && $saved['municipio']==='001', 'Persistencia mantiene columnas y ceros iniciales');
[$status,$exogenaHtml]=request($client,$base,$exogenaList);
verify($status===200 && str_contains($exogenaHtml,'&lt;b&gt;Ejemplo&lt;/b&gt;') && str_contains($exogenaHtml,'1.000.000'), 'Listado escapa datos y muestra valor');
$exogenaEdit='exogena/5247/registro&id='.$exogenaId;
[$status,$exogenaHtml]=request($client,$base,$exogenaEdit);
verify($status===200 && str_contains($exogenaHtml,'value="001234567"'), 'Editor recupera datos guardados');
$exogenaRecord['pago']='2000000';
verify(request($client,$base,$exogenaEdit,$exogenaRecord)[0]===303, 'Permite corregir registro propio');
verify((int)$db->query('SELECT COUNT(*) FROM exogena_5247')->fetchColumn()===1
    && json_decode($db->query('SELECT datos FROM exogena_5247')->fetchColumn(),true)['pago']==='2000000', 'Corrección no duplica registros');
$otherCompany=$db->prepare("INSERT INTO contribuyentes (funcionario_id,nombre,tipo,documento_tipo,documento_numero,fuentes)
    VALUES (101,'Empresa ajena ficticia','juridica','NIT','900000002','[]')");
$otherCompany->execute();$otherCompanyId=(int)$db->lastInsertId();
$foreignRow=$db->prepare('INSERT INTO exogena_5247 (contribuyente_id,anio,estructura,datos) VALUES (?,2024,?,?)');
$foreignRow->execute([$otherCompanyId,'plantilla-2024',json_encode($saved)]);$foreignExogenaId=(int)$db->lastInsertId();
verify(request($client,$base,'exogena/5247&contribuyente='.$otherCompanyId)[0]===404, 'Empresa jurídica ajena queda oculta');
verify(request($client,$base,'exogena/5247/registro&id='.$foreignExogenaId)[0]===404, 'No lee registro 5247 ajeno');
verify(request($client,$base,'exogena/5247/registro&id='.$foreignExogenaId,$exogenaRecord)[0]===404, 'No modifica registro 5247 ajeno');
verify(request($client,$base,'exogena/5247/registro&id=999999',$exogenaRecord)[0]===404, 'No crea registro mediante edición inexistente');
verify(request($client,$base,'exogena/5247/registro&contribuyente='.$taxpayerId,$exogenaRecord)[0]===404, 'Servidor rechaza informante natural aunque reciba POST');
