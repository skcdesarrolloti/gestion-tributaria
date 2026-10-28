<?php
declare(strict_types=1);
$pensionRoute='registro&contribuyente='.$taxpayerId.'&anio=2025&cedula=pensiones';
[$s,$pensionHtml]=request($client,$base,$pensionRoute);
verify($s===200 && str_contains($pensionHtml,'99 · Ingresos brutos por rentas de pensiones') && !str_contains($pensionHtml,'58 · Ingresos brutos'),'Pensiones muestra sus ingresos y no los de capital');
$incomeHierarchyDom=new DOMDocument();@$incomeHierarchyDom->loadHTML($pensionHtml);$incomeHierarchyXpath=new DOMXPath($incomeHierarchyDom);
verify($incomeHierarchyXpath->query('//nav[@aria-label="Orientación y regreso"]//nav[@aria-label="Cédulas del formulario 210"]//a[@aria-current="page" and normalize-space(.)="Pensiones"]')->length===1
    && $incomeHierarchyXpath->query('//nav[@aria-label="Secciones de Registro de información"]//a')->length===2
    && $incomeHierarchyXpath->query('//nav[@aria-label="Secciones de Registro de información"]//a[contains(@href,"nomina")]')->length===0,
    'Ingresos: cédulas persistentes debajo de la categoría; nómina y extractos no son cédulas ni categorías');
[, $generalHierarchy]=request($client,$base,'registro&contribuyente='.$taxpayerId.'&anio=2025&componente=ingresos&cedula=general');
$incomeHierarchyDom=new DOMDocument();@$incomeHierarchyDom->loadHTML($generalHierarchy);$incomeHierarchyXpath=new DOMXPath($incomeHierarchyDom);
verify($incomeHierarchyXpath->query('//nav[@aria-label="Orientación y regreso"]//nav[@aria-label="Rentas de la cédula general"]')->length===1
    && strpos($generalHierarchy,'aria-label="Cédulas del formulario 210"')<strpos($generalHierarchy,'id="fuentes"'),
    'Ingresos: rentas de la cédula general disponibles antes de las preguntas y del contenido');
$pensionMovement='ingresos/movimiento&id='.$profileId.'&origen=registro&anio=2025&cedula=pensiones';
[$s,$pensionForm]=request($client,$base,$pensionMovement);
verify($s===200 && str_contains($pensionForm,'value="'.$codeId('210',99).'"') && !str_contains($pensionForm,'value="'.$codeId('210',58).'"'),'El formulario de pensiones limita las opciones a su sección');
$pensionData=array_replace($movement,['_csrf_token'=>token($pensionForm),'concepto_id'=>$codeId('210',99),'fecha'=>'2025-06-01','descripcion'=>'Mesada sintética','valor_original'=>'777','valor_cop'=>'777']);
verify(request($client,$base,$pensionMovement,array_replace($pensionData,['concepto_id'=>$codeId('210',58)]))[0]===422,'POST no permite trasladar el registro a otra cédula');
[$s,,$pensionBack]=request($client,$base,$pensionMovement,$pensionData);
verify($s===303 && str_contains($pensionBack,'cedula=pensiones') && str_contains(request($client,$base,$pensionRoute)[1],'Mesada sintética'),'Guarda pensión y regresa al mismo año y cédula');
verify(!str_contains(request($client,$base,str_replace('anio=2025','anio=2026',$pensionRoute))[1],'Mesada sintética'),'Pensión 2025 no aparece en 2026');
$dividendRoute=str_replace('cedula=pensiones','cedula=dividendos',$pensionRoute);
[$s,$dividendHtml]=request($client,$base,$dividendRoute);
verify($s===200 && str_contains($dividendHtml,'104 · Dividendos') && str_contains($dividendHtml,'107 · 1a.') && str_contains($dividendHtml,'108 · 2a.') && str_contains($dividendHtml,'109 · Dividendos') && !str_contains($dividendHtml,'99 · Ingresos'),'Dividendos separa utilidades anteriores, subcédulas y exterior');
verify(str_contains(request($client,$base,str_replace('cedula=pensiones','cedula=ganancias',$pensionRoute))[1],'112 · Ingresos por ganancias ocasionales'),'Ganancias ocasionales se captura en su sección independiente');
$legalEntity=$db->query("SELECT p.*,c.nombre FROM perfiles_renta p JOIN contribuyentes c ON c.id=p.contribuyente_id WHERE p.tipo='juridica' AND p.residencia='residente' LIMIT 1")->fetch();
$entityDividend='registro&contribuyente='.$legalEntity['contribuyente_id'].'&anio='.$legalEntity['anio'].'&seccion=dividendos';
[$s,$entityDividendHtml]=request($client,$base,$entityDividend);
verify($s===200 && str_contains($entityDividendHtml,'49 · Dividendos') && !str_contains($entityDividendHtml,'52 · Dividendos') && !str_contains($entityDividendHtml,'aria-label="Cédulas del formulario 210"'),'Sociedad tiene secciones 110 sin cédulas ni dividendos exclusivos de naturales');
$oldIds=(int)$db->query('SELECT COUNT(*) FROM movimientos_ingresos WHERE perfil_id='.$profileId)->fetchColumn();
(new App\Services\Migrator($db,$root.'/database'))->run();
verify((int)$db->query('SELECT COUNT(*) FROM movimientos_ingresos WHERE perfil_id='.$profileId)->fetchColumn()===$oldIds,'Volver a migrar conserva los movimientos previos');
verify(request($client,$base,'registro&seccion=inventada')[0]===422,'Rechaza sección no reconocida');
