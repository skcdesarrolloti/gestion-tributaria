<?php
declare(strict_types=1);
namespace App\Services;

/** Formato BBVA Colombia mensual: reconstruye columnas y concilia todos los renglones. */
final class ExtractoBbva
{
    public static function cents(string $value): int
    {
        if (strlen($value)>20 || !preg_match('/^-?(?:[0-9]{1,3}(?:,[0-9]{3})*|[0-9]+)\.[0-9]{2}$/D',$value)) { throw new \InvalidArgumentException('Importe bancario no reconocido.'); }
        return (int)str_replace([',','.'],'',$value);
    }

    public static function metadata(string $text, array $pages): array
    {
        if (!str_contains(mb_strtoupper($text),'BBVA') || !preg_match('/PERIODO DESDE:\s*(\d{2}-\d{2}-\d{4})\s+HASTA:\s*(\d{2}-\d{2}-\d{4})/u',$text,$dates)) {
            throw new \InvalidArgumentException('Formato pendiente.');
        }
        $start=\DateTimeImmutable::createFromFormat('!d-m-Y',$dates[1]);$end=\DateTimeImmutable::createFromFormat('!d-m-Y',$dates[2]);
        if (!$start || !$end || $start->format('d-m-Y')!==$dates[1] || $end->format('d-m-Y')!==$dates[2]
            || $start->format('d')!=='01' || $start->format('Y-m')!==$end->format('Y-m') || $end->format('d')!==$end->format('t')) {
            throw new \InvalidArgumentException('Se requiere extracto mensual completo.');
        }
        $first=$pages[0];$holder='';$account='';
        foreach ($first as $c) {
            if ($c['x']>35 && $c['x']<80 && $c['y']>740 && $c['y']<750) { $holder=$c['text']; }
            if (preg_match('/^[0-9]{18}$/D',$c['text'])) { $account=substr($c['text'],-4); }
        }
        if (!$holder || !$account) { throw new \InvalidArgumentException('No se reconoció la identidad de la cuenta.'); }
        return ['titular'=>$holder,'cuenta'=>$account,'inicio'=>$start->format('Y-m-d'),'fin'=>$end->format('Y-m-d'),'paginas'=>count($pages)];
    }

    public static function parse(string $text, array $pages): array
    {
        $metadata=self::metadata($text,$pages);$first=$pages[0];
        $start=new \DateTimeImmutable($metadata['inicio']);$end=new \DateTimeImmutable($metadata['fin']);
        $summary=self::summary($first);$rows=[];
        foreach ($pages as $p=>$cells) {
            foreach ($cells as $c) {
                if ($c['x']<125 || $c['x']>175 || !preg_match('/^\d{2}-\d{2}-\d{4}$/D',$c['text'])) { continue; }
                $d=\DateTimeImmutable::createFromFormat('!d-m-Y',$c['text']);
                if (!$d || $d->format('d-m-Y')!==$c['text'] || $d<$start || $d>$end) { throw new \InvalidArgumentException('Fecha de movimiento fuera del período.'); }
                $row=['fecha'=>$d->format('Y-m-d'),'operacion'=>'','numero'=>'','descripcion'=>'','cargo'=>0,'abono'=>0,'saldo'=>null,'pagina'=>$p+1];
                foreach ($cells as $v) {
                    if (abs($v['y']-$c['y'])>1.1) { continue; }
                    $x=$v['x'];$t=$v['text'];
                    if ($x<70 && ctype_digit($t)) { $row['numero']=$t; }
                    elseif ($x>=70 && $x<125) { $row['operacion']=$t; }
                    elseif ($x>=178 && $x<400 && !preg_match('/^[0-9,.]+$/D',$t)) { $row['descripcion'].=$t; }
                    elseif ($x>=400 && $x<455) { $row['cargo']=self::cents($t); }
                    elseif ($x>=455 && $x<515) { $row['abono']=self::cents($t); }
                    elseif ($x>=515) { $row['saldo']=self::cents($t); }
                }
                if (!$row['descripcion'] || $row['saldo']===null || $row['cargo']<0 || $row['abono']<0 || ($row['cargo']>0)===($row['abono']>0)) { throw new \InvalidArgumentException('Renglón incompleto o ambiguo.'); }
                $row['categoria']=self::category($row);$row['y']=$c['y'];$rows[]=$row;
            }
        }
        if (!$rows || count($rows)>1500) { throw new \InvalidArgumentException('Sin detalle completo.'); }
        usort($rows,static fn($a,$b)=>($a['pagina']<=>$b['pagina'])?:($b['y']<=>$a['y']));
        $balance=$summary['inicial'];$credits=0;$debits=0;$totals=array_fill_keys(['pension','rendimientos','gmf','gmf_reintegro','retenciones','iva','otros_abonos','otros_debitos'],0);
        foreach ($rows as &$r) {
            $balance+=$r['abono']-$r['cargo'];$credits+=$r['abono'];$debits+=$r['cargo'];
            if ($balance!==$r['saldo']) { throw new \InvalidArgumentException('Saldo de renglón no concilia.'); }
            $totals[$r['categoria']]+=$r['abono']+$r['cargo'];unset($r['y']);
        }
        unset($r);
        if ($balance!==$summary['final'] || $credits!==$summary['abonos']+$summary['intereses']+$totals['gmf_reintegro']
            || $debits!==$summary['cargos']+$summary['gmf']+$summary['iva']+$summary['retenciones']+$totals['gmf_reintegro']
            || $totals['gmf']-$totals['gmf_reintegro']!==$summary['gmf'] || $totals['rendimientos']!==$summary['intereses']) {
            throw new \InvalidArgumentException('Totales no concilian con el resumen.');
        }
        return $metadata+['estado'=>'leido','version'=>'bbva-mensual-v2','resumen'=>$summary,'totales'=>$totals,'movimientos'=>$rows];
    }

    private static function category(array $row): string
    {
        $t=mb_strtoupper($row['descripcion']);
        if ($row['abono']>0) {
            if (in_array($t,['CORRECCION IMPTO DECRETO','ANU. COM DECRETO'],true)) { return 'gmf_reintegro'; }
            if (str_starts_with($t,'ABONO MESADA PENSIONAL')) { return 'pension'; }
            if (str_starts_with($t,'ABONO POR INTERESES DE CUENTA')) { return 'rendimientos'; }
            return 'otros_abonos';
        }
        if (str_contains($t,'IMPUESTO 4X1.000')) { return 'gmf'; }
        if (str_starts_with($t,'RETENCION')) { return 'retenciones'; }
        if (str_starts_with($t,'IVA ')) { return 'iva'; }
        return 'otros_debitos';
    }

    private static function summary(array $cells): array
    {
        $map=['SALDO CIERRE MES ANTERIOR'=>'inicial','+ ABONOS'=>'abonos','+ INTERESES RECIBIDOS'=>'intereses',
            '- CARGOS'=>'cargos','- IVA'=>'iva','- 4 POR MIL'=>'gmf','- RETENCIONES'=>'retenciones','SALDO FINAL'=>'final'];
        $summary=[];
        foreach ($cells as $c) {
            if (!isset($map[$c['text']])) { continue; }
            $right=$c['x']>330;
            foreach ($cells as $v) {
                if (abs($v['y']-$c['y'])<1.5 && ($right?$v['x']>510:($v['x']>280 && $v['x']<335))) {
                    $summary[$map[$c['text']]]=self::cents($v['text']);
                }
            }
        }
        if (count($summary)!==8) { throw new \InvalidArgumentException('Resumen incompleto.'); }return $summary;
    }
}
