<?php
use App\Services\{RetencionTarifa,RetencionLaboral,RetencionDatos,NominaReserva,NominaData};

$retUvt=52374;
check(RetencionTarifa::calculate(95*$retUvt*100,$retUvt)===0,'Retención: límite de 95 UVT sin impuesto.');
check(RetencionTarifa::calculate(100*$retUvt*100,$retUvt)===4975530,'Retención: tramo 19% sin confundir tabla anual.');
check(RetencionTarifa::calculate(200*$retUvt*100,$retUvt)===125697600,'Retención: tramo 28% y componente fijo.');
foreach ([95,150,360,640,945,2300] as $boundary) {
    check(RetencionTarifa::calculate($boundary*$retUvt*100,$retUvt)>=0,'Retención: frontera '.$boundary.' UVT.');
}
check(RetencionTarifa::rounded(149999,'miles')===100000 && RetencionTarifa::rounded(150000,'miles')===200000,'Redondeo del pagador explícito.');
$retValues=array_fill_keys(NominaData::FIELDS,0);
$retValues=array_replace($retValues,['salario'=>500000000,'devengado'=>500000000,'salud'=>20000000,'pension'=>20000000,'retencion'=>15000000,'neto'=>445000000,'deducido'=>55000000]);
$retRow=['id'=>1,'revision'=>1,'huella'=>str_repeat('a',64),'inicio'=>'2026-01-01','fin'=>'2026-01-15','empleador_documento'=>'999888777',
    'datos'=>['empleador'=>'Pagador ficticio','valores'=>$retValues]];
$retRows=[$retRow,array_replace($retRow,['id'=>2,'inicio'=>'2026-01-16','fin'=>'2026-01-30'])];
$retGroups=RetencionLaboral::groups($retRows);$retGroup=reset($retGroups);
check($retGroup['completo'] && RetencionLaboral::check($retGroup,[],2026)['estado']==='Pendiente de soporte','Mes completo sin soporte no se presume OK.');
$retSingle=RetencionLaboral::groups([$retRow]);
check(!reset($retSingle)['completo'],'Quincena única no es mes completo.');
$retPost=['vivienda'=>'0','prepagada'=>'0','usado25'=>'0','usado1340'=>'0','dependientes'=>'no','procedimiento'=>'1','redondeo'=>'peso',
    'completo'=>'1','ordinario'=>'1','verificado'=>'1','soporte'=>'Depuración mensual ficticia verificada'];
$retReview=RetencionDatos::read($retPost)+['huella'=>$retGroup['huella']];
$retResult=RetencionLaboral::check($retGroup,$retReview,2026);
check($retResult['base']===690000000 && $retResult['exencion25']===230000000,'Base: diez millones menos aportes y exención 25%, sin topes divididos por doce.');
check($retResult['esperada']===36564900 && $retResult['estado']==='Revisar','Retención esperada independiente y diferencia detectada.');
$retGroup['valores']['retencion']=$retResult['esperada'];
check(RetencionLaboral::check($retGroup,$retReview,2026)['estado']==='OK','OK solo al coincidir con el cálculo soportado.');
$retCap=$retReview;$retCap['usado25']=790*$retUvt*100;
check(RetencionLaboral::check($retGroup,$retCap,2026)['exencion25']===0,'Exención anual consumida no se reutiliza.');
$retCap['usado1340']=1340*$retUvt*100;
check(RetencionLaboral::check($retGroup,$retCap,2026)['base']===920000000,'Límite global anual agotado no reduce base.');
$retProc2=RetencionDatos::read(array_replace($retPost,['procedimiento'=>'2','porcentaje'=>'7.25']))+['huella'=>$retGroup['huella']];
check(RetencionLaboral::check($retGroup,$retProc2,2026)['esperada']===50025000,'Procedimiento 2 aplica porcentaje certificado a base depurada.');
$retPrior=$retRows; $retPrior[]=array_replace($retRow,['id'=>3,'inicio'=>'2026-02-01','fin'=>'2026-02-28']);
$retBefore=RetencionLaboral::groups($retPrior);$retPrior[0]['revision']=2;$retAfter=RetencionLaboral::groups($retPrior);
check($retBefore['2-999888777']['huella']!==$retAfter['2-999888777']['huella'],'Corregir enero invalida topes certificados de febrero.');
$retOther=array_replace($retRow,['id'=>9,'empleador_documento'=>'999888778']);
check(count(RetencionLaboral::groups([...$retRows,$retOther]))===2,'Pagadores separados aunque comparten mes.');
foreach (['prepagada'=>'','usado25'=>'-1','procedimiento'=>'3','verificado'=>'','porcentaje'=>'99'] as $field=>$bad) {
    $rejected=false;try { RetencionDatos::read(array_replace($retPost,['procedimiento'=>'2','porcentaje'=>'1'],[$field=>$bad])); } catch (InvalidArgumentException) { $rejected=true; }
    check($rejected,'No acepta dato incompleto o inválido: '.$field);
}
$reserve=NominaReserva::read(['futuro_bruto'=>'110000000','futuro_aportes'=>'8800000','futuro_retencion'=>'4000000',
    'anticipo_anterior'=>'0','saldo_favor'=>'0','declaraciones'=>'3','cuotas'=>'6','hipotesis'=>'1']);
