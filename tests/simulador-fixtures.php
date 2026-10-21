<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/app/Support/helpers.php';

function simulatorFixture(): array
{
    return \App\Services\SimuladorFields::read(['nombre'=>'Escenario ficticio','pensionado'=>'no','servicios'=>'no_aplica',
        'declaraciones'=>'3','metodo_anticipo'=>'actual','ceros_confirmados'=>'1','elegibilidad'=>'1']);
}

function simulatorProfile(): array
{
    return ['tipo'=>'natural','residencia'=>'residente','regimen'=>'ordinario','anio'=>'2026'];
}
