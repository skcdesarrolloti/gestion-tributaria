<?php
declare(strict_types=1);
namespace App\Services;

final class PensionResumen
{
    public static function months(array $movements,array $documents,array $receipts): array
    {
        $months=array_fill(1,12,['bruto'=>null,'salud'=>null,'neto'=>null,'bancos'=>[],'comprobantes'=>[]]);
        foreach ($movements as $m) {
            if ($m['estado']!=='real' || !in_array((int)$m['casilla'],[99,100],true)) { continue; }
            $month=(int)substr($m['fecha'],5,2);$field=(int)$m['casilla']===99?'bruto':'salud';
            $months[$month][$field]=($months[$month][$field]??0)+(int)$m['valor_cop'];
        }
        foreach (ExtractoData::monthlyDocuments($documents) as $doc) {
            $month=(int)$doc['mes'];$reading=ExtractoData::reading($doc);
            $months[$month]['bancos'][]=['id'=>(int)$doc['id'],'label'=>$doc['banco'].' '.$doc['cuenta']];
            if (($reading['estado']??'')==='leido') { $months[$month]['neto']=($months[$month]['neto']??0)+$reading['totales']['pension']; }
        }
        foreach ($receipts as $row) { $months[(int)$row['mes']]['comprobantes'][]=(int)$row['comprobante_id']; }
        return $months;
    }
}
