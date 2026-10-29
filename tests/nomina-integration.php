<?php
declare(strict_types=1);
// Fixtures de nómina ficticios; nunca usar datos de los archivos familiares.
$db->prepare("INSERT INTO contribuyentes (funcionario_id,tipo,documento_tipo,documento_numero,nombre,fuentes) VALUES (100,'natural','CC','999111222','Titular nómina ficticio','[]')")->execute();
$nomTax=(int)$db->lastInsertId();
$db->prepare("INSERT INTO perfiles_renta (contribuyente_id,anio,tipo,residencia,regimen,servicios,soporte_perfil,formulario,version) VALUES (?,2025,'natural','residente','ordinario','no_aplica','Prueba','210','2026-10')")->execute([$nomTax]);
$nomProfile=(int)$db->lastInsertId();
[$s,$nomPage]=request($client,$base,'nomina&anio=2025');
verify($s===200 && str_contains($nomPage,'Solo se envían las filas elegidas') && token($nomPage)!=='','Nómina: lector protegido y alcance de privacidad visibles');
[, $nomContextPage]=request($client,$base,'nomina&anio=2025&contribuyente='.$nomTax);
$nomContextDom=new DOMDocument();@$nomContextDom->loadHTML($nomContextPage);$nomContextXpath=new DOMXPath($nomContextDom);
verify($nomContextXpath->query('//nav[@aria-label="Secciones de Registro de información"]//a[@aria-current="page" and normalize-space(.)="Ingresos"]')->length===1
    && $nomContextXpath->query('//nav[@aria-label="Rentas de la cédula general"]//a[@aria-current="page" and normalize-space(.)="Trabajo"]')->length===1
    && str_contains($nomContextPage,'name="contribuyente" value="'.$nomTax.'"')
    && str_contains($nomContextPage,'route=nomina&amp;anio=2025&amp;contribuyente='.$nomTax),
    'Nómina: herramienta dentro de Ingresos y Trabajo conserva ficha/año en sus formularios');
verify(request($client,$base,'nomina&anio=2025&contribuyente=999999999')[0]===404,'Nómina: contexto inexistente no se conserva como si fuera una ficha autorizada');
$nomValues=array_fill_keys(App\Services\NominaData::FIELDS,0);
$nomValues=array_replace($nomValues,['salario'=>100000000,'comisiones'=>20000000,'devengado'=>120000000,'salud'=>4000000,'pension'=>4000000,'retencion'=>1000000,'deducido'=>9000000,'neto'=>111000000]);
$nomRow=['contribuyente_id'=>$nomTax,'perfil_id'=>$nomProfile,'documento'=>'999111222','empleador_documento'=>'999888777','empleador'=>'Empresa ficticia',
    'inicio'=>'2025-01-01','fin'=>'2025-01-15','archivo'=>'prueba.xls','hoja'=>'Enero','archivo_sha'=>str_repeat('a',64),'valores'=>$nomValues];
$nomConfig=json_decode($nomContextXpath->query('//script[@id="nomina-config"]')->item(0)->textContent,true);
verify(count($nomConfig['targets'])===1 && $nomConfig['targets'][0]['id']===$nomTax,'Nómina: persona seleccionada limita los datos del lector a sus propias filas');
$db->exec("INSERT INTO contribuyentes (funcionario_id,tipo,documento_tipo,documento_numero,nombre,fuentes) VALUES (100,'natural','CC','999111333','Otra persona ficticia','[]')");$nomOther=(int)$db->lastInsertId();
[, $nomMissing]=request($client,$base,'nomina&anio=2025&contribuyente='.$nomOther);
verify(str_contains($nomMissing,'Preparar 2025 de Otra persona ficticia') && str_contains($nomMissing,'Extractos bancarios'),'Nómina: falta de perfil anual ofrece preparación concreta y conserva acceso a extractos');
verify(request($client,$base,'nomina&anio=2025&contribuyente='.$nomOther,['_csrf_token'=>token($nomMissing),'accion'=>'revisar','filas'=>json_encode([$nomRow])])[0]===422,'Nómina: servidor rechaza filas de otra persona aunque el Socio pueda consultar ambas');
$nomPreview=static function(array $rs) use($client,$base,$nomPage): array { return request($client,$base,'nomina&anio=2025',['_csrf_token'=>token($nomPage),'accion'=>'revisar','filas'=>json_encode($rs)]); };
$nomCommit=static function(string $html,bool $replace=false) use($client,$base): array {
    preg_match('/name="preview_token" value="([a-f0-9]+)"/',$html,$m);
    return request($client,$base,'nomina&anio=2025',['_csrf_token'=>token($html),'accion'=>'guardar','preview_token'=>$m[1]??'','reemplazar'=>$replace?'1':'0']);
};
verify(request($client,$base,'nomina&anio=2025',['accion'=>'revisar','filas'=>json_encode([$nomRow])])[0]===419,'Nómina: vista previa exige CSRF');
$nomSecond=array_replace($nomRow,['inicio'=>'2025-01-16','fin'=>'2025-01-31']);
[$s,$nomHtml]=$nomPreview([array_replace($nomRow,['fin'=>'2025-01-16']),$nomSecond]);
verify($s===200 && str_contains($nomHtml,'Vista previa · aún no guardada') && !(bool)$db->query('SELECT 1 FROM nominas_laborales LIMIT 1')->fetchColumn(),'Nómina: revisar no guarda movimientos');
preg_match('/name="preview_token" value="([a-f0-9]+)"/',$nomHtml,$nomPeriodToken);
$nomAdjust=['_csrf_token'=>token($nomHtml),'accion'=>'ajustar_periodo','preview_token'=>$nomPeriodToken[1],
    'fila'=>'0','inicio'=>'2025-01-01','fin'=>'2025-01-15'];
