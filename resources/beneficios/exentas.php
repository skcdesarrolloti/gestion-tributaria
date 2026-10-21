<?php
declare(strict_types=1);

// Catálogo de consulta: no contiene datos personales ni un motor de cálculo.
return [
    [
        'id'=>'trabajo-25',
        'title'=>'Renta exenta del 25% de trabajo',
        'scope'=>'Pagos laborales y rentas de trabajo no laborales que cumplan la norma.',
        'limit'=>'25% de la base depurada en orden legal, máximo 790 UVT al año; además límite conjunto. Para trabajo independiente elegir costos o esta exención sobre esos ingresos, no ambos.',
        'evidence'=>'Certificados de ingresos, aportes y depuración; elección documentada.',
        'action'=>'Comparar costos reales frente a la exención antes del cierre.',
        'norm'=>'ET 206.10, 206 par. 5 y 336',
    ],
    [
        'id'=>'pension-voluntaria',
        'title'=>'Aportes a fondos o seguros voluntarios de pensión',
        'scope'=>'Aportante a productos admitidos, con renta y condiciones verificadas.',
        'limit'=>'En conjunto con AFC: hasta 30% del ingreso laboral o tributario y 3.800 UVT anuales; también límite general cuando corresponda. Permanencia general de 10 años y excepciones legales; no toda pensión voluntaria es RAIS.',
        'evidence'=>'Certificado del fondo, fechas, origen de aportes y condiciones de retiro.',
        'action'=>'Calcular cupo real y necesidad de liquidez antes de inmovilizar dinero.',
        'norm'=>'ET 126-1 y 336.3',
    ],
    [
        'id'=>'afc',
        'title'=>'Ahorro AFC para vivienda',
        'scope'=>'Persona natural con cuenta AFC y depósitos elegibles.',
        'limit'=>'Comparte 30% y 3.800 UVT con pensión voluntaria, no suma otro cupo. Permanencia general de 10 años o destino de vivienda conforme a requisitos; revisar límite general.',
        'evidence'=>'Certificado AFC, consignaciones y documentación de compra o financiación de vivienda.',
        'action'=>'Planear uso del ahorro y verificar requisitos antes de retirarlo.',
        'norm'=>'ET 126-4 y 336.3',
    ],
    [
        'id'=>'cesantias-laborales',
        'title'=>'Cesantías e intereses sobre cesantías',
        'scope'=>'Trabajador que recibe o realiza fiscalmente estas prestaciones.',
        'limit'=>'Exención total si promedio mensual de últimos seis meses no supera 350 UVT; tabla decreciente hasta 650 UVT y requisitos de mínimo legal. Revisar realización, transición y límite aplicable.',
        'evidence'=>'Certificado laboral o del fondo, promedio salarial y fechas de reconocimiento.',
        'action'=>'Separar saldo patrimonial de ingreso realizado y revisar el certificado.',
        'norm'=>'ET 27, 206.4 y 336',
    ],
    [
        'id'=>'pensiones',
        'title'=>'Pensión y prestaciones pensionales asimiladas',
        'scope'=>'Pensionado que cumple requisitos, incluidas pensiones del exterior según la norma.',
        'limit'=>'Exención hasta 1.000 UVT mensuales; reglas particulares para indemnización sustitutiva y devolución de saldos. Su cédula tiene depuración propia, no consume automáticamente cupo de la cédula general.',
        'evidence'=>'Certificado pensional, meses, aportes a salud y reconocimiento; soporte extranjero cuando proceda.',
        'action'=>'Revisar pensiones por separado y luego los demás ingresos del pensionado.',
        'norm'=>'ET 206.5, 206 par. 3 y 337',
    ],
    [
        'id'=>'indemnizaciones',
        'title'=>'Indemnizaciones laborales exentas específicas',
        'scope'=>'Accidente de trabajo, enfermedad, maternidad y gastos de entierro previstos en la ley.',
        'limit'=>'Solo supuestos y mínimos legales de los numerales 1 a 3 y parágrafo 1; no convertir toda indemnización por despido en renta exenta. Revisar límite y naturaleza.',
        'evidence'=>'Certificación de origen, reconocimiento y monto legal.',
        'action'=>'Clasificar la indemnización antes de incluirla como salario ordinario.',
        'norm'=>'ET 206.1 a 3, 206 par. 1 y 336',
    ],
];
