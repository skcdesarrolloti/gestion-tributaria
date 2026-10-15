<?php
declare(strict_types=1);

require_once __DIR__.'/exogena-catalog-fixtures.php';
$firstCatalogId=null;
foreach (App\Services\ExogenaSchemas::all() as $code=>$schema) {
    $prefix='exogena/'.$code;
    $recordRoute=$prefix.'/registro&contribuyente='.$companyId;
    [$status,$catalogHtml]=request($client,$base,$recordRoute);
    verify($status===200 && str_contains($catalogHtml,'name="anio" value="2025"')
        && str_contains($catalogHtml,'name="'.$schema['amount'].'" value=""'), "$code abre estructura 2025 con importes vacíos");
    verify((int)$db->query("SELECT COUNT(*) FROM exogena_registros WHERE formato='$code'")->fetchColumn()===0,
        "$code empieza sin datos precargados");
    $fixture=catalogFixture($schema);$fixture['_csrf_token']=token($catalogHtml);
    verify(request($client,$base,$recordRoute,array_replace($fixture,['_csrf_token'=>'']))[0]===419, "$code exige CSRF");
    verify(request($client,$base,$recordRoute,array_replace($fixture,['anio'=>'2026']))[0]===422, "$code respeta período del esquema");
    verify(request($client,$base,$recordRoute,$fixture)[0]===303, "$code guarda borrador por cliente");
    $id=(int)$db->query("SELECT id FROM exogena_registros WHERE formato='$code'")->fetchColumn();
    if ($firstCatalogId===null) { $firstCatalogId=$id; }
    $saved=json_decode($db->query("SELECT datos FROM exogena_registros WHERE id=$id")->fetchColumn(),true);
    verify(array_keys($saved)===array_keys($schema['fields']), "$code persiste exactamente sus campos");
    [$status,$catalogHtml]=request($client,$base,$prefix.'&contribuyente='.$companyId);
    verify($status===200 && str_contains($catalogHtml,'Registros de 2025'), "$code lista registros propios");
    if (isset($fixture['raz'])) { verify(str_contains($catalogHtml,'&lt;b&gt;ficticia&lt;/b&gt;'), "$code escapa identidad"); }
    $fixture[$schema['amount']]='1234567';
    verify(request($client,$base,$prefix.'/registro&id='.$id,$fixture)[0]===303, "$code permite editar sin duplicar");
    verify((int)$db->query("SELECT COUNT(*) FROM exogena_registros WHERE formato='$code'")->fetchColumn()===1,
        "$code mantiene un solo registro propio");
    $foreign=$db->prepare('INSERT INTO exogena_registros (contribuyente_id,formato,anio,estructura,datos) VALUES (?,?,2025,?,?)');
    $foreign->execute([$otherCompanyId,(string)$code,$schema['structure'],json_encode($saved)]);$foreignId=(int)$db->lastInsertId();
    verify(request($client,$base,$prefix.'/registro&id='.$foreignId)[0]===404, "$code no lee registros ajenos");
    verify(request($client,$base,$prefix.'/registro&id='.$foreignId,$fixture)[0]===404, "$code no modifica registros ajenos");
    verify(request($client,$base,$prefix.'&contribuyente='.$otherCompanyId)[0]===404, "$code oculta clientes ajenos");
    verify(request($client,$base,$prefix.'/registro&contribuyente='.$taxpayerId,$fixture)[0]===404, "$code conserva alcance de informante jurídico");
    verify(request($client,$base,$prefix.'/registro&id=999999',$fixture)[0]===404, "$code rechaza edición inexistente");
}
verify(request($client,$base,'exogena/1005/registro&id='.$firstCatalogId)[0]===404, 'Un ID de 1003 no se abre como 1005');
verify(request($client,$base,'exogena/1003&anio=2026')[0]===422, 'Consulta distingue versiones por año');
verify(request($client,$base,'exogena&contribuyente='.$otherCompanyId)[0]===404, 'Panel de cliente también verifica propietario');
[$status,$workspaceHtml]=request($client,$base,'exogena&contribuyente='.$companyId);
verify($status===200 && str_contains($workspaceHtml,'Contratos de colaboración')
    && str_contains($workspaceHtml,'Rentas de trabajo y pensiones')
    && str_contains($workspaceHtml,'route=exogena%2F5252&amp;contribuyente='.$companyId), 'Panel agrupa formatos y conserva cliente en enlaces');
verify(json_decode($db->query('SELECT datos FROM exogena_1001 WHERE id='.$paymentId)->fetchColumn(),true)['pago_deducible']==='2000000',
    'Nuevos formatos conservan los registros anteriores');
