# Pruebas funcionales

Usar una base aislada de pruebas, nunca producción.

1. Configurar la app con una base vacía y setup_key aleatoria. Abrirla: crea tablas y presenta primer acceso sin phpMyAdmin.
2. Repetir la apertura: no duplica tablas/versiones ni borra datos.
3. Enviar instalación sin CSRF: 419. Clave incorrecta: 403. Contraseñas diferentes: 422.
4. Crear administrador con clave correcta. Reabrir instalador: 404.
5. Inicio sin sesión redirige al login.
6. Contraseña incorrecta o usuario inexistente: 401 con el mismo mensaje.
7. Login sin CSRF: 419. Login válido: cambia sesión y abre inicio.
8. Perfil muestra datos escapados y no contiene el hash.
9. Logout por GET: 405. Botón de cierre: termina sesión.
10. Desactivar la cuenta mediante un script en la base de pruebas: invalida también la sesión abierta.
11. Superar 10 intentos usuario/IP en 15 minutos: 429 y Retry-After.
12. Probar migración de columna y reabrir: la crea una vez. Simular DDL aplicado sin registro: su comprobación permite recuperar.
13. Alterar un SQL aplicado en pruebas: se bloquea el arranque; restaurar el archivo para continuar.
14. Revisar login/inicio/perfil en móvil y escritorio, navegación con Tab y protección de archivos descrita en docs/DESARROLLO.md.

Automatizado sin base: php bin/check.php y php tests/unit.php. La prueba de integración requiere MySQL local; no usar credenciales reales en tests.