$reserve['huella']=NominaReserva::fingerprint($retRows);
$reserveResult=NominaReserva::calculate($retRows,$reserve,['tipo'=>'natural','residencia'=>'residente','regimen'=>'ordinario','anio'=>'2026']);
check($reserveResult['estado']==='calculado' && $reserveResult['retenido_real']===300000 && $reserveResult['retenciones']===4300000,'Reserva distingue retenciones reales y futuras, sin duplicar reales.');
check($reserveResult['reserva']===(int)ceil($reserveResult['saldo_pagar']/6),'Reserva distribuye saldo con anticipo y créditos.');
$retRows[0]['revision']=5;
check(NominaReserva::calculate($retRows,$reserve,[])['estado']==='pendiente','Importación corregida exige confirmar proyección.');
$guide=App\Services\RetencionGuia::field('procedimiento');
check(isset($guide['choices']['1'],$guide['choices']['2'],$guide['choices']['pendiente']) && str_contains($guide['hint'],'empleador'),
    'Ayuda explica cada procedimiento y qué hacer si no se conoce.');
foreach (array_merge(array_keys(RetencionDatos::MONEY),['procedimiento','dependientes','redondeo','porcentaje','soporte','completo','ordinario','verificado'],
    array_keys(NominaReserva::MONEY),['declaraciones','cuotas','hipotesis']) as $helpKey) {
    check(App\Services\RetencionGuia::field($helpKey)['hint']!=='','Todo campo de retención y reserva tiene contexto: '.$helpKey);
}
$exampleSource=$retGroup;$exampleResult=App\Services\RetencionEjemplo::build($retGroup,2026);
check($retGroup===$exampleSource && !isset($exampleResult['estado']) && $exampleResult['esperada']===36564900,
    'Ejemplo calcula sin alterar nómina ni producir un estado OK.');
$retPendingInput=false;
try { RetencionDatos::read(array_replace($retPost,['procedimiento'=>'pendiente'])); }
catch (InvalidArgumentException $error) { $retPendingInput=str_contains($error->getMessage(),'Todavía falta confirmar'); }
check($retPendingInput,'No lo sé no se transforma en un procedimiento ni en validación.');
$retLatestGroups=['1-999888777'=>array_replace($retGroup,['mes'=>1]),'2-999888777'=>array_replace($retGroup,['mes'=>2,'completo'=>false])];
check(RetencionLaboral::latestKey($retLatestGroups)==='1-999888777','Acceso directo abre último mes completo y no un mes parcial.');
