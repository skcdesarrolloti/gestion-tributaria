<?php
declare(strict_types=1);

// Diccionario de captura; referencia DIAN indicada abajo. Sin datos de clientes.
return [
    'code'=>'1008', 'version'=>'7', 'year'=>'2025',
    'name'=>'Cuentas por Cobrar', 'group'=>'cuentas',
    'structure'=>'1008-v7-ag2025', 'amount'=>'sal',
    'source'=>'https://www.dian.gov.co/normatividad/Normatividad/Resoluci%C3%B3n%20000162%20de%2031-10-2023.pdf',
    'pages'=>'280–286', 'norm'=>'ET 631 literal i y 631-3; resoluciones DIAN 162/2023, 188/2024, 227/2025 y modificaciones aplicables. Referencia revisada 14/10/2026.',
    'fields'=>[
        'cpt'=>['Concepto','code',4,true,'Código del concepto DIAN del año. No es una cuenta contable; revisar el catálogo aplicable.'],
        'tdoc'=>['Tipo de Documento','code',2,true,'Código DIAN del documento del informado; conservar sus dígitos.'],
        'nid'=>['Número de Identificación','id',20,true,'Identificación del tercero como texto, sin puntos, guiones, comas ni espacios.'],
        'dv'=>['Digito de Verificación','code',1,false,'Dígito de verificación, si se conoce y corresponde al documento; no se calcula ni se asume cero.'],
        'apl1'=>['Primer Apellido del Deudor','text',60,false,'Primer Apellido del Deudor. Dato de una persona natural; completar cuando corresponda al informado.'],
        'apl2'=>['Segundo Apellido del Deudor','text',60,false,'Segundo Apellido del Deudor. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom1'=>['Primer Nombre del Deudor','text',60,false,'Primer Nombre del Deudor. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom2'=>['Otros Nombres del Deudor','text',60,false,'Otros Nombres del Deudor. Dato de una persona natural; completar cuando corresponda al informado.'],
        'raz'=>['Razón Social del Deudor','text',450,false,'Razón social de la entidad informada; para persona natural completar sus nombres y apellidos.'],
        'dir'=>['Dirección','text',200,false,'Dirección del informado. Revisar su obligatoriedad según país y formato.'],
        'dpto'=>['Código del Departamento','code',2,false,'Código DANE de departamento de dos dígitos, conservando ceros.'],
        'mun'=>['Código del Municipio','code',3,false,'Código DANE municipal de tres dígitos, conservando ceros.'],
        'pais'=>['País de Residencia o domicilio','code',4,true,'Código DIAN del país; no escribir su nombre ni asumir Colombia.'],
        'sal'=>['Saldo cuentas por Cobrar al 31 de diciembre','money',20,true,'Saldo cuentas por Cobrar al 31 de diciembre. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
    ],
];
