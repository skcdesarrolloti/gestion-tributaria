# Revisión de campos del contribuyente

Revisión de presentación y finalidad: 22/10/2026. Referencia de estructura:
[DIAN, secciones del RUT](https://www.dian.gov.co/impuestos/Documents/Secciones-RUT-DIAN.pdf).
Se conserva la lectura de las hojas reconocidas, sin atribuir al PDF hechos que no contiene.
No se incorporan nuevas reglas de liquidación ni se da por vigente una obligación por marcar una respuesta.

## Qué permanece y por qué

| Naturaleza | Información | Finalidad dentro del perfil |
| --- | --- | --- |
| RUT | Identificación, contacto, actividad principal/secundaria y fechas, responsabilidades, clasificación aduanera | Conocer al titular y lo inscrito, sin clasificar automáticamente ingresos ni liquidar obligaciones. |
| RUT y soporte societario | Constitución/reforma, capital, vinculación, socios, junta, representantes, profesionales, establecimientos | Conocer propiedad, control, representación y lugares de actividad. Capital y porcentajes no se convierten en ingresos ni valoración fiscal de acciones. |
| Complemento personal opcional | Nacimiento, nacionalidades, vínculos familiares, parentesco, inicio/retiro de relaciones, aperturas/cierres de establecimientos | Describir edad, relación y vigencia de los vínculos que no siempre aparecen en el RUT. Las ayudas indican el soporte y evitan presumir beneficios. |
| Complemento de sucesión | Fallecimiento, fecha, estado y adjudicación | Identificar la representación y situación del titular. Se omiten en personas jurídicas, conservando cualquier dato histórico. |
| Complemento laboral/pensional | Vinculación, tipo de pensión, reconocimiento y pagador | Distinguir condiciones personales. No captura pagos ni calcula renta o aportes. |
| Contraste tributario | Residencia y país, permanencia a revisar, identificación exterior, sujeción, régimen, contabilidad, IVA, agente retenedor y facturación | Caracterizar el alcance y las condiciones del titular. No confundir dirección con residencia ni inscripción con cumplimiento. Cada respuesta se documenta por año. |
| Control documental | Disponibilidad, número/fecha de copia, inscripción inicial opcional, consulta oficial del estado DIAN, soporte, notas y revisión | Conocer la procedencia y fecha de los datos. El PDF no consulta el estado actual ni tiene un vencimiento inferido. «Revisado» corresponde al usuario, no a una aprobación profesional. |
| Excepciones de catálogo | Casilla 53 sin códigos y códigos no encontrados | Distinguir un dato pendiente de un documento realmente vacío y conservar códigos para su revisión. |

Cada campo visible tiene una ayuda «?» con su utilidad. Los complementos tienen
explicación particular en `resources/caracterizacion/ayudas.php`; los datos del
RUT comparten la finalidad de su sección y conservan las precisiones particulares.
Las ayudas se abren con clic o teclado, se cierran con Escape y se presentan sin
extender el formulario. En móvil aparecen sobre el contenido, con desplazamiento interno.

## Qué se trasladó

En la parte inferior del perfil, **Datos que se diligencian en otros componentes**
abre una lista breve. Cada enlace conserva contribuyente y año y permite guardar.

| Componente | Campos |
| --- | --- |
| Seguridad social | EPS, administradora pensional, ARL, riesgo, empleadores/contratos concurrentes, existencia de PILA, ingresos adicionales para revisar aportes, requerimientos UGPP y situaciones especiales de aportes. |
| Beneficios por dependientes | Condiciones de dependencia, estudios/discapacidad/ingresos propios y revisión de solicitud por otra persona. La identidad y parentesco siguen disponibles en el perfil. |
| Obligaciones del año | Obligación de declarar renta, formulario previsto, revisión de exógena, impuesto al patrimonio, declaración de activos exteriores, RUB y precios de transferencia. Necesitan evaluación del período; no son atributos que el RUT resuelva por sí solo. |
| Ingresos y eventos | Fuentes y eventos del año del bloque histórico `ingresos`, fuera del perfil. |
| Costos y beneficios | Preguntas de soportes de costos, vivienda, educación, salud, dependientes, aportes voluntarios, AFC, GMF, donaciones, compras y otros beneficios del bloque histórico `beneficios`. |
| Patrimonio y deudas | Bienes, cuentas nacionales/exteriores, inversiones, participaciones valoradas, deudas y sus soportes del bloque histórico `patrimonio`. |

Estas fichas reúnen información preparatoria existente. **No calculan ni transfieren
importes al simulador**. La calculadora de aportes continúa pendiente de sus ajustes.
No se crea un segundo expediente del contribuyente.

## Campos retirados sin borrar información

- `actividades.otras_revisadas`: control redundante de revisión, sustituido por las actividades visibles y los soportes.
- `aduanas.organizaciones`: complemento histórico sin correspondencia precisa con una casilla; no debe solicitarse como requisito del perfil.

Ambas claves permanecen almacenadas si ya tenían información; no se vuelven a pedir.
No se borra ni modifica ningún esquema, cuenta, documento o dato de producción.

## Conservación y permisos

La ruta `planeacion/datos` utiliza los mismos bloques JSON y el mismo control de
revisión que la ficha. Cada formulario acepta únicamente sus campos; los demás
se recuperan del valor guardado en el servidor. La captura de personas conserva
los complementos trasladados mediante referencias a sus renglones y rechaza
referencias inexistentes o duplicadas. La revisión impide sobrescribir una
edición de otra ventana. No hay copias paralelas ni sincronización por ejecutar.

Se mantiene sesión, autorización por propietario, CSRF, validación y escape HTML.
Las pruebas cubren guardado en ambos sentidos, exclusión de campos fuera de alcance,
conservación de otras personas, rechazo de referencias inválidas y concurrencia.
