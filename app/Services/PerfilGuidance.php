<?php
declare(strict_types=1);

namespace App\Services;

final class PerfilGuidance
{
    private const ET = 'https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm';

    public static function fields(string $type): array
    {
        $year = [];
        foreach (['2025','2026'] as $value) {
            $year[$value] = self::item($value, 'Período al que corresponden los ingresos y operaciones.',
                "Analiza los movimientos de $value; el año de presentación puede ser posterior. Esta versión admite 2025 y 2026.", 'ET 1 y 595; instructivo del formulario aplicable');
        }
        $residence = [
            'residente'=>self::item('Residente', 'Condición fiscal, no equivalente a nacionalidad ni a dirección de vivienda.',
                'Verifica todos los criterios del artículo 10, no solo permanencia. Define el alcance sobre ingresos y patrimonio nacional y del exterior.', 'ET 9 y 10'),
            'no_residente'=>self::item('No residente', 'Persona que no cumple los criterios de residencia fiscal aplicables.',
                'Revisar fuente nacional, obligación de declarar y tratamientos particulares antes de seleccionar el formulario.', 'ET 9, 10, 24 y 592'),
        ];
        if ($type === 'sucesion') {
            foreach ($residence as &$entry) {
                $entry['definicion'] = 'Se evalúa la residencia fiscal del causante al momento de fallecer.';
                $entry['alcance'] = 'Causante residente: referencia 210; no residente: 110. Revisar período, representación y titularidad de los ingresos.';
                $entry['norma'] = 'ET 7, 9 y 10; instructivo DIAN de sucesiones';
            }
            unset($entry);
        } elseif ($type === 'juridica') {
            $residence = [
                'residente'=>self::item('Domicilio o nacionalidad fiscal en Colombia', 'Para entidades se revisa domicilio y, en sociedades, nacionalidad fiscal; no se usa automáticamente la prueba de días de una persona.',
                    'Comprobar constitución, domicilio o sede efectiva de administración según corresponda a la entidad.', 'ET 12 y 12-1'),
                'no_residente'=>self::item('Entidad del exterior sin domicilio fiscal en Colombia', 'Condición que requiere analizar la entidad y su presencia en Colombia.',
                    'Revisar ingresos de fuente nacional, establecimiento permanente y obligación de declarar. No determina una exención automática.', 'ET 12, 20, 20-1 y 24'),
            ];
        }
        $regimes = [
            'ordinario'=>self::item('Ordinario', 'Renta bajo las reglas generales aplicables al contribuyente.',
                'Disponible: depuración inicial 210/110. No calcula todavía el impuesto final ni certifica beneficios.', 'ET 26, 240 o 241 según corresponda'),
            'simple'=>self::item('SIMPLE — consulta; cálculo pendiente', 'Impuesto unificado sujeto a requisitos de inscripción y permanencia.',
                'Referencia: formulario 260. Revisar elegibilidad, exclusiones y RUT; no aplicar las deducciones y tarifas del ordinario. No permite crear perfil en esta etapa.', 'ET 903, 905 y 906'),
        ];
        if ($type === 'sucesion') { unset($regimes['simple']); }
        if ($type === 'juridica') {
            $regimes['especial'] = self::item('Especial (ESAL) — consulta; cálculo pendiente', 'Tratamiento de las entidades sin ánimo de lucro que cumplan las condiciones legales y de calificación.',
                'Ser ESAL no basta para pertenecer al régimen especial. Revisar actividad meritoria, calificación, permanencia y beneficio neto. No permite crear perfil aquí.', 'ET 19, 356, 358 y 359');
            $regimes['especial_cooperativo'] = self::item('Especial (sector cooperativo) — consulta; cálculo pendiente', 'Variante del régimen especial con reglas propias para las entidades del artículo 19-4.',
                'No copiar automáticamente las reglas de otras ESAL; comprobar naturaleza, requisitos y determinación del excedente. No permite crear perfil aquí.', 'ET 19-4');
        }
        $services = [
            'pendiente'=>self::item('Pendiente de verificar', 'Todavía no se ha establecido el tratamiento de servicios personales.',
                'Las casillas 43–45 no se habilitan; no trasladar honorarios a otra casilla por defecto. Verificar antes de fijar el perfil.', 'ET 103, 206 numeral 10 y 336'),
            'no_aplica'=>self::item('No aplica', 'No existe una elección de tratamiento de servicios personales que deba realizarse en este perfil.',
                'Por ejemplo, entidad jurídica o persona sin esos ingresos. No significa que todos los ingresos estén exentos.', 'ET 103, 206 numeral 10 y 336: ámbito de aplicación'),
            'costos'=>self::item('Costos en lugar de exención del art. 206.10', 'Elección de costos y gastos procedentes para servicios personales sin relación laboral.',
                'Habilita 43–45 en el 210. Exige relación con la actividad y soportes; no se acumula la exención del 206.10 para esos mismos ingresos.', 'ET 107, 206 numeral 10 y 336'),
            'exencion'=>self::item('Exención art. 206.10, si procede', 'Renta exenta del 25% sujeta a depuración, condiciones y topes legales.',
                'Para los mismos servicios personales no se toman simultáneamente sus costos. En esta etapa se orienta la elección: todavía no se calcula la exención.', 'ET 206 numeral 10 y parágrafo 5, 336'),
        ];
        if ($type === 'juridica') { unset($services['costos'], $services['exencion']); }
        return [
            'anio'=>['label'=>'Año gravable','options'=>$year],
            'residencia'=>['label'=>$type==='sucesion'?'Residencia fiscal del causante al fallecer':($type==='juridica'?'Condición fiscal de la entidad':'Residencia fiscal del año'),'options'=>$residence],
            'regimen'=>['label'=>'Régimen tributario','options'=>$regimes],
            'servicios'=>['label'=>'Servicios personales: tratamiento a revisar','options'=>$services],
        ];
    }

    private static function item(string $label, string $definition, string $scope, string $norm): array
    {
        return ['label'=>$label,'definicion'=>$definition,'alcance'=>$scope,'norma'=>$norm,'url'=>self::ET];
    }
}
