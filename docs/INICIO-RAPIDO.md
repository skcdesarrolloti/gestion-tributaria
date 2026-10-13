# Ejecutar una copia local

El sitio ya funciona en Hostinger. Esta guía solo sirve para desarrollar en el computador.

1. Usa PHP 8.2+ con pdo_mysql y mbstring, y una instancia MySQL/MariaDB local.
2. Copia config/local.example.php como config/local.php. Configura database con una base local de trabajo y auth_database con una base local de funcionarios de prueba. Nunca uses las cuentas reales en pruebas automáticas.
3. Configura base_path = '' y secure_cookies = false únicamente en HTTP local.
4. Inicia el servidor desde la carpeta del proyecto:

```powershell
php -S 127.0.0.1:8082 -t public
```

Abre http://127.0.0.1:8082/login.php. Las tablas del proyecto se gestionan automáticamente. La base de funcionarios debe existir y contener las cuentas de prueba; la aplicación no crea ni copia funcionarios.

Para verificar el login sin preparar cuentas a mano, utiliza tests/integration.php: crea sus propias bases aisladas y sus cuentas sintéticas, prueba el flujo y elimina esas bases. Revisa docs/VALIDACION.md para ejecutarlo.

El login usa user_others_apss y pass_others_apss de la conexión auth_database. Consulta docs/ACCESO-FUNCIONARIOS.md para entender ambas conexiones. No existe primer administrador ni setup_key.

Verificaciones habituales:

```powershell
php bin/check.php
php tests/unit.php
```

Los errores internos quedan en storage/logs/app.log. No publiques ese archivo ni config/local.php en Git.
