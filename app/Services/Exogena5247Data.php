<?php
declare(strict_types=1);

namespace App\Services;

final class Exogena5247Data
{
    public static function read(array $source): array
    {
        $record=['anio'=>trim(\input($source,'anio'))];
        foreach (Exogena5247Fields::all() as $key=>$field) { $record[$key]=trim(\input($source,$key)); }
        return $record;
    }

    public static function errors(array $record): array
    {
        $errors=[];
        if (!preg_match('/^20[0-9]{2}$/D',$record['anio'])) { $errors[]='Escribe un año gravable de cuatro dígitos entre 2000 y 2099.'; }
        foreach (Exogena5247Fields::all() as $key=>[$column,$label,$max]) {
            if (mb_strlen($record[$key])>$max) { $errors[]="$label supera $max caracteres."; }
        }
        foreach (['tipo_contrato'=>2,'concepto'=>4,'documento_tipo'=>2,'pais'=>3,'participante_tipo'=>2] as $key=>$digits) {
            $value=$record[$key];
            $pattern=$key==='tipo_contrato'?'/^[1-9][0-9]?$/D':'/^[0-9]{'.$digits.'}$/D';
            if (!preg_match($pattern,$value)) { $errors[]=Exogena5247Fields::all()[$key][1].': revisa el código numérico.'; }
        }
        foreach (['departamento'=>2,'municipio'=>3] as $key=>$digits) {
            if ($record[$key]!=='' && !preg_match('/^[0-9]{'.$digits.'}$/D',$record[$key])) {
                $errors[]=Exogena5247Fields::all()[$key][1].': usa '.$digits.' dígitos o déjalo pendiente.';
            }
        }
        foreach (['identificacion','participante_identificacion'] as $key) {
            if (!preg_match('/^[A-Za-z0-9]{1,20}$/D',$record[$key])) { $errors[]=Exogena5247Fields::all()[$key][1].': completa el documento sin puntos, espacios ni guiones.'; }
        }
        if ($record['fideicomiso']!=='' && !preg_match('/^[A-Za-z0-9]{1,20}$/D',$record['fideicomiso'])) {
            $errors[]='Identificación del fideicomiso: revisa el valor sin separadores.';
        }
        if ($record['razon_social']==='' && ($record['primer_apellido']==='' || $record['primer_nombre']==='')) {
            $errors[]='Completa razón social o, para una persona natural, primer apellido y primer nombre.';
        }
        foreach (Exogena5247Fields::money() as $key) {
            if (!preg_match('/^(0|[1-9][0-9]{0,14})$/D',$record[$key])) {
                $errors[]=Exogena5247Fields::all()[$key][1].': escribe pesos enteros sin separadores (hasta 15 dígitos), incluido cero.';
            }
        }
        return $errors;
    }
}
