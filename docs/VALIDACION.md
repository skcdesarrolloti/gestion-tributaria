# Validación de la base MVC

Verificación local realizada el 13 de octubre de 2026 con PHP 8.2.12, MariaDB 10.4.32 y Apache de XAMPP, usando bases temporales aisladas. Las pruebas funcionales no usaron cuentas reales. La estructura central se consultó en modo de solo lectura para confirmar los campos del login; no se extrajeron contraseñas.

## Resultado

- Sintaxis PHP y límite de 250 líneas: correctos.
- Pruebas unitarias de CSRF, escape HTML y límite de intentos: correctas.
- 30 comprobaciones de integración: login directo sin instalador, dos bases aisladas, credenciales institucionales heredadas y con hash, rechazo de cuentas locales, sesión, perfil, logout, cuenta inactiva y migraciones de columnas.
- Comprobado que la autenticación no crea la base central, no ejecuta migraciones allí y no modifica contraseñas; las sesiones locales antiguas quedan invalidadas.
- Migraciones: repetición sin duplicados, recuperación tras DDL confirmado y detección de cambios en scripts ya aplicados.
- Apache en subcarpeta: login y perfil correctos; raíz redirige a public; configuración, SQL, logs y .git devuelven 403.
- Revisión en navegador: login, inicio y perfil; panel a 360px sin desbordamiento horizontal.
- CSS compilado con el tema original y Tailwind 3.4.17.

## Repetir integración

La prueba requiere MySQL/MariaDB local, extensiones pdo_mysql y curl, y permiso para crear bases temporales. Se ejecuta sin config/local.php: guarda ese archivo privado en otro lugar antes y restáuralo al terminar.

```powershell
$env:TEST_DB_HOST = '127.0.0.1'
$env:TEST_DB_PORT = '3306'
$env:TEST_DB_USER = 'root'
php tests/integration.php
```

Si el usuario local tiene contraseña, configura TEST_DB_PASSWORD en el entorno de la terminal. No guardes credenciales reales en este documento. La prueba genera una base mvc_test_ aleatoria y otra con sufijo _auth para los funcionarios sintéticos; elimina exclusivamente esas dos bases al terminar. No apuntes estas pruebas a producción.

GitHub Actions ejecutará lint, pruebas unitarias e integración con un servicio MySQL 8.0 temporal. El resultado remoto debe consultarse en la pestaña Actions; la verificación local no confirma por sí sola esa ejecución.

## Alcance y dependencia de compilación

El usuario confirmó que ambos proyectos ya funcionan en Hostinger. El nuevo acceso institucional requiere completar auth_database en el archivo privado; el login con una cuenta real todavía no se ha verificado. No se incluyen módulos de negocio todavía.

`npm audit` reportó 7 avisos en dependencias de desarrollo de Tailwind 3.4.17 (2 moderados y 5 altos). Se conserva la versión del tema de referencia; no se hizo una migración mayor de estilos. Estas dependencias se usan solo para compilar CSS localmente y no se suben al hosting. Revisar los avisos antes de ampliar esa cadena de compilación; no procesar fuentes de terceros no confiables con ella.
