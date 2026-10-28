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
    public static function latestKey(array $groups): string
    {
        uasort($groups,static fn($a,$b)=>$b['mes']<=>$a['mes']?:strcmp($a['documento'],$b['documento']));
        foreach ($groups as $key=>$g) { if ($g['completo'] && $g['valores']['retroactivos']===0) { return (string)$key; } }
        return (string)(array_key_first($groups)??'');
    }
    public static function check(array $g, array $review, int $year): array
    {
        $pending=static fn($reason)=>['estado'=>'Pendiente de soporte','motivo'=>$reason];
        if (!$g['completo']) { return $pending('Faltan períodos para cubrir el mes de este empleador.'); }
        if (!$review) { return $pending('La nómina ya está cargada. Falta confirmar el método y los datos que usó tu empleador para calcular el descuento. Completa el paso 2; no tienes que adivinar.'); }
        if (($review['huella']??'')!==$g['huella']) { return $pending('La nómina cambió desde la última revisión. Confirma otra vez los datos del paso 2 antes de comparar.'); }
        if (($review['parametros']??ParametrosAnuales::originalFingerprint($year))!==ParametrosAnuales::fingerprint($year)) {
            return $pending('Los parámetros del año cambiaron desde la revisión. Contrasta la nueva versión antes de confirmar otra vez.');
        }
        if ($g['valores']['retroactivos']>0) { return $pending('Los retroactivos requieren depuración particular; este control cubre pagos ordinarios.'); }
        $rules=ParametrosAnuales::year($year);$uvt=$rules['uvt']*100;$v=$g['valores'];
        $net=max(0,$v['devengado']-$v['aportes']);
        $ded=min($review['vivienda'],$rules['vivienda_uvt']*$uvt)+min($review['prepagada'],$rules['prepagada_uvt']*$uvt);
        $ded+=$review['dependientes']==='si'?min(intdiv($v['devengado']*$rules['dependientes_pct'],100),$rules['dependientes_uvt']*$uvt):0;
        $ex=min(intdiv(max(0,$net-$ded)*$rules['exencion_pct'],100),max(0,$rules['exencion_uvt']*$uvt-$review['usado25']));
        $benefits=min($ded+$ex,intdiv($net*$rules['conjunto_pct'],100),max(0,$rules['conjunto_uvt']*$uvt-$review['usado1340']));
        $base=max(0,$net-$benefits);
        $expected=$review['procedimiento']==='1'?RetencionTarifa::calculate($base,intdiv($uvt,100),$rules['tramos']):
            intdiv($base*$review['porcentaje']+5000,10000);
        $expected=RetencionTarifa::rounded($expected,$review['redondeo']);
        $difference=$v['retencion']-$expected;
        return ['estado'=>abs($difference)<=100?'OK':'Revisar','motivo'=>'Comparación mensual por empleador.',
            'base'=>$base,'deducciones'=>$ded,'exencion25'=>$ex,'beneficios'=>$benefits,'esperada'=>$expected,
            'real'=>$v['retencion'],'diferencia'=>$difference];
    }
}
