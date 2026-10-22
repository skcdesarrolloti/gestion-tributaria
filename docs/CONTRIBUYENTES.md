# Paso 1: Contribuyentes

Este módulo identifica a la persona o entidad que se analizará. No determina todavía
la obligación de declarar, la residencia fiscal, el régimen, las cédulas ni el impuesto.
Esos datos se revisarán por año en las siguientes etapas de la planeación.

El alcance de las etapas siguientes, incluyendo régimen, patrimonio en el exterior,
campos DIAN y revisión UGPP, está en [ALCANCE-PLANEACION.md](ALCANCE-PLANEACION.md).
Las opciones cambian con el tipo elegido; consulta [FORMULARIOS-DIAN.md](FORMULARIOS-DIAN.md)
para sus diferencias, la regla de sucesiones y los bloques pendientes de cada formulario.

## Cómo usarlo

1. Inicia sesión y entra a **Contribuyentes** desde la navegación o el inicio.
2. Abre **¿Qué es un contribuyente?** para consultar la explicación inicial.
3. Pulsa **Registrar contribuyente**.
   Al comienzo puedes **Subir RUT y rellenar datos**, elegir el año y revisar la
   identificación propuesta antes de guardar. El alta conserva también los campos
   reconocidos del perfil y el soporte privado. Si ya existe, usa el perfil anterior.
   Los pasos siguientes siguen disponibles para completar o registrar manualmente.
4. Selecciona persona natural, persona jurídica o sucesión ilíquida.
5. Escribe nombre o razón social, tipo y número de documento. Para entidades y
   sucesiones utiliza el NIT del RUT; escríbelo sin el dígito de verificación.
6. El dígito de verificación se puede registrar aparte. Es opcional y su valor no
   se contrasta con el PDF cuando se usa la importación del RUT; el registro manual
   no realiza esa comprobación.
7. Marca todas las fuentes de ingresos conocidas; puedes dejarlas pendientes.
   Pensión y arrendamientos pueden coexistir en una persona natural.
8. Guarda. El registro aparecerá en **Mis contribuyentes**; usa **Editar datos**
   para corregirlo posteriormente.

No se importan declaraciones de renta desde los PDF de referencia. La carga del
RUT tiene su propio flujo de lectura y confirmación descrito a continuación.

## Identificación y permisos

La opción **Subir RUT en PDF** completa campos desde una copia digital y conserva
el soporte privado después de confirmar. Revisa los pasos, límites y comprobación
de estado en [IMPORTACION-RUT.md](IMPORTACION-RUT.md). La captura manual sigue disponible.

### Perfil tributario y RUT ampliado · 21/10/2026

Desde **Mis contribuyentes → Completar perfil tributario y RUT** o desde **Editar
identificación básica**, abrir la caracterización de 2025 o 2026. No requiere PDF.
Primero debe existir el registro básico del contribuyente. El botón es visible y
reemplaza la explicación anterior de bloques aún no implementados.

La portada ofrece 13 bloques, sin desplegar todos los campos:

| Bloque | Captura disponible |
| --- | --- |
| Identificación RUT | Formulario y fechas, seccional, documento, expedición, nombres separados, razón social, nombre comercial, sigla, nacimiento y nacionalidad |
| Ubicación | País, departamento, municipio, dirección, correo, postal y teléfonos |
| Actividades | Principal, secundaria, otras dos, fechas, ocupación y establecimientos |
| Responsabilidades | Renglones con códigos de casilla 53 y control de casilla sin códigos |
| Entidad / sucesión | Naturaleza y forma, constitución, registro, capital, vinculación, fallecimiento y estado de sucesión |
| Aduanas | Códigos de casilla 54 y complementos 55–61 |
| Fiscal anual | Residencia y sus datos de revisión, régimen, declaración, formulario, contabilidad, IVA, retenciones, facturación, exógena, patrimonio, exterior, RUB y precios de transferencia |
| Fuentes y eventos | Todas las fuentes comunes, ingresos exteriores, ganancias ocasionales, ventas de activos, cripto, ECE y pérdidas |
| Seguridad social | Vinculación, tipo de pensión, pagador, EPS/AFP/ARL, riesgo, nómina concurrente, PILA y UGPP |
| Beneficios | Preguntas sobre costos y beneficios para revisar requisitos y soportes |
| Relacionados | Renglones de representantes, apoderados, herederos, socios, junta, contador, revisor, cónyuge y dependientes |
| Establecimientos | Renglones con ubicación, actividad, registro y apertura/cierre |
| Patrimonio | Bienes, cuentas e inversiones nacionales/extranjeras y deudas; país, moneda, valores declarados, titularidad, fecha y soporte |

