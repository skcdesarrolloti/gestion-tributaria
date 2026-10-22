<?php
declare(strict_types=1);
namespace App\Services;

/** Distribuye la captura sin mover ni duplicar los valores históricos. */
final class PerfilAlcance
{
    public const PERSONAL=['vinculacion','tipo_pension','fecha_pension','pagador_pension'];
    public const FISCAL=['residencia','dias_colombia','pais_residencia','nit_exterior','sujecion','regimen','contabilidad','iva','retencion','facturacion'];
    public static function components(): array
    {
        return ['ingresos'=>['Ingresos y eventos','ingresos'], 'costos'=>['Costos y beneficios','beneficios'],
            'dependientes'=>['Beneficios por dependientes','relacionados',['dependiente']],
            'seguridad'=>['Seguridad social','seguridad'], 'declaracion'=>['Obligaciones del año','fiscal'],
            'patrimonio'=>['Patrimonio y deudas','patrimonio']];
    }
    public static function schema(array $schema, bool $component, string $type): array
    {
        $block=$schema['key']; $keys=null;
        if ($block==='seguridad') { $keys=self::PERSONAL; }
        if ($block==='fiscal') { $keys=self::FISCAL; }
        if ($keys!==null) {
            $schema['fields']=$component?array_diff_key($schema['fields'],array_flip($keys)):array_intersect_key($schema['fields'],array_flip($keys));
        }
        if (!$component && $block==='relacionados') { unset($schema['rows']['dependencia'],$schema['rows']['reclamado']); }
        if (!$component && $block==='aduanas') { unset($schema['fields']['organizaciones']); }
        if (!$component && $block==='actividades') { unset($schema['fields']['otras_revisadas']); }
        if (!$component && $block==='entidad' && $type==='juridica') {
            foreach (['fallecido','fallecimiento_fecha','sucesion_estado','liquidacion_fecha'] as $key) { unset($schema['fields'][$key]); }
        }
        return $schema;
    }
    public static function preserve(array $saved, array $posted, array $visible, array $full, array $rawRows): array
    {
        $posted['campos']=array_replace($saved['campos'],array_intersect_key($posted['campos'],$visible['fields']));
        if (!isset($visible['rows'])) { return $posted; }
        $hidden=array_diff_key($full['rows'],$visible['rows']);
        if (!$hidden) { return $posted; }
        $scoped=CaracterizacionData::read(['filas'=>$rawRows],$visible)['filas']; $origins=[];
        foreach ($rawRows as $row) {
            $clean=CaracterizacionData::read(['filas'=>[$row]],$visible);
            if (!$clean['filas']) { continue; }
            $raw=\input($row,'_origen'); $origin=$raw===''?null:filter_var($raw,FILTER_VALIDATE_INT,['options'=>['min_range'=>0]]);
            if ($origin!==null && ($origin===false || !isset($saved['filas'][$origin]) || isset($origins[$origin]))) {
                throw new \InvalidArgumentException('La referencia del renglón cambió. Recarga antes de guardar.');
            }
            if ($origin!==null) { $origins[$origin]=true; }
        }
        $index=0;
        foreach ($rawRows as $row) {
            if (!CaracterizacionData::read(['filas'=>[$row]],$visible)['filas']) { continue; }
            $raw=\input($row,'_origen');
            $old=$raw!==''?$saved['filas'][(int)$raw]:[];
            $scoped[$index]=array_replace(array_fill_keys(array_keys($full['rows']),''),array_intersect_key($old,$hidden),$scoped[$index]);
            $index++;
        }
        $posted['filas']=$scoped; return $posted;
    }
    public static function origins(array $record, ?array $raw=null, array $schema=[]): array
    {
        $rows=$raw===null?$record['filas']:array_values(array_filter($raw,static fn($r)=>CaracterizacionData::read(['filas'=>[$r]],$schema)['filas']!==[]));
        foreach ($record['filas'] as $i=>&$row) { $row['_origen']=$raw===null?(string)$i:\input($rows[$i]??[],'_origen'); }
        unset($row); return $record;
    }
}
