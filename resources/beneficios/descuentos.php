<?php
declare(strict_types=1);

// Catálogo de consulta: no contiene datos personales ni un motor de cálculo.
return [
    [
        'id'=>'donaciones',
        'title'=>'Donaciones generales a entidades admitidas',
        'scope'=>'Donante con entidades y requisitos de los arts. 22, 23 o régimen especial.',
        'limit'=>'Descuento general de 25% de la donación, no deducción del 100%. Límite conjunto del art. 258 y reglas del art. 259; verificar arrastre y destino.',
        'evidence'=>'Certificado válido de donación, pago y condición del donatario.',
        'action'=>'Verificar entidad antes de donar; el ahorro no recupera todo el dinero donado.',
        'norm'=>'ET 125-1 a 125-3, 257, 258 y 259',
    ],
    [
        'id'=>'bancos-alimentos',
        'title'=>'Donaciones elegibles a bancos de alimentos',
        'scope'=>'Alimentos aptos y bienes de higiene o aseo donados a entidades admitidas.',
        'limit'=>'Hasta 37% en el supuesto específico del art. 257, con transporte certificado y reglas de límites; saldo bajo regla especial de hasta cuatro períodos. No aplicar 37% a cualquier donación monetaria.',
        'evidence'=>'Certificado con bienes, valor y transporte desagregado; condición del banco.',
        'action'=>'Verificar modalidad de donación y no acumular 25% y 37% sobre el mismo hecho.',
        'norm'=>'ET 257, 258 y 259; Ley 2380/2024',
    ],
    [
        'id'=>'impuesto-exterior',
        'title'=>'Impuesto de renta pagado en el exterior',
        'scope'=>'Residente con rentas extranjeras también sujetas a impuesto en Colombia.',
        'limit'=>'Crédito por impuesto extranjero admisible sin superar impuesto colombiano de las mismas rentas; aplicar reglas de fuente, dividendos y convenios según el caso.',
        'evidence'=>'Declaraciones, certificados de pago y cálculo por renta y país.',
        'action'=>'Conciliar los ingresos y evitar perder el crédito por falta de soporte.',
        'norm'=>'ET 254 y 259',
    ],
    [
        'id'=>'dividendos',
        'title'=>'Descuento por dividendos de persona natural residente',
        'scope'=>'Socio persona natural residente con dividendos dentro del supuesto legal.',
        'limit'=>'Tabla del art. 254-1: cero hasta 1.090 UVT; sobre el exceso, 19%. No significa que todos los dividendos estén libres de impuesto; revisar origen y año de utilidades.',
        'evidence'=>'Certificado de dividendos con utilidades, tratamiento y retenciones.',
        'action'=>'Solicitar a la sociedad la composición de los dividendos antes de proyectarlos.',
        'norm'=>'ET 49, 242, 254-1, 330 y 331',
    ],
    [
        'id'=>'investigacion',
        'title'=>'Inversión en investigación, desarrollo o innovación calificada',
        'scope'=>'Persona que invierte en proyecto calificado por autoridad competente.',
        'limit'=>'Descuento de 30% de inversión elegible; cupos, calificación y art. 258. No aplica a una compra ordinaria de tecnología sin proyecto calificado.',
        'evidence'=>'Calificación, cupo, certificación e inversión efectuada.',
        'action'=>'Revisar calificación antes de comprometer recursos.',
        'norm'=>'ET 256, 258 y 259',
    ],
    [
        'id'=>'iva-productivo',
        'title'=>'IVA de activos fijos reales productivos',
        'scope'=>'Responsable de IVA con activo y operación que cumplan el art. 258-1.',
        'limit'=>'Descuento del IVA pagado admisible en el año o períodos siguientes, con reglas de puesta en uso y leasing. No simultáneo con costo, gasto o IVA descontable.',
        'evidence'=>'Factura, pago, activo, responsabilidad IVA y fecha de uso.',
        'action'=>'Definir el tratamiento del IVA una sola vez y documentarlo.',
        'norm'=>'ET 258-1 y 259',
    ],
];