**Guardar bloque** admite borradores incompletos. Cada bloque conserva estado,
fuente documental, fecha y notas. Sin respuesta no equivale a No. No aplica exige
justificación; no permite omitir naturaleza y representación de jurídicas/sucesiones
como si fueran personas naturales sin representación. Los renglones vacíos se
ignoran; hasta 100 por bloque, sin recorte silencioso.

El estado se deriva de respuestas y condiciones mínimas: Pendiente, Faltan datos,
Importado del RUT, Completo/pendiente de revisión documental y Revisado con documentos
por el usuario. El último requiere campos
mínimos, soporte y fecha. No certifica integridad fiscal ni examen profesional.
El tablero compara SIMPLE/IVA/pensión/dependientes/exterior y el perfil existente
de Ingresos por año. No modifica ese perfil, sus movimientos ni sus cálculos.
Los años no se copian automáticamente. No presenta ni actualiza el RUT en la DIAN.

Los catálogos contienen 508 actividades/clasificaciones, 49 responsabilidades y
74 usuarios aduaneros extraídos de las fuentes oficiales citadas en pantalla, con
fecha 21/10/2026. Incluyen 0010, 0020, 0081, 0082 y 0090 con ceros iniciales. Las
etiquetas identifican códigos; no trasladan instrucciones de elegibilidad antiguas
de la página institucional. Si falta un código puede anotarse para verificación;
ese bloque queda pendiente de revisión documental. Para lugares, formas y códigos no
catalogados se admite transcripción manual; no se inventan listas completas.

Migraciones 014–016: caracterización por contribuyente/año/bloque, catálogo e
inserción oficial. Conservan datos previos y no afectan la base central. Una futura
actualización del catálogo requiere otra migración, sin editar las aplicadas.
Guardar compara la revisión del bloque; una ventana antigua devuelve 409 y no
sobrescribe otra captura. Para revertir código conservar tablas y hacer respaldo;
no borrar caracterizaciones ni modificar scripts aplicados.

La calculadora recibida de seguridad social sigue sin integrarse por los errores
registrados en ALCANCE-PLANEACION.md. Este bloque solo caracteriza condiciones.

### Reglas del registro

- El número no admite puntos, espacios ni guiones. Pasaportes permiten letras y números.
- Se evita repetir el mismo tipo y número de documento dentro de la cuenta.
- Cada funcionario activo puede crear, listar y editar exclusivamente sus registros.
  El filtro se aplica en el servidor; el rol administrador no permite ver registros ajenos.
- El documento puede existir en cuentas diferentes. Compartir registros entre miembros
  del equipo requiere una política explícita posterior.
- No hay borrado ni cálculos tributarios en esta etapa.
- Las fuentes son una caracterización inicial, no una clasificación fiscal definitiva.

## Estructura y despliegue

El controlador `ContribuyenteController` coordina el flujo, el modelo `Contribuyente`
consulta la base y `ContribuyenteData` valida el formulario. Las vistas están en
`app/Views/contribuyentes`, con cabecera, CSRF y pie compartidos.

La migración `002_contribuyentes.sql` crea la tabla, la relación con el funcionario y
el índice que evita duplicados. Su comprobación verifica columnas, índice y relación inicial.
La migración `003_contribuyentes_funcionario_central.sql` retira la relación con la
tabla histórica local, porque el propietario es ahora el ID autenticado en la base
central. No copia cuentas ni escribe en auth_database; los filtros del modelo
verifican el propietario. Ambas migraciones deben desplegarse juntas.
La aplicación la ejecuta automáticamente al arrancar, sin importar SQL manualmente.

Conservar la configuración privada del servidor. Desplegar código, migración,
comprobaciones y CSS compilado juntos. Verificar el flujo en el sitio después de
desplegar; las pruebas locales no confirman el funcionamiento de Hostinger.

Para volver temporalmente al código anterior, conservar la tabla y sus registros:
el cambio es aditivo. Una eliminación futura requiere respaldo y revisión expresa.

## Comprobaciones

Ejecutar `php bin/check.php`, `php tests/unit.php` y la integración aislada descrita
en `docs/VALIDACION.md`. La integración comprueba registro y edición, varios tipos,
fuentes múltiples y pendientes, CSRF, escape HTML, duplicados y acceso entre cuentas.

Referencias de las explicaciones:

- [DIAN: renta de personas naturales AG 2025](https://micrositios.dian.gov.co/renta-personas-naturales-ag-2025/todo-lo-que-usted-debe-saber-sobre-renta/).
- [DIAN: personas naturales y sucesiones ilíquidas](https://micrositios.dian.gov.co/renta-personas-naturales-ag-2022/las-personas-naturales-y-las-sucesiones-iliquidas/).
- [DIAN: renta de personas jurídicas](https://www.dian.gov.co/impuestos/sociedades/Paginas/Renta-personas-juridicas.aspx).
