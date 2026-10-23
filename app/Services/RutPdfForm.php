<?php
declare(strict_types=1);
namespace App\Services;

final class RutPdfForm
{
    public static function extract(array $pdf): array
    {
        $normalized=RutPdfReader::normalize($pdf['text']);
        foreach (['numero de formulario','identificacion tributaria','responsabilidades'] as $marker) {
            if (!str_contains($normalized,$marker)) { throw new \InvalidArgumentException('No se reconoció un formulario RUT 001 de la DIAN.'); }
        }
        if (preg_match('/(?:^|\n)\s*(BORRADOR|SUSPENDIDO|CANCELADO)\s*(?:\n|$)/iu',$pdf['sheets'][0]['text']??$pdf['text'])) {
            throw new \InvalidArgumentException('El documento contiene una marca de borrador, suspensión o cancelación. Revisa su estado en la DIAN antes de importarlo.');
        }
        $g=new RutPdfGrid($pdf['cells']); $nit=self::digits($g->value(25,190,600,610,true));
        $dv=self::digits($g->value(190,201,600,610,true)); $form=self::digits($g->value(400,596,690,715,true));
        if (!preg_match('/^[0-9]{5,15}$/D',$nit) || !preg_match('/^[0-9]$/D',$dv) || self::digit($nit)!==$dv || !preg_match('/^[0-9]{8,20}$/D',$form)) {
            throw new \InvalidArgumentException('No se pudo validar el NIT, su dígito y el número de formulario. Este diseño requiere revisión manual; no se cargaron datos.');
        }
        $fields=[];
        foreach (require dirname(__DIR__,2).'/resources/rut-pdf-fields.php' as $block=>$mapping) {
            foreach ($mapping as $key=>$r) {
                $value=$g->value(...$r);
                if (($r[4]??false)) { $value=self::digits($value); }
                if ($value!=='') { $fields[$block][$key]=$value; }
            }
        }
        foreach (['principal_inicio','secundaria_inicio'] as $key) {
            if (isset($fields['actividades'][$key])) {
                $date=\DateTimeImmutable::createFromFormat('!Ymd',$fields['actividades'][$key]);
                if ($date && $date->format('Ymd')===$fields['actividades'][$key]) { $fields['actividades'][$key]=$date->format('Y-m-d'); }
                else { unset($fields['actividades'][$key]); }
            }
        }
        if (isset($fields['identificacion']['expedicion_fecha'])) {
            $date=\DateTimeImmutable::createFromFormat('!Ymd',$fields['identificacion']['expedicion_fecha']);
            if ($date && $date->format('Ymd')===$fields['identificacion']['expedicion_fecha']) { $fields['identificacion']['expedicion_fecha']=$date->format('Y-m-d'); }
            else { unset($fields['identificacion']['expedicion_fecha']); }
        }
        $docCode=self::digits($g->value(282,307,566,572,true));
        if (isset(['13'=>'CC','22'=>'CE','31'=>'NIT','41'=>'PAS'][$docCode])) { $fields['identificacion']['identificacion_tipo']=['13'=>'CC','22'=>'CE','31'=>'NIT','41'=>'PAS'][$docCode]; }
        foreach (['expedicion_pais','expedicion_departamento','expedicion_municipio'] as $key) {
            if (isset($fields['identificacion'][$key])) { $fields['identificacion'][$key]=self::location($fields['identificacion'][$key]); }
        }
        foreach (['pais','departamento','municipio'] as $key) {
            if (isset($fields['ubicacion'][$key])) { $fields['ubicacion'][$key]=self::location($fields['ubicacion'][$key]); }
        }
        $generated=''; $formalized='';
        if (preg_match('/Fecha\s+generaci[oó]n\s+documento\s+PDF:\s*(\d{2})-(\d{2})-(\d{4})/iu',$pdf['text'],$m)) { $generated=$m[3].'-'.$m[2].'-'.$m[1]; }
        if (preg_match('/(\d{4}-\d{2}-\d{2})\s*\//',$pdf['text'],$m)) { $formalized=$m[1]; }
        $fields['identificacion']['rut_formulario']=$form; $fields['identificacion']['rut_estado']='disponible';
        if ($generated!=='') { $fields['identificacion']['rut_fecha']=$generated; }
        if ($formalized!=='') { $fields['identificacion']['rut_formalizacion']=$formalized; }
        $responsibilities=$g->codes(291.5,61,20.5,26);
        $customs=array_merge($g->codes(167.5,61,20.5,10),$g->codes(143.5,61,20.5,10));
        foreach (['modo'=>[150,164],'cpc'=>[135,148]] as $key=>[$bottom,$top]) {
            $values=[];
            foreach ([[463,505],[505,547],[547,596]] as $i=>[$left,$right]) {
                $value=self::digits($g->value($left,$right,$bottom,$top,true));
                if ($value!=='') { $values[]='Servicio '.($i+1).': '.$value; }
            }
            if ($values) { $fields['aduanas'][$key]=implode('; ',$values); }
        }
        $details=RutPdfDetails::extract($pdf['sheets']??[],$nit,$form);
        $fields=array_replace_recursive($fields,$details['fields']);
        $report=array_merge([['pagina'=>1,'hoja'=>1,'resultado'=>'Datos de la hoja principal reconocidos y formato de NIT/DV comprobado; contrastar campos con el documento']],$details['report']);
        return ['nit'=>$nit,'dv'=>$dv,'formulario'=>$form,'generado'=>$generated,'formalizado'=>$formalized,'lectura_hojas'=>$report,
            'pages'=>$pdf['pages'],'reader'=>$pdf['reader'],'fields'=>$fields,'rows'=>[
                'responsabilidades'=>array_map(static fn($v)=>['codigo'=>$v],$responsibilities),
                'aduanas'=>array_map(static fn($v)=>['codigo'=>$v],array_unique($customs)),
                ...$details['rows'],
            ],'estado_dian'=>'pendiente_consulta_oficial'];
    }

    private static function digits(string $value): string { return preg_replace('/\s+/','',$value); }
    private static function location(string $value): string { return trim(preg_replace('/[0-9 ]+$/u','',$value)); }

    public static function digit(string $nit): string
    {
        $weights=[71,67,59,53,47,43,41,37,29,23,19,17,13,7,3]; $sum=0;
        foreach (str_split(str_pad($nit,15,'0',STR_PAD_LEFT)) as $i=>$number) { $sum+=(int)$number*$weights[$i]; }
        $remainder=$sum%11; return (string)($remainder>1?11-$remainder:$remainder);
    }
}
