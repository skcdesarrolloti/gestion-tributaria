<?php
declare(strict_types=1);

namespace App\Services;

final class Exogena1007Data
{
    public static function read(array $source): array
    {
        return IngresoData::read($source,array_merge(['anio'],array_keys(Exogena1007Fields::all())));
    }

    public static function errors(array $record): array
    {
        return ExogenaData::errors($record,Exogena1007Fields::all(),Exogena1007Fields::money());
    }
}
