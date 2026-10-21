<?php
declare(strict_types=1);
use App\Services\{RutPdfForm,RutPdfReader,RutImportPlan,CaracterizacionSchema,CaracterizacionData};
require_once __DIR__.'/rut-fixtures.php';
$rutPath=tempnam(sys_get_temp_dir(),'rut-test-');
try {
    file_put_contents($rutPath,rutFixturePdf());
    $rut=RutPdfForm::extract(RutPdfReader::read($rutPath));
    check($rut['nit']==='12345678' && $rut['dv']===RutPdfForm::digit('12345678'),'Lee NIT y verifica dígito desde el PDF, sin confiar en el nombre del archivo.');
    check($rut['fields']['actividades']['principal']==='0010' && $rut['fields']['actividades']['principal_inicio']==='2020-01-01','Conserva ceros iniciales y normaliza fecha de actividad.');
    check(array_column($rut['rows']['responsabilidades'],'codigo')===['05','22'],'Extrae los códigos de responsabilidad como renglones separados.');
    check($rut['formalizado']==='2022-08-08' && $rut['estado_dian']==='pendiente_consulta_oficial','Fecha de trámite antigua no se transforma en vencimiento ni estado oficial verificado.');
    $schemas=CaracterizacionSchema::all(['ciiu'=>['0010'=>'Asalariados'],'responsabilidad'=>['05'=>'Renta','22'=>'Terceros']]);
    $tax=['tipo'=>'natural','documento_tipo'=>'CC','documento_numero'=>'12345678','dv'=>''];
    $plan=RutImportPlan::build($rut,$tax,$schemas,'2026'); $preview=['file'=>['parsed'=>$rut,'sha256'=>str_repeat('a',64)],'plan'=>$plan];
    $blocks=['ubicacion'=>['datos'=>CaracterizacionData::read(['campos'=>['direccion'=>'Dato previo del usuario']],$schemas['ubicacion'])]];
    $merge=RutImportPlan::merge($preview,array_keys($plan['items']),false,$blocks,$schemas,'natural');
    check($merge['updates']['ubicacion']['campos']['direccion']==='Dato previo del usuario' && count($merge['skipped'])===1,'Importación por defecto conserva datos distintos y no borra otros campos.');
    $merge=RutImportPlan::merge($preview,array_keys($plan['items']),true,$blocks,$schemas,'natural');
    check($merge['updates']['ubicacion']['campos']['direccion']==='Direccion ficticia 1','Reemplazo solo cuando se elige expresamente.');
    check(!isset($merge['updates']['fiscal']) && !isset($merge['updates']['patrimonio']),'No inventa residencia, régimen anual ni patrimonio a partir del RUT.');
    $mismatch=false;
    try { RutImportPlan::build($rut,array_replace($tax,['documento_numero'=>'99999999']),$schemas,'2026'); } catch (InvalidArgumentException) { $mismatch=true; }
    check($mismatch,'No importa el RUT de otra identificación.');
    file_put_contents($rutPath,'No es un PDF'); $invalid=false;
    try { RutPdfReader::read($rutPath); } catch (InvalidArgumentException) { $invalid=true; }
    check($invalid,'Rechaza archivos que solo se renombran como PDF.');
} finally { unlink($rutPath); }
