<?php
declare(strict_types=1);
namespace App\Services;

final class PrestamoCruce
{
    public static function months(array $loans,array $pensions,array $banks,string $year): array
    {
        $months=array_fill(1,12,['pago'=>null,'capital'=>null,'intereses'=>null,'seguros'=>null,'otros'=>null,'documentos'=>[],'neto'=>null,'libranza'=>null,'banco'=>null]);
        foreach ($loans as $doc) {
            if (substr($doc['fecha_pago'],0,4)!==$year) { continue; }$m=(int)substr($doc['fecha_pago'],5,2);$d=$doc['datos']['pagado'];
            foreach (['pago'=>$d['total'],'capital'=>$d['capital'],'intereses'=>$d['intereses'],'seguros'=>($d['vida']??0)+($d['incendio']??0),'otros'=>($d['mora']??0)+($d['comision']??0)+($d['cobranza']??0)] as $k=>$v) { $months[$m][$k]=($months[$m][$k]??0)+$v; }
            $months[$m]['documentos'][]=$doc;
        }
        foreach ($pensions as $p) { $m=(int)$p['mes'];$months[$m]['neto']=$p['datos']['neto']??null;$months[$m]['libranza']=$p['datos']['libranza']??null; }
        foreach ($banks as $doc) { $d=ExtractoData::reading($doc);if (($d['estado']??'')==='leido') { $m=(int)$doc['mes'];$months[$m]['banco']=($months[$m]['banco']??0)+$d['totales']['pension']; } }
        return $months;
    }
}
