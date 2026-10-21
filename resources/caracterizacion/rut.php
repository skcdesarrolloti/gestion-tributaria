<?php
use App\Services\ContribuyenteData;
return [
 'identificacion'=>['title'=>'RUT · Identificación','guide'=>'Casillas 4–37. El NIT y nombre principal se conservan en Editar datos; aquí amplías la información del documento consultado. No se cambia el RUT en la DIAN.', 'fields'=>[
  'rut_estado'=>$f('¿Dispones del RUT?','select',['disponible'=>'Sí, lo tengo','pendiente'=>'No, por conseguir'],true),
  'rut_formulario'=>$f('4 · Número del formulario RUT'), 'rut_fecha'=>$f('Fecha de generación / consulta del RUT','date'),
  'rut_formalizacion'=>$f('61 · Fecha del trámite que consta en el RUT (no equivale a vencimiento)','date'),
  'tipo_contribuyente_rut'=>$f('24 · Tipo y código de contribuyente tal como figura en el RUT'), 'rut_buzon'=>$f('14 · Buzón electrónico'),
  'rut_estado_dian'=>$f('Estado actual según consulta oficial DIAN','select',['activo'=>'Activo, contrastado en consulta DIAN','suspendido'=>'Suspendido','cancelado'=>'Cancelado'],false,'La lectura del PDF no consulta ni certifica el estado actual ante la DIAN. Registra aquí el resultado de la consulta oficial y su referencia en el soporte del bloque.'),
  'rut_inscripcion'=>$f('Fecha de inscripción en el RUT','date'), 'rut_seccional'=>$f('12 · Dirección seccional (código y nombre)'),
  'identificacion_tipo'=>$f('25 · Tipo del documento de identidad','select',ContribuyenteData::DOCUMENTOS),
  'identificacion_numero'=>$f('26 · Número del documento de identidad (distinto del campo NIT si corresponde)'),
  'expedicion_fecha'=>$f('27 · Fecha de expedición del documento','date'),
  'expedicion_pais'=>$f('28 · País de expedición'), 'expedicion_departamento'=>$f('29 · Departamento de expedición'), 'expedicion_municipio'=>$f('30 · Ciudad / municipio de expedición'),
  'apellido1'=>$f('31 · Primer apellido'), 'apellido2'=>$f('32 · Segundo apellido'), 'nombre1'=>$f('33 · Primer nombre'), 'otros_nombres'=>$f('34 · Otros nombres'),
  'razon_social'=>$f('35 · Razón social'), 'nombre_comercial'=>$f('36 · Nombre comercial'), 'sigla'=>$f('37 · Sigla'),
  'nacimiento'=>$f('Fecha de nacimiento (complemento de planeación)','date'), 'nacionalidad'=>$f('Nacionalidad o nacionalidades (complemento)'),
 ]],
 'ubicacion'=>['title'=>'RUT · Ubicación y contacto','guide'=>'Casillas 38–45. Copia los nombres y códigos si los conoces; no confundas dirección de contacto con residencia fiscal.', 'fields'=>[
  'pais'=>$f('38 · País','text',[],true), 'departamento'=>$f('39 · Departamento / estado','text',[],true), 'municipio'=>$f('40 · Ciudad / municipio','text',[],true),
  'direccion'=>$f('41 · Dirección principal','text',[],true), 'correo'=>$f('42 · Correo electrónico','email',[],true), 'postal'=>$f('43 · Código postal'),
  'telefono1'=>$f('44 · Teléfono 1','text',[],true), 'telefono2'=>$f('45 · Teléfono 2'),
 ]],
 'actividades'=>['title'=>'RUT · Actividades económicas','guide'=>'Casillas 46–52. Catálogo DIAN de la Resolución 227/2025: clases CIIU y códigos especiales, incluidos 0010 y 0020. No se asigna un código solo por marcar una fuente de ingresos. Busca por código o descripción.', 'fields'=>[
  'principal'=>$f('46 · Actividad principal','select',$ciiu,true,'La principal debe corresponder a la clasificación del contribuyente y su RUT. Los códigos 0010, 0020, 0081, 0082 y 0090 son otras clasificaciones DIAN, no clases CIIU.'),
  'principal_inicio'=>$f('47 · Fecha inicio de actividad principal','date'), 'secundaria'=>$f('48 · Actividad secundaria','select',$ciiu), 'secundaria_inicio'=>$f('49 · Fecha inicio de actividad secundaria','date'),
  'otra1'=>$f('50 · Otra actividad 1','select',$ciiu), 'otra2'=>$f('50 · Otra actividad 2','select',$ciiu),
  'ocupacion'=>$f('51 · Ocupación (código y descripción)'), 'establecimientos'=>$f('52 · Número de establecimientos activos','integer'),
  'otras_revisadas'=>$f('¿Revisaste si existen actividades secundarias y otras?','select',$yn,true),
 ]],
 'responsabilidades'=>['title'=>'RUT · Responsabilidades','guide'=>'Casilla 53. Añade un renglón por código del RUT. Las descripciones son identificadores del catálogo de la DIAN, no una decisión automática sobre obligaciones o beneficios. No mezclar con códigos aduaneros de la casilla 54.', 'fields'=>[
  'sin_responsabilidades'=>$f('¿El RUT consultado tiene la casilla 53 sin códigos?','select',$yn,true),
  'otros_codigos'=>$f('Códigos que no encuentras en la lista (por verificar)'),
 ],'rows'=>['codigo'=>$f('53 · Código de responsabilidad','select',$responsabilidad,true)]],
 'entidad'=>['title'=>'RUT · Entidad, constitución y sucesión','allow_na'=>true,'guide'=>'Hoja 2 del RUT y complementos de representación. Para una persona natural viva puede no aplicar. No se crea una persona jurídica por el fallecimiento de una persona natural.', 'fields'=>[
  'naturaleza'=>$f('62 · Naturaleza (código y descripción)'), 'forma'=>$f('63–69 · Forma asociativa, fondo, cooperativa u otra organización (código y descripción)'),
  'beneficio'=>$f('70 · Estado / beneficio inscrito (código y descripción)'), 'documento_constitucion'=>$f('71–74 · Documento de constitución, número y notaría'),
  'constitucion_fecha'=>$f('73 · Fecha de constitución','date'), 'registro_entidad'=>$f('75 · Entidad de registro'), 'registro_fecha'=>$f('76 · Fecha de registro','date'), 'matricula'=>$f('77 · Matrícula mercantil'),
  'registro_lugar'=>$f('79 · Municipio de registro'), 'vigencia_desde'=>$f('80 · Vigencia desde','date'), 'vigencia_hasta'=>$f('81 · Vigencia hasta (de la entidad, no del RUT)','date')+['max_date'=>'9999-12-31'],
  'capital_nacional'=>$f('Capital nacional (%)','percent'), 'capital_extranjero'=>$f('Capital extranjero (%)','percent'), 'vinculacion'=>$f('Vinculación económica y entidades vinculadas','textarea'),
  'fallecido'=>$f('¿El titular falleció?','select',$yn,true), 'fallecimiento_fecha'=>$f('Fecha de fallecimiento','date'), 'sucesion_estado'=>$f('Estado de la sucesión','select',['sin_abrir'=>'Sin abrir','iliquida'=>'Ilíquida','liquidada'=>'Liquidada','no_aplica'=>'No aplica']),
  'liquidacion_fecha'=>$f('Fecha de liquidación / adjudicación de la sucesión','date'),
 ]],
 'aduanas'=>['title'=>'RUT · Aduanas y actividad exterior','allow_na'=>true,'guide'=>'Casillas 54–60. Registra los códigos que efectivamente figuran en el RUT; no se determina por esta captura la obligación aduanera. La fecha 61 se conserva en Identificación.', 'fields'=>[
  'formas_exportacion'=>$f('55 · Forma de exportación'), 'tipo_exportador'=>$f('56 · Tipo de exportador'), 'modo'=>$f('57 · Modo'), 'cpc'=>$f('58 · CPC'),
  'anexos'=>$f('59 · ¿Anexa documentos?','select',$yn), 'folios'=>$f('60 · Número de folios','integer'), 'organizaciones'=>$f('Organizaciones (complemento histórico; no corresponde a la casilla 61)'),
  'otros_codigos'=>$f('Códigos aduaneros no encontrados (por verificar)'),
 ],'rows'=>['codigo'=>$f('54 · Código de obligado / usuario aduanero','select',$aduana,true)]],
];
