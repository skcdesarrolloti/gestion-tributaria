# Verificación funcional del acceso independiente

Usar bases aisladas de pruebas, nunca modificar cuentas reales para ejecutar este checklist.

1. Sin cuentas propias, comprobar que dirige a activación privada y que conocer el login no permite activarla.
2. Comprobar código de un solo uso, CSRF y cierre de activación después del primer Socio.
3. Confirmar que database conserva procesos y cuentas propias; no existe conexión a funcionarios.
4. Activar la cuenta del Socio autorizado según docs/CUENTAS-INDEPENDIENTES.md y entrar con su contraseña propia.
5. Probar clave incorrecta y cuenta inexistente: mismo error genérico.
6. Crear un Socio y un Usuario externo; comprobar cambio obligatorio de clave inicial y aislamiento de su ficha.
7. Una cuenta que solo existe en la tabla local antigua no permite ingresar.
8. Login y logout sin CSRF: 419. Logout por GET: 405.
9. Verificar inicio y perfil; no se muestran hashes ni contraseñas.
10. Suspender una cuenta propia: invalida también su sesión abierta. Cambiar y restablecer claves revoca las sesiones anteriores.
11. Superar el límite de intentos: 429 y Retry-After.
12. Verificar que las migraciones solo afectan database y conservan su historial.
13. Revisar diseño móvil/escritorio y protección de archivos descrita en docs/DESARROLLO.md.

Ejecutar php bin/check.php, php tests/unit.php y php tests/integration.php con MySQL local. Las pruebas crean datos ficticios, comprueban que no se utiliza la base antigua y que funciona aunque esta no exista.
