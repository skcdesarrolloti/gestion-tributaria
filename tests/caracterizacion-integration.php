<?php
declare(strict_types=1);
$profileRoute='contribuyentes/perfil&id='.$taxpayerId.'&anio=2026';
[$status,$profileHtml]=request($client,$base,$profileRoute);
verify($status===200 && str_contains($profileHtml,'Información del contribuyente') && !str_contains($profileHtml,'Qué falta completar'),'La ficha abre una sola pestaña y elimina el tablero de bloques');
verify(request($client,$base,'contribuyentes/perfil&id='.$otherId)[0]===404,'Perfil ampliado oculta contribuyentes ajenos');
verify(request($client,$base,$profileRoute.'&bloque=../config')[0]===422,'Bloques solo del catálogo permitido');
verify(request($client,$base,$profileRoute.'&bloque=actividades',['_csrf_token'=>''])[0]===419,'Perfil ampliado exige CSRF');
$post=['_csrf_token'=>token($profileHtml),'revision'=>'0','estado'=>'declarado',
    'campos'=>['principal'=>'0020','principal_inicio'=>'2020-01-01'],'notas'=>'<script>solo texto</script>'];
$activityRoute=$profileRoute.'&bloque=actividades';
verify(request($client,$base,$activityRoute,array_replace($post,['campos'=>['principal'=>'999999']]))[0]===422,'Valida códigos DIAN en servidor');
verify(request($client,$base,$activityRoute,$post)[0]===303,'Guarda actividad económica sin PDF');
verify(request($client,$base,$activityRoute,$post)[0]===409,'No pierde cambios por crear el mismo bloque en dos ventanas');
[$status,$profileHtml]=request($client,$base,$activityRoute);
verify($status===200 && str_contains($profileHtml,'value="0020" selected') && str_contains($profileHtml,'&lt;script&gt;solo texto&lt;/script&gt;'),'Reabre códigos con ceros y escapa notas');
$stale=$post;$stale['revision']='1';
verify(request($client,$base,$activityRoute,$stale)[0]===303,'Actualiza revisión vigente');
verify(request($client,$base,$activityRoute,$stale)[0]===409,'Rechaza edición desde una revisión antigua');
[$status,$yearHtml]=request($client,$base,'contribuyentes/perfil&id='.$taxpayerId.'&anio=2025&bloque=actividades');
verify($status===200 && !str_contains($yearHtml,'value="0020" selected'),'No mezcla la caracterización entre años');
$family=['_csrf_token'=>token($profileHtml),'revision'=>'0','aplicabilidad'=>'si','estado'=>'borrador','filas'=>[
    ['rol'=>'dependiente','nombre'=>'Familiar ficticio','documento_tipo'=>'CC','documento'=>'123123','soporte'=>'Pendiente registro']]];
verify(request($client,$base,$profileRoute.'&bloque=relacionados',$family)[0]===303,'Guarda renglones de familia y representación');
verify(request($client,$base,'contribuyentes/perfil&id='.$otherId.'&anio=2026&bloque=actividades',$post)[0]===404,'POST no modifica perfil ajeno');
verify((int)$db->query('SELECT COUNT(*) FROM catalogos_rut WHERE grupo="ciiu"')->fetchColumn()===508,'Catálogo CIIU y otras clasificaciones cargado por migración');
verify((int)$db->query('SELECT COUNT(*) FROM caracterizaciones_tributarias WHERE contribuyente_id='.$otherId)->fetchColumn()===0,'Contribuyente ajeno permanece sin cambios');
