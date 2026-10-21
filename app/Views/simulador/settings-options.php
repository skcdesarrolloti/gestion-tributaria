<?php
// Ayudas de captura: los valores y las reglas del motor permanecen sin cambios.
$scenarioNames=['Situación actual','Proyección al cierre del año','Alternativa para comparar'];
if ($record['nombre']!=='' && !in_array($record['nombre'],$scenarioNames,true)) {
    $scenarioNames[]=$record['nombre']; // Conservar nombres de versiones ya guardadas.
}
$settings=[
    'pensionado'=>[
        'label'=>'¿Recibes pensión?', 'hint'=>'Incluye a quien recibe pensión y también otros ingresos.',
        'options'=>['si'=>'Sí, recibo pensión','no'=>'No recibo pensión'], 'article'=>'206',
        'help'=>'Escoge Sí si recibes una pensión. Puedes recibir además salarios, honorarios, arriendos o dividendos: se registran por separado. Esta respuesta no convierte todos tus ingresos en exentos. En la sección mensual se anotan la pensión y la salud descontada, con los certificados correspondientes.',
    ],
    'servicios'=>[
        'label'=>'Si recibes honorarios, ¿cómo los vas a tratar?', 'hint'=>'Si no conoces el tratamiento, déjalo por confirmar.',
        'options'=>['no_aplica'=>'No recibo honorarios','costos'=>'Restar costos y gastos procedentes','exencion'=>'Aplicar exención del 25%, si procede'], 'article'=>'336',
        'help'=>'Honorarios son pagos por servicios independientes. Si no los recibes, escoge No recibo honorarios. Si los recibes, hay que revisar los requisitos y comparar costos procedentes con la exención del artículo 206 numeral 10; no se aplican ambos a esos mismos ingresos. Ser pensionado no decide esta elección. Si falta revisarla, conserva Por confirmar; el resultado quedará pendiente.',
    ],
    'declaraciones'=>[
        'label'=>'¿Esta es tu primera, segunda o una declaración posterior?', 'hint'=>'Se refiere a tu historia como declarante, no al año 2025 o 2026.',
        'options'=>['1'=>'Primera declaración · 25%','2'=>'Segunda declaración · 50%','3'=>'Tercera o posterior · 75%'], 'article'=>'807',
        'help'=>'Revisa las declaraciones anteriores. Si ya has presentado varias declaraciones de renta, escoge Tercera o posterior. Estos porcentajes se usan para calcular el anticipo del año siguiente; no son la tarifa de tu impuesto ni dependen de que estés pensionado. Si no conoces tu historia de declaraciones, déjalo por confirmar.',
    ],
    'metodo_anticipo'=>[
        'label'=>'¿Qué base quieres comparar para el anticipo?', 'hint'=>'El promedio requiere el impuesto neto de la declaración anterior.',
        'options'=>['actual'=>'Impuesto neto del año que estoy proyectando','promedio'=>'Promedio del año proyectado y el anterior'], 'article'=>'807',
        'help'=>'La primera opción usa el impuesto neto que calcula esta hoja. La segunda usa el promedio de ese valor y el impuesto neto del año anterior, que debes registrar en la sección de datos anuales; no copies el saldo que pagaste. Puedes guardar una versión con cada método para compararlos. Para una primera declaración esta hoja admite únicamente la base del año proyectado.',
    ],
];
