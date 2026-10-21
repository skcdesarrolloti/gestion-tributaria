<?php
declare(strict_types=1);
namespace App\Services;

final class RutImportRows
{
    /** Añade personas/lugares y completa coincidencias sin borrar los renglones manuales. */
    public static function merge(array $rows, array $incoming, string $block, bool $replace, bool &$changed): array
    {
        $changed=false;
        $index=self::index($rows,$incoming,$block);
        if ($index!==null) {
            $row=&$rows[$index];
            foreach ($incoming as $field=>$value) {
                if ($field==='rol' && $value==='socio_junta' && in_array($row[$field],['socio','junta'],true)) { continue; }
                if ($value!=='' && $value!==($row[$field]??'') && ($replace || ($row[$field]??'')==='')) { $row[$field]=$value; $changed=true; }
            }
            return $rows;
        }
        if (count($rows)>=100) { throw new \InvalidArgumentException('La importación supera 100 renglones en '.$block.'. Revisa el documento antes de continuar.'); }
        $rows[]=$incoming; $changed=true; return $rows;
    }

    public static function index(array $rows, array $incoming, string $block): ?int
    {
        foreach ($rows as $i=>$row) {
            $role=$incoming['rol']??''; $current=$row['rol']??'';
            $sameRole=$role===$current || ($role==='socio_junta' && in_array($current,['socio','junta'],true));
            $sameIdentity=$incoming['documento']??'';
            $sameIdentity=($sameIdentity!=='' && ($row['documento']??'')!=='')?$sameIdentity===$row['documento']
                :(($incoming['nombre']??'')!=='' && $incoming['nombre']===$row['nombre']);
            $samePlace=$block==='establecimientos' && (($incoming['matricula']??'')!=='' && ($row['matricula']??'')!==''
                ?$incoming['matricula']===$row['matricula']
                :($incoming['nombre']!=='' && $incoming['nombre']===$row['nombre'] && ($incoming['direccion']==='' || $row['direccion']==='' || $incoming['direccion']===$row['direccion'])));
            $same=$block==='relacionados'
                ? ($sameIdentity && $sameRole
                    && ($role!=='revisor' || $incoming['parentesco']===$row['parentesco']))
                : $samePlace;
            if ($same) { return $i; }
        }
        return null;
    }
}
