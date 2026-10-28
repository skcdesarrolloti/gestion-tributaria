<?php
declare(strict_types=1);
namespace App\Services;

/** Explicaciones de captura; no sustituye las reglas de cálculo ni confirma soportes. */
final class RetencionGuia
{
    public static function field(string $key): array
    {
        return (self::monthly()+self::reserve())[$key]??['hint'=>'','details'=>[],'choices'=>[]];
    }
    private static function monthly(): array
    {
        return [
            'procedimiento'=>['hint'=>'Es el método que usa tu empleador. No debes escoger el que dé un descuento menor.',
                'details'=>['Dónde saberlo: en la liquidación de retención o preguntando a quien prepara tu nómina. El archivo importado no lo informa.'],
                'choices'=>['1'=>'Procedimiento 1: se calcula la base del mes y se aplica la tabla mensual. El descuento puede variar cuando cambian tus pagos o beneficios.',
                    '2'=>'Procedimiento 2: el empleador calcula un porcentaje con información histórica y lo aplica durante seis meses. Necesitamos el porcentaje certificado para enero–junio o julio–diciembre.',
                    'pendiente'=>'Si no sabes cuál usan, déjalo pendiente y pide esa información a nómina. Aún no podemos confirmar si el descuento es correcto.']],
            'dependientes'=>['hint'=>'Son familiares que cumplen las condiciones legales para este beneficio. Ayudar a un familiar no basta por sí solo.',
                'details'=>['Puede comprender hijos, cónyuge o compañero, padres o hermanos según edad, estudios, ingresos o dependencia certificada. Consulta el artículo 387 en la ayuda legal de abajo.',
                    'Para revisar lo retenido necesitamos saber si este beneficio fue solicitado, soportado y aplicado por nómina durante el mes.'],
                'choices'=>['si'=>'Sí: nómina confirmó que aplicó el beneficio con sus soportes. El sistema calcula el 10% del ingreso bruto, con el límite mensual correspondiente.',
                    'no'=>'No: nómina confirmó que ese mes no aplicó el beneficio. No se resta una deducción por dependientes en esta comparación.',
                    'pendiente'=>'No lo sé: consulta si nómina tiene tu solicitud y sus soportes. No marques Sí solo por tener hijos u otros familiares.']],
            'redondeo'=>['hint'=>'Indica cómo aproxima nómina el resultado final. Sirve para explicar diferencias pequeñas.',
                'details'=>['Confírmalo en la liquidación del empleador. No cambies esta opción solo para hacer coincidir las cifras.'],
                'choices'=>['peso'=>'Al peso: un resultado de $110.123,48 se aproxima a $110.123.',
                    'miles'=>'Al millar: un resultado de $110.123 se aproxima a $110.000.',
                    'pendiente'=>'Si no lo sabes, pide a nómina su criterio de redondeo. La comparación quedará pendiente.']],
            'porcentaje'=>['hint'=>'Solo para procedimiento 2. Es el porcentaje del semestre que te confirmó el empleador; por ejemplo, escribe 7.25 para 7,25%.',
                'details'=>['No lo obtengas dividiendo el descuento por tu salario bruto: se aplica sobre una base depurada, que puede ser diferente.',
                    'Comprobamos su aplicación mensual. Revisar cómo se obtuvo históricamente ese porcentaje requiere sus antecedentes.']],
            'vivienda'=>['hint'=>'Valor mensual de intereses de vivienda que aceptó nómina según el certificado. No es la cuota completa del crédito.',
                'details'=>['Dónde encontrarlo: certificado de intereses y liquidación de retención del empleador. No incluyas el capital que amortizas.',
                    'Escribe 0 si confirmaste que ese mes no se aplicó esta deducción. Si no sabes, deja el campo vacío y consulta. El sistema aplica el límite mensual.']],
            'prepagada'=>['hint'=>'Valor mensual aceptado por nómina por medicina prepagada o seguro de salud que cumple las condiciones.',
                'details'=>['La salud obligatoria descontada en la nómina ya se recuperó del archivo: no la vuelvas a escribir aquí.',
                    'Usa el certificado y la liquidación del empleador. Si confirmaste que no aplicó este beneficio, escribe 0; un dato desconocido se deja vacío.']],
            'usado25'=>['hint'=>'Parte del beneficio laboral del 25% que ya se usó en meses anteriores de este mismo año. No es el 25% de tu salario actual.',
                'details'=>['Este beneficio tiene un tope anual. Pedimos el acumulado para no utilizar otra vez un cupo que ya se consumió.',
                    'En enero, antes del primer mes del año, se propone 0. Desde febrero usa el acumulado de la liquidación de nómina, no una suma inventada ni un porcentaje sobre tu ingreso.']],
            'usado1340'=>['hint'=>'Total de beneficios sujetos al límite compartido que nómina ya utilizó antes de este mes, dentro del mismo año.',
                'details'=>['Es un control del cupo anual de deducciones y rentas exentas. No es un pago adicional ni debes escribir 1.340 como valor.',
                    'En enero se propone 0. En los demás meses pide el acumulado a nómina. UVT es una unidad tributaria cuyo valor en pesos cambia cada año; el sistema hace esa conversión.']],
            'soporte'=>['hint'=>'Escribe de dónde tomaste los datos. No se te está pidiendo adjuntar un archivo en este campo.',
                'details'=>['Ejemplo: «Liquidación de retención de enero entregada por nómina; procedimiento 1; sin vivienda ni prepagada».',
                    'La referencia permite recordar qué información revisaste. Escribir una nota sin comprobar los datos no los convierte en confirmados.']],
            'completo'=>['hint'=>'Márcalo cuando comprobaste que están todos los pagos del mes de este empleador. Dos quincenas no garantizan que no falte una bonificación.'],
            'ordinario'=>['hint'=>'Márcalo si no hay pagos con tratamiento especial: cesantías, primas separadas, indemnizaciones, retroactivos, AFC o aportes voluntarios. Si existen, esta comprobación ordinaria queda pendiente.'],
            'verificado'=>['hint'=>'Márcalo solo después de contrastar los campos con la información de nómina. No es una confirmación de que ya conoces el resultado.'],
        ];
    }
    private static function reserve(): array
    {
        return [
            'futuro_bruto'=>['hint'=>'Lo que todavía falta sumar al año por salarios y primas ordinarias, antes de descuentos. No repitas lo ya importado.',
                'details'=>['Incluye las quincenas pendientes. Ejemplo: si faltan dos pagos de $2.000.000 brutos, registrarías $4.000.000. Revisa las primas por separado al preparar el total.']],
            'futuro_aportes'=>['hint'=>'Salud, pensión y solidaridad obligatorias que esperas descontar de esos pagos adicionales.',
                'details'=>['No incluyas préstamos, libranzas ni descuentos personales. No se supone automáticamente un aporte a pensión para quien no lo tiene.']],
            'futuro_retencion'=>['hint'=>'Retención de renta que esperas que nómina descuente en los pagos que faltan. No es un nuevo ingreso.',
                'details'=>['Es una previsión para estimar cuánto estará cubierto al finalizar el año. Usa 0 solo si tienes fundamento para esperar que no habrá más retención.']],
            'anticipo_anterior'=>['hint'=>'Valor que la declaración del año anterior dejó como anticipo para cubrir este año.',
                'details'=>['No es una retención de nómina. Verifica tu declaración anterior y que el valor corresponda al año que estás preparando. Si desconoces el dato, no lo sustituyas por 0.']],
            'saldo_favor'=>['hint'=>'Crédito de una declaración anterior que aún puedes utilizar en este año.',
                'details'=>['Confirma que no se haya pedido en devolución, compensado o usado antes. No es el saldo de tu cuenta bancaria.']],
            'declaraciones'=>['hint'=>'Cuenta las declaraciones de renta: no los años trabajados ni los meses con descuentos de nómina.',
                'choices'=>['1'=>'Primera declaración de renta: para este ejercicio se estima anticipo con 25% sobre la base del impuesto actual, antes de restar las retenciones según la regla.',
                    '2'=>'Segunda declaración: en esta hipótesis se utiliza 50% con la base del impuesto actual.',
                    '3'=>'Tercera o posterior: se utiliza 75% con la base del impuesto actual. El anticipo se muestra separado del impuesto.']],
            'cuotas'=>['hint'=>'En cuántas partes quieres separar tu reserva personal de dinero (entre 1 y 12). No crea cuotas ni pagos ante la DIAN.'],
            'hipotesis'=>['hint'=>'Este ejemplo anual solo incluye salarios ordinarios. Tu saldo completo exige agregar pensión, rendimientos y los demás componentes que correspondan.'],
        ];
    }
}
