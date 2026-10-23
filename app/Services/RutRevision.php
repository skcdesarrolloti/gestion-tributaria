<?php
declare(strict_types=1);
namespace App\Services;

/** Contraste de datos leídos, sin certificar exhaustividad ni estado oficial DIAN. */
final class RutRevision
{
    public static function build(array $parsed,array $blocks,array $schemas,array $taxpayer): array
    {
        $groups=[];$counts=['coincide'=>0,'pendiente'=>0,'diferente'=>0];
        foreach ($parsed['fields']??[] as $block=>$fields) {
            foreach ($fields as $key=>$value) {
                if ((string)$value==='') { continue; }
                $current=(string)($blocks[$block]['datos']['campos'][$key]??'');
                self::add($groups,$counts,$block,$schemas[$block]['title']??$block,$schemas[$block]['fields'][$key]['label']??$key,(string)$value,$current,$schemas[$block]['fields'][$key]['options'][$value]??'');
            }
        }
        foreach ($parsed['rows']??[] as $block=>$rows) {
            $saved=$blocks[$block]['datos']['filas']??[];
            foreach ($rows as $i=>$row) {
                $groupKey=$block;$title=$schemas[$block]['title']??$block;
                if ($block==='relacionados') {
                    $groupKey.='-'.($row['rol']??'otro');
                    $title=match($row['rol']??'') { 'representante','apoderado','heredero'=>'RUT · Representantes y apoderados','socio_junta','socio','junta'=>'RUT · Socios y junta por precisar','contador'=>'RUT · Contador','revisor'=>'RUT · Revisor fiscal',default=>$title };
                }
                $index=null;
                if (in_array($block,['relacionados','establecimientos'],true)) {
                    $index=RutImportRows::index($saved,$row+['nombre'=>'','direccion'=>'','matricula'=>'','parentesco'=>''],$block);
                    if ($index===null && ($row['soporte']??'')!=='') {
                        $matches=array_keys(array_filter($saved,static fn($r)=>($r['soporte']??'')===$row['soporte']));
                        if (count($matches)===1) { $index=$matches[0]; }
                    }
                } else {
                    foreach ($saved as $j=>$candidate) { if (($candidate['codigo']??'')===($row['codigo']??'')) { $index=$j;break; } }
                }
                foreach ($row as $key=>$value) {
                    if ((string)$value==='' || str_starts_with($key,'_')) { continue; }
                    $current=$index===null?'':(string)($saved[$index][$key]??'');
                    self::add($groups,$counts,$groupKey,$title,'Renglón '.($i+1).' · '.($schemas[$block]['rows'][$key]['label']??$key),(string)$value,$current,$schemas[$block]['rows'][$key]['options'][$value]??'');
                }
            }
        }
        if (($taxpayer['documento_tipo']??'')==='NIT') {
            self::add($groups,$counts,'identificacion','RUT · Identificación','5 · NIT',(string)($parsed['nit']??''),(string)$taxpayer['documento_numero']);
            self::add($groups,$counts,'identificacion','RUT · Identificación','6 · DV',(string)($parsed['dv']??''),(string)($taxpayer['dv']??''));
        }
        $pages=$parsed['lectura_hojas']??[];
        if (!array_filter($pages,static fn($p)=>(int)$p['pagina']===1)) { array_unshift($pages,['pagina'=>1,'hoja'=>1,'resultado'=>'Datos de la hoja principal registrados; contrastar con el PDF']); }
        return compact('groups','counts','pages')+['pages_count'=>(int)($parsed['pages']??0),'reader'=>$parsed['reader']??'No informado'];
    }
    private static function add(array &$groups,array &$counts,string $key,string $title,string $label,string $pdf,string $current,string $meaning=''): void
    {
        $state=$pdf===$current?'coincide':($current===''?'pendiente':'diferente');$counts[$state]++;
        $groups[$key]['title']=$title;$groups[$key]['rows'][]=compact('label','pdf','current','state','meaning');
    }
}
