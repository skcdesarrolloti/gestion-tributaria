<?php
declare(strict_types=1);

// Diccionario de captura; referencia DIAN indicada abajo. Sin datos de clientes.
return [
    'code'=>'5250', 'version'=>'2', 'year'=>'2025',
    'name'=>'IVA generado en Contratos de Colaboración Empresarial', 'group'=>'contratos',
    'structure'=>'5250-v2-ag2025', 'amount'=>'ivag',
    'source'=>'https://www.dian.gov.co/normatividad/Normatividad/Resoluci%C3%B3n%20000233%20de%2030-10-2025.pdf',
    'pages'=>'117–123', 'norm'=>'ET 631 literal f y 631-3; resoluciones DIAN 162/2023, 188/2024, 227/2025 y 233/2025; modificaciones aplicables. Referencia revisada 14/10/2026.',
    'fields'=>[
        'tcon'=>['Tipo de Contrato','code',2,true,'Tipo de contrato: 1 mandato/administración delegada; 2 consorcio/unión temporal; 3 hidrocarburos/minerales; 4 joint venture; 5 cuentas en participación; 6 cooperación con entidades públicas.'],
        'tdoc'=>['Tipo de Documento','code',2,true,'Código DIAN del documento del informado; conservar sus dígitos.'],
        'nid'=>['Número de Identificación','id',20,true,'Identificación del tercero como texto, sin puntos, guiones, comas ni espacios.'],
        'apl1'=>['Primer Apellido del informado','text',60,false,'Primer Apellido del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'apl2'=>['Segundo Apellido del informado','text',60,false,'Segundo Apellido del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom1'=>['Primer Nombre del informado','text',60,false,'Primer Nombre del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom2'=>['Otros Nombres del informado','text',60,false,'Otros Nombres del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'raz'=>['Razón Social del Informado','text',450,false,'Razón social de la entidad informada; para persona natural completar sus nombres y apellidos.'],
        'ivag'=>['IVA generado','money',18,true,'IVA generado. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
        'ivar'=>['IVA resultante por devoluciones en compras anuladas rescindidas o resueltas','money',18,true,'IVA resultante por devoluciones en compras anuladas rescindidas o resueltas. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
        'imco'=>['Impuesto al consumo','money',18,true,'Impuesto al consumo. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
        'tdopa'=>['Tipo documento participante en contrato','code',2,true,'Código del documento del participante, distinto del tercero informado.'],
        'nidpa'=>['Identificación participante en contrato','id',20,true,'Identificación del participante como texto, sin separadores.'],
        'ncon'=>['Número de Contrato','text',20,false,'Número del contrato cuando se conoce; conservar su referencia documental.'],
        'niespj'=>['Número de Identificación de Entidades sin Personería Jurídica - NIESPJ','id',20,false,'Identificación de la entidad sin personería jurídica. Obligatoria para contratos de tipo 3, 4 o 5 según la resolución.'],
    ],
];
