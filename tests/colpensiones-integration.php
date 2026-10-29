<?php
declare(strict_types=1);
require_once __DIR__.'/colpensiones-fixtures.php';
$db->exec("INSERT INTO contribuyentes (funcionario_id,tipo,documento_tipo,documento_numero,nombre,fuentes) VALUES (100,'natural','CC','55667788','Pensionado ficticio','[]')");
$colTax=(int)$db->lastInsertId();$colDatabase=new App\Core\Database($dbConfig);$colIncomes=new App\Models\Ingreso($colDatabase);
$colProfile=$colIncomes->createProfile(['anio'=>'2025','residencia'=>'residente','regimen'=>'ordinario','servicios'=>'no_aplica','soporte_perfil'=>'Prueba ficticia'],['id'=>$colTax,'tipo'=>'natural'],100);
$colRoute='pensiones/comprobante&perfil='.$colProfile;$colPageRoute='registro&contribuyente='.$colTax.'&anio=2025&componente=ingresos&cedula=pensiones';
[$s,$colPage]=request($client,$base,$colPageRoute);
verify($s===200 && str_contains($colPage,'data-colpensiones') && str_contains($colPage,'Leer comprobante aquí'),'Pensiones incluye carga de Colpensiones y revisión dentro del mismo panel');
$colPath=$temp.'/colpensiones-ficticio.pdf';file_put_contents($colPath,colpensionesFixture());
$colPost=['_csrf_token'=>token($colPage),'comprobante_pdf'=>new CURLFile($colPath,'application/pdf','colpensiones-ficticio.pdf')];
verify(rutRequest($client,$base,$colRoute,array_replace($colPost,['_csrf_token'=>'']))[0]===419,'Colpensiones exige CSRF antes de conservar el PDF');
[$s,$colPreview]=rutRequest($client,$base,$colRoute,$colPost);$colPreview=json_decode($colPreview,true);
verify($s===200 && $colPreview['lectura']['registros'][0]['bruto']===450000000 && $colPreview['lectura']['registros'][0]['salud']===54000000,'Carga HTTP lee bruto y salud del PDF sintético');
$colDoc=$colPreview['id'];
verify((int)$db->query('SELECT COUNT(*) FROM movimientos_ingresos WHERE perfil_id='.$colProfile)->fetchColumn()===0,'Subir PDF no crea ingresos antes de revisar y confirmar');
[$s,$colDup]=rutRequest($client,$base,$colRoute,$colPost);
verify($s===200 && json_decode($colDup,true)['id']===$colDoc && (int)$db->query('SELECT COUNT(*) FROM comprobantes_pension WHERE perfil_id='.$colProfile)->fetchColumn()===1,'PDF repetido reutiliza el soporte sin duplicarlo');
$colConfirm=['_csrf_token'=>token($colPage),'accion'=>'guardar','comprobante'=>(string)$colDoc,'confirmado'=>'1','versiones'=>'{}',
    'filas'=>json_encode([['mes'=>'1','bruto'=>'4500000','salud'=>'540000']])];
verify(request($client,$base,$colRoute,array_replace($colConfirm,['confirmado'=>'0']))[0]===422,'No importa sin revisión explícita del titular y los valores');
[$s,$colSaved]=request($client,$base,$colRoute,$colConfirm);$colSaved=json_decode($colSaved,true);
verify($s===200 && $colSaved['impuesto']['pension_bruta']===4500000 && $colSaved['impuesto']['pension_salud']===540000,'Guardar genera 99 y 100 y devuelve cálculo actualizado sin navegar');
$colModel=new App\Models\ComprobantePension($colDatabase);$colCurrent=$colModel->rows($colProfile,100);
verify(count($colCurrent)===1 && count($colIncomes->movements($colProfile,100))===2,'Una mesada produce exactamente dos movimientos derivados');
verify(request($client,$base,$colRoute,$colConfirm)[0]===422,'Vista previa antigua no pisa una importación más reciente');
[$s,$colReload]=request($client,$base,$colRoute.'&comprobante='.$colDoc);$colReload=json_decode($colReload,true);
$colConfirm['versiones']=json_encode($colReload['versiones']);
verify(request($client,$base,$colRoute,$colConfirm)[0]===200 && count($colModel->rows($colProfile,100))===1,'Confirmar otra vez los mismos datos no duplica el mes');
$colConfirm['filas']=json_encode([['mes'=>'1','bruto'=>'4600000','salud'=>'552000']]);
verify(request($client,$base,$colRoute,$colConfirm)[0]===422,'Corregir exige marcar sustitución de la mesada importada');
$colConfirm['reemplazar']='1';verify(request($client,$base,$colRoute,$colConfirm)[0]===200,'Corrección sustituye valores conservando historia');
verify((int)$db->query('SELECT COUNT(*) FROM mesadas_pension WHERE perfil_id='.$colProfile)->fetchColumn()===2
    && count($colModel->rows($colProfile,100))===1 && count($colIncomes->movements($colProfile,100))===2,'Corrección conserva versión previa y no suma una segunda pensión');
[$s,$colVisible]=request($client,$base,$colPageRoute);
verify($s===200 && str_contains($colVisible,'$4.600.000') && str_contains($colVisible,'Corregir desde el comprobante'),'Mesas y casillas muestran lo importado y remiten a su fuente para corregir');
$colMovement=$colIncomes->movements($colProfile,100)[0];$colManual=App\Services\IngresoData::read($colMovement,App\Models\Ingreso::MOVEMENT_FIELDS);
$colDenied=false;try { $colIncomes->saveMovement($colManual,$colProfile,100,(int)$colMovement['id']); }catch (DomainException) { $colDenied=true; }
verify($colDenied,'No permite modificar aisladamente un movimiento derivado del comprobante');
[$s,$colPdfBody]=request($client,$base,'pensiones/documento&comprobante='.$colDoc);
verify($s===200 && str_starts_with($colPdfBody,'%PDF-'),'Comprobante se consulta como PDF privado dentro del aplicativo');
file_put_contents($colPath,colpensionesFixture('99990000'));
verify(rutRequest($client,$base,$colRoute,$colPost)[0]===422,'Titular diferente se rechaza sin crear ingresos');
file_put_contents($colPath,colpensionesFixture('55667788','2025-01 a 2025-12'));
[$s,$colAnnual]=rutRequest($client,$base,$colRoute,$colPost);$colAnnual=json_decode($colAnnual,true);
verify($s===200 && $colAnnual['lectura']['anual_sin_desglose'],'Certificado anual queda conservado sin repartirlo en doce');
verify(request($client,$base,$colRoute,array_replace($colConfirm,['comprobante'=>(string)$colAnnual['id']]))[0]===422,'POST no fuerza la importación de un total anual sin detalle');
$colScope=curl_init();curl_setopt($colScope,CURLOPT_COOKIEFILE,'');
try {
    [, $colLogin]=request($colScope,$base,'login');request($colScope,$base,'login',['_csrf_token'=>token($colLogin),'username'=>'hash-user','password'=>'Otra-clave-segura']);
    verify(request($colScope,$base,$colRoute.'&comprobante='.$colDoc)[0]===404 && request($colScope,$base,'pensiones/documento&comprobante='.$colDoc)[0]===404,'Un tercero no consulta comprobantes ni PDF ajenos');
} finally { curl_close($colScope); }
