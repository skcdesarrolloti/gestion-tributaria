# Referencia obligatoria de estilos y login

Origen: C:\Workspace\desarrollo-skc\control-servicios-comerciales.
Commit de referencia inicial: e4c134c7a7ec75431c15b078ab3c9707e680c0bf.

## Estilos

Se copia el tema de tailwind.config.cjs a resources/theme/comerciales.cjs y se utiliza desde la configuración local. resources/css/tailwind.css conserva la fuente original. public/assets/css/tailwind.css contiene el resultado listo para hosting. app.css solo agrega distribución y accesibilidad; no redefine la identidad visual.

El login conserva el HTML y las clases del formulario actual de public/login.php de referencia: tarjeta, Poppins, iconos Material Symbols, azul y amarillo del tema. El antiguo archivo login.css de referencia no es usado por ese formulario actual y no se adopta como tema alternativo.

Para incorporar cambios del proyecto original, ejecuta desde PowerShell:

```powershell
powershell -ExecutionPolicy Bypass -File bin/sync-theme.ps1
npm ci
npm run build:css
```

Compara visualmente login e inicio antes del commit. Revisa también cambios de public/login.php en la referencia y traslada solo su presentación a app/Views/auth/login.php. El script sincroniza el tema; no copia lógica de negocio ni secretos. Cada repositorio guarda su propia copia para desplegarse de forma independiente. No hay dependencia de una ruta de Windows en producción.

## Autenticación

El modelo Funcionario consulta wp_jet_cct_funcionarios en la base de ESTE proyecto, con los mismos campos de acceso: user_others_apss, pass_others_apss y activo = 'Si'. Guarda contraseñas con password_hash y valida con password_verify, como el modo seguro del original. No admite claves de texto plano.

Las cookies tienen nombres distintos por proyecto, HttpOnly y SameSite=Lax. Secure debe estar activo en producción HTTPS. La sesión cambia de identificador al entrar, vence tras 30 minutos sin actividad y comprueba que el funcionario siga activo en cada página protegida. El cierre exige POST y token CSRF. El acceso limita a 10 intentos por usuario/IP y 30 por IP en 15 minutos, incluidos los exitosos.

El proyecto no requiere WordPress ni accede a la base comercial. Si se necesitan las mismas cuentas, se debe implementar una migración autorizada de los campos compatibles y hashes; no copiar datos reales al repositorio. El acceso mediante enlaces firmados del sistema comercial no se incluye en esta base: requiere definir una integración y su secreto.

Poppins e iconos se cargan desde Google Fonts; el CSS principal está alojado en el propio proyecto.
