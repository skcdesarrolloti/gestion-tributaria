<?php
declare(strict_types=1);
namespace App\Services;

/** Hipótesis salarial explícita; reutiliza el motor anual y no altera ingresos. */
final class NominaReserva
{
    public const MONEY=[
        'futuro_bruto'=>'Devengado adicional esperado hasta diciembre (incluye primas pendientes)',
        'futuro_aportes'=>'Aportes obligatorios adicionales esperados hasta diciembre',
        'futuro_retencion'=>'Retenciones laborales adicionales esperadas hasta diciembre',
        'anticipo_anterior'=>'Anticipo anterior imputable a este año',
        'saldo_favor'=>'Saldo a favor anterior disponible y no utilizado',
    ];
    public static function fingerprint(array $rows): string
    {
        $parts=array_map(static fn($r)=>[$r['id'],$r['revision'],$r['huella']],$rows);
        sort($parts);return hash('sha256',json_encode($parts,JSON_THROW_ON_ERROR));
    }
    public static function read(array $post): array
    {
        $v=[];
        foreach (self::MONEY as $key=>$label) {
            $value=\input($post,$key);
            if (!preg_match('/^(0|[1-9][0-9]{0,10})$/D',$value)) { throw new \InvalidArgumentException($label.': escribe pesos enteros, incluido 0 si corresponde.'); }
            $v[$key]=(int)$value;
        }
        $v['declaraciones']=\input($post,'declaraciones');
        if (!in_array($v['declaraciones'],['1','2','3'],true)) { throw new \InvalidArgumentException('Selecciona el número de declaraciones para estimar el anticipo.'); }
        $v['cuotas']=filter_var(\input($post,'cuotas'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>12]]);
        if ($v['cuotas']===false || \input($post,'hipotesis')!=='1') { throw new \InvalidArgumentException('Indica entre 1 y 12 reservas y confirma el alcance de la hipótesis.'); }
        if ($v['futuro_aportes']+$v['futuro_retencion']>$v['futuro_bruto']) { throw new \InvalidArgumentException('Aportes y retenciones futuros superan el devengado futuro.'); }
        return $v;
    }
    public static function calculate(array $rows,array $settings,array $profile): array
    {
        if (!$rows || !$settings || ($settings['huella']??'')!==self::fingerprint($rows)) {
            return ['estado'=>'pendiente','motivo'=>'Confirma los valores futuros y los saldos anteriores con la nómina actual. Cada importación o corrección exige revisar la proyección.'];
        }
        $gross=0;$contributions=0;$withheld=0;$cut='';
        foreach ($rows as $r) {
            $v=NominaData::amounts($r['datos']['valores']);$gross+=$v['devengado'];
            $contributions+=$v['aportes'];$withheld+=$v['retencion'];$cut=max($cut,$r['fin']);
            if ($v['retroactivos']>0) { return ['estado'=>'pendiente','motivo'=>'Hay retroactivos: revisa su tratamiento en el simulador anual antes de estimar la reserva.']; }
        }
        $raw=SimuladorFields::read(array_fill_keys(SimuladorFields::numeric(),'0'));
        $raw=array_replace($raw,['ceros_confirmados'=>'1','elegibilidad'=>'1','pensionado'=>'no','servicios'=>'no_aplica',
            'declaraciones'=>$settings['declaraciones'],'metodo_anticipo'=>'actual',
            'salario_1'=>(string)((int)round($gross/100)+$settings['futuro_bruto']),
            'incr_laboral'=>(string)((int)round($contributions/100)+$settings['futuro_aportes']),
            'retenciones'=>(string)((int)round($withheld/100)+$settings['futuro_retencion']),
            'anticipo_anterior'=>(string)$settings['anticipo_anterior'],'saldo_favor'=>(string)$settings['saldo_favor']]);
        $result=RentaSimulador::calculate($raw,$profile);
        if ($result['estado']!=='calculado') { return ['estado'=>'pendiente','motivo'=>implode(' ',$result['errores'])]; }
        return $result+['corte'=>$cut,'retenido_real'=>$withheld/100,'retencion_futura'=>$settings['futuro_retencion'],
            'reserva'=>(int)ceil($result['saldo_pagar']/$settings['cuotas']),'cuotas'=>$settings['cuotas']];
    }
}
