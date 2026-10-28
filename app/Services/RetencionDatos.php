<?php
declare(strict_types=1);
namespace App\Services;

final class RetencionDatos
{
    public const MONEY=[
        'vivienda'=>'Intereses de vivienda certificados del mes',
        'prepagada'=>'Salud prepagada / seguro admisible del mes',
        'usado25'=>'Exención del 25% ya utilizada antes de este mes en el año',
        'usado1340'=>'Deducciones y rentas exentas sujetas al límite de 1.340 UVT ya utilizadas antes del mes',
    ];
    public static function read(array $post): array
    {
        $v=[];
        foreach (self::MONEY as $key=>$label) {
            $value=\input($post,$key);
            if (!preg_match('/^(0|[1-9][0-9]{0,10})$/D',$value)) { throw new \InvalidArgumentException($label.': escribe pesos enteros sin separadores, incluido 0 si corresponde.'); }
            $v[$key]=(int)$value*100;
        }
        foreach (['procedimiento'=>['1','2'],'dependientes'=>['si','no'],'redondeo'=>['peso','miles']] as $key=>$allowed) {
            $v[$key]=\input($post,$key);
            if (!in_array($v[$key],$allowed,true)) {
                $label=['procedimiento'=>'el método del empleador','dependientes'=>'si nómina aplicó el beneficio de dependientes','redondeo'=>'cómo redondeó nómina'][$key];
                throw new \InvalidArgumentException('Todavía falta confirmar '.$label.'. Consulta la ayuda de ese campo o pide el dato a nómina. No se ha validado ni cambiado el descuento importado.');
            }
        }
        $percent=\input($post,'porcentaje');
        if ($v['procedimiento']==='2' && (!preg_match('/^(0|[1-9][0-9]?)(\.[0-9]{1,2})?$/D',$percent) || (float)$percent>39)) {
            throw new \InvalidArgumentException('Procedimiento 2: registra el porcentaje fijo certificado del semestre, entre 0 y 39, con punto decimal.');
        }
        $v['porcentaje']=$v['procedimiento']==='2'?(int)round((float)$percent*100):0;
        $v['soporte']=trim(\input($post,'soporte'));
        if (mb_strlen($v['soporte'])<10 || mb_strlen($v['soporte'])>1000) { throw new \InvalidArgumentException('Identifica los soportes y la depuración del empleador (10 a 1.000 caracteres).'); }
        foreach (['completo','ordinario','verificado'] as $key) {
            if (\input($post,$key)!=='1') { throw new \InvalidArgumentException('Confirma mes completo, alcance ordinario y soportes revisados antes de validar.'); }
            $v[$key]=true;
        }
        return $v;
    }
}
