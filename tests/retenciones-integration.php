<?php
// Se ejecuta después de los fixtures aislados de nómina: enero completo, febrero parcial.
$retRoute='retenciones&id='.$nomProfile.'&mes=1&empleador=999888777';
[, $retRegister]=request($client,$base,'registro&contribuyente='.$nomTax.'&anio=2025');
$retDom=new DOMDocument();@$retDom->loadHTML($retRegister);$retXpath=new DOMXPath($retDom);
verify($retXpath->query('//a[contains(@href,"vista=reserva") and not(ancestor::details)]')->length===1,
    'Reserva: botón visible fuera de paneles cerrados y enlaces de empleador válidos');
verify($retXpath->query('//a[normalize-space(.)="Validar retención mensual" and not(ancestor::details)]')->length===1,
    'Retención: acceso visible junto al registro de nómina');
[$s,$retPage]=request($client,$base,$retRoute);
verify($s===200 && str_contains($retPage,'Pendiente de soporte'),'Retención: importar no marca OK sin soporte');
verify(str_contains($retPage,'Resultado de la validación mensual') && str_contains($retPage,'Ejemplo didáctico: no es una validación')
    && preg_match('/name="usado25"[^>]*value="0"/',$retPage),'Retención: comparación visible, ejemplo separado y enero con acumulado anterior cero');
$guideDom=new DOMDocument();@$guideDom->loadHTML($retPage);$guideXpath=new DOMXPath($guideDom);
$guideControls=$guideXpath->query('//form[@data-retencion-form]//*[@name and not(@type="hidden")]');$allGuided=true;
foreach ($guideControls as $control) {
    $guideId=$control->getAttribute('aria-describedby');
    if ($guideId==='' || $guideXpath->query('//*[@id="'.$guideId.'"]')->length!==1) { $allGuided=false; }
}
verify($allGuided,'Retención: cada campo tiene una explicación asociada accesible');
[$s,$retDefault]=request($client,$base,'retenciones&id='.$nomProfile);
verify($s===200 && str_contains($retDefault,'Enero · Empresa ficticia'),'Retención: entrada directa elige último mes completo');
verify(request($client,$base,'retenciones&id='.$nomProfile.'&periodo=2-999888777')[0]===200
    && request($client,$base,'retenciones&id='.$nomProfile.'&periodo=1-000000000')[0]===404,'Retención: selector conserva alcance del perfil y del pagador');
$retHidden=static function(string $html,string $name): string { preg_match('/name="'.preg_quote($name,'/').'" value="([^"]*)"/',$html,$m);return html_entity_decode($m[1]??''); };
$retPost=['_csrf_token'=>token($retPage),'revision'=>$retHidden($retPage,'revision'),'huella'=>$retHidden($retPage,'huella'),
    'vivienda'=>'0','prepagada'=>'0','usado25'=>'0','usado1340'=>'0','dependientes'=>'no','procedimiento'=>'1','redondeo'=>'peso',
    'completo'=>'1','ordinario'=>'1','verificado'=>'1','soporte'=>'Certificado ficticio <script>no ejecutar</script>'];
$retBefore=$db->query('SELECT id,valor_cop FROM movimientos_ingresos ORDER BY id')->fetchAll();
verify(request($client,$base,$retRoute,array_replace($retPost,['_csrf_token'=>'']))[0]===419,'Retención: guardar exige CSRF');
verify(request($client,$base,$retRoute,array_replace($retPost,['verificado'=>'']))[0]===422,'Retención: exige soportes y confirmaciones');
[$s,$retUnknown]=request($client,$base,$retRoute,array_replace($retPost,['procedimiento'=>'pendiente']));
verify($s===422 && str_contains($retUnknown,'Todavía falta confirmar el método del empleador'),'Retención: opción desconocida explica el pendiente y no se confirma');
verify(request($client,$base,$retRoute,$retPost)[0]===303,'Retención: guarda revisión con permisos del Socio');
[$s,$retSaved]=request($client,$base,$retRoute);
verify($s===200 && str_contains($retSaved,'Retención esperada:') && str_contains($retSaved,'&lt;script&gt;') && !str_contains($retSaved,'<script>no ejecutar'),
    'Retención: muestra cálculo y escapa referencia del soporte');
