<?php
declare(strict_types=1);
$restricted=curl_init();curl_setopt($restricted,CURLOPT_COOKIEFILE,'');
try {
    [, $loginHtml]=request($restricted,$base,'login');
    verify(request($restricted,$base,'login',['_csrf_token'=>token($loginHtml),'username'=>'otro','password'=>'Otra-clave-segura-2026'])[0]===303,'Cuenta institucional existente entra sin crear una cuenta local');
    verify(!str_contains(request($restricted,$base,'contribuyentes')[1],'Registro privado ajeno'),'Sin asignación no obtiene acceso ni siquiera por haber creado registros antes');
    verify(request($restricted,$base,'accesos')[0]===403 && request($restricted,$base,'contribuyentes/formulario')[0]===403,'Usuario no administra permisos ni registra nuevos contribuyentes');
    $grantRoute='accesos&funcionario='.$otherOwner;
    [$s,$grantHtml]=request($client,$base,$grantRoute);
    verify($s===200 && str_contains($grantHtml,'Cuenta institucional activa') && !str_contains($grantHtml,'pass_others_apss'),'Socio abre asignaciones sin exponer claves centrales');
    $grantPost=['_csrf_token'=>token($grantHtml),'revision'=>'0','perfil'=>'usuario','contribuyente_id'=>(string)$taxpayerId,'activo'=>'1'];
    verify(request($client,$base,$grantRoute,array_replace($grantPost,['_csrf_token'=>'']))[0]===419,'Asignar acceso exige CSRF');
    verify(request($client,$base,$grantRoute,array_replace($grantPost,['contribuyente_id'=>'']))[0]===422,'Usuario activo exige una ficha explícita');
    [$s,,$location]=request($client,$base,$grantRoute,$grantPost);
    verify($s===303 && str_contains($location,'route=accesos&funcionario='),'Guarda rol y vinculación sin cambiar cuentas centrales');
    $visible=request($restricted,$base,'contribuyentes')[1];
    verify(str_contains($visible,'Nombre actualizado') && !str_contains($visible,'Registro privado ajeno') && !str_contains($visible,'Registrar contribuyente'),'Usuario ve solo su ficha asignada y no sus antiguos clientes');
    $ownRoute='contribuyentes/perfil&id='.$taxpayerId.'&anio=2026&tab=ubicacion';
    [$s,$ownHtml]=request($restricted,$base,$ownRoute);
    verify($s===200 && str_contains($ownHtml,'Guardar información'),'Usuario puede completar manualmente su ficha');
    $q=$db->prepare('SELECT revision FROM caracterizaciones_tributarias WHERE contribuyente_id=? AND anio=2026 AND bloque="ubicacion"');$q->execute([$taxpayerId]);$rev=$q->fetchColumn()?:0;
    $ownPost=['_csrf_token'=>token($ownHtml),'revision'=>(string)$rev,'estado'=>'borrador','campos'=>['direccion'=>'Dirección ficticia del titular']];
    verify(request($restricted,$base,$ownRoute,$ownPost)[0]===303,'Usuario guarda campos faltantes de su propio RUT');
    verify(request($restricted,$base,'contribuyentes/perfil&id='.$otherId)[0]===404 && request($restricted,$base,'contribuyentes/perfil&id='.$otherId,$ownPost)[0]===404,'Cambiar un ID no permite consultar ni modificar otro contribuyente');
    verify(request($restricted,$base,'ingresos/detalle&id='.$otherProfileId)[0]===404 && request($restricted,$base,'simulador&id='.$otherProfileId)[0]===404,'Usuario no accede a ingresos ni escenarios de otro titular');
    [$s,$ownSheet]=request($restricted,$base,'simulador&id='.$profileId);
    verify($s===200 && request($restricted,$base,'simulador&id='.$profileId,array_replace($simPost,['_csrf_token'=>token($ownSheet)]))[0]===303,'Usuario guarda un escenario exclusivamente de su contribuyente asignado');
    $ownPdf=tempnam(sys_get_temp_dir(),'rut-usuario-');file_put_contents($ownPdf,rutFixturePdf());
    try {
        $ownUpload=['_csrf_token'=>token($ownHtml),'accion'=>'leer','aplicar_vacios'=>'1','rut_pdf'=>new CURLFile($ownPdf,'application/pdf','rut-propio.pdf')];
        [$s,,$ownLocation]=rutRequest($restricted,$base,'contribuyentes/rut&id='.$taxpayerId.'&anio=2026',$ownUpload);
        verify($s===303 && str_contains($ownLocation,'tab=identificacion'),'Usuario importa un RUT coincidente en la ficha asignada sin cambiar su propietario');
    } finally { unlink($ownPdf); }
    verify(request($restricted,$base,'contribuyentes/rut/documento&documento='.$doc['id'])[0]===200,'Usuario descarga el RUT de su ficha asignada');
    $clone=$db->prepare('INSERT INTO rut_documentos (contribuyente_id,anio,titulo,archivo,sha256,bytes,metadatos) SELECT ?,anio,titulo,archivo,sha256,bytes,metadatos FROM rut_documentos WHERE id=?');$clone->execute([$otherId,$doc['id']]);$foreignDocument=(int)$db->lastInsertId();
    verify(request($restricted,$base,'contribuyentes/rut/documento&documento='.$foreignDocument)[0]===404,'Usuario no descarga un PDF ajeno aunque conozca su ID');
    verify(request($client,$base,'contribuyentes/rut/documento&documento='.$foreignDocument)[0]===200,'Socio puede consultar documentos de todos los titulares');
    verify(request($restricted,$base,'accesos&funcionario=100',$grantPost)[0]===403,'Usuario no puede elevarse a Socio con un POST directo');
    $grantPost['revision']='1';$grantPost['contribuyente_id']=(string)$otherCompanyId;
    verify(request($client,$base,$grantRoute,$grantPost)[0]===303,'Socio puede corregir la ficha autorizada para una cuenta');
    verify(request($restricted,$base,$ownRoute)[0]===404,'Una reasignación revoca inmediatamente la ficha anterior en la misma sesión');
    [$s,$exHtml]=request($restricted,$base,'exogena/5247/registro&id='.$foreignExogenaId);
    verify($s===200,'Usuario asignado a una entidad abre solo los registros de esa entidad');
    $authorizedPayment=array_replace($exogenaRecord,['_csrf_token'=>token($exHtml)]);
    verify(request($restricted,$base,'exogena/5247/registro&id='.$foreignExogenaId,$authorizedPayment)[0]===303,'El permiso de edición asignado funciona también en Exógena');
    verify(request($restricted,$base,'exogena/5247/registro&id='.$exogenaId)[0]===404,'No expone registros de otra empresa');
    $grantPost['revision']='2';$grantPost['activo']='0';
    verify(request($client,$base,$grantRoute,$grantPost)[0]===303 && request($restricted,$base,'exogena/5247/registro&id='.$foreignExogenaId)[0]===404,'Suspender acceso bloquea documentos en una sesión ya abierta');
    verify(request($client,$base,$grantRoute,$grantPost)[0]===409,'Una asignación antigua no sobrescribe cambios recientes');
    [$s,$selfHtml]=request($client,$base,'accesos&funcionario=100');
    $selfPost=['_csrf_token'=>token($selfHtml),'revision'=>'1','perfil'=>'usuario','contribuyente_id'=>(string)$taxpayerId,'activo'=>'1'];
    verify(request($client,$base,'accesos&funcionario=100',$selfPost)[0]===422,'No permite eliminar al último Socio activo');
    verify((int)$db->query('SELECT COUNT(*) FROM historial_accesos')->fetchColumn()===3,'Audita concesión, reasignación y suspensión sin guardar contraseñas');
    // La autorización inicial solo puede vincularse al login expresamente aprobado.
    $bootDb=new App\Core\Database($dbConfig);$bootAccess=new App\Models\AccesoTributario($bootDb);
    $db->exec(file_get_contents($root.'/database/migrations/019_socio_inicial_autorizado.sql'));
    $db->prepare('UPDATE accesos_tributarios SET login_hash=? WHERE funcionario_id=0')->execute([hash('sha256','socio-fixture')]);
    $bootAccess->forUser(['_ID'=>$otherOwner,'user_others_apss'=>'otro']);
    verify((int)$db->query('SELECT COUNT(*) FROM accesos_tributarios WHERE funcionario_id=0')->fetchColumn()===1,'Un visitante distinto no reclama la autorización inicial');
    $insertUser->execute(['Socio inicial ficticio','socio-fixture','Clave-sintetica-no-real']);$bootId=(int)$authDb->lastInsertId();
    $bootRole=$bootAccess->forUser(['_ID'=>$bootId,'user_others_apss'=>'socio-fixture']);
    verify($bootRole['perfil']==='socio' && (int)$bootRole['funcionario_id']===$bootId,'La autorización inicial se vincula al ID real de la cuenta aprobada');
    $db->prepare('DELETE FROM accesos_tributarios WHERE funcionario_id=?')->execute([$bootId]);
} finally { curl_close($restricted); }
