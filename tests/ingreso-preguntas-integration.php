<?php
declare(strict_types=1);
$incomeQuestionRoute='registro&contribuyente='.$preparationTaxpayer.'&anio=2026';
$questionsRoute='analisis&contribuyente='.$preparationTaxpayer.'&anio=2026';
[$s,$focusedIncomeHtml]=request($client,$base,$incomeQuestionRoute);
verify($s===200 && !str_contains($focusedIncomeHtml,'id="fuentes"') && !str_contains($focusedIncomeHtml,'respuestas[pensiones]'),
    'Laborales no repite preguntas de otras fuentes del año');
[$s,$questionsHtml]=request($client,$base,$questionsRoute);
verify($s===200 && str_contains($questionsHtml,'13 por confirmar') && str_contains($questionsHtml,'Pendiente por confirmar'),'Análisis conserva revisión anual de fuentes por grupos');
$questionDom=new DOMDocument();@$questionDom->loadHTML($questionsHtml);$questionXpath=new DOMXPath($questionDom);
verify($questionXpath->query('//details[@id="fuentes" and not(@open)]')->length===1,'Revisión anual cerrada por defecto aunque haya fuentes pendientes');
$saveQuestions='registro/fuentes&contribuyente='.$preparationTaxpayer.'&anio=2026';
$activityPost=['_csrf_token'=>token($questionsHtml),'revision'=>'0','grupo'=>'actividad','respuestas'=>['salarios'=>'si','honorarios'=>'no','pensiones'=>'si','negocios'=>'no']];
verify(request($client,$base,$saveQuestions,array_replace($activityPost,['_csrf_token'=>'']))[0]===419,'Respuestas anuales exigen CSRF');
[$s,,$questionBack]=request($client,$base,$saveQuestions,$activityPost);
verify($s===303 && str_contains($questionBack,'route=analisis') && str_contains($questionBack,'anio=2026') && str_contains($questionBack,'#fuentes'),'Guarda fuentes y regresa a Análisis del mismo contribuyente y año');
verify(request($client,$base,$saveQuestions,$activityPost)[0]===409,'No pierde respuestas al guardar desde una revisión antigua');
[$s,$savedQuestionHtml]=request($client,$base,$questionsRoute);
verify($s===200 && str_contains($savedQuestionHtml,'9 por confirmar') && str_contains(request($client,$base,$incomeQuestionRoute)[1],'value="no_aplica" selected'),'Conserva pendientes anuales y usa la ausencia confirmada de honorarios al preparar el registro');
$capitalPost=['_csrf_token'=>token($savedQuestionHtml),'revision'=>'1','grupo'=>'capital','respuestas'=>['arriendos'=>'si','rendimientos'=>'no','dividendos'=>'si']];
verify(request($client,$base,$saveQuestions,$capitalPost)[0]===303,'Guarda capital sin borrar actividad');
$savedAnswers=$characterization->blocksFor($preparationTaxpayer,$owner,2026)['ingresos']['datos']['campos'];
verify($savedAnswers['pensiones']==='si' && $savedAnswers['arriendos']==='si' && !isset($savedAnswers['valor_cop']),'Salario, pensión y capital coexisten sin crear importes');
verify(request($client,$base,'registro&contribuyente='.$preparationTaxpayer.'&anio=2025')[0]===200 && !isset($characterization->blocksFor($preparationTaxpayer,$owner,2025)['ingresos']),'Respuestas no se trasladan a otro año');
$profileQuestionRoute='ingresos&contribuyente='.$preparationTaxpayer.'&origen=registro&anio=2026';
$conflictingProfile=['_csrf_token'=>token($savedQuestionHtml),'anio'=>'2026','residencia'=>'residente','regimen'=>'ordinario','servicios'=>'costos','soporte_perfil'=>'Prueba de coherencia anual'];
verify(request($client,$base,$profileQuestionRoute,$conflictingProfile)[0]===422,'Condiciones fiscales no contradicen ausencia de honorarios confirmada');
$scopeClient=curl_init();curl_setopt($scopeClient,CURLOPT_COOKIEFILE,'');
try {
    [, $scopeLogin]=request($scopeClient,$base,'login');request($scopeClient,$base,'login',['_csrf_token'=>token($scopeLogin),'username'=>'hash-user','password'=>'Otra-clave-segura']);
    [, $scopePage]=request($scopeClient,$base,'registro&contribuyente='.$taxpayerId);
    verify(request($scopeClient,$base,$saveQuestions,array_replace($activityPost,['_csrf_token'=>token($scopePage)]))[0]===404,'Usuario no responde por un contribuyente ajeno');
} finally { curl_close($scopeClient); }
$auditDocument=$db->query('SELECT contribuyente_id,anio FROM rut_documentos ORDER BY id DESC LIMIT 1')->fetch();
[$s,$auditHtml]=request($client,$base,'contribuyentes/perfil&id='.$auditDocument['contribuyente_id'].'&anio='.$auditDocument['anio'].'&tab=documentos');
verify($s===200 && str_contains($auditHtml,'Revisar RUT frente al perfil') && str_contains($auditHtml,'Cobertura de páginas y límites de lectura'),'Documentos expone contraste del RUT y alcance sin certificar lectura total');
$referenceDocument=$db->query('SELECT d.contribuyente_id,d.anio FROM rut_documentos d WHERE NOT EXISTS (SELECT 1 FROM rut_documentos o WHERE o.contribuyente_id=d.contribuyente_id AND o.anio=IF(d.anio=2025,2026,2025)) LIMIT 1')->fetch();
[$s,$otherYearAudit]=request($client,$base,'contribuyentes/perfil&id='.$referenceDocument['contribuyente_id'].'&anio='.($referenceDocument['anio']==2025?'2026':'2025').'&tab=documentos');
verify($s===200 && str_contains($otherYearAudit,'Puedes revisarlo aquí sin volver a subirlo') && str_contains($otherYearAudit,'Comparación con el perfil '.$referenceDocument['anio']),'RUT disponible en otro año se consulta sin exigir una nueva carga ni comparar contra campos del año equivocado');
