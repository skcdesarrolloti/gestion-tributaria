<?php
declare(strict_types=1);
// Un Socio con cambio pendiente puede recuperar otra cuenta sin modificar su clave.
$recovering=curl_init();curl_setopt($recovering,CURLOPT_COOKIEFILE,'');
$q=$db->prepare('SELECT clave_hash FROM usuarios_tributarios WHERE id=?');$q->execute([$otherOwner]);$unchangedHash=$q->fetchColumn();
$db->prepare('UPDATE usuarios_tributarios SET cambiar_clave=1 WHERE id=?')->execute([$otherOwner]);
try {
    [, $recoverLogin]=request($recovering,$base,'login');
    verify(request($recovering,$base,'login',['_csrf_token'=>token($recoverLogin),'username'=>'otro','password'=>'Otra-clave-segura-2026'])[0]===303,'Socio inicia sesión con clave inicial pendiente');
    [$s,$recoverHtml]=request($recovering,$base,'accesos&funcionario='.$createdId);
    verify($s===200 && str_contains($recoverHtml,'Restablecer contraseña'),'Socio abre Usuarios y accesos sin cambiar su clave');
    verify(request($recovering,$base,'usuarios/crear')[0]===200,'Las opciones del módulo de usuarios funcionan para el Socio pendiente');
    verify(str_contains(request($recovering,$base,'contribuyentes')[2],'route=perfil'),'La excepción no desbloquea el resto de módulos');
    $recoverPost=['_csrf_token'=>token($recoverHtml),'usuario_id'=>(string)$createdId,'current_password'=>'Otra-clave-segura-2026','password'=>'Recuperada-ficticia-2026','confirmation'=>'Recuperada-ficticia-2026'];
    verify(request($recovering,$base,'usuarios/restablecer',array_replace($recoverPost,['_csrf_token'=>'']))[0]===419,'Recuperar desde Socio pendiente sigue exigiendo CSRF');
    verify(request($recovering,$base,'usuarios/restablecer',array_replace($recoverPost,['current_password'=>'incorrecta']))[0]===422,'Recuperar sigue exigiendo la contraseña actual del Socio');
    verify(request($recovering,$base,'usuarios/restablecer',$recoverPost)[0]===303,'Socio pendiente restablece otra cuenta');
    $q=$db->prepare('SELECT clave_hash,cambiar_clave FROM usuarios_tributarios WHERE id=?');$q->execute([$otherOwner]);$afterRecovery=$q->fetch();
    verify($afterRecovery['clave_hash']===$unchangedHash && (int)$afterRecovery['cambiar_clave']===1,'Restablecer otra cuenta no cambia la clave ni el estado del Socio');
    $q=$db->prepare('SELECT clave_hash FROM usuarios_tributarios WHERE id=?');$q->execute([$createdId]);
    verify(password_verify('Recuperada-ficticia-2026',$q->fetchColumn()),'La nueva clave se guarda solo en la cuenta seleccionada');
} finally {
    $db->prepare('UPDATE usuarios_tributarios SET cambiar_clave=0 WHERE id=?')->execute([$otherOwner]);curl_close($recovering);
}
