<?php
declare(strict_types=1);

// Diccionario de captura; referencia DIAN indicada abajo. Sin datos de clientes.
return [
    'code'=>'1005', 'version'=>'9', 'year'=>'2025',
    'name'=>'IVA Descontable', 'group'=>'iva',
    'structure'=>'1005-v9-ag2025', 'amount'=>'vimp',
    'source'=>'https://www.dian.gov.co/normatividad/Normatividad/Resoluci%C3%B3n%20000233%20de%2030-10-2025.pdf',
    'pages'=>'79–84', 'norm'=>'ET 631 literal e y 631-3; resoluciones DIAN 162/2023, 188/2024, 227/2025 y 233/2025; modificaciones aplicables. Referencia revisada 14/10/2026.',
    'fields'=>[
        'tdoc'=>['Tipo de Documento','code',2,true,'Código DIAN del documento del informado; conservar sus dígitos.'],
        'nid'=>['Número de Identificación','id',20,true,'Identificación del tercero como texto, sin puntos, guiones, comas ni espacios.'],
        'dv'=>['Digito de Verificación','code',1,false,'Dígito de verificación, si se conoce y corresponde al documento; no se calcula ni se asume cero.'],
        'apl1'=>['Primer Apellido del informado','text',60,false,'Primer Apellido del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'apl2'=>['Segundo Apellido del informado','text',60,false,'Segundo Apellido del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom1'=>['Primer Nombre del informado','text',60,false,'Primer Nombre del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom2'=>['Otros Nombres del informado','text',60,false,'Otros Nombres del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'raz'=>['Razón Social del Informado','text',450,false,'Razón social de la entidad informada; para persona natural completar sus nombres y apellidos.'],
        'vimp'=>['Impuesto descontable','money',20,true,'Impuesto descontable. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
        'ivade'=>['IVA resultante por devoluciones en ventas anuladas, rescindidas o resueltas','money',20,true,'IVA resultante por devoluciones en ventas anuladas, rescindidas o resueltas. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
    ],
];
