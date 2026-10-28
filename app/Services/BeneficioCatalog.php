<?php
declare(strict_types=1);

namespace App\Services;

final class BeneficioCatalog
{
    public const REVIEWED='2026-10-21';
    public const ET='https://normograma.dian.gov.co/dian/compilacion/docs/estatuto_tributario.htm';

    public static function groups(): array
    {
        return ['costos'=>'Costos y gastos de la actividad','deducciones'=>'Deducciones personales',
            'exentas'=>'Rentas exentas','no-renta'=>'Ingresos no constitutivos de renta',
            'descuentos'=>'Descuentos del impuesto','especiales'=>'Incentivos con revisión particular',
            'exclusiones'=>'Conceptos que no deben confundirse con deducciones'];
    }

    public static function year(string $year): array
    {
        if (!preg_match('/^[0-9]{4}$/D',$year)) { throw new \InvalidArgumentException('Año inválido.'); }
        return array_intersect_key(ParametrosAnuales::year((int)$year),['uvt'=>true,'source'=>true]);
    }

    public static function all(): array
    {
        $items=[];
        foreach (self::groups() as $group=>$label) {
            foreach (require dirname(__DIR__,2).'/resources/beneficios/'.$group.'.php' as $item) {
                $item['group']=$group;
                preg_match('/ET ([0-9]+(?:-[0-9]+)?)/',$item['norm'],$article);
                $item['source']=match($item['id']) {
                    'energia'=>'https://normograma.dian.gov.co/dian/compilacion/docs/ley_1715_2014.htm#11',
                    'cine'=>'https://normograma.dian.gov.co/dian/compilacion/docs/ley_0814_2003.htm#16',
                    'empleo-discapacidad'=>'https://normograma.dian.gov.co/dian/compilacion/docs/ley_0361_1997.htm#31',
                    default=>self::ET.(isset($article[1])?'#'.$article[1]:''),
                };
                $items[]=$item;
            }
        }
        return $items;
    }

    public static function filter(string $group, string $query): array
    {
        $query=self::normalize($query);
        return array_values(array_filter(self::all(),static function ($item) use ($group,$query) {
            return ($group==='' || $item['group']===$group)
                && ($query==='' || str_contains(self::normalize(implode(' ',$item)),$query));
        }));
    }

    private static function normalize(string $value): string
    {
        return strtr(mb_strtolower(trim($value)),['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u']);
    }
}
