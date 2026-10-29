<?php
declare(strict_types=1);
$bankDataBefore=$db->query('SELECT id,lectura,sha256 FROM extractos_bancarios ORDER BY id')->fetchAll();
$capitalBankRoute='registro&contribuyente='.$readBankTax.'&anio=2025&componente=ingresos&cedula=general&renta=capital';
[$s,$capitalBankHtml]=request($client,$base,$capitalBankRoute);
verify($s===200 && str_contains($capitalBankHtml,'Rendimientos financieros por banco') && str_contains($capitalBankHtml,'Rendimientos abonados')
    && !str_contains($capitalBankHtml,'4×1000 cobrado'),'Capital conserva rendimientos por cuenta sin columnas de GMF');
$gmfBankRoute=str_replace('componente=ingresos','componente=costos',$capitalBankRoute);
[$s,$gmfBankHtml]=request($client,$base,$gmfBankRoute);
verify($s===200 && str_contains($gmfBankHtml,'4×1000 por banco') && str_contains($gmfBankHtml,'4×1000 cobrado')
    && str_contains($gmfBankHtml,'Reintegros') && !str_contains($gmfBankHtml,'Rendimientos abonados'),'Costos separa cobros y reintegros de GMF sin presentarlos como ingreso');
[$s,$gmfBankDetail]=request($client,$base,$gmfBankRoute.'&documento='.$readBankId.'&buscar=COMERCIO');
verify($s===200 && str_contains($gmfBankDetail,'<object') && str_contains($gmfBankDetail,'name="componente" value="costos"')
    && str_contains($gmfBankDetail,'name="renta" value="capital"'),'Consultar y buscar en un PDF de Costos conserva el contexto, sin enviar a Pensiones');
verify($db->query('SELECT id,lectura,sha256 FROM extractos_bancarios ORDER BY id')->fetchAll()===$bankDataBefore,'Separar controles reutiliza los PDF sin alterar lecturas ni duplicarlos');
file_put_contents($bankPath,rutFixturePdf('88448844'));
$bankContextPost=array_replace($bankPost,['_csrf_token'=>token($gmfBankHtml),'banco'=>'OTRO BANCO','cuenta'=>'7788','mes'=>'9']);
unset($bankContextPost['extracto_lectura']);
[$s,,$bankCostReturn]=rutRequest($client,$base,'extractos&contribuyente='.$readBankTax.'&anio=2025&origen=costos',$bankContextPost);
verify($s===303 && str_contains($bankCostReturn,'componente=costos') && str_contains($bankCostReturn,'#detalle'),
    'Subir extracto de otro banco desde Costos devuelve al mismo componente');
verify(rutRequest($client,$base,'extractos&contribuyente='.$readBankTax.'&anio=2025&origen=capital',$bankContextPost)[0]===409,
    'Subir en Capital el mismo PDF ya cargado en Costos no lo duplica');
file_put_contents($bankPath,rutFixturePdf('88448845'));$bankContextPost['mes']='10';
[$s,,$bankCapitalReturn]=rutRequest($client,$base,'extractos&contribuyente='.$readBankTax.'&anio=2025&origen=capital',$bankContextPost);
verify($s===303 && str_contains($bankCapitalReturn,'componente=ingresos') && str_contains($bankCapitalReturn,'renta=capital') && str_contains($bankCapitalReturn,'seccion=financieros'),
    'Subir desde Capital devuelve a rendimientos y conserva compatibilidad con 110');
$taxAnalysisRoute='analisis&contribuyente='.$nomTax.'&anio=2025';
[$s,$taxAnalysisHtml]=request($client,$base,$taxAnalysisRoute);
verify($s===200 && str_contains($taxAnalysisHtml,'¿Cómo va mi impuesto con salario y pensión?') && str_contains($taxAnalysisHtml,'Impuesto del escenario:')
    && str_contains($taxAnalysisHtml,'Tabla anual, ET 241'),'Análisis calcula desde salarios guardados y explica origen y tabla anual');
