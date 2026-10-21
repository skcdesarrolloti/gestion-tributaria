<?php
declare(strict_types=1);

// Catálogo de consulta: no contiene datos personales ni un motor de cálculo.
return [
    [
        'id'=>'gasto-personal',
        'title'=>'Consumos personales y gastos domésticos',
        'scope'=>'Mercado, ropa, colegio, vacaciones, servicio doméstico y vivienda personal.',
        'limit'=>'No son costos de actividad por ser gastos reales. Algunas compras pueden analizarse exclusivamente bajo el 1% si cumplen todos sus requisitos; colegio no crea una deducción educativa general.',
        'evidence'=>'Separación de pagos personales y del negocio.',
        'action'=>'No trasladar gastos familiares a la empresa ni a costos del socio sin fundamento.',
        'norm'=>'ET 107, 115 par. 4 y 336.5',
    ],
    [
        'id'=>'capital-deudas',
        'title'=>'Abonos a capital y compra de activos',
        'scope'=>'Pago de créditos, compra de vivienda, vehículos o participaciones.',
        'limit'=>'No son deducciones automáticas. Revisar costo fiscal, intereses procedentes, depreciación o beneficio específico; el aporte a la sociedad no es gasto de renta del socio.',
        'evidence'=>'Contrato, tabla de amortización y título del activo.',
        'action'=>'Separar patrimonio, deuda, capital e interés.',
        'norm'=>'ET 26, 69, 107, 119 y 128',
    ],
    [
        'id'=>'perdida-sociedad',
        'title'=>'Pérdidas y gastos de la sociedad',
        'scope'=>'Socio que pretende restar pérdidas o costos de su empresa en su declaración.',
        'limit'=>'Los socios no deducen ni compensan las pérdidas de la sociedad contra sus propias rentas. Cada contribuyente tiene costos e ingresos propios.',
        'evidence'=>'Contabilidad separada y certificados de la sociedad.',
        'action'=>'Separar el presupuesto familiar del empresarial y documentar operaciones vinculadas.',
        'norm'=>'ET 147 y 107',
    ],
    [
        'id'=>'dividendos-sin-costos',
        'title'=>'Gastos contra dividendos y pensión',
        'scope'=>'Socio con dividendos o persona que recibe solo pensión.',
        'limit'=>'La cédula de dividendos no admite costos ni deducciones; pensiones tienen depuración específica. No trasladar a ellas beneficios de cédula general sin habilitación.',
        'evidence'=>'Certificados y clasificación por fuente.',
        'action'=>'Identificar cédula antes de buscar el beneficio; revisar descuento específico de dividendos.',
        'norm'=>'ET 330, 337 y 254-1',
    ],
    [
        'id'=>'beneficios-juridicos',
        'title'=>'Beneficios exclusivos de personas jurídicas',
        'scope'=>'Beneficios que la norma reserva a sociedades o entidades.',
        'limit'=>'No trasladar al socio el descuento ambiental general del art. 255 por ser dueño de la empresa. Tampoco asumir que becas de empresa son matrícula personal deducible.',
        'evidence'=>'Norma de cada incentivo y sujeto autorizado.',
        'action'=>'Verificar primero quién tiene el derecho: sociedad o persona natural.',
        'norm'=>'ET 255, 107-2 y 8',
    ],
    [
        'id'=>'retenciones',
        'title'=>'Retenciones, anticipos y saldos a favor',
        'scope'=>'Pagos previos del impuesto y créditos acreditados.',
        'limit'=>'Pueden reducir saldo por pagar, pero no son costos ni reducen por sí solos el impuesto causado. Deben conciliarse para proyectar caja sin confundir ahorro con anticipo.',
        'evidence'=>'Certificados de retención y declaraciones anteriores.',
        'action'=>'Conciliar anticipos y retenciones al final de la simulación.',
        'norm'=>'ET 373 y 807',
    ],
];
