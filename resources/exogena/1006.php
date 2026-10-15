<?php
declare(strict_types=1);

// Diccionario de captura; referencia DIAN indicada abajo. Sin datos de clientes.
return [
    'code'=>'1006', 'version'=>'8', 'year'=>'2025',
    'name'=>'IVA Generado', 'group'=>'iva',
    'structure'=>'1006-v8-ag2025', 'amount'=>'imp',
    'source'=>'https://www.dian.gov.co/normatividad/Normatividad/Resoluci%C3%B3n%20000162%20de%2031-10-2023.pdf',
    'pages'=>'267–272', 'norm'=>'ET 631 literal f y 631-3; resoluciones DIAN 162/2023, 188/2024, 227/2025 y modificaciones aplicables. Referencia revisada 14/10/2026.',
    'fields'=>[
        'tdoc'=>['Tipo de Documento','code',2,true,'Código DIAN del documento del informado; conservar sus dígitos.'],
        'nid'=>['Número de Identificación','id',20,true,'Identificación del tercero como texto, sin puntos, guiones, comas ni espacios.'],
        'dv'=>['Digito de Verificación','code',1,false,'Dígito de verificación, si se conoce y corresponde al documento; no se calcula ni se asume cero.'],
        'apl1'=>['Primer Apellido del informado','text',60,false,'Primer Apellido del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'apl2'=>['Segundo Apellido del informado','text',60,false,'Segundo Apellido del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom1'=>['Primer Nombre del informado','text',60,false,'Primer Nombre del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom2'=>['Otros Nombres del informado','text',60,false,'Otros Nombres del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'raz'=>['Razón Social del Informado','text',450,false,'Razón social de la entidad informada; para persona natural completar sus nombres y apellidos.'],
        'imp'=>['Impuesto generado','money',20,true,'Impuesto generado. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
        'iva'=>['IVA recuperado en devoluciones en compras anuladas, rescindidas o resueltas','money',20,true,'IVA recuperado en devoluciones en compras anuladas, rescindidas o resueltas. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
        'icon'=>['Impuesto nacional al consumo','money',20,true,'Impuesto nacional al consumo. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
    ],
];
