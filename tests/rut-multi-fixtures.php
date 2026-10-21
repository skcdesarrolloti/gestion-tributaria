<?php
declare(strict_types=1);
require_once __DIR__.'/rut-fixtures.php';

function rutMultiFixture(string $nit='12345678', bool $mismatch=false): string
{
    preg_match('/stream\n(.*?)\nendstream/s',rutFixturePdf($nit),$m);$streams=[$m[1]];
    $data=[
        2=>[[125,569.5,'2'],[300,569.5,'12'],[130,459.5,'20200101'],[130,339.5,'30001231'],[530,483.5,'100'],[45,242.5,'01'],[140,242.5,'20200101']],
        3=>[[41,569.5,'REPRS LEGAL PRIN 18'],[330,567.5,'20200101'],[114,543.5,'13'],[140,543.5,'333444555'],[38,521.5,'REPRESENTANTE FICTICIO']],
        4=>[[110,567.5,'13'],[140,567.5,'444555666'],[38,545.5,'SOCIO FICTICIO'],[36,497.5,'10000'],[230,497.5,'33.33'],[330,497.5,'20200101']],
        5=>[[114,363.5,'13'],[140,363.5,'555666777'],[38,341.5,'CONTADOR FICTICIO'],[490,365.5,'12345'],[40,293.5,'20200101']],
        6=>[[38,557.5,'Sucursal 02'],[170,557.5,'0111'],[38,533.5,'LOCAL FICTICIO'],[38,509.5,'Departamento 11'],[210,509.5,'Ciudad 001'],[38,485.5,'DIRECCION FICTICIA'],[38,461.5,'12345'],[270,461.5,'20200101'],[38,437.5,'3000000000']],
        7=>[[45,543.5,'02'],[140,543.5,'20210101']],
    ];
    foreach ($data as $sheet=>$cells) {
        $cells=array_merge([[25,760,'Formulario 001 Hoja '.$sheet],[515,704.5,'99999999999'],[28,603.5,$mismatch&&$sheet===6?'87654321':$nit]],$cells);
        $stream=[];
        foreach ($cells as [$x,$y,$value]) { $stream[]='BT /F1 8 Tf 1 0 0 1 '.$x.' '.$y.' Tm ('.$value.') Tj ET'; }
        $streams[]=implode("\n",$stream);
    }
    return rutFixturePages($streams);
}
