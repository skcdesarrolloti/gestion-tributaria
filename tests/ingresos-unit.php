<?php
declare(strict_types=1);

use App\Services\{IngresoData, IngresoSummary};
use App\Services\PerfilGuidance;

$profile=['anio'=>2025,'formulario'=>'210','servicios'=>'costos'];
$concept=['id'=>1,'casilla'=>58,'clase'=>'ingreso'];
$movement=['concepto_id'=>'1','fecha'=>'2025-09-30','estado'=>'real','descripcion'=>'Interés de ejemplo',
    'tercero'=>'Entidad ficticia','pais'=>'Colombia','moneda'=>'COP','valor_original'=>'2000000',
    'conversion'=>'','valor_cop'=>'2000000','soporte'=>'Certificado ficticio','justificacion'=>''];
check(IngresoData::movementErrors($movement,$profile,$concept)===[], 'Ingreso válido con soporte.');
check(IngresoData::movementErrors(array_replace($movement,['fecha'=>'2025-02-30']),$profile,$concept)!==[], 'Fecha imposible rechazada.');
check(IngresoData::movementErrors(array_replace($movement,['fecha'=>'2026-01-01']),$profile,$concept)!==[], 'Movimiento pertenece a su año.');
check(IngresoData::movementErrors(array_replace($movement,['valor_cop'=>'2e6']),$profile,$concept)!==[], 'Importes sin notación científica.');
check(IngresoData::movementErrors(array_replace($movement,['moneda'=>'USD']),$profile,$concept)!==[], 'Conversión extranjera requiere respaldo.');
check(IngresoData::movementErrors($movement,$profile,array_replace($concept,['clase'=>'reduccion']))!==[], 'Reducción exige fundamento particular.');
check(!IngresoData::allowed(['clase'=>'ingreso','casilla'=>43],array_replace($profile,['servicios'=>'pendiente'])), 'Servicios sujetos a opción de costos.');
check(!IngresoData::allowed(['clase'=>'calculado','casilla'=>61],$profile), 'Totales no capturables.');
$concepts=[['casilla'=>58,'clase'=>'ingreso','formula'=>''],['casilla'=>59,'clase'=>'reduccion','formula'=>''],
    ['casilla'=>60,'clase'=>'reduccion','formula'=>''],['casilla'=>61,'clase'=>'calculado','formula'=>'58-59-60']];
$summary=IngresoSummary::build($concepts,[['casilla'=>58,'estado'=>'real','valor_cop'=>2000000],
    ['casilla'=>59,'estado'=>'real','valor_cop'=>100000],['casilla'=>60,'estado'=>'real','valor_cop'=>300000],
    ['casilla'=>58,'estado'=>'proyectado','valor_cop'=>500000]]);
check($summary[61]===['real'=>1600000,'proyectado'=>500000], 'Depuración separa real y proyección adicional.');
check(IngresoSummary::build($concepts,[['casilla'=>60,'estado'=>'real','valor_cop'=>100000]])[61]['real']===-100000,
    'No oculta inconsistencias negativas como ingresos cero.');
$annual=['anio'=>'2025','residencia'=>'residente','regimen'=>'ordinario','servicios'=>'no_aplica','soporte_perfil'=>'Evidencia ficticia'];
check(IngresoData::profileErrors($annual,'juridica')===[], 'Entidad admite no aplica sin confundir con pendiente.');
check(IngresoData::profileErrors(array_replace($annual,['servicios'=>'costos']),'juridica')!==[], 'Empresa no elige exención/costos personales.');
check(isset(PerfilGuidance::fields('natural')['regimen']['options']['simple']), 'SIMPLE visible como consulta.');
check(isset(PerfilGuidance::fields('juridica')['regimen']['options']['especial']), 'RTE visible para entidades.');
check(!isset(PerfilGuidance::fields('sucesion')['regimen']['options']['simple']), 'No ofrece SIMPLE a sucesiones por defecto.');
$entity=['formulario'=>'110','tipo'=>'juridica','residencia'=>'residente'];
check(!IngresoData::allowed(['clase'=>'ingreso','casilla'=>52],$entity), 'Sociedad no captura dividendos exclusivos de persona natural no residente.');
check(!IngresoData::allowed(['clase'=>'ingreso','casilla'=>54],$entity), 'Sociedad residente no recibe casilla de receptor extranjero.');
check(IngresoData::allowed(['clase'=>'ingreso','casilla'=>52],['formulario'=>'110','tipo'=>'natural','residencia'=>'no_residente']), 'No residente natural mantiene su casilla 52.');
$all110=array_map(static fn($code)=>['casilla'=>$code,'clase'=>'ingreso','formula'=>''],range(47,57));
$all110[]=['casilla'=>58,'clase'=>'calculado','formula'=>'47+48+49+50+51+52+53+54+55+56+57'];
$all110[]=['casilla'=>80,'clase'=>'ingreso','formula'=>''];
$sum110=IngresoSummary::build($all110,[['casilla'=>49,'estado'=>'real','valor_cop'=>100],['casilla'=>80,'estado'=>'real','valor_cop'=>900]]);
check($sum110[58]['real']===100, 'Total bruto 110 incorpora dividendos y separa ganancias ocasionales.');
