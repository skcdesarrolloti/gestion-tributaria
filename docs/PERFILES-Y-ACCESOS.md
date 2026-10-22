# Socio y Usuario

Las cuentas son propias de Gestión Tributaria desde el 22/10/2026, por instrucción expresa del responsable. No se utiliza la base de funcionarios.

| Acción | Socio | Usuario |
| --- | --- | --- |
| Consultar y editar contribuyentes, RUT y documentos | Todos | Solo su ficha asignada |
| Trabajar con ingresos, proyecciones, escenarios y Exógena | Todos | Solo los correspondientes a su ficha |
| Crear contribuyentes y cuentas | Sí | No |
| Asignar perfiles, restablecer claves y suspender accesos | Sí | No |
| Cambiar su propia contraseña | Sí | Sí |

Abrir **Usuarios y accesos → Crear usuario** para dar acceso a una persona externa. No necesita ser empleado. Elegir Socio para acceso total, o Usuario y su contribuyente para acceso individual. Cada alta obliga a cambiar la contraseña inicial. El acceso se puede suspender sin borrar documentos. No se puede retirar al último Socio activo.

Para la primera entrada tras la separación, seguir [Cuentas independientes](CUENTAS-INDEPENDIENTES.md): activación mediante la sesión previa válida del Socio autorizado o un secreto privado. No existe registro público.

AuthService comprueba cuenta, acceso activo y versión de clave en cada solicitud. AccesoSql limita consultas y escrituras en los modelos: Socio global; Usuario únicamente la ficha asignada. Cambiar IDs o ser el creador histórico de otra ficha no amplía permisos. Los PDF permanecen privados y pasan por la misma autorización.

Las operaciones POST exigen CSRF. Las asignaciones usan revisión vigente, transacción, bloqueo y auditoría sin contraseñas. Reasignar o suspender se refleja en la siguiente página. Los cambios de contraseña revocan sesiones anteriores.

No deducir la ficha autorizada comparando nombres o mediante un RUT subido: el Socio la selecciona expresamente. Varias cuentas pueden apuntar a la misma ficha si se autoriza.

Verificar con php bin/check.php, php tests/unit.php y php tests/integration.php en una base aislada. Los recorridos comprueban cuenta externa, aislamiento, RUT, escenarios, Exógena, suspensión, último Socio y contraseñas. No utilizar credenciales reales en las pruebas.
