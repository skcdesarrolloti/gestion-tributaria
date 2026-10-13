# Acceso con los funcionarios existentes

Ambos proyectos usan las cuentas ya definidas en wp_jet_cct_funcionarios de la base central u350704768_M4FvI, igual que Control de Servicios Comerciales.

## Qué escribe el funcionario en el login

- Usuario: su valor de user_others_apss.
- Contraseña: su valor de pass_others_apss.
- La cuenta debe tener activo = 'Si'.

No hay formulario para crear administradores, clave de instalación ni registro de cuentas locales. Una pestaña antigua de instalación lleva al login. setup_key, si aún existe en el archivo privado, se ignora y se puede retirar.

## Dos conexiones diferentes

- database: base de datos del proyecto, que ya está configurada. Allí se guardan sus procesos y se aplican las migraciones.
- auth_database: base central de funcionarios. Se utiliza exclusivamente para SELECT; no se ejecutan migraciones, ni se crean bases, ni se cambian contraseñas o usuarios.

Conserva el bloque database de config/local.php. Añade, al mismo nivel, este bloque con las credenciales MySQL que permiten consultar la base central:

```php
'auth_database' => [
    'host' => 'localhost',
    'port' => 3306,
    'name' => 'u350704768_M4FvI',
    'username' => 'USUARIO_MYSQL_BASE_CENTRAL',
    'password' => 'CLAVE_MYSQL_BASE_CENTRAL',
],
'auth_allow_legacy_passwords' => true,
```

El usuario y la clave MySQL de la base central son distintos del usuario y contraseña personales del funcionario. Usa el host real de esa conexión. No reemplaces los datos de la base inmobiliaria o tributaria. No guardes config/local.php ni contraseñas en Git.

La conexión de funcionarios no toma por defecto las credenciales de database, porque corresponden a otra base. Si falta auth_database, la pantalla de login puede abrir, pero no será posible autenticar hasta completar su conexión. No copies funcionarios entre bases para resolverlo.

## Compatibilidad y sesiones

El sistema comercial tiene habilitadas sus contraseñas heredadas. Estos proyectos conservan esa compatibilidad con auth_allow_legacy_passwords = true: las claves existentes se comparan mediante hash_equals y los hashes reconocidos se validan mediante password_verify. Nunca se escribe o transforma pass_others_apss. Cuando el sistema central migre todas sus claves a hashes, se podrá desactivar la compatibilidad en coordinación con ese sistema.

Las cookies son independientes por proyecto. No es inicio de sesión único: cada portal solicita las mismas credenciales y mantiene su propia sesión. Se comprueba activo en las páginas protegidas; una sesión antigua de una cuenta local ya no autoriza un funcionario central con el mismo ID.

La tabla local de funcionarios creada por la migración 001 se conserva por historial y para no borrar datos. El login ya no la consulta. No modificar 001 ni vaciar tablas para cambiar la autenticación.

Si falla la entrada, revisar primero el log privado y la conexión auth_database; después confirmar que la cuenta central está activa. No exponer hashes ni claves en el log o en una pantalla de diagnóstico.
