<?php
declare(strict_types=1);
namespace App\Services;

/** Lectura de las hojas 2–7 del formulario 001; no decide condiciones fiscales. */
final class RutPdfDetails
{
    public static function extract(array $sheets, string $nit, string $form): array
    {
        $fields=[]; $rows=[]; $report=[];
        foreach (array_slice($sheets,1) as $i=>$sheet) {
            $page=$i+2; $g=new RutPdfGrid($sheet['cells']);
            $pageNit=preg_replace('/\s/','',$g->value(25,190,600,610,true));
            $pageForm=preg_replace('/\s/','',$g->value(400,596,690,715,true));
            if (($pageNit!=='' && $pageNit!==$nit) || ($pageForm!=='' && $pageForm!==$form)) { throw new \InvalidArgumentException('La página '.$page.' no coincide con el NIT y formulario de la primera hoja. No se importó el documento.'); }
            if ($pageNit==='' || $pageForm==='') { $report[]=['pagina'=>$page,'hoja'=>0,'resultado'=>'No se pudo identificar el titular/formulario: completar esta página manualmente']; continue; }
            preg_match('/Hoja\s+([2-7])\b/iu',$sheet['text'],$match); $number=(int)($match[1]??0);
            $before=count($fields,COUNT_RECURSIVE)+count($rows,COUNT_RECURSIVE);
            if ($number===2) {
                foreach (require dirname(__DIR__,2).'/resources/rut-entity-fields.php' as $key=>$r) {
                    [$left,$right,$y,$kind]=$r; $value=self::value($g,$left,$right,$y,$kind);
                    if (in_array($key,['documento_constitucion','constitucion_numero','notaria','registro_entidad','matricula','registro_departamento','registro_lugar'],true)) { $value=preg_replace('/\s+/','',$value); }
                    if ($value!=='') { $fields['entidad'][$key]=$value; }
                }
            }
            if ($number===2 || $number===7) {
                $states=[];
                for ($r=0;$r<($number===2?5:22);$r++) {
                    $y=($number===2?242.5:543.5)-24*$r; $code=self::value($g,45,137,$y);
                    if ($code==='') { continue; }
                    $states[]='89: '.$code.' | 90: '.self::value($g,137,255,$y,'date').' | 91: '.self::value($g,255,426,$y,'digits').' | 92: '.self::value($g,426,456,$y,'digits');
                }
                if ($states) { $fields['entidad']['estados_rut']=trim(($fields['entidad']['estados_rut']??'').' '.implode('; ',$states)); }
            }
            if (in_array($number,[3,4,5],true)) {
                $people=RutPdfPeople::extract($g,$number,$form,$page);
                $rows['relacionados']=array_merge($rows['relacionados']??[],$people);
            }
            if ($number===6) { $rows['establecimientos']=array_merge($rows['establecimientos']??[],self::places($g)); }
            $read=count($fields,COUNT_RECURSIVE)+count($rows,COUNT_RECURSIVE)>$before;
            $report[]=['pagina'=>$page,'hoja'=>$number,'resultado'=>$number?($read?'Datos reconocidos':'Hoja reconocida sin datos adicionales legibles'):'Diseño no reconocido: revisar manualmente'];
        }
        return compact('fields','rows','report');
    }

    public static function value(RutPdfGrid $g, float $left, float $right, float $y, string $kind='text'): string
    {
        $value=$g->value($left,$right,$y-3,$y+3,in_array($kind,['date','digits'],true));
        if ($kind==='date') {
            $value=preg_replace('/[^0-9]/','',$value); $d=\DateTimeImmutable::createFromFormat('!Ymd',$value);
            return $d && $d->format('Ymd')===$value?$d->format('Y-m-d'):'';
        }
        if ($kind==='digits') { return preg_replace('/\s/','',$value); }
        if ($kind==='percent') { return preg_replace('/\s|%/','',str_replace(',','.',$value)); }
        return $value==='-'?'':$value;
    }

    private static function places(RutPdfGrid $g): array
    {
        $rows=[];
        for ($i=0;$i<3;$i++) {
            $y=557.5-144*$i; $v=static fn($l,$r,$delta=0,$kind='text')=>self::value($g,$l,$r,$y+$delta,$kind);
            $name=$v(36,596,-24); $code=$v(36,152);
            if ($name==='' && $code==='') { continue; }
            $row=['nombre'=>$name,'tipo_rut'=>$code,'actividad'=>$v(152,596,0,'digits'),'departamento'=>$v(36,208,-48),
                'ciudad'=>$v(208,596,-48),'direccion'=>$v(36,596,-72),'matricula'=>$v(36,255,-96,'digits'),
                'fecha_matricula'=>$v(255,596,-96,'date'),'telefono'=>$v(36,255,-120,'digits'),'cierre'=>$v(255,596,-120,'date')];
            $normalized=RutPdfReader::normalize($code);
            foreach (['establecimiento','agencia','sucursal','oficina'] as $type) { if (str_contains($normalized,$type)) { $row['tipo']=$type; break; } }
            $rows[]=$row;
        }
        return $rows;
    }
}
