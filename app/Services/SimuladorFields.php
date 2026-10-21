<?php
declare(strict_types=1);

namespace App\Services;

final class SimuladorFields
{
    public const VERSION='pn-2025-2026-v1';
    public const MONTHS=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

    public static function basic(): array
    {
        return [
            'honorarios'=>['Honorarios y servicios independientes','ET 103'],
            'otros_trabajo'=>['Otros pagos de trabajo elegibles para art. 206.10, sin cesantías','ET 103 y 206'],
            'cesantias'=>['Cesantías e intereses realizados en el año, separados de los salarios','ET 27 y 206.4'],
            'promedio_cesantias'=>['Salario mensual promedio de los últimos seis meses (solo si hay cesantías)','ET 206.4'],
            'incr_laboral'=>['Salud/pensión obligatorias y otros INCR laborales procedentes','ET 55 y 56'],
            'incr_honorarios'=>['INCR procedentes de honorarios, incluidos aportes obligatorios','ET 55 y 56'],
            'arriendos'=>['Ingresos brutos por arrendamientos','ET 335'],
            'intereses'=>['Rendimientos e intereses financieros brutos','ET 335'],
            'otros_capital'=>['Otros ingresos de capital','ET 335'],
            'incr_capital'=>['INCR procedentes de capital, con porcentaje anual verificado','ET 38 a 41'],
            'no_laborales'=>['Ventas y otros ingresos no laborales','ET 335'],
            'devoluciones'=>['Devoluciones, rebajas y descuentos no laborales','ET 26'],
            'incr_no_laborales'=>['INCR procedentes no laborales','ET 336.2'],
            'dividendos_ng'=>['Dividendos nacionales de utilidades 2017+ no gravadas en la sociedad','ET 49 y 242'],
            'dividendos_g'=>['Dividendos nacionales de utilidades 2017+ gravadas en la sociedad','ET 49 y 242'],
            'base_aportes'=>['Ingreso laboral/tributario certificado para límite 30% FPV+AFC','ET 126-1 y 126-4'],
            'base_rais'=>['Ingreso laboral/tributario certificado para límite 25% RAIS','ET 55'],
            'ingreso_gravable_cesantia_ind'=>['Ingreso gravable certificado para límite de cesantías de independientes','ET 126-1'],
            'retenciones'=>['Retenciones del año, incluidas las de dividendos','ET 373'],
            'anticipo_anterior'=>['Anticipo del año anterior imputable a este año','ET 807'],
            'saldo_favor'=>['Saldo a favor anterior disponible, no solicitado ni compensado','ET 815'],
            'impuesto_anterior'=>['Impuesto neto de renta del año anterior, para promedio del anticipo','ET 807'],
            'patrimonio'=>['Patrimonio bruto fiscal, incluidos activos exteriores cuando corresponda','ET 261'],
            'deudas'=>['Deudas fiscales procedentes (no disminuyen directamente el impuesto)','ET 283'],
        ];
    }

    public static function coverage(): array
    {
        $manual=['inflacionario','cesantias-laborales'];
        $pending=['indemnizaciones','impuesto-exterior','investigacion','iva-productivo','bancos-alimentos'];
        $items=[];
        foreach (BeneficioCatalog::all() as $item) {
            $id=$item['id']; $group=$item['group'];
            $item['mode']=match(true) {
                $group==='exclusiones'=>'control',
                $group==='costos'=>'costos',
                $group==='especiales' || in_array($id,$pending,true)=>'revision',
                in_array($id,$manual,true)=>'dato-verificado',
                default=>'automatico',
            };
            $items[$id]=$item;
        }
        return $items;
    }

