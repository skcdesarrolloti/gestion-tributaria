<?php
declare(strict_types=1);
// Solo soportes sintéticos; no abrir archivos bancarios reales en esta base.
$bankRoute='extractos&contribuyente='.$taxpayerId.'&anio=2025';
[$s,$bankHtml]=request($client,$base,$bankRoute);
verify($s===200 && str_contains($bankHtml,'Subir extracto bancario en PDF'),'Extractos: abre carga sin exigir preparar ingresos ni tener un RUT');
$bankPath=$temp.'/extracto-sintetico.pdf';file_put_contents($bankPath,rutFixturePdf());
$bankPost=['_csrf_token'=>token($bankHtml),'mes'=>'6','banco'=>'Banco prueba','cuenta'=>'0012','titular_confirmado'=>'1',
    'extracto_pdf'=>new CURLFile($bankPath,'application/pdf','extracto-prueba.pdf')];
verify(rutRequest($client,$base,$bankRoute,array_replace($bankPost,['_csrf_token'=>'']))[0]===419,'Extractos: exige CSRF antes de almacenar PDF');
verify(rutRequest($client,$base,$bankRoute,array_replace($bankPost,['mes'=>'auto']))[0]===422,'Extractos: no inventa un mes si el PDF no contiene un período reconocido');
[$s,,$bankHeaders]=rutRequest($client,$base,$bankRoute,array_replace($bankPost,['titular_confirmado'=>'0']));
verify($s===303 && str_contains($bankHeaders,'guardado=1'),'Extractos: guarda PDF mediante carga HTTP real');
$bankDoc=$db->query('SELECT * FROM extractos_bancarios LIMIT 1')->fetch();$bankDocId=(int)$bankDoc['id'];
verify(json_decode($bankDoc['lectura'],true)['titular_confirmado']===false,'Extractos: guarda sin casilla y conserva revisión visual pendiente');
verify(is_file($temp.'/extractos-bancarios/'.$bankDoc['archivo']) && $bankDoc['sha256']===hash_file('sha256',$bankPath),'Extractos: conserva PDF privado íntegro');
[$s,$bankHtml]=request($client,$base,$bankRoute.'&documento='.$bankDocId);
verify($s===200 && str_contains($bankHtml,'1/12 meses con PDF') && str_contains($bankHtml,'Lectura y clasificación pendientes')
    && str_contains($bankHtml,'Diciembre') && str_contains($bankHtml,'Falta PDF') && str_contains($bankHtml,'<object'),'Extractos: muestra doce meses, pendientes y visor integrado sin inventar importes');
[$s,$bankBytes,$bankInline]=request($client,$base,'extractos/documento&documento='.$bankDocId);
verify($s===200 && hash('sha256',$bankBytes)===$bankDoc['sha256'] && str_contains($bankInline,'SAMEORIGIN') && str_contains($bankInline,'inline;'),'Extractos: PDF protegido se consulta dentro de la aplicación');
$fileCount=count(glob($temp.'/extractos-bancarios/*.pdf'));
verify(rutRequest($client,$base,$bankRoute,array_replace($bankPost,['mes'=>'7','extracto_pdf'=>new CURLFile($bankPath,'application/pdf','renombrado.pdf')]))[0]===409
    && count(glob($temp.'/extractos-bancarios/*.pdf'))===$fileCount,'Extractos: copia renombrada no duplica soporte y limpia archivo rechazado');
verify(rutRequest($client,$base,str_replace('2025','2026',$bankRoute),$bankPost)[0]===409,'Extractos: no permite atribuir el mismo PDF a otro año');
file_put_contents($bankPath,rutFixturePdf('87654321'));
verify(rutRequest($client,$base,$bankRoute,$bankPost)[0]===409,'Extractos: otro PDF para misma cuenta/mes no sustituye el original');
verify(rutRequest($client,$base,$bankRoute,array_replace($bankPost,['mes'=>'7']))[0]===303,'Extractos: nuevo mes se conserva separado');
verify(!str_contains(request($client,$base,str_replace('2025','2026',$bankRoute))[1],'2/12 meses con PDF'),'Extractos: años separados');
verify(request($client,$base,$bankRoute.'&documento=9999999')[0]===404 && request($client,$base,'extractos&anio=2027')[0]===422,'Extractos: rechaza documento inexistente y año fuera de alcance');
$bankModel=new App\Models\ExtractoBancario(new App\Core\Database($dbConfig));
verify($bankModel->findFor($bankDocId,$otherOwner)===null && !$bankModel->listFor($taxpayerId,$otherOwner,2025),'Extractos: Usuario sin asignación no consulta documentos ajenos');
$deniedBank=false;
try { $bankModel->saveFor($taxpayerId,$otherOwner,['anio'=>2025,'mes'=>8,'banco'=>'OTRO','cuenta'=>'0012'],array_replace($bankDoc,['sha256'=>str_repeat('e',64)])); }
catch (DomainException) { $deniedBank=true; }
verify($deniedBank,'Extractos: modelo vuelve a comprobar permisos al escribir');
$anonymousBank=curl_init();curl_setopt($anonymousBank,CURLOPT_COOKIEFILE,'');
try { verify(request($anonymousBank,$base,'extractos/documento&documento='.$bankDocId)[0]===303,'Extractos: PDF exige sesión incluso con identificador conocido'); }
finally { curl_close($anonymousBank); }
file_put_contents($bankPath,'No es un PDF');
verify(rutRequest($client,$base,$bankRoute,array_replace($bankPost,['mes'=>'8']))[0]===422,'Extractos: extensión PDF no permite contenido falso');
verify(count(glob($temp.'/extractos-bancarios/*.pdf'))===2,'Extractos: rechazos no dejan archivos huérfanos');
require_once __DIR__.'/extractos-fixtures.php';
$db->exec("INSERT INTO contribuyentes (funcionario_id,tipo,documento_tipo,documento_numero,nombre,fuentes) VALUES (100,'natural','CC','987001234','Titular banco ficticio','[]')");
$readBankTax=(int)$db->lastInsertId();$readBankRoute='extractos&contribuyente='.$readBankTax.'&anio=2025';
file_put_contents($bankPath,bankFixturePdf());
$readBankPost=array_replace($bankPost,['mes'=>'6','banco'=>'BBVA','cuenta'=>'1234',
    'extracto_lectura'=>new CURLFile($bankPath,'application/pdf','lectura.pdf')]);
