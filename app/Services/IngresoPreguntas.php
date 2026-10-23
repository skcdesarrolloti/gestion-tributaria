<?php
declare(strict_types=1);
namespace App\Services;

final class IngresoPreguntas
{
    public static function groups(string $type): array
    {
        $activity=$type==='juridica'
            ?['honorarios'=>['¿Ingresos por prestación de servicios?','Ingresos de actividades ordinarias; confirmar su reconocimiento fiscal.'],'negocios'=>['¿Ventas o actividades de negocio?','Ingresos de actividades ordinarias.']]
            :['salarios'=>['¿Salarios y otros pagos laborales?','Trabajo de la cédula general del 210; en no residentes se revisa el 110.'],
                'honorarios'=>['¿Honorarios o servicios personales independientes?','Trabajo; la elección de costos o exención se confirma en las condiciones del año.'],
                'pensiones'=>['¿Pensiones o prestaciones pensionales?','Cédula de pensiones del 210; puede coexistir con otros ingresos.'],
                'negocios'=>['¿Ventas o actividades de negocio?','Revisar naturaleza de la operación antes de clasificarla.']];
        return [
            'actividad'=>['title'=>$type==='juridica'?'Actividad de la entidad':'Trabajo, pensión y actividad','fields'=>$activity],
            'capital'=>['title'=>'Capital y participaciones','fields'=>[
                'arriendos'=>['¿Arrendamientos?','Capital en el 210; en el 110 se revisa la actividad de la entidad.'],
                'rendimientos'=>['¿Intereses o rendimientos financieros?','Capital en el 210; ingresos financieros en el 110.'],
                'dividendos'=>['¿Dividendos o participaciones?','Separar origen y año de utilidades conforme al certificado.']]],
            'especiales'=>['title'=>'Exterior y operaciones por revisar','fields'=>[
                'exterior'=>['¿Ingresos o impuestos pagados en el exterior?','El exterior es un origen, no una cédula. Identificar la naturaleza y conversión de cada operación.'],
                'ocasionales'=>['¿Herencias, donaciones, loterías u otras ganancias ocasionales?','Sección independiente; revisar el hecho y sus soportes.'],
                'ventas_activos'=>['¿Ventas de inmuebles, acciones u otros activos?','La naturaleza y el tiempo de posesión requieren revisión antes de clasificar como renta o ganancia ocasional.'],
                'cripto'=>['¿Operaciones con criptoactivos?','Revisar operación, reconocimiento y soportes. No se asigna una cédula automáticamente.'],
                'ece'=>['¿Participaciones con rentas pasivas de entidades controladas del exterior?','Revisión ECE específica pendiente de depuración en el aplicativo.'],
                'otros'=>['¿Otros ingresos o eventos que no sabes clasificar?','Describe el caso en los soportes de su registro; no se envía automáticamente a No laborales.']]],
        ];
    }
    public static function prepare(array $block,string $type): array
    {
        $groups=self::groups($type);$answers=[];$pending=0;$first=null;
        foreach ($groups as $key=>&$group) {
            $group['pending']=0;
            foreach ($group['fields'] as $field=>$definition) {
                $value=$block['datos']['campos'][$field]??'';$answers[$field]=in_array($value,['si','no'],true)?$value:'';
                if ($answers[$field]==='') { $group['pending']++;$pending++;$first??=$key; }
            }
        }
        unset($group);return compact('groups','answers','pending','first')+['revision'=>(int)($block['revision']??0)];
    }
    public static function merge(array $saved,string $type,string $group,array $posted): array
    {
        $fields=self::groups($type)[$group]['fields']??null;
        if (!$fields || array_diff_key($posted,$fields) || count($posted)!==count($fields)) { throw new \InvalidArgumentException('Revisa el grupo y sus respuestas.'); }
        foreach ($fields as $key=>$definition) {
            if (!is_string($posted[$key]) || !in_array($posted[$key],['','si','no'],true)) { throw new \InvalidArgumentException('Usa Sí, No o Pendiente por confirmar.'); }
            $saved['campos'][$key]=$posted[$key];
        }
        $saved['filas']??=[];$saved['estado']='borrador';$saved['fecha']=date('Y-m-d');
        return $saved;
    }
    public static function profileErrors(array $profile,array $answers,string $type): array
    {
        if ($type==='juridica' || $profile['residencia']!=='residente') { return []; }
        if (($answers['honorarios']??'')==='si' && $profile['servicios']==='no_aplica') { return ['Confirmaste honorarios o servicios personales en este año. Revisa su tratamiento: costos, exención o pendiente de verificar; No aplica contradice esa respuesta.']; }
        if (($answers['honorarios']??'')==='no' && in_array($profile['servicios'],['costos','exencion'],true)) { return ['Confirmaste que no hay honorarios ni servicios personales. Revisa esa respuesta o selecciona No aplica para su tratamiento.']; }
        return [];
    }
}
