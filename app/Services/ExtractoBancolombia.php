<?php
declare(strict_types=1);
namespace App\Services;

/** Ahorros Bancolombia: una columna firmada de valor y saldo por operación. */
final class ExtractoBancolombia
{
    public static function metadata(string $text,array $pages): array
    {
        $normal=PrestamoCajaSocial::normal($text);
        if (!str_contains($normal,'BANCOLOMBIA') || !str_contains($normal,'CUENTA DE AHORROS')
            || !preg_match('/DESDE:\s*(20\d{2})\/(\d{2})\/(\d{2})\s+HASTA:\s*(20\d{2})\/(\d{2})\/(\d{2})/',$text,$m)) { throw new \InvalidArgumentException('Formato Bancolombia pendiente.'); }
        if (!checkdate((int)$m[2],(int)$m[3],(int)$m[1]) || !checkdate((int)$m[5],(int)$m[6],(int)$m[4])) { throw new \InvalidArgumentException('Fecha bancaria inválida.'); }
        $printed=$m[1].'-'.$m[2].'-'.$m[3];$from=new \DateTimeImmutable($printed);$end=new \DateTimeImmutable($m[4].'-'.$m[5].'-'.$m[6]);
        // DESDE puede ser el saldo al cierre anterior: 31/03 corresponde a movimientos desde 01/04.
        if ($from->format('d')===$from->format('t')) { $from=$from->modify('+1 day'); }
        $a=(int)$from->format('m');$b=(int)$end->format('m');
        if ($from->format('d')!=='01' || $end->format('d')!==$end->format('t') || $from->format('Y')!==$end->format('Y') || $a>$b || $b-$a>2) { throw new \InvalidArgumentException('Período bancario no admitido.'); }
        $holder='';$account='';foreach ($pages[0] as $c) {
            if ($c['x']>=10 && $c['x']<40 && $c['y']>672 && $c['y']<678) { $holder=$c['text']; }
            if ($c['x']>=390 && $c['x']<410 && abs($c['y']-664.8)<1 && preg_match('/^\d{11}$/D',$c['text'])) { $account=substr($c['text'],-4); }
        }
        if (!$holder || !$account) { throw new \InvalidArgumentException('No se reconoció titular o cuenta.'); }
        return ['banco'=>'BANCOLOMBIA','titular'=>$holder,'cuenta'=>$account,'inicio'=>$from->format('Y-m-d'),'fin'=>$end->format('Y-m-d'),
            'fecha_saldo_anterior'=>$printed,'meses_cubiertos'=>range($a,$b),'paginas'=>count($pages)];
    }
    public static function parse(string $text,array $pages): array
    {
        $meta=self::metadata($text,$pages);$rows=[];$year=(int)substr($meta['inicio'],0,4);
        foreach ($pages as $p=>$cells) {
            $dates=array_filter($cells,static fn($c)=>$c['x']>=30 && $c['x']<60 && $c['y']<610 && preg_match('/^\d{1,2}\/\d{2}$/D',$c['text']));
            usort($dates,static fn($a,$b)=>$b['y']<=>$a['y']);
            foreach ($dates as $date) {
                [$day,$month]=array_map('intval',explode('/',$date['text']));
                if (!checkdate($month,$day,$year)) { throw new \InvalidArgumentException('Fecha de movimiento inválida.'); }
                $r=['fecha'=>sprintf('%04d-%02d-%02d',$year,$month,$day),'numero'=>'','descripcion'=>'','pagina'=>$p+1];$value=null;$balance=null;
                foreach ($cells as $c) {
                    if (abs($c['y']-$date['y'])>0.7) { continue; }
                    if ($c['x']>=75 && $c['x']<245) { $r['descripcion'].=($r['descripcion']?' ':'').$c['text']; }
                    elseif ($c['x']>=395 && $c['x']<505) { if ($value!==null) { throw new \InvalidArgumentException('Importe ambiguo.'); }$value=self::cents($c['text']); }
                    elseif ($c['x']>=505 && $c['x']<605) { if ($balance!==null) { throw new \InvalidArgumentException('Saldo ambiguo.'); }$balance=self::cents($c['text']); }
                }
                if (!$r['descripcion'] || !$value || $balance===null) { throw new \InvalidArgumentException('Movimiento incompleto.'); }
                $r+=['operacion'=>$r['fecha'],'cargo'=>max(0,-$value),'abono'=>max(0,$value),'saldo'=>$balance];$r['categoria']=self::category($r);$rows[]=$r;
            }
        }
        return ExtractoPeriodo::reconcile($meta,self::summary($pages[0]),$rows,'bancolombia-ahorros-v1');
    }
    private static function summary(array $cells): array
    {
        $result=[];foreach (['inicial'=>[200,310,472.6],'abonos'=>[200,310,457.6],'cargos'=>[200,310,442.6],'final'=>[200,310,427.6],
            'intereses'=>[500,590,441.8],'retenciones'=>[500,590,426.8]] as $key=>[$left,$right,$y]) {
            $found=array_values(array_filter($cells,static fn($c)=>$c['x']>$left && $c['x']<$right && abs($c['y']-$y)<0.5));
            if (count($found)!==1) { throw new \InvalidArgumentException('Resumen incompleto.'); }$result[$key]=self::cents($found[0]['text']);
        }return $result;
    }
    private static function cents(string $value): int
    {
        return ExtractoBbva::cents(preg_replace('/^(-?)\.([0-9]{2})$/D','${1}0.$2',$value));
    }
    private static function category(array $row): string
    {
        $t=PrestamoCajaSocial::normal($row['descripcion']);
        if ($row['abono']>0) {
            if (str_starts_with($t,'PAGO PENSION ')) { return 'pension'; }
            if ($t==='ABONO INTERESES AHORROS') { return 'rendimientos'; }
            return 'otros_abonos';
        }
        if (str_contains($t,'4X1000')) { return 'gmf'; }
        if (str_starts_with($t,'RETEFUENTE') || str_starts_with($t,'RETENCION EN LA FUENTE')) { return 'retenciones'; }
        return 'otros_debitos';
    }
}
