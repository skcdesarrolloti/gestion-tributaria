<?php
declare(strict_types=1);

use App\Services\{Exogena1001Fields,Exogena1001Data};

$blank1001=Exogena1001Data::read(['anio'=>'2025']);
check(count(Exogena1001Fields::all())===20 && count(Exogena1001Fields::money())===8,
    '1001 v10 contiene 20 campos y 8 importes del anexo oficial.');
check(count(array_filter(array_diff_key($blank1001,['anio'=>true])))===0, '1001 inicia vacío sin país ni importes asumidos.');
$payment1001=array_replace($blank1001,['concepto'=>'5004','documento_tipo'=>'31','identificacion'=>'001234567',
    'razon_social'=>'Entidad ficticia','pais'=>'169','direccion'=>'Dirección ficticia','departamento'=>'05','municipio'=>'001']);
foreach (Exogena1001Fields::money() as $field) { $payment1001[$field]='0'; }
$payment1001['pago_deducible']='1000000';
check(Exogena1001Data::errors($payment1001)===[], 'Pago documentado con ubicación Colombia y ceros explícitos.');
check(Exogena1001Data::errors(array_replace($payment1001,['direccion'=>'']))!==[], 'Colombia exige dirección.');
check(Exogena1001Data::errors(array_replace($payment1001,['municipio'=>'1']))!==[], 'No inventa ceros del código municipal.');
check(Exogena1001Data::errors(array_replace($payment1001,['pais'=>'249','direccion'=>'','departamento'=>'','municipio'=>'']))===[],
    'País exterior admite ubicación local vacía.');
check(Exogena1001Data::errors(array_replace($payment1001,['anio'=>'2026']))!==[], 'No aplica v10 AG2025 a 2026.');
check(Exogena1001Data::errors(array_replace($payment1001,['retencion_iva_exterior'=>'']))!==[], 'No convierte retención desconocida a cero.');
check(Exogena1001Data::errors(array_replace($payment1001,['pago_deducible'=>'999999999999999999']))===[], 'Importes de 18 dígitos sin float.');
check(Exogena1001Data::errors(array_replace($payment1001,['pago_deducible'=>'-1']))!==[], 'Rechaza negativos.');
