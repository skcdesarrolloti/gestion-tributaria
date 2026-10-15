<?php
declare(strict_types=1);

namespace App\Services;

final class ExogenaSchemaData
{
    public static function read(array $source, array $schema): array
    {
        return IngresoData::read($source,array_merge(['anio'],array_keys($schema['fields'])));
    }

    public static function errors(array $record, array $schema): array
    {
        $errors=[];
        if ($record['anio']!==$schema['year']) { $errors[]='Esta estructura corresponde al año gravable '.$schema['year'].'.'; }
        foreach ($schema['fields'] as $key=>[$label,$kind,$max,$required,$help]) {
            $value=$record[$key];
            if ($value==='') {
                if ($required) { $errors[]='Completa '.$label.'. Un dato desconocido no equivale a cero.'; }
                continue;
            }
            if (mb_strlen($value)>$max) { $errors[]="$label supera $max caracteres."; }
            $pattern=match($kind) {
                'code'=>'/^[0-9]{1,'.$max.'}$/D',
                'id'=>'/^[A-Za-z0-9]{1,'.$max.'}$/D',
                'money','digits'=>'/^(0|[1-9][0-9]{0,'.($max-1).'})$/D',
                default=>null,
            };
            if ($pattern && !preg_match($pattern,$value)) { $errors[]='Revisa '.$label.': sin separadores, signos ni notación científica.'; }
            if (in_array($key,['cpt','tdoc','tdocb','tdocde','tdocpcc','tdopa','dpto','mun','mcpo'],true)
                && strlen($value)!==$max) { $errors[]="$label requiere $max dígitos, conservando ceros iniciales."; }
        }
        if ($schema['code']!=='2276' && $record['raz']==='' && ($record['apl1']==='' || $record['nom1']==='')) {
            $errors[]='Completa razón social o primer apellido y primer nombre del informado.';
        }
        if (isset($record['pais'],$record['dir']) && ltrim($record['pais'],'0')==='169'
            && ($record['dir']==='' || $record['dpto']==='' || ($record['mun']??$record['mcpo']??'')==='')) {
            $errors[]='Para Colombia completa dirección, departamento y municipio.';
        }
        if (isset($record['tcon'])) {
            $type=ltrim($record['tcon'],'0');
            if (!in_array($type,['1','2','3','4','5','6'],true)) { $errors[]='Revisa el tipo de contrato (1 a 6).'; }
            if (in_array($type,['3','4','5'],true) && $record['niespj']==='') { $errors[]='Este tipo de contrato requiere NIESPJ.'; }
        }
        foreach ([['tdocde','nitde'],['tdocpcc','nitpcc']] as [$type,$id]) {
            if (isset($record[$type],$record[$id]) && (($record[$type]==='')!==($record[$id]===''))) {
                $errors[]='Completa juntos el tipo y la identificación: '.$schema['fields'][$id][0].'.';
            }
        }
        if ($schema['code']==='1010' && preg_match('/^[0-9]{1,2}$/D',$record['dec'])) {
            $places=(int)$record['dec'];
            if ($places>20) { $errors[]='La posición decimal del porcentaje no puede superar 20.'; }
            elseif (preg_match('/^[0-9]+$/D',$record['por'])) {
                $digits=ltrim($record['por'],'0')?:'0'; $limit='100'.str_repeat('0',$places);
                if (strlen($digits)>strlen($limit) || (strlen($digits)===strlen($limit) && strcmp($digits,$limit)>0)) {
                    $errors[]='El porcentaje de participación no puede superar 100%.';
                }
            }
        }
        return $errors;
    }
}
