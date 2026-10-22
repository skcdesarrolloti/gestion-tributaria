# Acceso independiente de funcionarios

La integración con la base de funcionarios fue retirada por instrucción expresa del responsable el 22/10/2026. Esta guía histórica queda sustituida por [Cuentas independientes](CUENTAS-INDEPENDIENTES.md).

No añadir auth_database, no copiar funcionarios ni contraseñas, y no conectar a la base central. La presentación del login conserva el tema comercial; su autenticación utiliza usuarios_tributarios de la base propia.

Los campos antiguos de configuración se pueden conservar durante el despliegue: no se abre ninguna conexión con ellos. Solo su identificación permite reconocer una sesión previa válida del Socio durante la activación inicial. Después pueden retirarse del archivo privado sin afectar el acceso propio.
