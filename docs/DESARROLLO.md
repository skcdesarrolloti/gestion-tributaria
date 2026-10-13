# Desarrollar sin archivos enormes

MVC separa el trabajo: el Modelo consulta la base, la Vista muestra la pantalla y el Controlador conecta ambas cosas.

Una visita entra por public/index.php, arranca bootstrap/app.php, busca la dirección en routes/web.php y ejecuta el controlador. Este consulta un modelo y pasa datos a una vista. Los servicios contienen reglas que se reutilizan.

## Agregar un módulo

1. Describe en español qué necesita hacer el usuario y quién podrá hacerlo.
2. Si requiere datos, crea database/migrations/002_nombre_del_cambio.sql y su comprobación database/checks/002_nombre_del_cambio.sql. La app lo ejecutará automáticamente; documenta su reversión con respaldo. No pidas al responsable entrar a phpMyAdmin; usa siempre migraciones.
3. Crea un modelo pequeño en app/Models; usa prepare y execute para los valores.
4. Crea un controlador en app/Controllers; comprueba sesión y permisos antes de leer o escribir. El login solo autentica; cada módulo debe definir sus permisos.
5. Crea la vista en app/Views y reutiliza partials/head, header, csrf y footer. Escapa los valores con e().
6. Registra la ruta en routes/web.php y su controlador en bootstrap/app.php. Las escrituras deben usar POST y validar Csrf::valid.
7. Usa el tema de referencia. Si agregas clases Tailwind, ejecuta npm ci y npm run build:css localmente; incluye el CSS generado en el commit.
8. Prueba errores, permisos y casos exitosos; actualiza estas guías.

Busca archivos de 50 a 150 líneas. El máximo de 250 líneas se valida para PHP. Divide consultas, reglas y fragmentos de HTML en archivos con nombres descriptivos. No crees una clase genérica gigante ni un controlador para todo.

## Guardar con Git

```powershell
git pull --ff-only
git switch -c feature/nombre-del-cambio
php bin/check.php
php tests/unit.php
git status
git diff
git add app routes database docs public resources
git diff --cached
git commit -m "Agregar descripción breve del cambio"
git push -u origin feature/nombre-del-cambio
```

Ajusta git add a las carpetas realmente modificadas. Nunca agregues claves, backups ni datos de clientes. Un commit guarda una versión local; push la envía a GitHub. Subir a GitHub no despliega por sí solo en Hostinger.

## Diagnóstico

- Pantalla de error: revisar storage/logs/app.log y config/local.php.
- Login vuelve siempre al formulario: revisar HTTPS, secure_cookies y base_path.
- 419: formulario vencido; recargar para renovar token.
- 429: límite de intentos alcanzado; esperar el tiempo indicado.
- Cambio de estilo no aparece: recompilar CSS y recargar sin caché.
