<?php
declare(strict_types=1);

// Catálogo de consulta: no contiene datos personales ni un motor de cálculo.
return [
    [
        'id'=>'primer-empleo',
        'title'=>'Primer empleo de menores de 28 años',
        'scope'=>'Persona natural empleadora y declarante con nuevo empleo certificado.',
        'limit'=>'120% del salario admisible, máximo 115 UVT mensuales por empleado, en año de contratación; certificación del Ministerio del Trabajo. Revisar imputación y límites de persona natural.',
        'evidence'=>'Contrato, edad, certificación de primer empleo, nómina y PILA.',
        'action'=>'Validar requisitos antes de tomar el incremento; no sumarlo otra vez al salario ordinario.',
        'norm'=>'ET 108-5, 108 y 336',
    ],
    [
        'id'=>'empleo-discapacidad',
        'title'=>'Empleo de personas con discapacidad',
        'scope'=>'Empleador declarante con discapacidad comprobada no inferior al 25%.',
        'limit'=>'Deducción de 200% de salarios y prestaciones pagados mientras subsista la condición; revisar certificación, causalidad e interacción con otros incentivos y art. 336.',
        'evidence'=>'Certificación válida, contrato, pagos, nómina y aportes.',
        'action'=>'Verificar acreditación y evitar duplicar la misma remuneración.',
        'norm'=>'Ley 361/1997 art. 31; ET 108 y 336',
    ],
    [
        'id'=>'energia',
        'title'=>'Inversión certificada en FNCE o gestión eficiente de energía',
        'scope'=>'Declarante que realiza directamente una inversión certificada por UPME.',
        'limit'=>'50% de inversión hasta en 15 años desde el año siguiente a entrada en operación; deducción anual no mayor al 50% de renta líquida previa. Revisar además encaje cedular y límites; instalar paneles por sí solo no acredita el beneficio.',
        'evidence'=>'Certificado UPME, inversión, titular y entrada en operación.',
        'action'=>'Obtener evaluación técnica y tributaria antes de invertir.',
        'norm'=>'Ley 1715/2014 art. 11, modificado por Ley 2099/2021; ET 336',
    ],
    [
        'id'=>'cine',
        'title'=>'Inversión o donación cinematográfica certificada',
        'scope'=>'Declarante que no es productor ni coproductor y cumple aprobación del proyecto.',
        'limit'=>'165% del dinero efectivamente invertido o donado al proyecto admitido, con certificado oficial; no cine publicitario ni telenovelas. Revisar límite cedular y demás incompatibilidades.',
        'evidence'=>'Aprobación del proyecto, certificado y pago en dinero.',
        'action'=>'Revisar riesgo de inversión, liquidez y ahorro real; no decidir solo por porcentaje.',
        'norm'=>'Ley 814/2003 arts. 16 y 17; ET 336',
    ],
    [
        'id'=>'donacion-especifica',
        'title'=>'Donaciones con régimen específico del art. 126-2',
        'scope'=>'Donación que encaje exactamente en destinatarios y requisitos especiales.',
        'limit'=>'El texto prevé 125% para supuestos específicos; requiere revisar vigencia aplicable, límites y concurrencia con art. 257. No aplicar de forma automática a cualquier fundación o club.',
        'evidence'=>'Reconocimiento del destinatario y certificado con requisitos legales.',
        'action'=>'Clasificar la donación con el contador antes de atribuirle un beneficio.',
        'norm'=>'ET 126-2, 125-1 a 125-3, 257 y 336',
    ],
];
