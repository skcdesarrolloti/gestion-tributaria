<?php
return [
 'fiscal'=>['title'=>'Planeación · Perfil fiscal del año','guide'=>'Complemento del RUT. ET 9, 10, 12-1 y 555-2: registrar residencia y régimen exige revisar los hechos y soportes del año. Esta caracterización no reemplaza ni modifica el perfil de Ingresos por año.', 'fields'=>[
  'residencia'=>$f('Residencia fiscal del año','select',['residente'=>'Residente','no_residente'=>'No residente','doble'=>'Posible doble residencia: revisar convenio'],true,'No se decide solo por nacionalidad, domicilio o RUT. Documenta permanencia, vínculos, certificados y convenio cuando corresponda.'),
  'dias_colombia'=>$f('Días de permanencia en Colombia a revisar','integer'), 'pais_residencia'=>$f('País de residencia fiscal declarado','text',[],true), 'nit_exterior'=>$f('Identificación tributaria extranjera'),
  'sujecion'=>$f('Condición frente al impuesto de renta','select',['contribuyente'=>'Contribuyente','no_contribuyente'=>'No contribuyente: revisar fundamento'],true,'Ser no contribuyente y no estar obligado a declarar son condiciones diferentes. Registra aparte el régimen y la obligación de presentar declaración.'),
  'regimen'=>$f('Régimen a contrastar con el RUT y el año','select',['ordinario'=>'Ordinario','simple'=>'SIMPLE','especial'=>'Especial','no_aplica'=>'No aplica: explicar condición en notas'],true),
  'obligacion_renta'=>$f('Obligación de declarar renta','select',['si'=>'Obligado según revisión','no'=>'No obligado según revisión','voluntaria'=>'Declaración voluntaria a revisar'],true),
  'formulario'=>$f('Formulario previsto','select',['210'=>'210','110'=>'110','260'=>'260','otro'=>'Otro: explicar en notas'],true),
  'contabilidad'=>$f('¿Obligado a llevar contabilidad?','select',$yn,true), 'iva'=>$f('Condición frente al IVA','select',['responsable'=>'Responsable','no_responsable'=>'No responsable','no_aplica'=>'No aplica'],true),
  'retencion'=>$f('¿Agente de retención?','select',$yn,true), 'facturacion'=>$f('¿Obligado a facturar electrónicamente?','select',$yn,true), 'exogena'=>$f('¿Debe revisar obligación de exógena?','select',$yn,true),
  'patrimonio_impuesto'=>$f('¿Debe revisar impuesto al patrimonio?','select',$yn,true), 'activos_exterior_decl'=>$f('¿Debe revisar declaración de activos en el exterior?','select',$yn,true),
  'rub'=>$f('¿Debe revisar beneficiarios finales / RUB?','select',$yn,true), 'precios_transferencia'=>$f('¿Hay operaciones a revisar por precios de transferencia?','select',$yn,true),
 ]],
 'ingresos'=>['title'=>'Planeación · Fuentes y eventos del año','guide'=>'Complemento de planeación; ET 26, 27, 103, 206 y 330–343. Identifica todas las fuentes, sin suponer que pensión excluye otras rentas. Los importes se registran en Ingresos o en la Hoja de planeación.', 'fields'=>[
  'salarios'=>$f('¿Salarios o pagos laborales?','select',$yn,true), 'honorarios'=>$f('¿Honorarios / servicios independientes?','select',$yn,true), 'pensiones'=>$f('¿Pensiones?','select',$yn,true),
  'arriendos'=>$f('¿Arrendamientos?','select',$yn,true), 'rendimientos'=>$f('¿Intereses / rendimientos?','select',$yn,true), 'negocios'=>$f('¿Ventas o actividades de negocio?','select',$yn,true), 'dividendos'=>$f('¿Dividendos / participaciones?','select',$yn,true),
  'exterior'=>$f('¿Ingresos o impuestos pagados en el exterior?','select',$yn,true), 'ocasionales'=>$f('¿Herencias, donaciones, loterías u otras ganancias ocasionales?','select',$yn,true),
  'ventas_activos'=>$f('¿Ventas de inmuebles, acciones u otros activos?','select',$yn,true), 'cripto'=>$f('¿Operaciones con criptoactivos?','select',$yn,true), 'ece'=>$f('¿Entidades controladas del exterior / rentas pasivas ECE?','select',$yn,true),
  'otros'=>$f('¿Otros ingresos o eventos sin clasificar?','select',$yn,true), 'perdidas'=>$f('¿Pérdidas o compensaciones de años anteriores?','select',$yn,true),
 ]],
 'seguridad'=>['title'=>'Planeación · Pensión y seguridad social','guide'=>'Caracterización para revisar aportes; no liquida PILA. Es distinta de las cédulas de renta. El aplicativo recibido permanece en revisión.', 'fields'=>[
  'vinculacion'=>$f('Vinculación durante el año','select',['dependiente'=>'Dependiente','independiente'=>'Independiente','mixta'=>'Dependiente e independiente','rentista'=>'Rentista de capital','sin_actividad'=>'Sin actividad'],true),
  'tipo_pension'=>$f('Situación pensional','select',['no'=>'No recibe pensión','vejez'=>'Vejez / jubilación','invalidez'=>'Invalidez','sobrevivencia'=>'Sobrevivientes / sustitución','anticipada'=>'Anticipada','exterior'=>'Pensión del exterior','varias'=>'Más de un tipo: precisar'],true,'La pensión de sobrevivientes no se debe tratar automáticamente igual que una pensión propia de vejez para decidir aportes sobre otras actividades.'),
  'fecha_pension'=>$f('Fecha de reconocimiento de pensión','date'), 'pagador_pension'=>$f('Entidad o entidades pagadoras'),
  'eps'=>$f('EPS / asegurador de salud','text',[],true), 'afp'=>$f('Administradora / régimen pensional'), 'arl'=>$f('ARL'), 'riesgo'=>$f('Clase de riesgo','select',['1'=>'I','2'=>'II','3'=>'III','4'=>'IV','5'=>'V','no_aplica'=>'No aplica']),
  'empleadores'=>$f('Empleadores / contratos concurrentes','textarea'), 'pila'=>$f('¿Existen planillas y certificados de aportes?','select',$yn,true),
  'ingresos_adicionales'=>$f('¿Ingresos adicionales a nómina o pensión?','select',$yn,true), 'ugpp'=>$f('¿Requerimientos o procesos UGPP?','select',$yn,true),
  'situacion_especial'=>$f('¿Exclusiones, devolución de saldos o situación especial de aportes?','select',$yn,true),
 ]],
 'beneficios'=>['title'=>'Planeación · Gastos y beneficios a revisar','guide'=>'Identifica oportunidades y documentos; no concede deducciones. ET 107, 126-1, 126-4, 206, 336, 387 y 771-2. Cada beneficio conserva sus requisitos y topes en el catálogo.', 'fields'=>[
  'costos'=>$f('¿Costos de actividad con soportes?','select',$yn,true), 'vivienda'=>$f('¿Intereses de vivienda / leasing habitacional?','select',$yn,true), 'educacion'=>$f('¿Intereses ICETEX?','select',$yn,true),
  'salud'=>$f('¿Medicina prepagada / seguros elegibles?','select',$yn,true), 'dependientes'=>$f('¿Dependientes económicos a revisar?','select',$yn,true), 'fpv'=>$f('¿Aportes voluntarios a pensiones?','select',$yn,true),
  'afc'=>$f('¿Aportes AFC / AVC?','select',$yn,true), 'gmf'=>$f('¿GMF certificado?','select',$yn,true), 'donaciones'=>$f('¿Donaciones con certificados?','select',$yn,true),
  'compras'=>$f('¿Compras con factura electrónica y medio de pago verificable?','select',$yn,true), 'otros'=>$f('¿Otros beneficios, descuentos o incentivos?','select',$yn,true),
 ]],
];
