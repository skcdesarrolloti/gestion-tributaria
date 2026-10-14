# Incidencia de acceso: 13 de octubre de 2026

El responsable reportó demora al enviar el login y Chrome mostró
`ERR_CONNECTION_RESET`. No hubo mensaje de rechazo de credenciales visible.

## Evidencia obtenida

- Petición HTTPS anónima al login: conexión cerrada durante la negociación TLS.
- Petición HTTPS al CSS estático: mismo fallo; no requiere ejecutar autenticación PHP.
- Petición HTTP al login: respuesta vacía, sin código HTTP.
- Petición HTTPS al dominio principal: código HTTP 200 desde el mismo equipo.
- Segunda biblioteca cliente (Python/OpenSSL), CSS: cierre inesperado durante TLS.
- DNS público responde con varias direcciones; esto por sí solo no demuestra un error
  de DNS, porque puede corresponder a una red de distribución de contenido.

Estas observaciones acotan el fallo a la conexión o servicio del subdominio en ese
momento; no determinan todavía su causa exacta ni excluyen otros fallos posteriores.
No se enviaron credenciales, se modificaron contraseñas ni se cambió configuración.
El módulo Contribuyentes sigue local en la rama codex/contribuyentes, sin push.

## Evidencia pendiente

Revisar en el panel privado de Hostinger el estado del sitio/subdominio y de su
certificado SSL. Si aparece un error PHP cuando se recupere la conexión, obtener
únicamente el fragmento reciente del log privado, sin secretos, antes de cambiar
la base, permisos o código. No activar display_errors ni publicar rutas de diagnóstico.

No afirmar que el login funciona hasta verificar la conexión y el flujo autenticado.
