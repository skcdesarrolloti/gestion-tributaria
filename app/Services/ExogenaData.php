<?php
declare(strict_types=1);

namespace App\Services;

final class ExogenaData
{
    public static function errors(array $record, array $fields, array $money): array
    {
        $errors=[];
        if (!preg_match('/^20[0-9]{2}$/D',$record['anio'])) { $errors[]='Revisa el año gravable de cuatro dígitos.'; }
        foreach ($fields as $key=>[$column,$label,$max]) {
            if (mb_strlen($record[$key])>$max) { $errors[]="$label supera $max caracteres."; }
        }
        foreach (['concepto'=>4,'documento_tipo'=>2] as $key=>$length) {
            if (!preg_match('/^[0-9]{'.$length.'}$/D',$record[$key])) { $errors[]=$fields[$key][1].': completa el código numérico.'; }
        }
        if (!preg_match('/^[0-9]{1,4}$/D',$record['pais'])) { $errors[]='Completa el código del país (hasta cuatro dígitos).'; }
        if (!preg_match('/^[A-Za-z0-9]{1,20}$/D',$record['identificacion'])) { $errors[]='Completa la identificación sin puntos, espacios ni guiones.'; }
        if ($record['razon_social']==='' && ($record['primer_apellido']==='' || $record['primer_nombre']==='')) {
            $errors[]='Completa razón social o primer apellido y primer nombre del tercero.';
        }
        foreach ($money as $key) {
            if (!preg_match('/^(0|[1-9][0-9]{0,17})$/D',$record[$key])) {
                $errors[]=$fields[$key][1].': escribe COP enteros, incluido cero, sin separadores y hasta 18 dígitos.';
            }
        }
        return $errors;
    }
}
