<?php
declare(strict_types=1);

use App\Services\ProyeccionSummary;

$concepts=[['casilla'=>58,'clase'=>'ingreso'],['casilla'=>60,'clase'=>'reduccion'],['casilla'=>61,'clase'=>'calculado']];
$movements=[
    ['casilla'=>58,'estado'=>'real','valor_cop'=>1000000,'fecha'=>'2026-10-01'],
    ['casilla'=>58,'estado'=>'proyectado','valor_cop'=>2000000,'fecha'=>'2026-11-01'],
    ['casilla'=>60,'estado'=>'real','valor_cop'=>300000,'fecha'=>'2026-10-02'],
    ['casilla'=>58,'estado'=>'proyectado','valor_cop'=>500000,'fecha'=>'2026-09-01'],
    ['casilla'=>61,'estado'=>'real','valor_cop'=>700000,'fecha'=>'2026-10-01'],
];
$result=ProyeccionSummary::build($concepts,$movements,'2026-10-14');
check($result['total']['real']['ingreso']===1000000, 'No duplica casillas derivadas como ingreso.');
check($result['total']['real']['reduccion']===300000, 'Reducciones separadas de ingresos brutos.');
check($result['total']['proyectado']['ingreso']===2500000, 'Proyección conserva solo lo adicional.');
check($result['meses'][10]['registros']===2 && $result['meses'][11]['proyectado']['ingreso']===2000000,
    'Asignación mensual de partidas sin derivados.');
check($result['meses'][12]['registros']===0 && count($result['meses'])===12, 'Meses vacíos quedan identificados.');
check($result['proyecciones_por_revisar']===1, 'Proyección pasada requiere revisión y no se vuelve real automáticamente.');
$movements[1]['estado']='real';
$changed=ProyeccionSummary::build($concepts,$movements,'2026-12-01');
check($changed['total']['real']['ingreso']+$changed['total']['proyectado']['ingreso']===3500000,
    'Conversión de proyectado a real mantiene cierre sin duplicar ingresos.');
check(ProyeccionSummary::build($concepts,[],'2026-10-14')['total']['registros']===0, 'Perfil sin datos no presume cobertura.');
