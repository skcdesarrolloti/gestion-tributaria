<?php
declare(strict_types=1);
namespace App\Services;

/** Propone datos sin convertir el RUT actual en prueba de condiciones históricas. */
final class PerfilAnualSugerencias
{
    public static function stored(\App\Models\Caracterizacion $model,array $taxpayer,int $owner,string $year): array
    {
        $id=(int)$taxpayer['id'];$blocks=$model->blocksFor($id,$owner,(int)$year);
        $rutYear=$model->latestRutYearFor($id,$owner)??$model->latestYearFor($id,$owner);
        $rutBlocks=$rutYear!==(int)$year?$model->blocksFor($id,$owner,$rutYear):[];
        return self::build($taxpayer,$year,$blocks,$rutBlocks,(string)$rutYear);
    }

    public static function build(array $taxpayer,string $year,array $blocks,array $rutBlocks=[],?string $rutYear=null): array
    {
        $record=IngresoData::read([],\App\Models\Ingreso::PROFILE_FIELDS);$record['anio']=$year;
        $origins=['anio'=>'Año que seleccionaste para registrar ingresos.'];$notes=[];$summary=[];
        $fiscal=$blocks['fiscal']['datos']['campos']??[];
        foreach (['residencia','regimen'] as $field) {
            if (isset(PerfilGuidance::fields($taxpayer['tipo'])[$field]['options'][$fiscal[$field]??''])) {
                $record[$field]=$fiscal[$field];$origins[$field]='Ya registrado en Condiciones tributarias de '.$year.'.';
            }
        }
        if (($fiscal['residencia']??'')==='doble') { $notes[]='Registraste posible doble residencia. Revisa el convenio y los hechos del año antes de elegir Residente o No residente.'; }
        $rut=array_replace($rutBlocks,array_intersect_key($blocks,array_flip(['identificacion','actividades','responsabilidades'])));
        $identity=$rut['identificacion']['datos']['campos']??[];$activities=$rut['actividades']['datos']['campos']??[];
        $codes=[];
        foreach ($rut['responsabilidades']['datos']['filas']??[] as $row) {
            if (preg_match('/^\d{1,2}$/D',(string)($row['codigo']??''))) { $codes[]=str_pad((string)(int)$row['codigo'],2,'0',STR_PAD_LEFT); }
        }
        $sourceYear=isset($blocks['responsabilidades'])?$year:($rutYear??$year);
        $source='RUT · casilla 53 del perfil '.$sourceYear;
        $regimes=array_intersect($codes,['04','05','47']);
        if (count(array_unique($regimes))===1 && $record['regimen']==='') {
            $code=reset($regimes);
            if ($code!=='04') { $record['regimen']=$code==='47'?'simple':'ordinario';$origins['regimen']=$source.' · responsabilidad '.$code.'. Confirma que correspondía a '.$year.'.'; }
            else { $notes[]='La responsabilidad 04 indica régimen especial. Confirma su modalidad; no se asumió régimen ordinario ni ESAL automáticamente.'; }
        } elseif (count(array_unique($regimes))>1) { $notes[]='El RUT contiene varias responsabilidades de renta ('.implode(', ',$regimes).'). Revisa el régimen del año; no se escogió uno por defecto.'; }
        if ($record['regimen']!=='' && count($regimes)===1) {
            $rutRegime=match(reset($regimes)) { '47'=>'simple','05'=>'ordinario',default=>'especial' };
            if ($record['regimen']!==$rutRegime) { $notes[]='La condición del año y la responsabilidad del RUT difieren. Conservamos tu dato anual; verifica cuál aplicaba antes de guardar.'; }
        }
        if ($taxpayer['tipo']==='juridica') { $record['servicios']='no_aplica';$origins['servicios']='Persona jurídica: no aplica la elección personal de costos o exención del art. 206.10.'; }
        elseif (($blocks['ingresos']['datos']['campos']['honorarios']??'')==='no') {
            $record['servicios']='no_aplica';$origins['servicios']='En Fuentes y eventos de '.$year.' registraste que no recibes honorarios o servicios independientes.';
        }
        if ($identity['rut_formulario']??'') { $summary[]='RUT '.$identity['rut_formulario']; }
        if ($identity['rut_fecha']??'') { $summary[]='Fecha de copia / consulta: '.$identity['rut_fecha']; }
        if ($activities['principal']??'') { $summary[]='Actividad principal registrada: '.$activities['principal'].match($activities['principal']) { '0020'=>' · Pensionado (puede tener otros ingresos)','0010'=>' · Asalariado (puede tener otros ingresos)',default=>'' }; }
        if ($codes) { $summary[]='Responsabilidades: '.implode(', ',array_unique($codes)); }
        if ($summary && $rutYear && $rutYear!==$year && !isset($blocks['responsabilidades'])) { $notes[]='La información RUT disponible está asociada a '.$rutYear.'. Se usa como referencia documental para '.$year.'; confirma los cambios que haya tenido.'; }
        $origins+=['residencia'=>'No se obtiene de la dirección del RUT. Confirma tu condición fiscal durante '.$year.'; si ya la registraste en ese año, se cargará aquí.',
            'regimen'=>'Falta una responsabilidad de renta concluyente o una condición anual guardada. Revisa la casilla 53 del RUT.',
            'servicios'=>'El RUT identifica actividades, pero no tu elección anual. Si no recibiste servicios personales, elige No aplica; si los recibiste, confirma costos o exención con sus soportes.'];
        $evidence=[];
        if ($summary) { $evidence[]='Referencia documental: '.implode('; ',$summary).'.'; }
        if ($fiscal && ($blocks['fiscal']['datos']['fuente']??'')!=='') { $evidence[]='Condiciones '.$year.': '.$blocks['fiscal']['datos']['fuente']; }
        $record['soporte_perfil']=mb_substr(implode(' ',$evidence),0,500);
        return compact('record','origins','notes','summary');
    }
}
