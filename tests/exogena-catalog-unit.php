<?php
declare(strict_types=1);

use App\Services\{ExogenaSchemas,ExogenaSchemaData,ExogenaFormats};
require_once __DIR__.'/exogena-catalog-fixtures.php';

$expected=['1003'=>14,'1005'=>10,'1006'=>11,'1008'=>14,'1009'=>14,'1010'=>16,'2276'=>45,
    '5249'=>14,'5250'=>15,'5251'=>18,'5252'=>18];
check(count(ExogenaFormats::all())===14 && count(ExogenaFormats::grouped())===6, 'Catorce formatos distribuidos en seis grupos.');
foreach ($expected as $code=>$count) {
    $schema=ExogenaSchemas::get($code);
    check(count($schema['fields'])===$count, "Campos verificados del anexo $code.");
    $blank=ExogenaSchemaData::read(['anio'=>'2025'],$schema);
    check(count(array_filter(array_diff_key($blank,['anio'=>true]),static fn($v)=>$v!==''))===0, "$code inicia completamente vacío.");
    check(ExogenaSchemaData::errors(catalogFixture($schema),$schema)===[], "$code admite captura estructural ficticia válida.");
    $sectionKeys=[];
    foreach (ExogenaSchemas::sections($schema) as $section) { array_push($sectionKeys,...array_keys($section['fields'])); }
    check($sectionKeys===array_keys($schema['fields']), "$code conserva orden y totalidad al agrupar campos.");
    $fixture=catalogFixture($schema);
    $fixture[$schema['amount']]='';
    check(ExogenaSchemaData::errors($fixture,$schema)!==[], "$code no convierte importes desconocidos en cero.");
}
$socio=ExogenaSchemas::get('1010');$fixture=catalogFixture($socio);
check(ExogenaSchemaData::errors(array_replace($fixture,['por'=>'1278915','dec'=>'5']),$socio)===[], 'Porcentaje 12.78915 conserva posición decimal.');
check(ExogenaSchemaData::errors(array_replace($fixture,['por'=>'1001','dec'=>'1']),$socio)!==[], 'No permite participación de 100.1%.');
check(ExogenaSchemaData::errors(array_replace($fixture,['por'=>'1','dec'=>'21']),$socio)!==[], 'Posiciones decimales máximas verificadas.');
$salary=ExogenaSchemas::get('2276');$fixture=catalogFixture($salary);
check(ExogenaSchemaData::errors(array_replace($fixture,['tdocde'=>'13']),$salary)!==[], 'Dependiente requiere tipo y número juntos.');
check(ExogenaSchemaData::errors(array_replace($fixture,['identfc'=>'123456789012345']),$salary)!==[], 'Fideicomiso 2276 conserva longitud de catorce dígitos de la tabla.');
foreach (['5249','5250','5251','5252'] as $code) {
    $schema=ExogenaSchemas::get($code);$fixture=catalogFixture($schema);$fixture['tcon']='3';
    check(ExogenaSchemaData::errors($fixture,$schema)!==[], "$code exige NIESPJ para tipo 3.");
    $fixture['niespj']='900000003';check(ExogenaSchemaData::errors($fixture,$schema)===[], "$code acepta NIESPJ documentado.");
}
check(ExogenaSchemas::money('99999999999999999999')==='99.999.999.999.999.999.999', 'Importes de veinte dígitos no pierden precisión al mostrarse.');
check(!ExogenaSchemas::has('../config/local'), 'Rutas de esquema restringidas a códigos conocidos.');
