<?php
declare(strict_types=1);
use App\Services\{PerfilAlcance,PerfilAyuda,CaracterizacionSchema,CaracterizacionData};
$all=CaracterizacionSchema::all();
$personal=PerfilAlcance::schema($all['seguridad'],false,'natural');
$aportes=PerfilAlcance::schema($all['seguridad'],true,'natural');
check(isset($personal['fields']['tipo_pension']) && !isset($personal['fields']['eps']) && isset($aportes['fields']['eps']),'El perfil conserva condición pensional y traslada afiliaciones a Seguridad social.');
$saved=CaracterizacionData::read(['campos'=>['tipo_pension'=>'vejez','eps'=>'Asegurador ficticio']],$all['seguridad']);
$posted=CaracterizacionData::read(['campos'=>['tipo_pension'=>'invalidez','eps'=>'No debe reemplazar']],$personal);
$merged=PerfilAlcance::preserve($saved,$posted,$personal,$all['seguridad'],[]);
check($merged['campos']['eps']==='Asegurador ficticio' && $merged['campos']['tipo_pension']==='invalidez','Editar condición personal no borra ni admite modificar afiliaciones desde ese formulario.');
$posted=CaracterizacionData::read(['campos'=>['eps'=>'Nuevo asegurador']],$aportes);
$merged=PerfilAlcance::preserve($merged,$posted,$aportes,$all['seguridad'],[]);
check($merged['campos']['tipo_pension']==='invalidez','Guardar el componente de aportes conserva la condición personal.');
$full=$all['relacionados'];$visible=PerfilAlcance::schema($full,false,'natural');
$saved=CaracterizacionData::read(['filas'=>[
 ['rol'=>'dependiente','nombre'=>'Uno','documento'=>'1','dependencia'=>'Certificado uno','reclamado'=>'no'],
 ['rol'=>'dependiente','nombre'=>'Dos','documento'=>'2','dependencia'=>'Certificado dos','reclamado'=>'si'],
]],$full);
$raw=[['rol'=>'dependiente','nombre'=>'Dos editado','documento'=>'2','_origen'=>'1','dependencia'=>'Inyección ignorada']];
$posted=CaracterizacionData::read(['filas'=>$raw],$visible);
$merged=PerfilAlcance::preserve($saved,$posted,$visible,$full,$raw);
check(count($merged['filas'])===1 && $merged['filas'][0]['dependencia']==='Certificado dos' && $merged['filas'][0]['reclamado']==='si','Quitar/reordenar personas no cruza ni borra los soportes de beneficios que quedan en otro componente.');
$caught=false;try { PerfilAlcance::preserve($saved,$posted,$visible,$full,[array_replace($raw[0],['_origen'=>'99'])]); } catch(InvalidArgumentException) { $caught=true; }
check($caught,'Rechaza referencias de renglones que no existen en la sección.');
$fiscal=PerfilAlcance::schema($all['fiscal'],false,'natural');
check(isset($fiscal['fields']['residencia']) && !isset($fiscal['fields']['obligacion_renta']),'La obligación de declarar se revisa fuera del perfil.');
$company=PerfilAlcance::schema($all['entidad'],false,'juridica');
check(!isset($company['fields']['fallecido']),'No pregunta si falleció el titular de una persona jurídica.');
check(str_contains(PerfilAyuda::text('identificacion','nacimiento',$all['identificacion']['fields']['nacimiento']),'no casilla del RUT'),'La ayuda no presenta la fecha de nacimiento como dato del formulario DIAN.');