    public static function numeric(): array
    {
        $keys=array_keys(self::basic());
        foreach (range(1,12) as $m) {
            foreach (['salario','pension','salud_pension','prepagada','dep387'] as $field) { $keys[]=$field.'_'.$m; }
        }
        foreach (self::coverage() as $id=>$item) {
            if ($item['mode']==='costos') {
                foreach (['servicios','capital','no_laborales'] as $source) { $keys[]='c_'.$id.'_'.$source; }
            }
            if ($item['mode']==='revision') { $keys[]='r_'.$id; }
        }
        return array_merge($keys,['b_vivienda','b_icetex','b_gmf','b_dependientes72','b_facturas',
            'b_cesantia_ind','b_fpv','b_afc','b_rais','b_donaciones']);
    }

    public static function read(array $source): array
    {
        $keys=array_merge(self::numeric(),['nombre','notas','ceros_confirmados','elegibilidad','pensionado','servicios',
            'declaraciones','metodo_anticipo','casos_especiales','ganancias_ocasionales','dividendos_otro_regimen','beneficios_revision']);
        return IngresoData::read($source,$keys);
    }

    public static function errors(array $v): array
    {
        $errors=self::storageErrors($v);
        if ($v['ceros_confirmados']!=='1') { $errors[]='Confirma que los importes vacíos son cero conocido; un dato pendiente impide calcular.'; }
        if ($v['elegibilidad']!=='1') { $errors[]='Confirma que los valores solicitados cumplen sus condiciones y no están duplicados.'; }
        foreach (range(1,12) as $m) {
            if (!in_array($v['dep387_'.$m],['','0','1'],true)) { $errors[]='Dependientes art. 387 se marca por mes, no por número de personas.'; }
        }
        if (!in_array($v['b_dependientes72'],['','0','1','2','3','4'],true)) { $errors[]='Máximo cuatro dependientes adicionales.'; }
        foreach (['pensionado'=>['si','no'],'servicios'=>['costos','exencion','no_aplica'],
            'declaraciones'=>['1','2','3'],'metodo_anticipo'=>['actual','promedio']] as $key=>$allowed) {
            if (!in_array($v[$key],$allowed,true)) { $errors[]='Selecciona '.$key.' antes de calcular.'; }
        }
        return array_unique($errors);
    }

    public static function storageErrors(array $v): array
    {
        $errors=[];
        foreach (self::numeric() as $key) {
            if ($v[$key]!=='' && !preg_match('/^(0|[1-9][0-9]{0,11})$/D',$v[$key])) { $errors[]='Revisa '.self::label($key).': pesos enteros, sin separadores y máximo 12 dígitos.'; }
        }
        if (mb_strlen($v['nombre'])>100 || mb_strlen($v['notas'])>1500) { $errors[]='Nombre máximo 100 caracteres y notas máximo 1.500.'; }
        return $errors;
    }

    public static function benefitInputs(): array
    {
        return ['vivienda'=>'b_vivienda','icetex'=>'b_icetex','gmf'=>'b_gmf','dependientes-72'=>'b_dependientes72',
            'factura-electronica'=>'b_facturas','cesantia-independiente'=>'b_cesantia_ind','pension-voluntaria'=>'b_fpv',
            'afc'=>'b_afc','rais-voluntario'=>'b_rais','donaciones'=>'b_donaciones'];
    }

    private static function label(string $key): string
    {
        if (isset(self::basic()[$key])) { return self::basic()[$key][0]; }
        if (preg_match('/^(salario|pension|salud_pension|prepagada|dep387)_([0-9]+)$/D',$key,$match)) {
            $labels=['salario'=>'Salario','pension'=>'Pensión','salud_pension'=>'Salud de pensión','prepagada'=>'Prepagada','dep387'=>'Dependientes'];
            return $labels[$match[1]].' · '.self::MONTHS[(int)$match[2]-1];
        }
        $coverage=self::coverage();
        foreach (self::benefitInputs() as $id=>$input) { if ($input===$key) { return $coverage[$id]['title']; } }
        foreach ($coverage as $id=>$item) {
            if ($key==='r_'.$id || str_starts_with($key,'c_'.$id.'_')) { return $item['title']; }
        }
        return 'el valor ingresado';
    }
}
