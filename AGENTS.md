# Instrucciones permanentes — Gestión Tributaria

Estas reglas aplican a toda la aplicación y a quien la desarrolle, incluidos asistentes de IA.

## Contexto del responsable

El responsable es un ingeniero industrial con experiencia limitada en programación. Explicar en español, con pasos concretos y comandos listos para copiar. Evitar complejidad innecesaria. No dar por conocidos términos técnicos; explicar dónde va cada archivo y cómo comprobar el resultado.

## Requisitos obligatorios

- Mantener PHP con MVC: controladores coordinan, modelos consultan, vistas presentan.
- Usar SIEMPRE como referencia visual y de login C:\Workspace\desarrollo-skc\control-servicios-comerciales.
- Conservar Poppins, colores, espaciados, componentes y formulario de acceso de esa referencia. No introducir otro tema o framework sin solicitud del usuario. Ver docs/ESTILOS-Y-LOGIN.md.
- Archivos propios pequeños: objetivo hasta 150 líneas; máximo 250 líneas por PHP, JS o CSS manual. Dividir por responsabilidad antes de superar el límite. Excepción: archivos generados, lockfiles y tema original de referencia.
- Desplegar en Hostinger con MySQL/MariaDB. Base asignada: u350704768_tributaria.
- La persona NO debe entrar a phpMyAdmin ni importar SQL. La aplicación crea y actualiza tablas/columnas automáticamente al arrancar, con scripts versionados en database/.
- TODA tabla, columna, índice, dato de catálogo o base nueva debe tener un script SQL versionado. Nunca dejar cambios manuales sin script.
- Crear migraciones nuevas numeradas; no modificar una migración ya aplicada. Documentar orden, respaldo y reversión.
- Ejecutar cambios de estructura exclusivamente mediante App/Services/Migrator en el arranque. No dispersar DDL por controladores o vistas. Cada migración SQL lleva una comprobación SQL del mismo nombre; ver database/README.md.
- Para crear bases incluir un script CREATE DATABASE; si Hostinger restringe ese permiso, explicar la creación en hPanel y dejar que la app cree las tablas.
- Usar consultas preparadas, escape HTML, CSRF en POST y password_hash/password_verify. No almacenar contraseñas en texto plano.
- No copiar .env, credenciales, datos de clientes ni claves del proyecto de referencia.
- Mantener config/local.php, SQL privados y logs fuera de Git. Nunca subir .git o archivos privados al directorio público sin protección.
- No reutilizar cookies de sesión entre los dos proyectos. El login consulta la tabla local de funcionarios; no asumir acceso a WordPress ni implementar SSO sin requisito explícito.
- Cada módulo nuevo debe proteger acceso y definir autorizaciones en el servidor. El rol inicial es informativo; no implica una política de permisos ya implementada.
- Mantener la aplicación ejecutable sin Composer ni Node en Hostinger. El CSS compilado debe quedar en Git.
- Actualizar documentación y pruebas relevantes con cada cambio. Ejecutar php bin/check.php y php tests/unit.php.
- Revisar git diff y git status antes del commit. Hacer commits claros y trabajar por ramas para cambios futuros. No hacer push forzado.

## Alcance inicial

Esta base incluye instalación web protegida, migraciones automáticas, autenticación, inicio y perfil. No inventar cálculos tributarios, entidades inmobiliarias, indicadores ni procesos de negocio sin requisitos del usuario.
