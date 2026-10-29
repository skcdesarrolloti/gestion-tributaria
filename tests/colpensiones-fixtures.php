<?php
declare(strict_types=1);
require_once __DIR__.'/rut-fixtures.php';

/** Modelo ficticio deliberadamente simple; no representa un comprobante real validado. */
function colpensionesFixture(string $document='55667788',string $period='ENERO DE 2025',string $gross='4.500.000,00',string $health='540.000,00'): string
{
    $lines=['COLPENSIONES','CERTIFICADO DE DEVENGADOS Y DEDUCIDOS','CEDULA: '.$document,'PERIODO: '.$period,
        'TOTAL DEVENGADO: $ '.$gross,'DEDUCCIONES','APORTE SALUD: $ '.$health,'TOTAL DEDUCCIONES: '.$health];
    $commands=[];foreach ($lines as $i=>$line) {
        $line=str_replace(['\\','(',')'],['\\\\','\\(','\\)'],$line);
        $commands[]='BT /F1 10 Tf 1 0 0 1 40 '.(740-$i*20).' Tm ('.$line.') Tj ET';
    }
    return rutFixturePages([implode("\n",$commands)]);
}