verify(rutRequest($client,$base,$readBankRoute,array_replace($readBankPost,['mes'=>'7']))[0]===422,'Extractos: período leído impide asignar el PDF al mes incorrecto');
verify(rutRequest($client,$base,$readBankRoute,array_replace($readBankPost,['mes'=>'auto']))[0]===303,'Extractos: detecta mes desde PDF sintético multipágina y guarda lectura conciliada');
$readBankId=(int)$db->query('SELECT MAX(id) FROM extractos_bancarios')->fetchColumn();
[$s,$readBankHtml]=request($client,$base,$readBankRoute.'&documento='.$readBankId.'&buscar=COMERCIO');
verify($s===200 && str_contains($readBankHtml,'Leído y conciliado · 2 páginas') && str_contains($readBankHtml,'$1.000,00')
    && str_contains($readBankHtml,'$10,00') && str_contains($readBankHtml,'$1,00') && str_contains($readBankHtml,'1 movimientos encontrados.'),
    'Extractos: acumulado mensual y búsqueda de gastos usan lectura guardada sin duplicar casillas');
verify(str_contains($readBankHtml,'PAGO COMERCIO FICTICIO') && !str_contains($readBankHtml,'<th scope="row">TRANSFERENCIA POR ACLARAR</th>'),'Extractos: búsqueda conserva el pago consultado y filtra los demás conceptos');
$readBankRecord=$db->query('SELECT * FROM extractos_bancarios WHERE id='.$readBankId)->fetch();
$readBankMeta=json_decode($readBankRecord['lectura'],true);
verify(isset($readBankMeta['archivo_consulta']) && is_file($temp.'/extractos-bancarios/'.$readBankMeta['archivo_consulta'])
    && is_file($temp.'/extractos-bancarios/'.$readBankRecord['archivo']),'Extractos: conserva original y copia de lectura privados sin guardar claves');
verify(!str_contains($readBankHtml,'name="pdf_password"') && str_contains($readBankHtml,'data-pdf-password') && str_contains($readBankHtml,'Detectar mes desde el PDF'),'Extractos: clave de apertura sin nombre de envío y selector automático presentes');
$beforeCopies=count(glob($temp.'/extractos-bancarios/*.pdf'));
verify(rutRequest($client,$base,$readBankRoute,$readBankPost)[0]===409 && count(glob($temp.'/extractos-bancarios/*.pdf'))===$beforeCopies,'Extractos: repetición limpia ambas copias nuevas sin duplicar documento');
require __DIR__.'/pensiones-navigation-integration.php';
$db->exec("INSERT INTO contribuyentes (funcionario_id,tipo,documento_tipo,documento_numero,nombre,fuentes) VALUES (100,'natural','CC','987001235','Titular banco ficticio','[]')");
$pendingBankTax=(int)$db->lastInsertId();$pendingBankRoute='extractos&contribuyente='.$pendingBankTax.'&anio=2025';
file_put_contents($bankPath,str_replace('(1.00)','(2.00)',bankFixturePdf()));
$pendingBankPost=array_replace($readBankPost,['mes'=>'auto','titular_confirmado'=>'0']);
verify(rutRequest($client,$base,$pendingBankRoute,array_replace($pendingBankPost,['cuenta'=>'9999']))[0]===422,'Extractos: PDF pendiente conserva comprobación de cuenta antes de guardar');
verify(rutRequest($client,$base,$pendingBankRoute,$pendingBankPost)[0]===303,'Extractos: detecta mes y guarda soporte sin conciliación ni revisión visual');
$pendingBankMeta=json_decode($db->query('SELECT lectura FROM extractos_bancarios WHERE contribuyente_id='.$pendingBankTax)->fetchColumn(),true);
verify($pendingBankMeta['estado']==='pendiente' && $pendingBankMeta['inicio']==='2025-06-01' && !isset($pendingBankMeta['totales']),
    'Extractos: conciliación fallida no publica importes ni borra el período leído');
