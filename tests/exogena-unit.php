<?php
declare(strict_types=1);

use App\Services\{Exogena5247Fields, Exogena5247Data};

check(count(Exogena5247Fields::all())===22, '5247 conserva las 22 columnas de la plantilla.');
check(array_column(array_values(Exogena5247Fields::all()),0)===range('A','V'), 'Orden A–V sin columnas omitidas.');
$blank5247=Exogena5247Data::read(['anio'=>Exogena5247Fields::DEFAULT_YEAR]);
check($blank5247['anio']==='2025' && count(array_filter(array_diff_key($blank5247,['anio'=>true]),
    static fn($value)=>$value!==''))===0, 'Captura inicial 2025 mantiene todos los campos vacíos, sin datos de muestra ni ceros.');
$exogena=Exogena5247Data::read(['anio'=>'2024','tipo_contrato'=>'1','concepto'=>'5005','documento_tipo'=>'13',
    'identificacion'=>'001234567','primer_apellido'=>'Ejemplo','primer_nombre'=>'Persona','departamento'=>'05',
    'municipio'=>'001','pais'=>'169','pago'=>'1000000','iva_costo'=>'0','retencion_renta'=>'35000',
    'retencion_renta_asumida'=>'0','retencion_iva'=>'0','retencion_no_domiciliados'=>'0',
    'participante_tipo'=>'31','participante_identificacion'=>'900000001']);
check(Exogena5247Data::errors($exogena)===[], 'Borrador válido permite tercero natural y empresa informante.');
check($exogena['identificacion']==='001234567' && $exogena['municipio']==='001', 'Preserva ceros iniciales.');
check(Exogena5247Data::errors(array_replace($exogena,['retencion_renta'=>'']))!==[], 'No convierte retención desconocida en cero.');
check(Exogena5247Data::errors(array_replace($exogena,['pago'=>'1e6']))!==[], 'Rechaza notación científica.');
check(Exogena5247Data::errors(array_replace($exogena,['pago'=>'-10']))!==[], 'Rechaza valores negativos de esta captura.');
check(Exogena5247Data::errors(array_replace($exogena,['pago'=>'1000000.50']))!==[], 'Captura explícita en pesos enteros.');
check(Exogena5247Data::errors(array_replace($exogena,['municipio'=>'1']))!==[], 'No inventa ceros para código municipal incompleto.');
check(Exogena5247Data::errors(array_replace($exogena,['primer_nombre'=>'','razon_social'=>'Empresa ficticia']))===[], 'Tercero jurídico usa razón social.');
check(Exogena5247Data::errors(array_replace($exogena,['primer_nombre'=>'','razon_social'=>'']))!==[], 'Exige identidad del tercero.');
check(Exogena5247Data::read(['pago'=>['1000']])['pago']==='', 'Rechaza arrays en campos de captura.');
