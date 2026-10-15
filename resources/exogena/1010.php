<?php
declare(strict_types=1);

// Diccionario de captura; referencia DIAN indicada abajo. Sin datos de clientes.
return [
    'code'=>'1010', 'version'=>'9', 'year'=>'2025',
    'name'=>'Información de socios', 'group'=>'cuentas',
    'structure'=>'1010-v9-ag2025', 'amount'=>'valnom',
    'source'=>'https://www.dian.gov.co/normatividad/Normatividad/Resoluci%C3%B3n%20000162%20de%2031-10-2023.pdf',
    'pages'=>'232–239', 'norm'=>'ET 631 literal a y 631-3; resoluciones DIAN 162/2023, 188/2024, 227/2025 y modificaciones aplicables. Referencia revisada 14/10/2026.',
    'fields'=>[
        'tdoc'=>['Tipo de Documento','code',2,true,'Código DIAN del documento del informado; conservar sus dígitos.'],
        'nid'=>['Número de Identificación','id',20,true,'Identificación del tercero como texto, sin puntos, guiones, comas ni espacios.'],
        'dv'=>['Digito de Verificación','code',1,false,'Dígito de verificación, si se conoce y corresponde al documento; no se calcula ni se asume cero.'],
        'apl1'=>['Primer Apellido del Accionista','text',60,false,'Primer Apellido del Accionista. Dato de una persona natural; completar cuando corresponda al informado.'],
        'apl2'=>['Segundo Apellido del Accionista','text',60,false,'Segundo Apellido del Accionista. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom1'=>['Primer Nombre del Accionista','text',60,false,'Primer Nombre del Accionista. Dato de una persona natural; completar cuando corresponda al informado.'],
        'nom2'=>['Otros Nombres del Accionista','text',60,false,'Otros Nombres del Accionista. Dato de una persona natural; completar cuando corresponda al informado.'],
        'raz'=>['Razón Social del Accionista','text',450,false,'Razón social de la entidad informada; para persona natural completar sus nombres y apellidos.'],
        'dir'=>['Dirección','text',200,false,'Dirección del informado. Revisar su obligatoriedad según país y formato.'],
        'dpto'=>['Código del Departamento','code',2,false,'Código DANE de departamento de dos dígitos, conservando ceros.'],
        'mun'=>['Código del Municipio','code',3,false,'Código DANE municipal de tres dígitos, conservando ceros.'],
        'pais'=>['País de Residencia o domicilio','code',4,true,'Código DIAN del país; no escribir su nombre ni asumir Colombia.'],
        'valnom'=>['Valor nominal de la acción, aporte o derecho social a diciembre 31','money',18,true,'Valor nominal de la acción, aporte o derecho social a diciembre 31. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
        'valprm'=>['Valor prima en colocación de acciones a diciembre 31','money',18,true,'Valor prima en colocación de acciones a diciembre 31. Valor en COP enteros, sin separadores; cero debe indicarse expresamente. No se calcula automáticamente.'],
        'por'=>['Porcentaje de participación','digits',20,true,'Porcentaje SIN separador decimal: 12.78915 se captura como 1278915; completar también su posición decimal. Máximo 100%.'],
        'dec'=>['Porcentaje de participación (posición decimal)','code',2,true,'Número de posiciones decimales de la casilla anterior, de 0 a 20; por ejemplo 5 para 12.78915. El cero se registra expresamente.'],
    ],
];
