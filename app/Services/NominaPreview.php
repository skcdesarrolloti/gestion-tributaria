<?php
declare(strict_types=1);
namespace App\Services;

/** Ordena la revisión visual; no modifica las filas que se importarán. */
final class NominaPreview
{
    public const MONTHS=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    public const COLUMNS=['devengado'=>'32 · Bruto','salud'=>'Salud','pension'=>'Pensión',
        'solidaridad'=>'Solidaridad','retencion'=>'132 · Retención','otros_descuentos'=>'Otros descuentos','neto'=>'Neto listado'];

    public static function groups(array $records): array
    {
        usort($records,static fn(array $a,array $b): int =>
            [$a['nombre'],$a['empleador_documento'],$a['inicio'],$a['fin']] <=>
            [$b['nombre'],$b['empleador_documento'],$b['inicio'],$b['fin']]);
        $groups=[];
        foreach ($records as $r) {
            $key=$r['perfil_id'].'|'.$r['empleador_documento'];
            $month=substr($r['inicio'],0,7);
            if (!isset($groups[$key])) {
                $groups[$key]=['nombre'=>$r['nombre'],'empleador'=>$r['empleador'],
                    'nit'=>$r['empleador_documento'],'meses'=>[]];
            }
            if (!isset($groups[$key]['meses'][$month])) {
                $groups[$key]['meses'][$month]=['titulo'=>self::MONTHS[(int)substr($month,5,2)-1].' '.substr($month,0,4),
                    'filas'=>[],'totales'=>array_fill_keys(array_keys(self::COLUMNS),0)];
            }
            $m=&$groups[$key]['meses'][$month];
            $m['filas'][]=$r;
            foreach (self::COLUMNS as $field=>$label) { $m['totales'][$field]+=$r['valores'][$field]; }
            unset($m);
        }
        return array_values($groups);
    }
}
