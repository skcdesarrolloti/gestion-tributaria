# Gestión Tributaria

Proyecto MVC en PHP 8.2+, ya publicado en Hostinger y conectado con Git. Continuar el desarrollo sobre esta estructura.
Base asignada: **u350704768_tributaria**.

**Accesos: Socio y Usuario.** El Socio consulta y edita toda la información; el Usuario
solo ve y completa el contribuyente que se le asigna. La cuenta inicial autorizada
se activa de forma privada conservando sus expedientes. Los Socios crean cuentas
propias desde **Usuarios y accesos → Crear usuario**; cada persona cambia su clave
en **Mi perfil**. No existe conexión a funcionarios ni inscripción pública.
Ver [primera entrada y cuentas independientes](docs/CUENTAS-INDEPENDIENTES.md).
Ver [perfiles y permisos](docs/PERFILES-Y-ACCESOS.md).

En Beneficios tributarios, **Consultar artículos aquí** permite leer las normas
dentro de cada ficha, sin abrir páginas adicionales. La colección DIAN muestra
fecha y fuente de consulta. Ver [alcance del visor](docs/ARTICULOS-EN-FICHAS.md).

**Menú de trabajo:** Usuarios y accesos (Socio), Contribuyentes, Beneficios
tributarios, Registro de información y Análisis y acciones. Exógena permanece
independiente. Registro selecciona contribuyente y año 2025/2026 y muestra Ingresos
o Costos y gastos en pestañas, reutilizando los movimientos existentes. Las 52
fichas y la hoja de cálculos se conservan. Ver [recorrido y alcance](docs/MENU-Y-REGISTRO.md).

**Ingresos por año:** el 210 muestra General, Pensiones y Dividendos; el 110
organiza ingresos por sus secciones, sin cédulas. Captura compacta por filas,
ayudas «?» plegadas y ganancias ocasionales separadas. Incluye registro bruto
de pensiones y dividendos; no completa sus depuraciones ni el impuesto final.
Ver [captura y fuentes DIAN](docs/INGRESOS.md).

**Extractos bancarios:** Registro → Subir o consultar extractos bancarios conserva
PDF privados por persona, año, banco, cuenta y mes, con visor interno y control
de meses faltantes. El formato mensual BBVA reconocido detecta mes, lee todas
las páginas, concilia saldos y acumula pensión neta, rendimientos y GMF neto de
reintegros. Permite buscar pagos y consultar el PDF dentro del aplicativo. Otros
formatos/cifrados quedan pendientes; aún no traslada importes a casillas DIAN.
Ver [alcance y continuidad](docs/EXTRACTOS-BANCARIOS.md).

**Ingresos laborales:** Registro → Subir nómina laboral permite seleccionar
hojas de XLS/XLSX, vincular por identificación exacta, revisar y confirmar.
Permite seleccionar personas y todos los períodos disponibles del año a la vez.
Suma quincenas por mes y alimenta 32/33 sin duplicar el devengado con su detalle;
muestra retenciones laborales 132 aparte. Solo guarda filas autorizadas elegidas,
no el libro completo. Correcciones con historial y bloqueo de duplicados.
Ver [alcance de nómina y casillas](docs/NOMINA-LABORAL.md).

Al preparar el año, Ingresos recupera el régimen propuesto por el RUT, las
condiciones anuales ya guardadas y la referencia documental. Cada campo indica
su origen o qué confirmar; no deduce residencia fiscal desde la dirección.
Las ayudas y artículos se consultan dentro de la misma pantalla, sin enlaces
externos ni pestañas adicionales.

**Preguntas pendientes:** Ingresos confirma las fuentes del año por tres grupos
plegables, con Sí/No/Pendiente, ayuda y guardado independiente. Recupera respuestas
existentes sin inferir ingresos por un código del RUT. **RUT y documentos** permite
contrastar el PDF con el perfil y ver diferencias y páginas leídas, también cuando
el documento está guardado en otro año; no exige volver a cargarlo.

**Recorrido actual: Inicio → Contribuyentes → Abrir perfil.** Una sola ficha con
pestañas por naturaleza de información, carga del RUT que completa vacíos y edición
de identificación en el mismo lugar. Socios, junta, representantes y profesionales
se muestran separados sin perder sus datos. Ingresos, costos y escenarios quedan
fuera de este recorrido. Ver [ficha única](docs/CONTRIBUYENTES.md).

Los campos tienen ayuda «?» sobre su finalidad. Los complementos de aportes,
beneficios y obligaciones anuales se diligencian en sus componentes, conservando
los datos existentes. Ver [revisión de campos](docs/ALCANCE-CAMPOS-PERFIL.md).

**La aplicación crea y actualiza las tablas automáticamente. No hay que entrar a phpMyAdmin ni importar SQL.** Las cuentas de socios y terceros son propias de este aplicativo.

Incluye login propio con los estilos de Servicios Comerciales, inicio protegido, perfil, cambio de clave, cierre de sesión y migraciones automáticas. [Contribuyentes](docs/CONTRIBUYENTES.md) reúne su identificación y perfil con permisos por cuenta.

