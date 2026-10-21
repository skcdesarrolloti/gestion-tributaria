<?php
declare(strict_types=1);
namespace App\Services;

final class RutImportPlan
{
    public static function build(array $parsed, array $taxpayer, array $schemas, string $year): array
    {
        self::matchIdentity($parsed,$taxpayer);
        $items=[]; $warnings=['Estado actual DIAN: pendiente de consulta oficial. El PDF no certifica que los datos no hayan cambiado.'];
        foreach ($parsed['fields'] as $block=>$fields) {
            foreach ($fields as $field=>$value) {
                if (!isset($schemas[$block]['fields'][$field])) { continue; }
                $single=$schemas[$block]; $single['fields']=[$field=>$single['fields'][$field]]; unset($single['rows']);
                $data=CaracterizacionData::read(['campos'=>[$field=>$value],'estado'=>'borrador'],$single);
                if (CaracterizacionData::errors($data,$single,$taxpayer['tipo'])) { $warnings[]='No se pudo validar '.$single['fields'][$field]['label'].'. Revisa ese campo manualmente.'; continue; }
                $items['campo:'.$block.':'.$field]=['block'=>$block,'field'=>$field,'kind'=>'campo','label'=>$single['fields'][$field]['label'],'value'=>$value];
            }
        }
        foreach ($parsed['rows'] as $block=>$rows) {
            $valid=[];
            foreach ($rows as $row) {
                if (isset($schemas[$block]['rows']['codigo']['options'][$row['codigo']])) { $valid[]=$row; }
                else { $warnings[]='Código '.$row['codigo'].' fuera del catálogo de '.$schemas[$block]['title'].': pendiente de verificar.'; }
            }
            if ($valid) { $items['filas:'.$block]=['block'=>$block,'field'=>'filas','kind'=>'filas','label'=>$schemas[$block]['title'],'value'=>$valid]; }
            if ($block==='responsabilidades' && count($valid)===count($rows) && $valid) {
                $items['campo:responsabilidades:sin_responsabilidades']=['block'=>$block,'field'=>'sin_responsabilidades','kind'=>'campo','label'=>'¿Casilla 53 sin códigos?','value'=>'no'];
            }
        }
        if ($parsed['generado']==='') { $warnings[]='No se identificó la fecha de generación. No se asignó una fecha supuesta.'; }
        elseif (substr($parsed['generado'],0,4)!==$year) { $warnings[]='El PDF se generó en otro año. Es una copia documental; no acredita las condiciones del año seleccionado.'; }
        if ($parsed['generado']!=='' && $parsed['generado']>date('Y-m-d')) { throw new \InvalidArgumentException('La fecha de generación del PDF es futura. Revisa el documento antes de importarlo.'); }
        if ($parsed['pages']>1) { $warnings[]='Esta versión lee las casillas de la primera hoja; revisa manualmente las hojas adicionales de entidad, representantes y establecimientos.'; }
        $warnings[]='No se importan datos ausentes: dependientes, patrimonio, cuentas, residencia fiscal y demás condiciones del año se completan aparte.';
        return compact('items','warnings');
    }

    public static function matchIdentity(array $parsed, array $taxpayer): void
    {
        $number=$taxpayer['documento_numero']; $document=$parsed['fields']['identificacion']['identificacion_numero']??'';
        $type=$parsed['fields']['identificacion']['identificacion_tipo']??'';
        $matches=$taxpayer['documento_tipo']==='NIT'?$number===$parsed['nit']:($number===$document && $taxpayer['documento_tipo']===$type);
        if (!$matches || ($taxpayer['documento_tipo']==='NIT' && $taxpayer['dv']!=='' && $taxpayer['dv']!==$parsed['dv'])) {
            throw new \InvalidArgumentException('El NIT o documento del PDF no coincide con este contribuyente. Revisa la identificación básica; no se cargaron datos.');
        }
    }

    public static function merge(array $preview, array $selected, bool $replace, array $blocks, array $schemas, string $type): array
    {
        $updates=[]; $applied=[]; $skipped=[];
        foreach ($selected as $key) {
            if (!is_string($key) || !isset($preview['plan']['items'][$key])) { throw new \InvalidArgumentException('Selección de campos inválida.'); }
            $item=$preview['plan']['items'][$key]; $block=$item['block'];
            if (!isset($updates[$block])) { $updates[$block]=CaracterizacionData::read($blocks[$block]['datos']??[],$schemas[$block]); }
            $target=&$updates[$block];
            if ($item['kind']==='filas') {
                if ($target['filas'] && $target['filas']!==$item['value'] && !$replace) { $skipped[]=$item['label']; continue; }
                if ($target['filas']===$item['value']) { continue; }
                $target['filas']=$item['value'];
                if (!empty($schemas[$block]['allow_na'])) { $target['aplicabilidad']='si'; }
            } else {
                $old=$target['campos'][$item['field']]??'';
                if ($old!=='' && $old!==$item['value'] && !$replace) { $skipped[]=$item['label']; continue; }
                if ($old===$item['value']) { continue; }
                $target['campos'][$item['field']]=$item['value'];
            }
            $applied[]=$key;
        }
        unset($target);
        foreach ($updates as $block=>&$data) {
            $changed=array_filter($applied,static fn($key)=>$preview['plan']['items'][$key]['block']===$block);
            if (!$changed) { unset($updates[$block]); continue; }
            $data['estado']='borrador'; $data['fecha']='';
            if ($data['fuente']==='') { $data['fuente']='RUT PDF · formulario '.$preview['file']['parsed']['formulario'].' · SHA-256 '.$preview['file']['sha256']; }
            $errors=CaracterizacionData::errors($data,$schemas[$block],$type);
            if ($errors) { throw new \InvalidArgumentException('Revisa los datos de '.$schemas[$block]['title'].' antes de aplicar el PDF.'); }
        }
        unset($data);
        return compact('updates','applied','skipped');
    }
}
