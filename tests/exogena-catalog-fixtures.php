<?php
declare(strict_types=1);

function catalogFixture(array $schema): array
{
    $record=App\Services\ExogenaSchemaData::read(['anio'=>'2025'],$schema);
    foreach ($schema['fields'] as $key=>[$label,$kind,$max,$required]) {
        if (!$required) { continue; }
        $record[$key]=match($kind) { 'money','digits'=>'0','code'=>'1','id'=>'900000001',default=>'Dato ficticio' };
    }
    foreach (['cpt'=>'1301','tdoc'=>'31','tdocb'=>'13','tdopa'=>'31','nidpa'=>'900000002',
        'raz'=>'Entidad <b>ficticia</b>','pap'=>'Apellido','pno'=>'Nombre','dir'=>'Dirección ficticia',
        'dpto'=>'05','mun'=>'001','mcpo'=>'001','pais'=>'169','tcon'=>'1','entinfo'=>'1','por'=>'100','dec'=>'0'] as $key=>$value) {
        if (array_key_exists($key,$record)) { $record[$key]=$value; }
    }
    return $record;
}
