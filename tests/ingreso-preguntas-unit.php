<?php
declare(strict_types=1);
use App\Services\{IngresoPreguntas,RutRevision};
$setup=IngresoPreguntas::prepare([],'natural');
check($setup['pending']===13 && $setup['answers']['pensiones']==='', 'Pregunta todas las fuentes faltantes sin tratar el vacío como No.');
check(!isset(IngresoPreguntas::groups('juridica')['actividad']['fields']['pensiones']), 'No pregunta pensiones personales a una sociedad.');
$saved=['campos'=>['salarios'=>'si','arriendos'=>'si','perdidas'=>'si'],'notas'=>'Nota preservada','estado'=>'documentado'];
$merged=IngresoPreguntas::merge($saved,'natural','actividad',['salarios'=>'si','honorarios'=>'no','pensiones'=>'si','negocios'=>'']);
check($merged['campos']['arriendos']==='si' && $merged['campos']['perdidas']==='si' && $merged['notas']==='Nota preservada' && $merged['estado']==='borrador', 'Actualiza un grupo sin borrar respuestas de otros componentes ni mantener contraste documental indebido.');
$invalid=false;try { IngresoPreguntas::merge($saved,'natural','capital',['arriendos'=>'si','rendimientos'=>'no','dividendos'=>['si']]); } catch (InvalidArgumentException) { $invalid=true; }
check($invalid,'Rechaza respuestas anidadas en el cuestionario.');
check(IngresoPreguntas::profileErrors(['residencia'=>'residente','servicios'=>'no_aplica'],['honorarios'=>'si'],'natural')!==[], 'Honorarios confirmados no admiten tratamiento No aplica contradictorio.');
$audit=RutRevision::build(['pages'=>1,'fields'=>['actividades'=>['principal'=>'0010','secundaria'=>'7490','otra2'=>'4290']]],['actividades'=>['datos'=>['campos'=>['principal'=>'0010','secundaria'=>'9999']]]],App\Services\CaracterizacionSchema::all(),['documento_tipo'=>'CC']);
check($audit['counts']===['coincide'=>1,'pendiente'=>1,'diferente'=>1] && count($audit['pages'])===1,'Contraste distingue coincidencias, faltantes y diferencias e incluye hoja principal de documentos antiguos.');
check($audit['groups']['actividades']['rows'][1]['current']==='9999','El contraste conserva la edición manual en vez de reemplazarla.');
$people=['rows'=>['relacionados'=>[['rol'=>'socio_junta','documento'=>'123','nombre'=>'Persona ficticia','soporte'=>'Página 4 renglón 1'],['rol'=>'contador','documento'=>'456','nombre'=>'Profesional ficticio','soporte'=>'Página 5']]]];
$peopleBlocks=['relacionados'=>['datos'=>['filas'=>[['rol'=>'junta','documento'=>'123','nombre'=>'Persona ficticia','soporte'=>'Página 4 renglón 1']]]]];
$peopleAudit=RutRevision::build($people,$peopleBlocks,App\Services\CaracterizacionSchema::all(),['documento_tipo'=>'CC']);
check(isset($peopleAudit['groups']['relacionados-socio_junta'],$peopleAudit['groups']['relacionados-contador']) && $peopleAudit['groups']['relacionados-socio_junta']['rows'][0]['current']==='junta','Separa personas por naturaleza y muestra la calidad precisada manualmente sin ocultarla como coincidencia.');
