# Cuentas de Gestión Tributaria

Por instrucción del responsable el 22/10/2026, la autenticación es independiente.
El aplicativo no conecta con la base de funcionarios. Las cuentas nuevas viven
en `usuarios_tributarios` de la base tributaria; migración y comprobación 021.
No se copian cuentas, claves ni hashes del sistema de empleados.

## Primera entrada después del cambio

1. Abrir el sitio en el navegador donde estaba iniciada la sesión del Socio.
2. En **Activar acceso independiente**, escribir el usuario del Socio ya autorizado,
   nombre y una contraseña nueva con su confirmación. El correo es opcional.
3. Si la sesión previa sigue vigente y corresponde a ese Socio, dejar la clave de
   activación vacía. Se conserva su identificador y sus permisos existentes.
4. Si venció, usar el código privado entregado con esta actualización. En el código
   del proyecto se conserva solo su huella SHA-256, nunca su valor. Como recuperación,
   el responsable del sitio puede consultar mediante el administrador de archivos
   el archivo **privado** `storage/activacion-inicial.json` y copiar su valor `clave`.
   No hay que editar tablas o configuración.
5. Activar e iniciar sesión con la nueva contraseña. Después abrir **Usuarios y accesos**.

La activación exige CSRF y prueba privada. Saber un nombre de usuario o ser el
primer visitante no concede acceso. Solo puede activarse un login que ya estuviera
autorizado como Socio. Una vez creada la primera cuenta, la activación se cierra y
se elimina su archivo secreto. No compartir ese archivo ni incorporarlo a Git.
No usar la contraseña de empleados como contraseña inicial de esta aplicación.

## Crear a un socio o tercero

**Usuarios y accesos → Crear usuario**. Indicar nombre, usuario, correo opcional,
perfil y contraseña inicial con confirmación. Los Usuarios requieren seleccionar
su contribuyente; los Socios no requieren asignación. Si falta la ficha, crearla
primero en Contribuyentes y después vincular el acceso, conservando el RUT existente.

- **Socio:** consulta y edita todos los expedientes y módulos; administra cuentas.
- **Usuario:** consulta y completa exclusivamente su ficha, documentos, ingresos,
  proyecciones y escenarios. No administra cuentas ni consulta otros titulares.

Comunicar el usuario y contraseña inicial por un medio privado; no se envían correos.
El primer ingreso obliga a sustituir esa contraseña antes de abrir datos o enviar
operaciones. No existe inscripción pública. Los accesos anteriores no crean
automáticamente cuentas: el Socio los vuelve a crear expresamente en esta pantalla.

## Contraseñas y suspensión

Cada persona abre **Mi perfil → Cambiar mi contraseña**, ingresa la clave actual,
la nueva y su confirmación. Mínimo 12 caracteres, máximo 72 bytes; solo hashes con
`password_hash`/`password_verify`. No se aceptan contraseñas heredadas en texto plano.
Cambiar una clave invalida las demás sesiones, conservando la sesión que la cambia.

Si la persona olvidó su clave: el Socio abre su asignación, despliega **Restablecer
contraseña**, confirma su propia clave y establece una temporal para el interesado.
Se invalidan todas las sesiones del destinatario y se exige cambiarla al ingresar.
No se recuperan ni muestran contraseñas anteriores. Suspender acceso también
invalida la sesión; no se puede suspender ni degradar al último Socio activo.

## Continuidad y comprobación

Los nombres internos históricos `funcionario_id`, `_ID` y `user_others_apss` se
mantienen como claves/alias de compatibilidad con los expedientes. No implican
una consulta a funcionarios. Las cuentas nuevas no reutilizan identificadores
ocupados por titulares históricos; una cuenta recreada con un login autorizado
conserva su ID y recibe los permisos expresamente seleccionados por el Socio.
Los datos, documentos y propietarios no se borran ni se reasignan masivamente.

`bootstrap/app.php` utiliza una sola conexión: `database`. Los campos antiguos de
`auth_database` presentes en la configuración privada solo sirven para reconocer
el identificador de una sesión anterior durante la activación; no se usan para
conectar ni autenticar. Después de activar pueden retirarse de la configuración
privada. El despliegue no reemplaza ese archivo.

Comprobar `php bin/check.php`, `php tests/unit.php`, `php tests/integration.php`.
La integración usa datos ficticios, prueba activación privada de una sola vez,
altas, duplicados, CSRF, aislamiento de Usuario, cambios y restablecimientos,
revocación de sesiones, suspensión y funcionamiento sin la base central.
No afirmar que se crearon cuentas reales sin comprobarlo en una sesión autorizada.
