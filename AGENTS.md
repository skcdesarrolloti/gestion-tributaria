# Continuar el desarrollo — Gestión Tributaria

Estas instrucciones aplican a toda la aplicación y a cualquier persona o asistente que trabaje en ella.

## Estado del proyecto

- El proyecto ya está publicado en Hostinger y conectado a este repositorio mediante Git. No volver a plantear instalación del hosting ni conexión con GitHub como tareas pendientes.
- Sitio: https://gestion-tributaria.sucasainmobiliaria.com.co/public/.
- La base de datos u350704768_tributaria ya existe. Conservar sus datos y la configuración privada del servidor.
- Publicado no significa validado: comprobar el funcionamiento de cada cambio y no dar por resuelto un error solo porque el código llegó a GitHub.
- El trabajo actual consiste en corregir errores y desarrollar los módulos que solicite el usuario sobre esta base MVC existente.
- Ya existen autenticación, perfil, inicio y migraciones automáticas. No reconstruir el proyecto desde cero.

## Cómo trabajar con el responsable

El responsable es un ingeniero industrial con experiencia limitada en programación. Explicar en español y con pasos concretos. Indicar qué se cambió, dónde se encuentra y cómo comprobarlo. Explicar los términos técnicos cuando sean necesarios. Evitar soluciones que le obliguen a administrar tablas manualmente.

## Arquitectura y archivos pequeños

- Mantener PHP 8.2+ con MVC: controladores coordinan, modelos consultan y vistas presentan.
- Flujo de entrada: public/index.php → bootstrap/app.php → routes/web.php → controlador → modelo/servicio → vista.
- Antes de crear un archivo, revisar si existe una responsabilidad equivalente y reutilizarla.
- Objetivo de 50 a 150 líneas por archivo propio; máximo 250 por PHP, JS o CSS manual. Dividir por responsabilidad antes de superar el límite.
- Las excepciones son archivos generados, lockfiles y el tema original de referencia. No separar código arbitrariamente solo para cumplir el número de líneas.
- Reutilizar app/Views/partials para cabecera, navegación, CSRF y pie de página.
- No introducir un framework o dependencias de ejecución innecesarias. El servidor ejecuta el PHP sin Composer ni Node.

## Estilos y login obligatorios

- Usar SIEMPRE como referencia C:\Workspace\desarrollo-skc\control-servicios-comerciales.
- Conservar Poppins, colores, espaciados, componentes y presentación del login de esa referencia.
- Leer docs/ESTILOS-Y-LOGIN.md antes de modificar la interfaz. No crear otro tema sin que el usuario lo solicite.
- El tema compartido está en resources/theme/comerciales.cjs. No editar a mano el CSS generado.
- Si se agregan clases Tailwind, ejecutar npm ci y npm run build:css localmente e incluir public/assets/css/tailwind.css en el commit.
- Las sesiones son independientes, pero las cuentas proceden de la base central u350704768_M4FvI, tabla wp_jet_cct_funcionarios. Usar user_others_apss/pass_others_apss y activo = 'Si', como Servicios Comerciales. Leer docs/ACCESO-FUNCIONARIOS.md. No crear cuentas locales, formularios de primer administrador ni SSO.

## Base de datos automática

- La persona NO debe entrar a phpMyAdmin ni importar SQL. La aplicación crea y actualiza tablas/columnas mediante scripts versionados.
- Para toda tabla, columna, índice o catálogo nuevo, crear la siguiente migración numerada en database/migrations y su comprobación del mismo nombre en database/checks.
- Leer database/README.md. Cada migración contiene una sentencia SQL; su comprobación devuelve si el cambio ya está aplicado.
- No modificar scripts ya aplicados ni borrar schema_migrations para ocultar errores. Crear una migración nueva cuando cambie el esquema.
- Las migraciones solo afectan database, nunca auth_database. La conexión central es de solo lectura y no debe crear bases, tablas o cuentas. Mantener intacta la migración histórica 001 y su tabla local, que el login ya no usa.
- Solo app/Services/Migrator ejecuta cambios de estructura durante el arranque. No dispersar CREATE o ALTER por controladores y vistas.
- Probar cambios de datos en una base aislada. No usar las credenciales de producción en las pruebas.
- La base actual ya está creada: no recrearla, vaciarla ni ejecutar el primer acceso como solución a un error.
- Si se requiere otra base en el futuro, incluir su script de creación; no cambiar la base asignada sin un requisito explícito.

## Configuración y seguridad

- La configuración privada existente está en config/local.php, fuera de Git. La aplicación actualmente NO lee .env.
- No reemplazar la configuración del servidor al corregir código. Usar config/local.example.php solo como referencia para desarrollo local.
- No copiar secretos, datos de clientes ni credenciales del proyecto de referencia.
- Usar consultas preparadas, escape HTML con e() y CSRF en operaciones POST. Validar hashes con password_verify y mantener la compatibilidad explícita con las claves heredadas centrales. Nunca escribir ni transformar contraseñas de funcionarios desde estos proyectos.
- Cada módulo nuevo debe verificar sesión y permisos en el servidor. El rol guardado no constituye por sí solo una política de autorización.
- No exponer logs, SQL ni datos de conexión mediante rutas públicas o mensajes de error.

## Flujo de trabajo

1. Leer este archivo, README.md y docs/DESARROLLO.md; revisar git status y los archivos afectados.
2. Identificar el requisito concreto y continuar sobre la estructura existente. No inventar procesos inmobiliarios ni cálculos tributarios.
3. Implementar el cambio en archivos pequeños, manteniendo estilos, permisos y migraciones automáticas.
4. Ejecutar php bin/check.php y php tests/unit.php. Si cambia autenticación o migraciones, ejecutar también la integración en una base aislada; ver docs/VALIDACION.md.
5. Probar el flujo afectado y actualizar la documentación para quien continúe trabajando.
6. Revisar git diff y git diff --cached antes de un commit. Nunca incluir config/local.php, logs, backups o datos reales.
7. Usar ramas para cambios futuros salvo que el usuario indique trabajar en main. No hacer push forzado. main está conectada al sitio: un push puede actualizarlo automáticamente.
8. Informar el resultado, las pruebas y cualquier verificación pendiente. No afirmar que producción funciona sin evidencia.

## Diagnóstico de errores

La pantalla «No fue posible completar la solicitud» es un mensaje genérico; no identifica la causa. Obtener las últimas líneas de storage/logs/app.log por un medio privado y revisar la excepción antes de cambiar credenciales, permisos, esquema o código. No activar display_errors en producción. Si falta evidencia, pedir únicamente el fragmento necesario, sin contraseñas, y avanzar con las tareas independientes.
