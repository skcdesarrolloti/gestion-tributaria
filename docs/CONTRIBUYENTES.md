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
4. Selecciona persona natural, persona jurídica o sucesión ilíquida.
5. Escribe nombre o razón social, tipo y número de documento. Para entidades y
   sucesiones utiliza el NIT del RUT; escríbelo sin el dígito de verificación.
6. El dígito de verificación se puede registrar aparte. Es opcional y su valor no
   se contrasta todavía con el RUT ni se calcula automáticamente.
7. Marca todas las fuentes de ingresos conocidas; puedes dejarlas pendientes.
   Pensión y arrendamientos pueden coexistir en una persona natural.
8. Guarda. El registro aparecerá en **Mis contribuyentes**; usa **Editar datos**
   para corregirlo posteriormente.

No se cargan declaraciones o datos reales de los PDF de referencia. Los documentos
sirvieron para identificar el requisito de múltiples fuentes de ingresos.

## Identificación y permisos

### Inventario de atributos (revisado el 21 de octubre de 2026)

El registro actual **no es todavía una ficha completa**. El formulario contiene una
ayuda desplegable que distingue campos disponibles de bloques pendientes:

| Bloque | Dónde se guarda hoy | Pendiente |
| --- | --- | --- |
| Identificación y fuentes | Contribuyente | Ampliar caracterización sin confundir fuentes con actividades |
| Actividad económica / RUT | No implementado | Códigos oficiales y descripción, principal/secundarias/otras, fechas y responsabilidades; catálogo vigente y referencia al RUT |
| Residencia, régimen y servicios | Perfil anual en Ingresos por año | Completar caracterización anual y evidencia; no trasladar a un dato permanente |
| Pensión | Escenario de planeación | Caracterización personal y soporte, sin reemplazar las demás fuentes |
| Dependientes y representación | No hay ficha de detalle | Relación, requisitos y soportes; fallecimiento, sucesión y representante cuando aplique |
| Patrimonio | Solo totales en escenario | Bienes, deudas y cuentas nacionales/extranjeras detallados |
| Seguridad social | No hay módulo integrado | Revisar y reutilizar el aplicativo que entregará el responsable |

Siguiente bloque de caracterización: actividades económicas, usando códigos y
descripciones oficiales, no una lista inventada a partir de las fuentes de ingresos.
Antes de desarrollar seguridad social, recibir el aplicativo existente, revisar su
código, reglas, vigencia y casos de prueba; documentar lo reutilizable y las correcciones.
No se ha recibido ni validado todavía ese aplicativo.

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
