<?php
declare(strict_types=1);

// Catálogo de consulta: no contiene datos personales ni un motor de cálculo.
return [
    [
        'id'=>'salud-obligatoria',
        'title'=>'Aportes obligatorios a salud',
        'scope'=>'Aportes obligatorios propios imputables al ingreso.',
        'limit'=>'Ingreso no constitutivo de renta, no deducción personal adicional. Solo el valor legal correspondiente, sin doble uso como gasto o prepagada.',
        'evidence'=>'PILA y certificados laborales o pensionales.',
        'action'=>'Conciliar por mes y por fuente, incluidos pagos de independientes.',
        'norm'=>'ET 56 y 336.2',
    ],
    [
        'id'=>'pension-obligatoria',
        'title'=>'Aportes obligatorios a pensión',
        'scope'=>'Aportes obligatorios propios conforme al régimen aplicable.',
        'limit'=>'Ingreso no constitutivo de renta; no equivale a eximir cualquier ahorro. Verificar obligación, base y régimen pensional vigente.',
        'evidence'=>'PILA y certificados del empleador o administradora.',
        'action'=>'Conciliar ingreso e IBC; esta ficha no calcula obligaciones UGPP.',
        'norm'=>'ET 55 y 336.2',
    ],
    [
        'id'=>'rais-voluntario',
        'title'=>'Cotizaciones voluntarias al RAIS',
        'scope'=>'Afiliado con cotizaciones voluntarias al régimen de ahorro individual admisibles.',
        'limit'=>'Hasta 25% del ingreso laboral o tributario anual y 2.500 UVT; distinto del fondo voluntario del art. 126-1. Retiro para fin distinto de mayor pensión o retiro anticipado genera renta y retención según art. 55.',
        'evidence'=>'Certificado que identifique producto RAIS, aportes y destino del retiro.',
        'action'=>'Confirmar producto y régimen antes de comparar alternativas de ahorro.',
        'norm'=>'ET 55 y 336.2',
    ],
    [
        'id'=>'inflacionario',
        'title'=>'Componente inflacionario de rendimientos financieros',
        'scope'=>'Persona natural no obligada a llevar contabilidad y rendimientos elegibles.',
        'limit'=>'Solo porcentaje decretado para el año y entidades o conceptos admitidos. El porcentaje anual no está incorporado en este catálogo; para 2026 verificarlo cuando se expida. No extrapolar el de 2025 ni eximir todos los intereses.',
        'evidence'=>'Certificado financiero y decreto anual aplicable.',
        'action'=>'Marcar pendiente hasta disponer del porcentaje oficial del período.',
        'norm'=>'ET 38, 40-1 y 41',
    ],
];
