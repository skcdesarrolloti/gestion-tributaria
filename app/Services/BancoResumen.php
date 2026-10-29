<?php
declare(strict_types=1);
namespace App\Services;

/** Solo lecturas conciliadas. Un soporte pendiente nunca equivale a cero. */
final class BancoResumen
{
    public static function build(array $documents): array
    {
        $accounts=[];$total=['rendimientos'=>0,'gmf'=>0,'gmf_reintegro'=>0,'pension'=>0];$pending=0;
        foreach ($documents as $document) {
            $key=$document['banco'].'|'.$document['cuenta'];
            $accounts[$key]??=['banco'=>$document['banco'],'cuenta'=>$document['cuenta'],'meses'=>[],
                'totales'=>array_fill_keys(array_keys($total),0),'leidos'=>0];
            $month=(int)$document['mes'];
            // La base impide duplicados; una colección repetida tampoco se suma dos veces.
            if (isset($accounts[$key]['meses'][$month])) { continue; }
            $reading=ExtractoData::reading($document);$ok=($reading['estado']??'')==='leido';
            $values=[];
            if ($ok) {
                foreach ($total as $field=>$unused) {
                    if (!isset($reading['totales'][$field]) || !is_int($reading['totales'][$field])) { $ok=false;break; }
                    $values[$field]=$reading['totales'][$field];
                }
            }
            $accounts[$key]['meses'][$month]=['documento'=>(int)$document['id'],'leido'=>$ok,'valores'=>$ok?$values:[]];
            if (!$ok) { $pending++;continue; }
            $accounts[$key]['leidos']++;
            foreach ($total as $field=>$unused) { $accounts[$key]['totales'][$field]+=$values[$field];$total[$field]+=$values[$field]; }
        }
        ksort($accounts);
        foreach ($accounts as &$account) { ksort($account['meses']); }unset($account);
        return ['cuentas'=>array_values($accounts),'totales'=>$total,'pendientes'=>$pending];
    }
}
