<?php
declare(strict_types=1);
use App\Services\{RutPdfForm,RutPdfReader,RutImportPlan,CaracterizacionSchema,CaracterizacionData,RutImportRows};
require_once __DIR__.'/rut-multi-fixtures.php';
$rutMultiPath=tempnam(sys_get_temp_dir(),'rut-multi-');
try {
    file_put_contents($rutMultiPath,rutMultiFixture());
    $pdf=RutPdfForm::extract(RutPdfReader::read($rutMultiPath));
    check(count($pdf['lectura_hojas'])===7 && array_column($pdf['lectura_hojas'],'hoja')===[1,2,3,4,5,6,7],'Recorre todas las hojas incluida la principal, por su número real y no por su posición.');
    check(array_column($pdf['rows']['relacionados'],'rol')===['representante','socio_junta','contador'],'Lee representantes, hoja de socios/junta y contador sin inventar la calidad ambigua.');
    check($pdf['rows']['relacionados'][2]['documento']==='555666777' && $pdf['rows']['relacionados'][2]['tarjeta']==='12345','El contador de la tercera sección conserva su identificación y tarjeta.');
    check($pdf['fields']['entidad']['vigencia_hasta']==='3000-12-31' && str_contains($pdf['fields']['entidad']['estados_rut'],'2021-01-01'),'Conserva vigencia de entidad y estados de hojas 2 y 7 sin tratarlos como estado DIAN en línea.');
    $place=$pdf['rows']['establecimientos'][0];
    check($place['nombre']==='LOCAL FICTICIO' && $place['fecha_matricula']==='2020-01-01' && !isset($place['inicio'],$place['pais']),'Importa el establecimiento sin confundir matrícula con apertura ni inventar país.');
    $schema=CaracterizacionSchema::all(['ciiu'=>['0010'=>'Asalariados','0111'=>'Cultivo'],'responsabilidad'=>['05'=>'Renta','22'=>'Terceros']]);
    $tax=['tipo'=>'natural','documento_tipo'=>'CC','documento_numero'=>'12345678','dv'=>''];
    $plan=RutImportPlan::build($pdf,$tax,$schema,'2026');
    $preview=['file'=>['parsed'=>$pdf,'sha256'=>str_repeat('a',64)],'plan'=>$plan];
    $merged=RutImportPlan::merge($preview,array_keys($plan['items']),false,[],$schema,'natural');
    check(count($merged['updates']['relacionados']['filas'])===3 && count($merged['updates']['establecimientos']['filas'])===1,'Las filas de las hojas adicionales llegan a los campos del perfil.');
    check($merged['updates']['entidad']['campos']['vigencia_hasta']==='3000-12-31','El validador permite la vigencia larga de entidad que figura en el RUT.');
    $row=$merged['updates']['relacionados']['filas'][0];$row['nombre']='Nombre corregido manualmente';$row['nacionalidad']='Colombia';
    $changed=false;$rows=RutImportRows::merge([$row],$merged['updates']['relacionados']['filas'][0],'relacionados',false,$changed);
    check(count($rows)===1 && $rows[0]['nombre']==='Nombre corregido manualmente' && $rows[0]['nacionalidad']==='Colombia','Reimportar no duplica personas ni borra valores manuales.');
    $row=$merged['updates']['relacionados']['filas'][1];$row['rol']='junta';
    $rows=RutImportRows::merge([$row],$merged['updates']['relacionados']['filas'][1],'relacionados',true,$changed);
    check(count($rows)===1 && $rows[0]['rol']==='junta','Una relectura no borra la calidad socio/junta ya precisada por el usuario.');
    $savedPlace=$merged['updates']['establecimientos']['filas'][0];$incomingPlace=$savedPlace;$incomingPlace['direccion']='';$savedPlace['pais']='Colombia';
    $places=RutImportRows::merge([$savedPlace],$incomingPlace,'establecimientos',false,$changed);
    check(count($places)===1 && $places[0]['pais']==='Colombia','Reimportar un establecimiento no duplica el lugar ni borra el país completado manualmente.');
    $data=$merged['updates']['identificacion'];
    check(CaracterizacionData::status($data,$schema['identificacion'],'natural')==='Importado del RUT · pendiente de revisión','Explica el origen importado sin el término ambiguo falta contraste.');
    file_put_contents($rutMultiPath,rutMultiFixture('12345678',true));$rejected=false;
    try { RutPdfForm::extract(RutPdfReader::read($rutMultiPath)); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'Una hoja adicional con NIT diferente impide mezclar contribuyentes.');
} finally { unlink($rutMultiPath); }
