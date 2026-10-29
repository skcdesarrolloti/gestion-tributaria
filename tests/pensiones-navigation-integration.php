<?php
// Reutiliza exclusivamente los PDF sintéticos conservados por extractos-integration.
$pensionRoute='registro&contribuyente='.$readBankTax.'&anio=2025&componente=ingresos&cedula=pensiones';
$bankSnapshot=$db->query('SELECT id,sha256,archivo,lectura FROM extractos_bancarios ORDER BY id')->fetchAll();
[$s,$pensionHtml]=request($client,$base,$pensionRoute);
verify($s===200 && str_contains($pensionHtml,'1 PDF guardado · 1 mes con soporte.') && str_contains($pensionHtml,'Control de meses · 2025')
    && str_contains($pensionHtml,'$1.000,00'),'Pensiones: muestra soportes y acumulados guardados aun sin perfil anual');
$pensionDom=new DOMDocument();@$pensionDom->loadHTML($pensionHtml);$pensionXpath=new DOMXPath($pensionDom);
verify($pensionXpath->query('//*[@aria-label="Pensiones y extractos guardados" and not(ancestor::details)]')->length===1,
    'Pensiones: los datos bancarios aparecen antes de desplegar casillas o preguntas');
verify(!str_contains($pensionHtml,'id="fuentes"') && !str_contains($pensionHtml,'Rendimientos acumulados')
    && !str_contains($pensionHtml,'4×1000 neto de reintegros') && str_contains($pensionHtml,'Pensión acumulada (neta)'),
    'Pensiones centra su tabla en el ingreso pensional sin cuestionarios generales ni columnas de otros ingresos');
verify((bool)preg_match('/href="[^"]*cedula=pensiones[^"]*documento='.$readBankId.'#detalle"/',$pensionHtml),
    'Pensiones: abrir un mes mantiene la consulta dentro de Pensiones');
[$s,$pensionDetail]=request($client,$base,$pensionRoute.'&documento='.$readBankId.'&buscar=COMERCIO');
verify($s===200 && str_contains($pensionDetail,'<object') && str_contains($pensionDetail,'1 movimientos encontrados.')
    && str_contains($pensionDetail,'name="route" value="registro"') && str_contains($pensionDetail,'name="cedula" value="pensiones"'),
    'Pensiones: visor y búsqueda reutilizan el PDF y conservan persona, año y sección');
verify(request($client,$base,$pensionRoute.'&documento='.$bankDocId)[0]===404
    && request($client,$base,str_replace('anio=2025','anio=2026',$pensionRoute).'&documento='.$readBankId)[0]===404,
    'Pensiones: no muestra documentos de otra persona ni de otro año');
[, $pensionOtherYear]=request($client,$base,str_replace('anio=2025','anio=2026',$pensionRoute));
verify(str_contains($pensionOtherYear,'0 PDF guardados') && !str_contains($pensionOtherYear,'Leído y conciliado · 2 páginas'),
    'Pensiones: año vacío informa ausencia de soportes sin ocultar otro año');
[, $pensionUpload]=request($client,$base,$pensionRoute.'&subir=1');
$pensionDom=new DOMDocument();@$pensionDom->loadHTML($pensionUpload);$pensionXpath=new DOMXPath($pensionDom);
verify($pensionXpath->query('//details[@id="subir-extracto" and @open]')->length===1 && str_contains($pensionUpload,'origen=pensiones'),
    'Pensiones: Subir PDF abre el formulario con retorno a la misma sección');
verify($db->query('SELECT id,sha256,archivo,lectura FROM extractos_bancarios ORDER BY id')->fetchAll()===$bankSnapshot,
    'Pensiones: consultar no borra ni transforma documentos y lecturas guardadas');
file_put_contents($bankPath,rutFixturePdf('11223344'));
[$s,,$pensionSavedHeaders]=rutRequest($client,$base,$bankRoute.'&origen=pensiones',array_replace($bankPost,['mes'=>'8']));
verify($s===303 && str_contains($pensionSavedHeaders,'route=registro') && str_contains($pensionSavedHeaders,'cedula=pensiones')
    && str_contains($pensionSavedHeaders,'guardado=1#detalle'),'Pensiones: después de subir el PDF regresa al documento en Pensiones');
