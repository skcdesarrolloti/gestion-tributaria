<?php
declare(strict_types=1);
// Un Socio sin fichas creadas ni asignadas debe poder operar sobre todos los expedientes.
[$s,$totalGrant]=request($client,$base,'accesos&funcionario='.$otherOwner);
$q=$db->prepare('SELECT revision FROM accesos_tributarios WHERE funcionario_id=?');$q->execute([$otherOwner]);
$totalPost=['_csrf_token'=>token($totalGrant),'revision'=>(string)$q->fetchColumn(),'perfil'=>'socio','contribuyente_id'=>'999999','activo'=>'1'];
verify(request($client,$base,'accesos&funcionario='.$otherOwner,$totalPost)[0]===303,'Socio obtiene acceso total sin depender de una ficha enviada por el navegador');
$q=$db->prepare('SELECT contribuyente_id FROM accesos_tributarios WHERE funcionario_id=?');$q->execute([$otherOwner]);
verify($q->fetchColumn()===null,'La asignación de contribuyente se omite al guardar un Socio');
$totalClient=curl_init();curl_setopt($totalClient,CURLOPT_COOKIEFILE,'');
$limitedClient=curl_init();curl_setopt($limitedClient,CURLOPT_COOKIEFILE,'');
try {
    [, $htmlTotal]=request($totalClient,$base,'login');
    request($totalClient,$base,'login',['_csrf_token'=>token($htmlTotal),'username'=>'otro','password'=>'Otra-clave-segura-2026']);
    $totalRoute='contribuyentes/perfil&id='.$taxpayerId.'&anio=2026&tab=ubicacion';
    [$s,$htmlTotal]=request($totalClient,$base,$totalRoute);
    verify($s===200 && str_contains($htmlTotal,'Guardar información') && !str_contains($htmlTotal,'modo de solo lectura'),'Socio puede editar una ficha creada por otro funcionario');
    $q=$db->prepare('SELECT revision FROM caracterizaciones_tributarias WHERE contribuyente_id=? AND anio=2026 AND bloque="ubicacion"');$q->execute([$taxpayerId]);
    $totalEdit=['_csrf_token'=>token($htmlTotal),'revision'=>(string)$q->fetchColumn(),'estado'=>'borrador','campos'=>['direccion'=>'Actualización global ficticia']];
    verify(request($totalClient,$base,$totalRoute,$totalEdit)[0]===303,'Socio guarda cambios reales sobre una ficha no asignada');
    $q=$db->prepare('SELECT funcionario_id FROM contribuyentes WHERE id=?');$q->execute([$taxpayerId]);
    verify((int)$q->fetchColumn()===100,'La edición global no cambia al creador del contribuyente');
    $pdfTotal=tempnam(sys_get_temp_dir(),'rut-socio-');file_put_contents($pdfTotal,rutFixturePdf());
    try {
        [$s,,$headersTotal]=rutRequest($totalClient,$base,'contribuyentes/rut&id='.$taxpayerId.'&anio=2026',[
            '_csrf_token'=>token($htmlTotal),'accion'=>'leer','aplicar_vacios'=>'1','rut_pdf'=>new CURLFile($pdfTotal,'application/pdf','rut-ficticio.pdf')]);
        verify($s===303 && str_contains($headersTotal,'tab=identificacion'),'Socio importa RUT de cualquier contribuyente');
    } finally { unlink($pdfTotal); }
    [$s,$htmlTotal]=request($totalClient,$base,'simulador&id='.$profileId);
    verify(request($totalClient,$base,'simulador&id='.$profileId,array_replace($simPost,['_csrf_token'=>token($htmlTotal)]))[0]===303,'Socio guarda escenarios de cualquier contribuyente');
    [$s,$htmlTotal]=request($totalClient,$base,'exogena/5247/registro&id='.$foreignExogenaId);
    verify(request($totalClient,$base,'exogena/5247/registro&id='.$foreignExogenaId,array_replace($exogenaRecord,['_csrf_token'=>token($htmlTotal)]))[0]===303,'Socio edita Exógena de una entidad no asignada');
    [$s,$htmlTotal]=request($client,$base,'accesos&funcionario=101');
    verify(request($client,$base,'accesos&funcionario=101',['_csrf_token'=>token($htmlTotal),'revision'=>'0','perfil'=>'usuario','contribuyente_id'=>(string)$taxpayerId,'activo'=>'1'])[0]===303,'Se conserva el perfil Usuario limitado a una ficha');
    [, $htmlLimited]=request($limitedClient,$base,'login');
    request($limitedClient,$base,'login',['_csrf_token'=>token($htmlLimited),'username'=>'hash-user','password'=>'Otra-clave-segura']);
    verify(request($limitedClient,$base,'contribuyentes/perfil&id='.$otherId,$totalEdit)[0]===404,'Acceso total de Socio no amplía los permisos de Usuario');
    verify(request($limitedClient,$base,'simulador&id='.$otherProfileId,$simPost)[0]===404,'Usuario no guarda escenarios ajenos');
    verify(request($limitedClient,$base,'exogena/5247/registro&id='.$foreignExogenaId,$exogenaRecord)[0]===404,'Usuario no edita otra empresa aunque figure como su creador histórico');
} finally { curl_close($totalClient);curl_close($limitedClient); }