En **Registrar contribuyente**, **Subir RUT y rellenar datos** es la primera opción.
Lee el PDF antes del alta, propone identificación y muestra lo que se importará.
**Guardar contribuyente** crea el registro, su perfil del año elegido y el soporte
privado en una sola operación. También permite continuar con captura manual.
Si el RUT corresponde a alguien ya registrado, completa los vacíos y abre directamente
su ficha por pestañas; conserva el registro existente.

La ruta histórica **Ingresos por año** conserva el [bloque inicial de ingresos](docs/INGRESOS.md), fuera del recorrido actual del contribuyente: perfil anual, fichas por casilla, movimientos reales/proyectados y depuración preliminar.

Cada perfil tiene **Proyección anual**, una [estructura de seguimiento](docs/PROYECCION-ANUAL.md)
con resumen real/esperado, ingresos por mes y bloques pendientes para calcular el impuesto
y comparar acciones antes del cierre. Los importes actuales son parciales; aún no estima el impuesto.

**Abrir perfil** reúne el RUT, actividades, responsabilidades, sociedad, personas,
participaciones, establecimientos y condiciones tributarias en pestañas. Reutiliza
los bloques guardados sin repetir la ficha básica ni mostrar las tarjetas anteriores.
Ver docs/CONTRIBUYENTES.md.

**Subir RUT en PDF**, dentro del perfil, recorre las hojas 1–7 reconocidas del formulario
001, rellena identificación, entidad y sus detalles, representantes, socios/junta,
contador y establecimientos. Completa automáticamente los vacíos y conserva los datos
existentes; las diferencias se corrigen en su pestaña. La ruta avanzada histórica
conserva la comparación y selección manual. Conserva el soporte privado. El RUT tiene vigencia indefinida; el estado
actual ante la DIAN requiere consulta oficial. Ver [importación RUT](docs/IMPORTACION-RUT.md).
Dentro de cada bloque, **Mostrar solo campos vacíos** permite continuar con los
faltantes sin volver a digitar lo importado. La entidad se organiza por secciones plegables.

## Continuar el trabajo

**Análisis y acciones** reúne proyección, hoja de cálculo y seguridad social.
**Beneficios tributarios** conserva las 52 fichas por siete categorías, hasta nueve por página;
su explicación se abre dentro de cada ficha. **Seguridad social** contiene la
revisión inicial del HTML recibido: detectados errores, el cálculo no está integrado.
Ver hallazgos y criterios de reutilización en docs/ALCANCE-PLANEACION.md.

**Hoja de planeación** es el simulador inicial por perfil de persona natural residente
ordinaria (2025/2026). Entradas celda por celda, pensión mensual separada, reglas
comunes, tarifa progresiva, retenciones/anticipo y versiones privadas comparables.
Relaciona las 52 fichas con su cobertura; un importe de una regla pendiente impide
mostrar saldo final. Se puede imprimir. Ver alcance y pruebas en docs/PROYECCION-ANUAL.md.

**Beneficios de renta** abre el catálogo inicial para socios y familia como personas
naturales residentes en régimen ordinario: 46 conceptos y seis controles, con norma,
límites, soportes y acciones. Consulta 2025/2026 y UVT por año; todavía no registra
gastos ni calcula ahorro. La comparación con el PDF del contador está pendiente.

**Exógena → 5247**, [módulo independiente para personas jurídicas](docs/EXOGENA-5247.md)
con los 22 campos de la plantilla 2024, registro y edición por empresa/año.
Inicia en 2025 con captura vacía; los datos se cargarán después de revisar el archivo que enviará el responsable.

También está **1007 → Ingresos Recibidos**, [estructura de once campos](docs/EXOGENA-1007.md)
para 2025, sin datos precargados. El nombre visible del 5247 es **Pagos o Abonos en cuenta**.

**1001 → Pagos o Abonos en Cuenta y Retenciones Practicadas** incorpora la
[estructura oficial de 20 campos, versión 10 para 2025](docs/EXOGENA-1001.md), sin precargar datos.

El [catálogo de Exógena](docs/CATALOGO-EXOGENA.md) reúne **14 formatos en seis grupos**,
con selección de cliente y conteos por formato para 2025. Incluye once estructuras nuevas
vacías, captura y edición de borradores. La importación mensual y la generación de XML
quedan para una etapa posterior.

1. Lee [AGENTS.md](AGENTS.md): reglas para continuar el proyecto.
2. Sigue [la guía de desarrollo](docs/DESARROLLO.md) para corregir errores o agregar módulos.
3. Consulta [migraciones](database/README.md), [estilos/login](docs/ESTILOS-Y-LOGIN.md) y [pruebas](docs/VALIDACION.md).
4. Si necesitas ejecutar una copia en tu computador, usa [la guía local](docs/INICIO-RAPIDO.md). La configuración de producción ya existe y debe conservarse.
5. Para continuar los módulos, consulta el [alcance de planeación](docs/ALCANCE-PLANEACION.md) y el [contrato de campos DIAN](docs/DICCIONARIO-CAMPOS.md): régimen, patrimonio nacional/exterior, ingresos, beneficios y revisión UGPP.

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

Las credenciales de conexión se guardan en config/local.php, excluido de Git. Conserva database para este proyecto. No se usa una conexión de funcionarios. Ver [cuentas independientes](docs/CUENTAS-INDEPENDIENTES.md).