verify(request($client,$base,$retRoute,$retPost)[0]===409,'Retención: revisión concurrente no sobrescribe');
verify($db->query('SELECT id,valor_cop FROM movimientos_ingresos ORDER BY id')->fetchAll()===$retBefore,'Retención: comprobar no cambia importes ni duplica casillas');
[$s,$retFeb]=request($client,$base,'retenciones&id='.$nomProfile.'&mes=2&empleador=999888777');
verify($s===200 && str_contains($retFeb,'Faltan períodos') && !str_contains($retFeb,'Guardar y comprobar retención'),'Retención: mes parcial no habilita validación');
$reserveRoute='retenciones&id='.$nomProfile.'&vista=reserva';
[, $reservePage]=request($client,$base,$reserveRoute);
$reservePost=['_csrf_token'=>token($reservePage),'revision'=>$retHidden($reservePage,'revision'),'huella'=>$retHidden($reservePage,'huella'),
    'futuro_bruto'=>'60000000','futuro_aportes'=>'4800000','futuro_retencion'=>'1000000',
    'anticipo_anterior'=>'0','saldo_favor'=>'0','declaraciones'=>'3','cuotas'=>'6','hipotesis'=>'1'];
verify(request($client,$base,$reserveRoute,$reservePost)[0]===303,'Reserva: hipótesis persiste sin alterar ingresos');
[, $reserveSaved]=request($client,$base,$reserveRoute);
verify(str_contains($reserveSaved,'Reserva sugerida por cuota') && str_contains($reserveSaved,'Saldo completo pendiente'),'Reserva: muestra faltante y alcance salarial provisional');
verify((bool)preg_match('/name="futuro_bruto"[^>]*value="60000000"/',$reserveSaved),'Reserva: recarga los supuestos guardados dentro del formulario');
[, $retStill]=request($client,$base,$retRoute);
verify(str_contains($retStill,'Certificado ficticio'),'Reserva: conserva revisión mensual al guardar otro grupo');
$retNewPost=array_replace($retPost,['revision'=>$retHidden($retStill,'revision'),'huella'=>$retHidden($retStill,'huella')]);
verify(request($client,$base,$retRoute,$retNewPost)[0]===303,'Retención: vuelve a guardar sin borrar la hipótesis anual');
[, $reserveKept]=request($client,$base,$reserveRoute);
verify((bool)preg_match('/name="futuro_bruto"[^>]*value="60000000"/',$reserveKept),'Retención: conserva los supuestos de reserva previamente guardados');
$db->exec('UPDATE nominas_laborales SET revision=revision+1 WHERE id='.(int)$getNom()[0]['id']);
[, $retChanged]=request($client,$base,$retRoute);[, $reserveChanged]=request($client,$base,$reserveRoute);
verify(str_contains($retChanged,'Pendiente de soporte') && !str_contains($reserveChanged,'Reserva sugerida por cuota'),'Cambiar nómina invalida control y proyección');
verify(request($client,$base,$reserveRoute,array_replace($reservePost,['revision'=>$retHidden($reserveChanged,'revision')]))[0]===409,'Reserva: no guarda contra nómina cambiada');
$retUser=curl_init();curl_setopt($retUser,CURLOPT_COOKIEFILE,'');
try {
    [, $retLogin]=request($retUser,$base,'login');request($retUser,$base,'login',['_csrf_token'=>token($retLogin),'username'=>'hash-user','password'=>'Otra-clave-segura']);
    verify(request($retUser,$base,$retRoute)[0]===404 && request($retUser,$base,$reserveRoute,$reservePost)[0]===404,'Retención y reserva: Usuario no consulta ni modifica otra persona');
    $retAccess=$db->query('SELECT contribuyente_id FROM accesos_tributarios WHERE funcionario_id=101')->fetchColumn();
    $db->exec('UPDATE accesos_tributarios SET contribuyente_id='.$nomTax.' WHERE funcionario_id=101');
    [, $retOwn]=request($retUser,$base,$retRoute);
    $retOwnPost=array_replace($retPost,['_csrf_token'=>token($retOwn),'revision'=>$retHidden($retOwn,'revision'),'huella'=>$retHidden($retOwn,'huella')]);
    verify(request($retUser,$base,$retRoute,$retOwnPost)[0]===303,'Retención: Usuario puede completar la revisión de su ficha asignada');
    $db->exec('UPDATE accesos_tributarios SET contribuyente_id='.(int)$retAccess.' WHERE funcionario_id=101');
} finally { curl_close($retUser); }
