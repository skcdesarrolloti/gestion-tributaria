<?php
use App\Services\ContribuyenteData;
return [
 'relacionados'=>['title'=>'Detalle · Representantes, familia y socios','allow_na'=>true,'guide'=>'Hojas 3–5 del RUT y complemento de dependientes. Añade las personas necesarias; los datos no acreditan automáticamente una deducción ni una representación.', 'fields'=>[], 'rows'=>[
  'rol'=>$f('Relación','select',['representante'=>'Representante legal','apoderado'=>'Apoderado','heredero'=>'Representante de sucesión / heredero','socio'=>'Socio / accionista','junta'=>'Miembro de junta','contador'=>'Contador','revisor'=>'Revisor fiscal','conyuge'=>'Cónyuge / compañero(a)','dependiente'=>'Dependiente económico','otro'=>'Otro: explicar'],true),
  'nombre'=>$f('Nombre completo / razón social','text',[],true), 'documento_tipo'=>$f('Tipo de documento','select',ContribuyenteData::DOCUMENTOS,true), 'documento'=>$f('Número de identificación','text',[],true), 'dv'=>$f('DV del NIT'),
  'nacionalidad'=>$f('Nacionalidad / país'), 'parentesco'=>$f('Parentesco o calidad de representación'), 'nacimiento'=>$f('Fecha de nacimiento','date'),
  'ingreso'=>$f('Fecha de ingreso / inicio de representación','date'), 'retiro'=>$f('Fecha de retiro / terminación','date'), 'participacion'=>$f('Participación (%)','percent'), 'capital'=>$f('Capital del socio (COP)','integer'),
  'tarjeta'=>$f('Tarjeta profesional del contador / revisor'), 'soporte'=>$f('Documento, poder, registro civil o certificado que respalda la relación','text',[],true),
  'dependencia'=>$f('Condiciones de dependencia, ingresos propios, estudios o discapacidad','textarea'), 'reclamado'=>$f('¿Otra persona solicita beneficio por este dependiente?','select',$yn),
 ]],
 'establecimientos'=>['title'=>'Detalle · Establecimientos','allow_na'=>true,'guide'=>'Hoja 6 del RUT. Registra cada establecimiento, agencia, sucursal, oficina u otro lugar de actividad. El conteo se contrasta con la casilla 52; no actualiza la DIAN.', 'fields'=>[], 'rows'=>[
  'tipo'=>$f('Tipo','select',['establecimiento'=>'Establecimiento','agencia'=>'Agencia','sucursal'=>'Sucursal','oficina'=>'Oficina','otro'=>'Otro'],true), 'nombre'=>$f('Nombre','text',[],true),
  'actividad'=>$f('Actividad económica','select',$ciiu,true), 'pais'=>$f('País','text',[],true), 'departamento'=>$f('Departamento / estado'), 'ciudad'=>$f('Ciudad','text',[],true), 'direccion'=>$f('Dirección','text',[],true),
  'matricula'=>$f('Matrícula mercantil / registro'), 'inicio'=>$f('Fecha de apertura','date'), 'cierre'=>$f('Fecha de cierre','date'),
 ]],
 'patrimonio'=>['title'=>'Detalle · Patrimonio, cuentas y deudas','guide'=>'ET 261, 269, 288 y 607. Incluye Colombia y exterior, cuentas bancarias, inversiones, participaciones, bienes, derechos y deudas; no se consideran deducciones de renta por el solo registro. No se calcula valoración fiscal ni conversión automática de moneda.', 'fields'=>[
  'tiene_activos'=>$f('¿Tiene bienes, derechos, cuentas o inversiones?','select',$yn,true), 'tiene_deudas'=>$f('¿Tiene deudas?','select',$yn,true),
  'exterior'=>$f('¿Tiene activos o cuentas en el exterior?','select',$yn,true), 'exterior_revisado'=>$f('¿Revisó expresamente las cuentas y activos del exterior?','select',$yn,true),
 ],'rows'=>[
  'tipo'=>$f('Tipo de partida','select',['cuenta'=>'Cuenta bancaria','inmueble'=>'Inmueble','inversion'=>'Inversión / acciones / participación','vehiculo'=>'Vehículo','cripto'=>'Criptoactivo','derecho'=>'Otro bien / derecho','deuda'=>'Deuda'],true),
  'descripcion'=>$f('Descripción / entidad financiera o acreedor','text',[],true), 'pais'=>$f('País de localización','text',[],true), 'moneda'=>$f('Moneda (por ejemplo COP, USD, EUR)','text',[],true),
  'referencia'=>$f('Referencia / identificación del bien (cuenta: últimos dígitos)'), 'porcentaje'=>$f('Porcentaje de titularidad','percent',[],true), 'valor_origen'=>$f('Valor en moneda de origen (sin separadores)','decimal'), 'valor_fiscal'=>$f('Valor fiscal en COP a la fecha de corte (sin separadores)','integer',[],true),
  'fecha_corte'=>$f('Fecha de corte del valor','date',[],true), 'soporte'=>$f('Certificado / escritura / extracto / soporte de deuda','text',[],true),
 ]],
];
