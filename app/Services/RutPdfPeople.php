<?php
declare(strict_types=1);
namespace App\Services;

final class RutPdfPeople
{
    public static function extract(RutPdfGrid $g, int $sheet, string $form, int $page): array
    {
        $rows=[];
        for ($i=0;$i<($sheet===5?3:5);$i++) {
            $y=($sheet===5?557.5:569.5)-96*$i;
            $v=static fn($l,$r,$delta=0,$kind='text')=>RutPdfDetails::value($g,$l,$r,$y+$delta,$kind);
            $partner=$sheet===4;
            $type=$v($partner?100:108,$partner?129:136,$sheet===3?-24:0,'digits');
            $doc=$v($partner?129:136,$partner?371:($sheet===3?405:365),$sheet===3?-24:0,'digits');
            $nameY=$sheet===3?-48:-24;
            $name=$v(36,596,$nameY);
            $company=$sheet===4?$v(36,596,-48):'';
            if ($doc==='' && $name==='' && $company==='') { continue; }
            $row=['nombre'=>$company?:$name,'documento'=>$doc,'documento_tipo_rut'=>$type,
                'dv'=>$v($partner?371:($sheet===3?405:365),$partner?397:($sheet===3?431:391),$sheet===3?-24:0,'digits'),
                'soporte'=>'RUT formulario '.$form.' · página '.$page.' · hoja '.$sheet.' · renglón '.($i+1)];
            $row['documento_tipo']=['13'=>'CC','22'=>'CE','31'=>'NIT','41'=>'PAS'][$type]??'';
            if ($sheet===3) {
                $quality=$v(36,214); $normalized=RutPdfReader::normalize($quality);
                $row['rol']=str_contains($normalized,'apoderad')?'apoderado':(str_contains($normalized,'hereder')?'heredero':'representante');
                $row['parentesco']=$quality; $row['ingreso']=$v(214,596,0,'date');
                $row['tarjeta']=$v(431,596,-24); $row['firma_nit']=$v(36,188,-72,'digits');
                $row['firma_dv']=$v(188,204,-72,'digits'); $row['firma_nombre']=$v(204,596,-72);
            } elseif ($sheet===4) {
                $row['rol']='socio_junta'; $row['nacionalidad']=$v(397,596);
                $row['capital']=$v(36,225,-72,'digits'); $row['participacion']=$v(225,318,-72,'percent');
                $row['ingreso']=$v(318,463,-72,'date'); $row['retiro']=$v(463,596,-72,'date');
            } else {
                $row['rol']=$i===2?'contador':'revisor'; $row['parentesco']=$i===0?'Revisor fiscal principal':($i===1?'Revisor fiscal suplente':'Contador');
                $row['tarjeta']=$v(391,596); $row['ingreso']=$v(36,204,-72,'date');
                $row['firma_nit']=$v(36,178,-48,'digits'); $row['firma_dv']=$v(178,204,-48,'digits'); $row['firma_nombre']=$v(204,596,-48);
            }
            if (isset($row['tarjeta'])) { $row['tarjeta']=preg_replace('/\s+/','',$row['tarjeta']); }
            $rows[]=$row;
        }
        return $rows;
    }
}
