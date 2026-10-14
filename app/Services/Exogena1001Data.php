<?php
declare(strict_types=1);

namespace App\Services;

final class Exogena1001Data
{
    public static function read(array $source): array
    {
        return IngresoData::read($source,array_merge(['anio'],array_keys(Exogena1001Fields::all())));
    }

    public static function errors(array $record): array
    {
        $errors=ExogenaData::errors($record,Exogena1001Fields::all(),Exogena1001Fields::money());
        if ($record['anio']!=='2025') { $errors[]='Este módulo usa versión 10 para 2025. Otro año requiere revisar su versión antes de guardar.'; }
        foreach (['departamento'=>2,'municipio'=>3] as $key=>$digits) {
            if ($record[$key]!=='' && !preg_match('/^[0-9]{'.$digits.'}$/D',$record[$key])) { $errors[]="Revisa $key: usa $digits dígitos."; }
        }
        if ($record['pais']!=='' && ltrim($record['pais'],'0')==='169'
            && ($record['direccion']==='' || $record['departamento']==='' || $record['municipio']==='')) {
            $errors[]='Para Colombia completa dirección, departamento y municipio del beneficiario.';
        }
        return $errors;
    }
}
