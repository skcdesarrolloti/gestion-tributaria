# Gestión Tributaria

Base MVC en PHP 8.2+ para desarrollar con Git y desplegar en Hostinger.
Base asignada: **u350704768_tributaria**.

**La aplicación crea y actualiza las tablas automáticamente. No hay que entrar a phpMyAdmin ni importar SQL.** Al abrirla por primera vez, un formulario protegido permite crear el administrador.

Incluye login de funcionarios con los estilos de Servicios Comerciales, inicio protegido, perfil, cierre de sesión y migraciones automáticas. Los módulos inmobiliarios/tributarios se desarrollarán cuando se definan sus requisitos.

## Por dónde empezar

1. [Instalación local](docs/INICIO-RAPIDO.md).
2. [Despliegue en Hostinger](docs/HOSTINGER.md).
3. [Reglas permanentes](AGENTS.md).
4. [Guía MVC](docs/DESARROLLO.md), [migraciones](database/README.md) y [estilos/login](docs/ESTILOS-Y-LOGIN.md).

No requiere Composer ni Node en el hosting. El CSS compilado está incluido. Node solo se utiliza localmente al modificar las clases Tailwind.

## Estructura

| Carpeta | Responsabilidad |
|---|---|
| app/Controllers | Recibir solicitudes y coordinar respuestas |
| app/Models | Consultas preparadas con PDO |
| app/Views | HTML y componentes compartidos |
| app/Services | Autenticación, CSRF y migraciones |
| app/Core | Configuración, conexión, rutas y vistas |
| bootstrap | Arranque y ejecución automática de migraciones |
| config | Ejemplo y configuración privada local.php |
| routes | Rutas permitidas y métodos HTTP |
| public | Entrada web y archivos CSS |
| database/migrations | Cambios SQL numerados |
| database/checks | Comprobación de cada cambio SQL |
| resources | Fuente CSS y tema original |
| storage | Logs e intentos de acceso privados |
| bin | Herramientas de desarrollo |
| tests | Pruebas automáticas y checklist manual |
| docs | Guías en español |

## Verificación

```powershell
php bin/check.php
php tests/unit.php
```

No hay cuentas ni contraseñas predeterminadas. Las credenciales de la base y la clave de instalación se guardan en config/local.php, excluido de Git.
