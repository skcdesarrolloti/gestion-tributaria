<?php
declare(strict_types=1);
// Cuentas independientes, sin credenciales ni conexión central disponibles.
[$s,$createHtml]=request($client,$base,'usuarios/crear');
verify($s===200 && str_contains($createHtml,'Crear usuario'),'Socio crea cuentas de terceros');
$newAccount=['_csrf_token'=>token($createHtml),'nombre'=>'Tercero ficticio','username'=>'tercero-prueba','correo'=>'prueba@example.test','perfil'=>'usuario','contribuyente_id'=>(string)$taxpayerId,'password'=>'Inicial-externa-2026','confirmation'=>'Inicial-externa-2026'];
verify(request($client,$base,'usuarios/crear',array_replace($newAccount,['_csrf_token'=>'']))[0]===419,'Alta de cuentas exige CSRF');
verify(request($client,$base,'usuarios/crear',array_replace($newAccount,['contribuyente_id'=>'']))[0]===422,'No crea Usuario sin una ficha asignada');
verify(request($client,$base,'usuarios/crear',$newAccount)[0]===303,'Crea Usuario externo vinculado a contribuyente existente');
$q=$db->prepare('SELECT * FROM usuarios_tributarios WHERE usuario=?');$q->execute(['tercero-prueba']);$createdAccount=$q->fetch();$createdId=(int)$createdAccount['id'];
verify(password_verify($newAccount['password'],$createdAccount['clave_hash']) && $createdAccount['clave_hash']!==$newAccount['password'],'Guarda únicamente el hash de contraseña');
verify($createdId>102,'No asigna identificadores históricos a cuentas nuevas');
verify(request($client,$base,'usuarios/crear',$newAccount)[0]===422,'Rechaza nombre de usuario duplicado');
$guest=curl_init();curl_setopt($guest,CURLOPT_COOKIEFILE,'');$older=curl_init();curl_setopt($older,CURLOPT_COOKIEFILE,'');
try {
    foreach ([$guest,$older] as $browser) {
        [, $guestLogin]=request($browser,$base,'login');
        verify(request($browser,$base,'login',['_csrf_token'=>token($guestLogin),'username'=>'tercero-prueba','password'=>$newAccount['password']])[0]===303,'Cuenta externa inicia sesión con su clave propia');
    }
    [$s,,$forced]=request($guest,$base,'contribuyentes');
    verify($s===303 && str_contains($forced,'route=perfil'),'Exige sustituir contraseña inicial antes de acceder a datos');
    verify(request($guest,$base,'usuarios/crear',$newAccount)[0]===303,'No elude cambio obligatorio mediante POST directo');
    [, $guestProfile]=request($guest,$base,'perfil');
    $change=['_csrf_token'=>token($guestProfile),'current_password'=>$newAccount['password'],'password'=>'Personal-externa-2026','confirmation'=>'Personal-externa-2026'];
    verify(request($guest,$base,'perfil',array_replace($change,['_csrf_token'=>'']))[0]===419,'Cambio de contraseña exige CSRF');
    verify(request($guest,$base,'perfil',array_replace($change,['current_password'=>'equivocada']))[0]===422,'Cambio de contraseña exige clave actual correcta');
    verify(request($guest,$base,'perfil',array_replace($change,['confirmation'=>'Otra-no-coincide']))[0]===422,'Comprueba confirmación de contraseña');
    verify(request($guest,$base,'perfil',$change)[0]===303,'Usuario establece contraseña personal');
    verify(request($older,$base,'perfil')[0]===303,'Cambio invalida otras sesiones anteriores');
    [$s,$guestList]=request($guest,$base,'contribuyentes');
    verify($s===200 && str_contains($guestList,'Nombre actualizado') && !str_contains($guestList,'Registro privado ajeno'),'La cuenta externa ve solo la documentación asignada');
    verify(request($guest,$base,'contribuyentes/perfil&id='.$otherId)[0]===404 && request($guest,$base,'usuarios/crear')[0]===403,'Tercero no ve otras fichas ni crea cuentas');
    [, $guestProfile]=request($guest,$base,'perfil');
    verify(request($guest,$base,'usuarios/restablecer',['_csrf_token'=>token($guestProfile),'usuario_id'=>'100'])[0]===403,'Usuario no puede restablecer la contraseña del Socio');
    [, $resetPage]=request($client,$base,'accesos&funcionario='.$createdId);
    $reset=['_csrf_token'=>token($resetPage),'usuario_id'=>(string)$createdId,'current_password'=>'Prueba-segura-2026','password'=>'Temporal-nueva-2026','confirmation'=>'Temporal-nueva-2026'];
    verify(request($client,$base,'usuarios/restablecer',array_replace($reset,['current_password'=>'errada']))[0]===422,'Restablecer exige la contraseña actual del Socio');
    verify(request($client,$base,'usuarios/restablecer',$reset)[0]===303,'Socio restablece clave temporal de tercero');
    verify(request($guest,$base,'perfil')[0]===303,'Restablecimiento revoca la sesión anterior del tercero');
    [, $guestLogin]=request($guest,$base,'login');
    verify(request($guest,$base,'login',['_csrf_token'=>token($guestLogin),'username'=>'tercero-prueba','password'=>'Personal-externa-2026'])[0]===401,'La contraseña sustituida deja de servir');
    verify(request($guest,$base,'login',['_csrf_token'=>token($guestLogin),'username'=>'tercero-prueba','password'=>'Temporal-nueva-2026'])[0]===303,'Admite nueva contraseña temporal');
    verify(str_contains(request($guest,$base,'contribuyentes')[2],'route=perfil'),'Restablecimiento exige de nuevo contraseña personal');
    verify(!str_contains(json_encode($db->query('SELECT * FROM historial_accesos')->fetchAll()),'Temporal-nueva-2026'),'La auditoría no contiene contraseñas');
} finally { curl_close($guest);curl_close($older); }