verify(request($client,$base,'nomina&anio=2025',array_replace($nomAdjust,['_csrf_token'=>'incorrecto']))[0]===419,'Nómina: corregir fecha exige CSRF');
[$s,$nomHtml]=request($client,$base,'nomina&anio=2025',$nomAdjust);
verify($s===200 && str_contains($nomHtml,'Período del encabezado original: 2025-01-01 a 2025-01-16')
    && !(bool)$db->query('SELECT 1 FROM nominas_laborales LIMIT 1')->fetchColumn(),'Nómina: ajusta encabezado sin guardar y conserva fecha original');
verify($nomCommit($nomHtml)[0]===200 && (int)$db->query('SELECT COUNT(*) FROM nominas_laborales')->fetchColumn()===2,'Nómina: importa dos quincenas con confirmación');
$getNom=static fn()=> $db->query('SELECT * FROM nominas_laborales WHERE perfil_id='.$nomProfile.' ORDER BY inicio')->fetchAll();
verify((int)$db->query('SELECT SUM(valor_cop) FROM movimientos_ingresos m JOIN conceptos_ingresos k ON k.id=m.concepto_id WHERE m.perfil_id='.$nomProfile.' AND k.casilla=32')->fetchColumn()===2400000,'Nómina: dos quincenas suman una sola vez en casilla 32');
$nomIncome=new App\Models\Ingreso(new App\Core\Database($dbConfig));
$nomTotals=App\Services\IngresoSummary::build($nomIncome->concepts($nomIncome->profileFor($nomProfile,100)),$nomIncome->movements($nomProfile,100));
verify($nomTotals[33]['real']===160000 && $nomTotals[34]['real']===2240000,'Nómina: 33 acumula aportes; 34 no resta retenciones');
verify($nomCommit($nomHtml)[0]===422,'Nómina: no reutiliza confirmaciones consumidas');
[$s,$nomRepeat]=$nomPreview([array_replace($nomRow,['archivo'=>'copia.xls'])]);
verify($s===200 && str_contains($nomRepeat,'Ya importada') && $nomCommit($nomRepeat)[0]===200 && count($getNom())===2,'Nómina: copia renombrada no duplica período ni importes');
$nomChanged=array_replace($nomRow,['valores'=>array_replace($nomValues,['salario'=>110000000,'devengado'=>130000000,'neto'=>121000000])]);
[$s,$nomChangeHtml]=$nomPreview([$nomChanged]);
verify(str_contains($nomChangeHtml,'Corrección') && $nomCommit($nomChangeHtml)[0]===422,'Nómina: cambios requieren confirmación explícita');
verify($nomCommit($nomChangeHtml,true)[0]===200 && (int)$getNom()[0]['revision']===2 && (int)$db->query('SELECT COUNT(*) FROM historial_nominas')->fetchColumn()===1,'Nómina: corrige sin sumar otra copia y conserva historial');
[$s,$nomStale]=$nomPreview([array_replace($nomChanged,['valores'=>array_replace($nomChanged['valores'],['retencion'=>2000000,'deducido'=>10000000,'neto'=>120000000])])]);
$db->exec('UPDATE nominas_laborales SET revision=revision+1 WHERE id='.(int)$getNom()[0]['id']);
verify($nomCommit($nomStale,true)[0]===422,'Nómina: una corrección concurrente exige revisar de nuevo');
verify($nomPreview([$nomRow,$nomRow])[0]===422,'Nómina: rechaza dos versiones del mismo período en un lote');
verify($nomPreview([array_replace($nomRow,['documento'=>'999111223'])])[0]===422,'Nómina: rechaza vínculo por documento diferente');
verify($nomPreview([array_replace($nomRow,['inicio'=>'2026-01-01','fin'=>'2026-01-15'])])[0]===422,'Nómina: no traslada fechas a otro año');
$nomOverlap=array_replace($nomRow,['inicio'=>'2025-01-14','fin'=>'2025-01-20']);
$nomFeb=array_replace($nomRow,['inicio'=>'2025-02-01','fin'=>'2025-02-15']);
[$s,$nomRollback]=$nomPreview([$nomFeb,$nomOverlap]);
verify($s===200 && $nomCommit($nomRollback)[0]===422 && count($getNom())===2,'Nómina: período superpuesto revierte todo el lote, incluidos movimientos');
$nomStored=$nomIncome->movements($nomProfile,100)[0];$nomManual=App\Services\IngresoData::read($nomStored,App\Models\Ingreso::MOVEMENT_FIELDS);
$nomDenied=false;try { $nomIncome->saveMovement($nomManual,$nomProfile,100,(int)$nomStored['id']); } catch (DomainException) { $nomDenied=true; }
verify($nomDenied,'Nómina: bloquea edición aislada de un movimiento derivado');
$nomDenied=false;try { $nomIncome->saveMovement($nomManual,$nomProfile,100,null); } catch (DomainException) { $nomDenied=true; }
verify($nomDenied,'Nómina: bloquea duplicación manual del mismo mes');
[$s,$nomRegister]=request($client,$base,'registro&contribuyente='.$nomTax.'&anio=2025');
verify($s===200 && str_contains($nomRegister,'Resumen mensual y acumulado de nómina') && str_contains($nomRegister,'Retención por renta') && str_contains($nomRegister,'Corregir desde nómina'),'Nómina: registro muestra meses, retención, casillas y origen corregible');
$nomUser=curl_init();curl_setopt($nomUser,CURLOPT_COOKIEFILE,'');
try {
    [, $nomLogin]=request($nomUser,$base,'login');request($nomUser,$base,'login',['_csrf_token'=>token($nomLogin),'username'=>'hash-user','password'=>'Otra-clave-segura']);
    [, $nomOwn]=request($nomUser,$base,'nomina&anio=2025');
    verify(!str_contains($nomOwn,'Titular nómina ficticio') && request($nomUser,$base,'nomina&anio=2025',['_csrf_token'=>token($nomOwn),'accion'=>'revisar','filas'=>json_encode([$nomRow])])[0]===422,'Nómina: Usuario no ve ni importa filas de otra persona');
} finally { curl_close($nomUser); }
[$s,$nomRevoked]=$nomPreview([$nomFeb]);
$nomPortableDatabase=new App\Core\Database($dbConfig);
$nomPortableDatabase->connection()->exec('SET collation_connection=utf8mb4_unicode_ci');
$nomPortableModel=new App\Models\NominaLaboral($nomPortableDatabase);
$nomPortableRow=App\Services\NominaData::read($nomFeb,['id'=>$nomTax,'tipo'=>'natural','documento_numero'=>'999111222'],
    ['id'=>$nomProfile,'anio'=>'2025','formulario'=>'210','regimen'=>'ordinario']);
$nomPortableRow['revision']=0;
verify($nomPortableModel->save([$nomPortableRow],100,false)['guardadas']===1,
    'Nómina: guarda con colación de conexión distinta a la de los parámetros sin error 1267');
$nomPortableIncome=new App\Models\Ingreso($nomPortableDatabase);
$nomPortableManual=array_replace($nomManual,['fecha'=>'2025-02-20']);
$nomPortableDenied=false;try { $nomPortableIncome->saveMovement($nomPortableManual,$nomProfile,100,null); } catch (DomainException) { $nomPortableDenied=true; }
verify($nomPortableDenied,'Nómina: protección contra captura manual funciona con colaciones distintas');
$db->exec('UPDATE accesos_tributarios SET activo=0 WHERE funcionario_id=100');
verify($nomCommit($nomRevoked)[0]===303 && count($getNom())===3,'Nómina: revocar acceso invalida confirmación abierta');
$db->exec('UPDATE accesos_tributarios SET activo=1 WHERE funcionario_id=100');
[, $nomLogin]=request($client,$base,'login');request($client,$base,'login',['_csrf_token'=>token($nomLogin),'username'=>'admin','password'=>'Prueba-segura-2026']);
