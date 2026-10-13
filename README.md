# Gestión Tributaria

Proyecto MVC en PHP 8.2+, ya publicado en Hostinger y conectado con Git. Continuar el desarrollo sobre esta estructura.
Base asignada: **u350704768_tributaria**.

**La aplicación crea y actualiza las tablas automáticamente. No hay que entrar a phpMyAdmin ni importar SQL.** Al abrirla por primera vez, un formulario protegido permite crear el administrador.

Incluye login de funcionarios con los estilos de Servicios Comerciales, inicio protegido, perfil, cierre de sesión y migraciones automáticas. Los módulos inmobiliarios/tributarios se desarrollarán cuando se definan sus requisitos.

## Continuar el trabajo

1. Lee [AGENTS.md](AGENTS.md): reglas para continuar el proyecto.
2. Sigue [la guía de desarrollo](docs/DESARROLLO.md) para corregir errores o agregar módulos.
3. Consulta [migraciones](database/README.md), [estilos/login](docs/ESTILOS-Y-LOGIN.md) y [pruebas](docs/VALIDACION.md).
4. Si necesitas ejecutar una copia en tu computador, usa [la guía local](docs/INICIO-RAPIDO.md). La configuración de producción ya existe y debe conservarse.

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
