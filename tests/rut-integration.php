<?php
declare(strict_types=1);
require_once __DIR__.'/rut-fixtures.php';
function rutRequest(CurlHandle $client,string $base,string $route,array $post): array
{
    curl_setopt_array($client,[CURLOPT_URL=>$base.'/index.php?route='.$route,CURLOPT_POST=>true,
        CURLOPT_POSTFIELDS=>$post,CURLOPT_HEADER=>true,CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>20]);
    $response=curl_exec($client);
    if ($response===false) { throw new RuntimeException(curl_error($client)); }
    $headerSize=curl_getinfo($client,CURLINFO_HEADER_SIZE);
    return [curl_getinfo($client,CURLINFO_HTTP_CODE),substr($response,$headerSize),substr($response,0,$headerSize)];
}
$rutPath=$temp.'/rut-ficticio.pdf'; file_put_contents($rutPath,rutFixturePdf());
$rutRoute='contribuyentes/rut&id='.$taxpayerId.'&anio=2026';
[$status,$rutHtml]=request($client,$base,$rutRoute);
verify($status===200 && str_contains($rutHtml,'multipart/form-data'),'Formulario RUT protegido permite carga PDF');
verify(request($client,$base,'contribuyentes/rut&id='.$otherId)[0]===403,'No sube RUT a un contribuyente ajeno');
$upload=['accion'=>'leer','_csrf_token'=>token($rutHtml),'rut_pdf'=>new CURLFile($rutPath,'application/pdf','rut-ficticio.pdf')];
verify(rutRequest($client,$base,$rutRoute,array_replace($upload,['_csrf_token'=>'']))[0]===419,'Carga PDF exige CSRF');
[$status,$rutHtml]=rutRequest($client,$base,$rutRoute,$upload);
verify($status===200 && str_contains($rutHtml,'Datos leídos') && str_contains($rutHtml,'Estado actual DIAN: pendiente'),'PDF se lee sin certificar un estado oficial no consultado');
preg_match('/name="preview_token" value="([a-f0-9]+)"/',$rutHtml,$match);$previewToken=$match[1]??'';
verify($previewToken!=='','Vista previa vinculada a la sesión');
$apply=['_csrf_token'=>token($rutHtml),'preview_token'=>$previewToken,'accion'=>'aplicar',
    'seleccion'=>['campo:ubicacion:direccion','campo:actividades:principal','filas:responsabilidades']];
verify(request($client,$base,$rutRoute,$apply)[0]===303,'Aplica selección y guarda soporte en transacción');
$q=$db->prepare('SELECT datos FROM caracterizaciones_tributarias WHERE contribuyente_id=? AND anio=2026 AND bloque=?');
$q->execute([$taxpayerId,'actividades']);$savedActivity=json_decode($q->fetchColumn(),true);
verify($savedActivity['campos']['principal']==='0020','La importación conserva una actividad diferente ya digitada');
$q->execute([$taxpayerId,'ubicacion']);$savedAddress=json_decode($q->fetchColumn(),true);
verify($savedAddress['campos']['direccion']==='Direccion ficticia 1','Completa el campo vacío desde el PDF');
$doc=$db->query('SELECT * FROM rut_documentos LIMIT 1')->fetch();
verify($doc && is_file($temp.'/rut-importaciones/'.$doc['archivo']) && $doc['sha256']===hash_file('sha256',$rutPath),'Soporte privado ligado al perfil y huella del PDF');
[$status,$download]=request($client,$base,'contribuyentes/rut/documento&documento='.$doc['id']);
verify($status===200 && hash('sha256',$download)===$doc['sha256'],'Descarga privada entrega el documento original íntegro');
verify(request($client,$base,$rutRoute,$apply)[0]===422,'Una confirmación consumida no se aplica dos veces');
[$status,$rutHtml]=rutRequest($client,$base,$rutRoute,$upload);
preg_match('/name="preview_token" value="([a-f0-9]+)"/',$rutHtml,$match);$apply['preview_token']=$match[1]??'';
$apply['reemplazar']='1';
verify(request($client,$base,$rutRoute,$apply)[0]===303,'Reemplazo requiere opción explícita');
$q->execute([$taxpayerId,'actividades']);$replaced=json_decode($q->fetchColumn(),true);
verify($replaced['campos']['principal']==='0010' && $replaced['estado']==='borrador','Reemplaza selección y exige nueva revisión del bloque');
verify((int)$db->query('SELECT COUNT(*) FROM rut_documentos')->fetchColumn()===1,'PDF repetido no duplica el soporte');
[$status,$rutHtml]=rutRequest($client,$base,$rutRoute,$upload);
preg_match('/name="preview_token" value="([a-f0-9]+)"/',$rutHtml,$match);$apply['preview_token']=$match[1]??'';
$db->exec('UPDATE caracterizaciones_tributarias SET revision=revision+1 WHERE contribuyente_id='.(int)$taxpayerId.' AND anio=2026 AND bloque="actividades"');
verify(request($client,$base,$rutRoute,$apply)[0]===409,'Vista previa antigua no sobrescribe cambios de otra ventana');
verify((int)$db->query('SELECT COUNT(*) FROM rut_documentos')->fetchColumn()===1,'Conflicto revierte la transacción sin duplicar documentos');
$tampered=$apply;$tampered['seleccion']=['campo:fiscal:campo_inventado'];
verify(request($client,$base,$rutRoute,$tampered)[0]===422,'No permite aplicar campos ajenos a la lectura');
// Restaurar el escenario de caracterización para las pruebas siguientes, sin datos reales.
$q=$db->prepare('UPDATE caracterizaciones_tributarias SET datos=? WHERE contribuyente_id=? AND anio=2026 AND bloque="actividades"');
$q->execute([json_encode($savedActivity),$taxpayerId]);
file_put_contents($rutPath,rutFixturePdf('87654321'));
verify(rutRequest($client,$base,$rutRoute,$upload)[0]===422,'Rechaza identidad distinta aunque se llame RUT');
file_put_contents($rutPath,'Archivo inválido');
verify(rutRequest($client,$base,$rutRoute,$upload)[0]===422,'Rechaza archivo que solo tiene extensión PDF');
$otherClient=curl_init();curl_setopt($otherClient,CURLOPT_COOKIEFILE,'');
try {
    [$s,$rutLoginHtml]=request($otherClient,$base,'login');
    request($otherClient,$base,'login',['_csrf_token'=>token($rutLoginHtml),'username'=>'otro','password'=>'Otra-clave-segura-2026']);
    verify(request($otherClient,$base,'contribuyentes/rut/documento&documento='.$doc['id'])[0]===404,'Ni otro administrador descarga el RUT ajeno');
} finally { curl_close($otherClient); }
require __DIR__.'/rut-multi-integration.php';
require __DIR__.'/rut-registro-integration.php';
require __DIR__.'/perfil-tabs-integration.php';
require __DIR__.'/perfil-alcance-integration.php';
