<?php
declare(strict_types=1);

// Diccionario de captura; referencia DIAN indicada abajo. Sin datos de clientes.
return [
    'code'=>'5251', 'version'=>'2', 'year'=>'2025',
    'name'=>'Saldo Cuentas por Cobrar en Contratos de Colaboración Empresarial', 'group'=>'contratos',
    'structure'=>'5251-v2-ag2025', 'amount'=>'saldos',
    'source'=>'https://www.dian.gov.co/normatividad/Normatividad/Resoluci%C3%B3n%20000233%20de%2030-10-2025.pdf',
    'pages'=>'124–131', 'norm'=>'ET 631 literal i y 631-3; resoluciones DIAN 162/2023, 188/2024, 227/2025 y 233/2025; modificaciones aplicables. Referencia revisada 14/10/2026.',
    'fields'=>[
        'tcon'=>['Tipo de Contrato','code',2,true,'Tipo de contrato: 1 mandato/administración delegada; 2 consorcio/unión temporal; 3 hidrocarburos/minerales; 4 joint venture; 5 cuentas en participación; 6 cooperación con entidades públicas.'],
        'cpt'=>['Concepto','code',4,true,'Código del concepto DIAN del año. No es una cuenta contable; revisar el catálogo aplicable.'],
        'tdoc'=>['Tipo de Documento','code',2,true,'Código DIAN del documento del informado; conservar sus dígitos.'],
        'nid'=>['Número de Identificación','id',20,true,'Identificación del tercero como texto, sin puntos, guiones, comas ni espacios.'],
        'apl1'=>['Primer Apellido del informado','text',60,false,'Primer Apellido del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'apl2'=>['Segundo Apellido del informado','text',60,false,'Segundo Apellido del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom1'=>['Primer Nombre del informado','text',60,false,'Primer Nombre del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom2'=>['Otros Nombres del informado','text',60,false,'Otros Nombres del informado. Dato de una persona natural; completar cuando corresponda al informado.'],
        'raz'=>['Razón Social del Informado','text',450,false,'Razón social de la entidad informada; para persona natural completar sus nombres y apellidos.'],
        'dir'=>['Dirección','text',200,false,'Dirección del informado. Revisar su obligatoriedad según país y formato.'],
        'dpto'=>['Código del Departamento','code',2,false,'Código DANE de departamento de dos dígitos, conservando ceros.'],
        'mun'=>['Código del Municipio','code',3,false,'Código DANE municipal de tres dígitos, conservando ceros.'],
        'pais'=>['País de Residencia o domicilio','code',4,true,'Código DIAN del país; no escribir su nombre ni asumir Colombia.'],
        'saldos'=>['Saldo cuentas por cobrar al 31- 12','money',18,true,'Saldo cuentas por cobrar al 31- 12. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
        'tdopa'=>['Tipo documento participante en contrato','code',2,true,'Código del documento del participante, distinto del tercero informado.'],
        'nidpa'=>['Identificación participante en contrato','id',20,true,'Identificación del participante como texto, sin separadores.'],
        'ncon'=>['Número de Contrato','text',20,false,'Número del contrato cuando se conoce; conservar su referencia documental.'],
        'niespj'=>['Número de Identificación de Entidades sin Personería Jurídica - NIESPJ','id',20,false,'Identificación de la entidad sin personería jurídica. Obligatoria para contratos de tipo 3, 4 o 5 según la resolución.'],
    ],
];
