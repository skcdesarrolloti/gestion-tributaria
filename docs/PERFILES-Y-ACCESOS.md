# Socio y Usuario

Implementado por instrucción del responsable el 22/10/2026. Dos perfiles propios
de Gestión Tributaria, distintos del rol institucional del funcionario.

| Acción | Socio | Usuario |
| --- | --- | --- |
| Consultar contribuyentes, RUT y documentos | Todos | Solo su ficha asignada |
| Consultar ingresos, patrimonio, escenarios y Exógena | Todos | Solo los correspondientes a su ficha |
| Completar datos / importar RUT / guardar movimientos | Todos los contribuyentes | Solo su ficha asignada |
| Crear contribuyentes | Sí | No |
| Asignar perfiles, fichas y suspender accesos | Sí | No |
| Crear cuentas institucionales o cambiar contraseñas | No | No |

El Socio habilitado tiene acceso total de consulta y edición, incluidos RUT,
ingresos, escenarios y Exógena. No necesita una ficha asignada. Este alcance
también aplica a los Socios ya guardados al abrir la siguiente página.

## Cómo habilitar a una persona

1. Entrar con la cuenta institucional del Socio inicial expresamente autorizado.
2. Abrir **Usuarios y accesos** en el menú superior.
3. Seleccionar la cuenta institucional activa y pulsar **Abrir asignación**.
4. Seleccionar **Usuario**, escoger **Su contribuyente**, dejar **Habilitado** y guardar.
5. Esa persona entra con sus credenciales institucionales y abre **Contribuyentes**.
   Verá solo la ficha asignada y podrá subir su RUT o completar los vacíos manualmente.

Para otro Socio, seleccionar **Socio**, dejar **Habilitado** y pulsar **Guardar acceso**.
El selector de contribuyente solo aparece para Usuario. El rol Socio permite
administrar accesos y por tanto debe concederse únicamente a quien se autorice.
No se puede suspender o degradar al último Socio activo.

Si la cuenta no aparece, debe existir y estar activa en el sistema central de
funcionarios. Este módulo no escribe en esa base ni crea usuarios externos. Las
asignaciones no actualizan datos ante la DIAN; se completa el expediente interno.

## Activación y continuidad

Las migraciones 018–020 crean permisos, autorización inicial y auditoría en la
base propia. No modifican cuentas, documentos o propietarios de fichas existentes.
La autorización inicial guarda la huella del login aprobado, sin contraseña ni
ID central supuesto. Al autenticarse esa cuenta, se vincula a su ID real una sola
vez. No se concede acceso por ser el primer visitante, por poseer una ficha, ni
por tener un rol central llamado Gerencia o administrador.

Una cuenta sin asignación activa puede iniciar sesión y consultar su cuenta y
el material de consulta, pero no obtiene expedientes tributarios ni puede crear
otros. Los demás funcionarios existentes deben recibir una asignación expresa.
No existe recuperación pública de Socio ni autoconcesión de permisos.

## Protección

- `AuthService` comprueba cuenta central activa y lee su permiso local en cada
  solicitud. No conserva el permiso como autoridad en la sesión.
- `AccesoSql` inserta condiciones explícitas preparadas en cada consulta de los
  modelos: el ID es siempre el actor autenticado, no un propietario enviado por el navegador.
- Lectura y escritura conceden alcance global al Socio activo y solo la ficha
  asignada al Usuario. Los modelos condicionan también las escrituras.
- Usuarios sin autorización reciben 404 al intentar abrir o descargar otro expediente.
  El PDF permanece en almacenamiento privado y pasa por la misma autorización.
- Reasignar o suspender se refleja al abrir la siguiente página, sin requerir logout.
- Las asignaciones usan CSRF, revisión vigente, transacción, bloqueo de permisos
  y comprobación del último Socio. La auditoría registra actor, cuenta y cambio;
  no almacena contraseñas. El directorio central solo devuelve ID, nombre y login.

No deducir el contribuyente autorizado comparando nombres o un RUT subido por el
interesado. El Socio debe escoger expresamente la ficha correcta. Varias cuentas
pueden apuntar a una ficha si se autoriza; cada Usuario tiene como máximo una ficha.

## Verificación

Ejecutar `php bin/check.php`, `php tests/unit.php` e integración aislada según
`VALIDACION.md`. `accesos-integration.php` prueba asignación, edición propia,
IDs alterados, descargas, ingresos, escenarios, Exógena, reasignación, suspensión,
escalamiento de permisos, concurrencia, último Socio y auditoría. Las pruebas
históricas comprueban CSRF en la edición global. `socio-total-integration.php`
prueba edición, importación RUT, escenarios y Exógena de otros titulares sin
asignación, y mantiene el rechazo de acceso ajeno para Usuario.
Completar la revisión visual del selector y comprobar la activación en la sesión
real del responsable después del despliegue. No probar con credenciales de producción.
