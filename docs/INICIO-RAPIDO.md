# Inicio rápido en Windows

## 1. Preparar

Usa PHP 8.2+ con pdo_mysql y mbstring, y MySQL/MariaDB (por ejemplo XAMPP). Inicia MySQL desde XAMPP; no necesitas abrir phpMyAdmin. Abre PowerShell en C:\Workspace\gestiones\gestion-tributaria. Si php no se reconoce, usa C:\xampp\php\php.exe.

## 2. Configurar

```powershell
Copy-Item config/local.example.php config/local.php
php bin/setup-key.php
```

Copia la clave aleatoria que imprimió el segundo comando y pégala en setup_key de config/local.php. Guárdala temporalmente: se pedirá para crear el administrador. No la envíes por URL ni la subas a Git.

Completa database.username y database.password con las credenciales de MySQL local. Mantén database.name = 'u350704768_tributaria', base_path = '' y cambia secure_cookies a false SOLO para HTTP local. No uses esa configuración en producción.

Si la base no existe y el usuario MySQL tiene permiso, la aplicación la crea. Si ya existe, se conecta sin modificar otras bases. Las tablas se crearán solas.

## 3. Iniciar

```powershell
php -S 127.0.0.1:8082 -t public
```

Abre http://127.0.0.1:8082/. Deja abierta la terminal; Ctrl+C detiene el servidor. Siempre usa -t public.

## 4. Primer administrador

La aplicación crea las tablas y muestra el primer acceso. Introduce la clave de instalación, tu nombre, un usuario y una contraseña de entre 12 y 72 bytes. Repite la contraseña y pulsa Crear administrador. Después inicia sesión normalmente.

El usuario de la aplicación no es el usuario de MySQL. Cuando ya existe una cuenta, la instalación queda cerrada. Borra el valor de setup_key de config/local.php después del primer acceso; no se necesita para el login. No borres todas las cuentas para restablecer una contraseña.

## 5. Verificar

En otra terminal ejecuta php bin/check.php y php tests/unit.php. Revisa tests/MANUAL.md.

Los errores internos se registran en storage/logs/app.log. Si falla la conexión, comprueba el servicio MySQL y las credenciales. Nadie necesita crear tablas manualmente.