$analysisSnapshot=$db->query('SELECT id,valor_cop FROM movimientos_ingresos ORDER BY id')->fetchAll();
verify(request($client,$base,$taxAnalysisRoute,['_csrf_token'=>'','aporte_simulado'=>'1000000'])[0]===419,'Comparación de aporte exige CSRF');
verify(request($client,$base,$taxAnalysisRoute,['_csrf_token'=>token($taxAnalysisHtml),'aporte_simulado'=>'1e9'])[0]===422,'Comparación rechaza importes ambiguos');
[$s,$taxTrialHtml]=request($client,$base,$taxAnalysisRoute,['_csrf_token'=>token($taxAnalysisHtml),'aporte_simulado'=>'1000000']);
verify($s===200 && str_contains($taxTrialHtml,'Aporte propuesto: $1.000.000') && str_contains($taxTrialHtml,'Reducción estimada:'),'Prueba de aporte compara en la misma página');
verify($db->query('SELECT id,valor_cop FROM movimientos_ingresos ORDER BY id')->fetchAll()===$analysisSnapshot,'Comparar un ahorro no escribe ingresos ni altera la nómina');
$pensionEntryRoute='ingresos/movimiento&id='.$nomProfile.'&origen=registro&anio=2025&cedula=pensiones';
[, $pensionEntryHtml]=request($client,$base,$pensionEntryRoute);
$pensionEntry=array_replace($movement,['_csrf_token'=>token($pensionEntryHtml),'fecha'=>'2025-01-31','estado'=>'real','pais'=>'Colombia',
    'tercero'=>'Pagador pensión ficticia','descripcion'=>'Mesada sintética','soporte'=>'Comprobante ficticio','justificacion'=>'ET 56 y 337',
    'moneda'=>'COP','valor_original'=>'4000000','valor_cop'=>'4000000','concepto_id'=>$codeId('210',99)]);
verify(request($client,$base,$pensionEntryRoute,$pensionEntry)[0]===303,'Registra bruto pensional sin convertir el neto bancario');
verify(request($client,$base,$pensionEntryRoute,array_replace($pensionEntry,['concepto_id'=>$codeId('210',100),'valor_original'=>'480000','valor_cop'=>'480000']))[0]===303,
    'Casilla 100 permite registrar aportes del comprobante por mes');
[, $mixedAnalysisHtml]=request($client,$base,$taxAnalysisRoute);
verify(str_contains($mixedAnalysisHtml,'Bruto registrado en 99: $4.000.000') && str_contains($mixedAnalysisHtml,'aportes registrados en 100: $480.000')
    && str_contains($mixedAnalysisHtml,'exención simulada: $3.520.000'),'Resumen reconstruye la pensión desde sus movimientos guardados');
$bankYearHtml=request($client,$base,str_replace('anio=2025','anio=2026',$gmfBankRoute))[1];
verify(str_contains($bankYearHtml,'No hay extractos guardados de este año.'),'GMF de un año no aparece como cobro del siguiente');
$check100=$db->query("SELECT COUNT(*) FROM conceptos_ingresos WHERE formulario='210' AND casilla=100 AND bloque='Pensiones'")->fetchColumn();
verify((int)$check100===1,'Migración incorpora casilla 100 sin duplicar catálogos previos');
$bankRestricted=curl_init();curl_setopt($bankRestricted,CURLOPT_COOKIEFILE,'');
try {
    [, $bankLogin]=request($bankRestricted,$base,'login');
    request($bankRestricted,$base,'login',['_csrf_token'=>token($bankLogin),'username'=>'hash-user','password'=>'Otra-clave-segura']);
    verify(request($bankRestricted,$base,$gmfBankRoute)[0]===404 && request($bankRestricted,$base,$taxAnalysisRoute)[0]===404,
        'Los controles bancarios y el impuesto no amplían acceso a terceros');
} finally { curl_close($bankRestricted); }
