<?php
declare(strict_types=1);
namespace App\Services;

/** Cuenta de ahorros Cuentamiga: concilia el trimestre y luego agrupa fechas reales. */
final class ExtractoCajaSocial
{
    private const MONTHS=['ENE'=>1,'FEB'=>2,'MAR'=>3,'ABR'=>4,'MAY'=>5,'JUN'=>6,'JUL'=>7,'AGO'=>8,'SEP'=>9,'OCT'=>10,'NOV'=>11,'DIC'=>12];
    public static function metadata(string $text,array $pages): array
    {
        $t=PrestamoCajaSocial::normal($text);$names=array_map([PrestamoCajaSocial::class,'normal'],ExtractoData::MONTHS);
        if ((!str_contains($t,'BANCO CAJA SOCIAL') && !str_contains($t,'BANCOCAJASOCIAL')) || !str_contains($t,'CUENTA DE AHORROS')
            || !preg_match('/PERIODO DEL INFORME\s+(\d{1,2}) DE ([A-Z]+) A (\d{1,2}) DE ([A-Z]+) DE (20\d{2})/',$t,$m)) { throw new \InvalidArgumentException('Formato de cuenta Caja Social pendiente.'); }
        $a=array_search($m[2],$names,true);$b=array_search($m[4],$names,true);$year=(int)$m[5];
        if (!$a || !$b || !checkdate($a,(int)$m[1],$year) || !checkdate($b,(int)$m[3],$year) || $a>$b || $b-$a>2) { throw new \InvalidArgumentException('Período bancario inválido.'); }
        $start=sprintf('%04d-%02d-%02d',$year,$a,$m[1]);$end=sprintf('%04d-%02d-%02d',$year,$b,$m[3]);
        if ($m[1]!=='1' && $m[1]!=='01') { throw new \InvalidArgumentException('Se requiere inicio de mes.'); }
        if ((new \DateTimeImmutable($end))->format('d')!==(new \DateTimeImmutable($end))->format('t')) { throw new \InvalidArgumentException('El período no termina al cierre de mes.'); }
        $holder='';$account='';foreach ($pages[0] as $c) {
            if ($c['x']>=30 && $c['x']<60 && $c['y']>692 && $c['y']<701) { $holder=$c['text']; }
            if ($c['x']>=30 && $c['x']<60 && preg_match('/^\d{11}$/D',$c['text'])) { $account=substr($c['text'],-4); }
        }
        if (!$holder || !$account) { throw new \InvalidArgumentException('No se reconoció titular o cuenta.'); }
        return ['banco'=>'BANCO CAJA SOCIAL','titular'=>$holder,'cuenta'=>$account,'inicio'=>$start,'fin'=>$end,'meses_cubiertos'=>range($a,$b),'paginas'=>count($pages)];
    }
    public static function parse(string $text,array $pages): array
    {
        $meta=self::metadata($text,$pages);$summary=self::summary($pages[0]);$rows=[];$last=null;
        foreach ($pages as $p=>$cells) {
            $dates=array_values(array_filter($cells,static fn($c)=>$c['x']>=35 && $c['x']<60 && preg_match('/^(ENE|FEB|MAR|ABR|MAY|JUN|JUL|AGO|SEP|OCT|NOV|DIC) \d{2}$/D',$c['text'])));
            usort($dates,static fn($a,$b)=>$b['y']<=>$a['y']);
            // La nota de la última operación puede continuar al comienzo de la página siguiente.
            if ($last!==null && $dates) { foreach ($cells as $c) { if ($c['x']>=70 && $c['x']<100 && $c['y']>$dates[0]['y']+1 && $c['y']<665) { $rows[$last]['descripcion'].=' '.$c['text']; } } }
            foreach ($dates as $i=>$c) {
                [$short,$day]=explode(' ',$c['text']);$month=self::MONTHS[$short];$year=(int)substr($meta['inicio'],0,4);
                if (!checkdate($month,(int)$day,$year)) { throw new \InvalidArgumentException('Fecha inválida.'); }$date=sprintf('%04d-%02d-%02d',$year,$month,$day);
                if ($date<$meta['inicio'] || $date>$meta['fin']) { throw new \InvalidArgumentException('Movimiento fuera del período.'); }
                $r=['fecha'=>$date,'operacion'=>$date,'numero'=>'','descripcion'=>'','cargo'=>0,'abono'=>0,'saldo'=>null,'pagina'=>$p+1];
                $bottom=$dates[$i+1]['y']??($p===0?114:25);
                foreach ($cells as $v) {
                    $x=$v['x'];$t=$v['text'];$same=abs($v['y']-$c['y'])<=1;
                    if ($same) {
                        if ($x>=190 && $x<245) { $r['numero']=$t; }
                        elseif ($x>=355 && $x<430) { $r['cargo']=abs(ExtractoBbva::cents($t)); }
                        elseif ($x>=430 && $x<510) { $r['abono']=abs(ExtractoBbva::cents($t)); }
                        elseif ($x>=510 && $x<580) { $r['saldo']=ExtractoBbva::cents($t); }
                    }
                    if ($x>=70 && $x<100 && $v['y']<=$c['y']+1 && $v['y']>$bottom+1) { $r['descripcion'].=($r['descripcion']?' ':'').$t; }
                }
                if (!$r['descripcion'] || $r['saldo']===null || ($r['cargo']>0)===($r['abono']>0)) { throw new \InvalidArgumentException('Renglón incompleto.'); }
                $rows[]=$r;$last=array_key_last($rows);
            }
        }
        if (!$rows || count($rows)>1500) { throw new \InvalidArgumentException('Detalle fuera de alcance.'); }
        return self::reconcile($meta,$summary,$rows);
    }
    private static function summary(array $cells): array
    {
        $values=[];foreach ($cells as $c) { if ($c['y']>350 && $c['y']<356 && $c['x']>60 && $c['x']<580) { $values[]=$c; } }
        usort($values,static fn($a,$b)=>$a['x']<=>$b['x']);if (count($values)!==5) { throw new \InvalidArgumentException('Resumen incompleto.'); }
        return array_combine(['inicial','abonos','cargos','intereses','final'],array_map(static fn($c)=>abs(ExtractoBbva::cents($c['text'])),$values));
    }
    private static function category(array $r): string
    {
        $t=PrestamoCajaSocial::normal($r['descripcion']);
        if ($r['abono']>0) {
            if (str_starts_with($t,'ABONO DE INTERESES')) { return 'rendimientos'; }
            if (preg_match('/(?:MESADA PENSIONAL|PAGO (?:DE )?PENSION|ABONO (?:DE )?PENSION)/',$t)) { return 'pension'; }
            return 'otros_abonos';
        }
        if (preg_match('/(?:4\s*[X*]\s*1[.,]?000|4\s*POR\s*MIL|GRAVAMEN MOVIMIENTO)/',$t)) { return 'gmf'; }
        return 'otros_debitos';
    }
    private static function reconcile(array $meta,array $summary,array $rows): array
    {
        $keys=['pension','rendimientos','gmf','gmf_reintegro','retenciones','iva','otros_abonos','otros_debitos'];$totals=array_fill_keys($keys,0);$monthly=[];$balance=$summary['inicial'];$credits=0;$debits=0;$lastDate='';
        foreach ($meta['meses_cubiertos'] as $month) { $monthly[$month]=['totales'=>array_fill_keys($keys,0),'resumen'=>['inicial'=>null,'final'=>null,'abonos'=>0,'cargos'=>0,'intereses'=>0,'gmf'=>0,'iva'=>0,'retenciones'=>0]]; }
        foreach ($rows as &$r) {
            if ($r['fecha']<$lastDate) { throw new \InvalidArgumentException('Movimientos desordenados.'); }$lastDate=$r['fecha'];$m=(int)substr($r['fecha'],5,2);$v=&$monthly[$m];
            $v['resumen']['inicial']??=$balance;$balance+=$r['abono']-$r['cargo'];
            if ($balance!==$r['saldo']) { throw new \InvalidArgumentException('El saldo de una operación no concilia.'); }
            $r['categoria']=self::category($r);$amount=$r['abono']+$r['cargo'];$totals[$r['categoria']]+=$amount;$v['totales'][$r['categoria']]+=$amount;
            $credits+=$r['abono'];$debits+=$r['cargo'];$v['resumen']['abonos']+=$r['abono'];$v['resumen']['cargos']+=$r['cargo'];$v['resumen']['final']=$balance;
        }unset($r,$v);
        if ($balance!==$summary['final'] || $credits!==$summary['abonos'] || $debits!==$summary['cargos'] || $totals['rendimientos']!==$summary['intereses']) { throw new \InvalidArgumentException('El trimestre no concilia con el resumen.'); }
        $carry=$summary['inicial'];foreach ($monthly as &$month) { $month['resumen']['inicial']??=$carry;$month['resumen']['final']??=$carry;$carry=$month['resumen']['final'];foreach (['intereses'=>'rendimientos','gmf'=>'gmf'] as $key=>$field) { $month['resumen'][$key]=$month['totales'][$field]; } }unset($month);
        $summary+=['gmf'=>$totals['gmf'],'iva'=>0,'retenciones'=>0];
        return $meta+['estado'=>'leido','version'=>'cajasocial-cuentamiga-v1','resumen'=>$summary,'totales'=>$totals,'movimientos'=>$rows,'meses'=>$monthly];
    }
}
