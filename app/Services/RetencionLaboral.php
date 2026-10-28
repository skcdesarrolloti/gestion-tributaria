<?php
declare(strict_types=1);
namespace App\Services;

final class RetencionLaboral
{
    public static function groups(array $rows): array
    {
        $groups=[];
        foreach ($rows as $r) {
            $month=(int)substr($r['inicio'],5,2);$key=$month.'-'.$r['empleador_documento'];
            $groups[$key]['mes']=$month;$groups[$key]['empleador']=$r['datos']['empleador'];
            $groups[$key]['documento']=$r['empleador_documento'];$groups[$key]['rows'][]=$r;
        }
        foreach ($groups as &$g) {
            usort($g['rows'],static fn($a,$b)=>strcmp($a['inicio'],$b['inicio']));
            $g['valores']=NominaData::monthly($g['rows'])[$g['mes']];
            // Un cambio previo del mismo pagador puede modificar el consumo de topes anuales.
            $history=array_values(array_filter($rows,static fn($r)=>$r['empleador_documento']===$g['documento'] && (int)substr($r['inicio'],5,2)<=$g['mes']));
            $g['huella']=NominaReserva::fingerprint($history);
            $g['completo']=self::complete($g['rows']);
        }
        unset($g);return $groups;
    }
    private static function complete(array $rows): bool
    {
        $next=1;$last=min(30,(int)date('t',strtotime($rows[0]['inicio'])));
        foreach ($rows as $r) {
            if ((int)substr($r['inicio'],8,2)!==$next) { return false; }
            $next=(int)substr($r['fin'],8,2)+1;
        }
        return $next>$last;
    }
    public static function check(array $g, array $review, int $year): array
    {
        $pending=static fn($reason)=>['estado'=>'Pendiente de soporte','motivo'=>$reason];
        if (!$g['completo']) { return $pending('Faltan períodos para cubrir el mes de este empleador.'); }
        if (!$review || ($review['huella']??'')!==$g['huella']) { return $pending('Revisa el procedimiento y los soportes de la nómina actual; una corrección invalida la revisión anterior.'); }
        if ($g['valores']['retroactivos']>0) { return $pending('Los retroactivos requieren depuración particular; este control cubre pagos ordinarios.'); }
        $uvt=BeneficioCatalog::year((string)$year)['uvt']*100;$v=$g['valores'];
        $net=max(0,$v['devengado']-$v['aportes']);
        $ded=min($review['vivienda'],100*$uvt)+min($review['prepagada'],16*$uvt);
        $ded+=$review['dependientes']==='si'?min(intdiv($v['devengado'],10),32*$uvt):0;
        $ex=min(intdiv(max(0,$net-$ded),4),max(0,790*$uvt-$review['usado25']));
        $benefits=min($ded+$ex,intdiv($net*40,100),max(0,1340*$uvt-$review['usado1340']));
        $base=max(0,$net-$benefits);
        $expected=$review['procedimiento']==='1'?RetencionTarifa::calculate($base,intdiv($uvt,100)):
            intdiv($base*$review['porcentaje']+5000,10000);
        $expected=RetencionTarifa::rounded($expected,$review['redondeo']);
        $difference=$v['retencion']-$expected;
        return ['estado'=>abs($difference)<=100?'OK':'Revisar','motivo'=>'Comparación mensual por empleador.',
            'base'=>$base,'deducciones'=>$ded,'exencion25'=>$ex,'beneficios'=>$benefits,'esperada'=>$expected,
            'real'=>$v['retencion'],'diferencia'=>$difference];
    }
}
