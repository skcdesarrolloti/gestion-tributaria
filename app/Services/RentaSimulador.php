<?php
declare(strict_types=1);

namespace App\Services;

final class RentaSimulador
{
    public static function calculate(array $raw, array $profile): array
    {
        $errors=SimuladorFields::errors($raw);
        if ($raw['declaraciones']==='1' && $raw['metodo_anticipo']==='promedio') {
            $errors[]='Primera declaración: no hay dos años declarados para calcular un promedio; utiliza la base actual.';
        }
        if ($profile['tipo']!=='natural' || $profile['residencia']!=='residente' || $profile['regimen']!=='ordinario') {
            $errors[]='El motor admite únicamente persona natural residente en régimen ordinario.';
        }
        if (!in_array((string)$profile['anio'],['2025','2026'],true)) { $errors[]='Año sin reglas verificadas.'; }
        foreach (['casos_especiales'=>'rentas o prestaciones especiales','ganancias_ocasionales'=>'ganancias ocasionales',
            'dividendos_otro_regimen'=>'dividendos de otro régimen','beneficios_revision'=>'beneficios por revisar'] as $key=>$label) {
            if ($raw[$key]==='1') { $errors[]='Cálculo pendiente por '.$label.': requiere regla o revisión particular; no se omite como cero.'; }
        }
        if ($errors) { return ['estado'=>'pendiente','errores'=>array_values(array_unique($errors)),'version'=>SimuladorFields::VERSION]; }
        $v=$raw;
        foreach (SimuladorFields::numeric() as $key) { $v[$key]=(int)$raw[$key]; }
        foreach (SimuladorFields::coverage() as $id=>$item) {
            if ($item['mode']==='revision' && $v['r_'.$id]>0) { $errors[]=$item['title'].': importe registrado, cálculo particular pendiente.'; }
        }
        $uvt=BeneficioCatalog::year((string)$profile['anio'])['uvt'];
        $v['salarios']=0; $pensGross=0; $pensHealth=0; $pensEx=0;
        foreach (range(1,12) as $m) {
            $v['salarios']+=$v['salario_'.$m]; $pensGross+=$v['pension_'.$m]; $pensHealth+=$v['salud_pension_'.$m];
            if ($v['salud_pension_'.$m]>$v['pension_'.$m]) { $errors[]='Salud de pensión supera la pensión en '.SimuladorFields::MONTHS[$m-1].'.'; }
            $pensEx+=min(max(0,$v['pension_'.$m]-$v['salud_pension_'.$m]),1000*$uvt);
        }
        if ($pensGross>0 && $v['pensionado']!=='si') { $errors[]='Hay ingresos de pensión: verifica la condición de pensionado.'; }
        if ($v['honorarios']>0 && $v['servicios']==='no_aplica') { $errors[]='Selecciona costos o exención para honorarios.'; }
        if ($v['cesantias']>0 && $v['promedio_cesantias']===0) { $errors[]='Completa promedio salarial de cesantías.'; }
        if (($v['b_fpv']+$v['b_afc'])>0 && $v['base_aportes']===0) { $errors[]='Falta base verificada para el límite FPV/AFC.'; }
        if ($v['b_cesantia_ind']>0 && $v['ingreso_gravable_cesantia_ind']===0) { $errors[]='Falta base verificada de cesantías independientes.'; }
        if ($v['b_rais']>0 && $v['base_rais']===0) { $errors[]='Falta base verificada del límite RAIS.'; }
        $workGross=$v['salarios']+$v['otros_trabajo']+$v['cesantias']+$v['honorarios'];
        $prepaid=0; foreach (range(1,12) as $m) { $prepaid+=$v['prepagada_'.$m]; }
        if ($prepaid>0 && $workGross===0) { $errors[]='Prepagada art. 387 sin rentas de trabajo: requiere revisión de procedencia.'; }
        $v['rais']=min($v['b_rais'],intdiv($v['base_rais'],4),2500*$uvt);
        $v['rais_laboral']=min($v['rais'],max(0,$v['salarios']+$v['otros_trabajo']-$v['incr_laboral']));
        $v['rais_honorarios']=$v['rais']-$v['rais_laboral'];
        $workIncr=$v['incr_laboral']+$v['incr_honorarios']+$v['rais'];
        $capitalGross=$v['arriendos']+$v['intereses']+$v['otros_capital'];
        $grossAll=$workGross+$capitalGross+$v['no_laborales']+$pensGross+$v['dividendos_ng']+$v['dividendos_g'];
        if ($v['base_aportes']>$grossAll || $v['base_rais']>$grossAll || $v['ingreso_gravable_cesantia_ind']>$grossAll) {
            $errors[]='Una base certificada supera los ingresos capturados: revisa integridad e imputación.';
        }
        $costs=['servicios'=>0,'capital'=>0,'no_laborales'=>0];
        foreach (SimuladorFields::coverage() as $id=>$item) {
            if ($item['mode']!=='costos') { continue; }
            foreach (array_keys($costs) as $source) { $costs[$source]+=$v['c_'.$id.'_'.$source]; }
        }
        if ($costs['servicios']>0 && $v['servicios']!=='costos') { $errors[]='No acumules costos de servicios con exención del 25% de esos ingresos.'; }
        if ($costs['servicios']>$v['honorarios']-$v['incr_honorarios']-$v['rais_honorarios']) { $errors[]='Costos de servicios superan su renta neta; pérdida requiere revisión.'; }
        $work=$workGross-$workIncr-$costs['servicios'];
        $capital=$capitalGross-$v['incr_capital']-$costs['capital'];
        $nonwork=$v['no_laborales']-$v['devoluciones']-$v['incr_no_laborales']-$costs['no_laborales'];
        if (min($work,$capital,$nonwork)<0) { $errors[]='Hay renta negativa: compensaciones/pérdidas requieren revisión; no se compensan con otra fuente automáticamente.'; }
        if ($v['incr_laboral']>$v['salarios']+$v['otros_trabajo'] || $v['incr_honorarios']+$v['rais_honorarios']>$v['honorarios']) { $errors[]='Revisa la imputación de INCR de trabajo y RAIS.'; }
        if ($v['b_dependientes72']>0 && $workGross===0) { $errors[]='La deducción adicional de dependientes requiere rentas de trabajo.'; }
        if ($errors) { return ['estado'=>'pendiente','errores'=>array_values(array_unique($errors)),'version'=>SimuladorFields::VERSION]; }
        $netBeforeCosts=$workGross-$workIncr+$capitalGross-$v['incr_capital']+$v['no_laborales']-$v['devoluciones']-$v['incr_no_laborales'];
        $d=RentaDeducciones::calculate($v,$uvt,$work,$netBeforeCosts);
        $generalBefore=$work+$capital+$nonwork;
        $limited=min($d['admitido'],$generalBefore);
        $general=max(0,$generalBefore-$limited-$d['dependientes72']-$d['compras1']);
        $corporate=RentaTarifa::percent($v['dividendos_g'],35);
        $dividends=$v['dividendos_ng']+$v['dividendos_g']-$corporate;
        $pension=max(0,$pensGross-$pensHealth-$pensEx);
        $base=RentaTarifa::thousand($general+$pension+$dividends);
        $tariff=RentaTarifa::calculate($base,$uvt);
        $divCredit=min($tariff['impuesto'],RentaTarifa::percent(max(0,$dividends-1090*$uvt),19));
        $grossTax=$tariff['impuesto']+$corporate;
        $donation=min(RentaTarifa::percent($v['b_donaciones'],25),intdiv($grossTax,4),max(0,$grossTax-$divCredit));
        $netTax=RentaTarifa::thousand(max(0,$grossTax-$divCredit-$donation));
        $percent=match($v['declaraciones']) { '1'=>25,'2'=>50,default=>75 };
        $advanceBase=$v['metodo_anticipo']==='promedio'?intdiv($netTax+$v['impuesto_anterior'],2):$netTax;
        $withholding=RentaTarifa::thousand($v['retenciones']);
        $advance=RentaTarifa::thousand(max(0,RentaTarifa::percent($advanceBase,$percent)-$withholding));
        $balance=$netTax+$advance-RentaTarifa::thousand($v['anticipo_anterior'])-$withholding-RentaTarifa::thousand($v['saldo_favor']);
        return ['estado'=>'calculado','version'=>SimuladorFields::VERSION,'errores'=>[],'uvt'=>$uvt,
            'trabajo_neto'=>$work,'capital_neto'=>$capital,'no_laboral_neto'=>$nonwork,'costos'=>$costs,
            'deducciones'=>$d,'beneficios_limitados'=>$limited,'renta_general'=>$general,'pension_bruta'=>$pensGross,
            'pension_salud'=>$pensHealth,'pension_exenta'=>$pensEx,'pension_gravada'=>$pension,'dividendos_base'=>$dividends,
            'base_gravable'=>$base,'tarifa'=>$tariff,'impuesto_dividendos'=>$corporate,'descuento_dividendos'=>$divCredit,
            'descuento_donaciones'=>$donation,'impuesto_neto'=>$netTax,'retenciones'=>$withholding,'anticipo'=>$advance,
            'saldo_pagar'=>max(0,$balance),'saldo_favor'=>max(0,-$balance),'patrimonio_liquido'=>$v['patrimonio']-$v['deudas'],
            'advertencias'=>['Estimación de planeación: no incluye sanciones, ganancias ocasionales ni casos especiales.',
                'Los valores de INCR y las bases certificadas requieren soporte; no son beneficios aprobados por el sistema.',
                'Redondeo de referencia: base agregada e importes finales a miles; conciliar casillas con formulario del año.']];
    }
}
