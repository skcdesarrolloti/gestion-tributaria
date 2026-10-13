# Desplegar Gestión Tributaria en Hostinger

## 1. Conexión inicial

La base asignada ya fue creada por el propietario. No necesitas volver a crearla: completa sus credenciales y abre la aplicación.

Usa PHP 8.2+ con pdo_mysql y mbstring, y HTTPS. Configura la base **u350704768_tributaria** con su usuario MySQL y contraseña. Si ya existe, no crees otra. El usuario MySQL puede tener un nombre distinto de la base.

Si la base no existe, la aplicación intenta crearla con el script 000_create_database.sql. Hostinger puede restringir CREATE DATABASE: en ese caso la única preparación externa es crear la base vacía y su usuario desde hPanel. **No se entra a phpMyAdmin ni se importan scripts:** la app crea tablas y aplica columnas nuevas por sí misma. [Referencia oficial sobre bases en hPanel](https://www.hostinger.com/support/1864324-how-to-upload-and-set-up-your-database-at-hostinger/).

El usuario MySQL necesita SELECT, INSERT, UPDATE, DELETE y permisos de estructura (CREATE, ALTER e INDEX) para futuras migraciones. El motor usa GET_LOCK para coordinar actualizaciones; si el servidor lo impide, el despliegue se detiene y queda registrado en el log.

## 2. Subir archivos

1. Crea public_html/gestion-tributaria/.
2. Sube app, bootstrap, config (sin local.php local), routes, public, database y storage; incluye el .htaccess de la raíz y los .htaccess interiores.
3. Conserva database/migrations, database/checks, database/schema_migrations.sql y database/000_create_database.sql: la aplicación los necesita.
4. No subas .git, node_modules, tests, logs locales, copias de seguridad ni database/private.
5. Conserva las carpetas vacías storage/logs y storage/rate-limits y habilita escritura para PHP. No uses permisos 777 indiscriminadamente.

La URL será https://TU-DOMINIO/gestion-tributaria/public/. El .htaccess de raíz redirige a public/ y bloquea las demás carpetas; Apache/LiteSpeed debe respetarlo. Si puedes elegir la raíz documental de un sitio/subdominio, apunta directamente a public y configura base_path = ''. Así los archivos privados quedan fuera de la raíz web.

## 3. Configurar producción

Copia config/local.example.php a config/local.php en el servidor. Completa las credenciales reales, base_path = '/gestion-tributaria/public' y secure_cookies = true. Genera una clave localmente con php bin/setup-key.php y pégala en setup_key. Es un secreto de instalación de al menos 32 caracteres. No se incluye en Git.

## 4. Abrir la app

Entra por HTTPS. El arranque aplica automáticamente los scripts pendientes y muestra el formulario del primer administrador. Usa tu clave de instalación, nombre, usuario y contraseña. Luego inicia sesión. Borra setup_key del archivo privado cuando termines. No hay usuarios por defecto.

## 5. Comprobar

- Inicio sin sesión redirige al login; el perfil funciona al entrar.
- El cierre de sesión devuelve al login.
- El instalador ya no permite crear otro administrador.
- Acceder por URL a config/local.php, database/migrations/001_funcionarios.sql, storage/logs/app.log y .git/config bajo la carpeta del proyecto devuelve 403 o 404.
- Si falla una actualización, revisa storage/logs/app.log; la app no continúa con un esquema parcialmente confirmado.

## Actualizar

1. Crea un respaldo de archivos y base con las herramientas de respaldo de hPanel.
2. Activa una ventana de mantenimiento para cambios de esquema de producción.
3. Sube el código y los nuevos pares de scripts migrations/checks; no reemplaces config/local.php ni borres storage.
4. Abre la app: detecta y ejecuta los scripts pendientes. Los ya aplicados se omiten y se comprueba que no hayan sido alterados.
5. Revisa las pantallas afectadas y el log.

No se necesita SSH, Composer, Node ni phpMyAdmin en el hosting. Un push a GitHub no despliega por sí solo. Para revertir, restaura el respaldo junto con un commit compatible; MySQL confirma DDL implícitamente, por lo que no se promete rollback automático de columnas.
