# Verificación funcional del login institucional

Usar bases aisladas de pruebas, nunca modificar cuentas reales para ejecutar este checklist.

1. Abrir login con la tabla local de funcionarios vacía: muestra el acceso normal, sin pedir crear administrador.
2. Abrir la ruta antigua instalacion: redirige al login; POST a esa ruta no crea cuentas.
3. Confirmar que database conserva los procesos del proyecto y auth_database consulta la base de funcionarios.
4. Entrar con user_others_apss/pass_others_apss de un funcionario activo.
5. Probar clave incorrecta y cuenta inexistente: mismo error genérico.
6. Probar una cuenta central con hash y otra con el formato heredado permitido: ambas ingresan sin modificar su contraseña.
7. Una cuenta que solo existe en la tabla local antigua no permite ingresar.
8. Login y logout sin CSRF: 419. Logout por GET: 405.
9. Verificar inicio y perfil; no se muestran hashes ni contraseñas.
10. Desactivar una cuenta de prueba central: invalida también su sesión abierta.
11. Superar el límite de intentos: 429 y Retry-After.
12. Verificar que las migraciones solo afectan database y conservan su historial.
13. Revisar diseño móvil/escritorio y protección de archivos descrita en docs/DESARROLLO.md.

Ejecutar php bin/check.php, php tests/unit.php y php tests/integration.php con MySQL local. Las pruebas de integración preparan los dos orígenes de datos con fixtures sintéticos.
