<?php
declare(strict_types=1);

// Diccionario de captura; referencia DIAN indicada abajo. Sin datos de clientes.
return [
    'code'=>'1003', 'version'=>'7', 'year'=>'2025',
    'name'=>'Retenciones en la fuente que le practicaron', 'group'=>'pagos',
    'structure'=>'1003-v7-ag2025', 'amount'=>'valor',
    'source'=>'https://www.dian.gov.co/normatividad/Normatividad/Resoluci%C3%B3n%20000162%20de%2031-10-2023.pdf',
    'pages'=>'248–254', 'norm'=>'ET 631 literal c y 631-3; resoluciones DIAN 162/2023, 188/2024, 227/2025 y modificaciones aplicables. Referencia revisada 14/10/2026.',
    'fields'=>[
        'cpt'=>['Concepto','code',4,true,'Código del concepto DIAN del año. No es una cuenta contable; revisar el catálogo aplicable.'],
        'tdoc'=>['Tipo de Documento','code',2,true,'Código DIAN del documento del informado; conservar sus dígitos.'],
        'nid'=>['Número de Identificación','id',20,true,'Identificación del tercero como texto, sin puntos, guiones, comas ni espacios.'],
        'dv'=>['Digito de Verificación','code',1,false,'Dígito de verificación, si se conoce y corresponde al documento; no se calcula ni se asume cero.'],
        'apl1'=>['Primer Apellido del informado','text',60,false,'Primer Apellido del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'apl2'=>['Segundo Apellido del informado','text',60,false,'Segundo Apellido del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom1'=>['Primer Nombre del informado','text',60,false,'Primer Nombre del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom2'=>['Otros Nombres del informado','text',60,false,'Otros Nombres del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'raz'=>['Razón Social del Informado','text',450,false,'Razón social de la entidad informada; para persona natural completar sus nombres y apellidos.'],
        'dir'=>['Dirección','text',200,true,'Dirección del informado. Revisar su obligatoriedad según país y formato.'],
        'dpto'=>['Departamento','code',2,true,'Código DANE de departamento de dos dígitos, conservando ceros.'],
        'mcpo'=>['Municipio','code',3,true,'Código DANE municipal de tres dígitos, conservando ceros.'],
        'valor'=>['Base acumulada del pago o abono sujeto a retención','money',20,true,'Base acumulada del pago o abono sujeto a retención; no confundir con toda la facturación. Para concepto 1309 revisar la base de IVA.'],
        'ret'=>['Retención en la fuente que le practicaron','money',20,true,'Retención que le practicaron al informante, distinta de las retenciones practicadas por él a terceros.'],
    ],
];
