<?php
declare(strict_types=1);
namespace App\Services;

final class PerfilAyuda
{
    public static function text(string $block, string $key, array $field): string
    {
        static $specific=null;
        $specific??=require dirname(__DIR__,2).'/resources/caracterizacion/ayudas.php';
        $entry=$specific[$block.'.'.$key]??null;
        if ($entry!==null) { return $entry.($field['help']!==''?' '.$field['help']:''); }
        $purpose=[
            'identificacion'=>'Permite identificar al titular y el documento utilizado para completar su ficha.',
            'ubicacion'=>'Permite localizar y contactar al contribuyente. La dirección de contacto no determina su residencia fiscal.',
            'actividades'=>'Permite conocer las actividades registradas y sus fechas, sin clasificar automáticamente los ingresos.',
            'responsabilidades'=>'Permite conocer las responsabilidades inscritas, sin dar por comprobado el cumplimiento de una obligación.',
            'entidad'=>'Permite conocer la naturaleza, constitución, capital y vínculos de la entidad.',
            'relacionados'=>'Permite identificar a la persona, su función y la relación con el titular.',
            'establecimientos'=>'Permite identificar y ubicar cada lugar de actividad y su registro.',
            'aduanas'=>'Permite conocer la clasificación aduanera registrada.',
        ];
        $prefix=isset($purpose[$block])?'Dato del RUT o transcripción de su información. ':'Dato complementario para este componente. ';
        if ($block==='relacionados') { $prefix='Dato de la relación: se toma del RUT cuando figure allí o del documento que la respalde. '; }
        $text=$prefix.($purpose[$block]??'Sirve para orientar la revisión del año; registrar la respuesta no calcula impuestos ni acredita un beneficio.');
        return $text.' Copia únicamente lo que puedas respaldar; si no corresponde, déjalo vacío.'.($field['help']!==''?' '.$field['help']:'');
    }
}
