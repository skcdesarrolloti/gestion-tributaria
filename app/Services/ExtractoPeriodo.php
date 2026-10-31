<?php
declare(strict_types=1);
namespace App\Services;

/** Concilia un período bancario y resume sus fechas reales, sin prorratear totales. */
final class ExtractoPeriodo
{
    public static function reconcile(array $meta,array $summary,array $rows,string $version): array
    {
        if (!$rows || count($rows)>1500) { throw new \InvalidArgumentException('Detalle fuera de alcance.'); }
        $keys=['pension','rendimientos','gmf','gmf_reintegro','retenciones','iva','otros_abonos','otros_debitos'];
        $totals=array_fill_keys($keys,0);$monthly=[];$balance=$summary['inicial'];$credits=0;$debits=0;$lastDate='';
        foreach ($meta['meses_cubiertos'] as $month) { $monthly[$month]=['totales'=>array_fill_keys($keys,0),'resumen'=>['inicial'=>null,'final'=>null,'abonos'=>0,'cargos'=>0,'intereses'=>0,'gmf'=>0,'iva'=>0,'retenciones'=>0]]; }
        foreach ($rows as $r) {
            $m=(int)substr($r['fecha'],5,2);
            if ($r['fecha']<$lastDate || $r['fecha']<$meta['inicio'] || $r['fecha']>$meta['fin'] || !isset($monthly[$m],$totals[$r['categoria']])) { throw new \InvalidArgumentException('Fecha o categoría fuera de alcance.'); }
            $lastDate=$r['fecha'];$v=&$monthly[$m];$v['resumen']['inicial']??=$balance;$balance+=$r['abono']-$r['cargo'];
            if ($balance!==$r['saldo']) { throw new \InvalidArgumentException('El saldo de una operación no concilia.'); }
            $amount=$r['abono']+$r['cargo'];$totals[$r['categoria']]+=$amount;$v['totales'][$r['categoria']]+=$amount;
            $credits+=$r['abono'];$debits+=$r['cargo'];$v['resumen']['abonos']+=$r['abono'];$v['resumen']['cargos']+=$r['cargo'];$v['resumen']['final']=$balance;
        }unset($v);
        if ($balance!==$summary['final'] || $credits!==$summary['abonos'] || $debits!==$summary['cargos'] || $totals['rendimientos']!==$summary['intereses']
            || (isset($summary['retenciones']) && $summary['retenciones']!==$totals['retenciones'])) { throw new \InvalidArgumentException('El período no concilia con el resumen.'); }
        $carry=$summary['inicial'];foreach ($monthly as &$month) {
            $month['resumen']['inicial']??=$carry;$month['resumen']['final']??=$carry;$carry=$month['resumen']['final'];
            foreach (['intereses'=>'rendimientos','gmf'=>'gmf','iva'=>'iva','retenciones'=>'retenciones'] as $key=>$field) { $month['resumen'][$key]=$month['totales'][$field]; }
        }unset($month);
        $summary+=['gmf'=>$totals['gmf'],'iva'=>$totals['iva'],'retenciones'=>$totals['retenciones']];
        return $meta+['estado'=>'leido','version'=>$version,'resumen'=>$summary,'totales'=>$totals,'movimientos'=>$rows,'meses'=>$monthly];
    }
}
