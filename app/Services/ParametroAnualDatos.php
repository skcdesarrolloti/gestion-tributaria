<?php
declare(strict_types=1);
namespace App\Services;

final class ParametroAnualDatos
{
    public static function read(array $post): array
    {
        $data=['uvt'=>self::integer($post['uvt']??null,1,1000000,'UVT')];
        $data['source']=trim(\input($post,'source'));$data['norma']=trim(\input($post,'norma'));
        $host=strtolower((string)parse_url($data['source'],PHP_URL_HOST));
        if (strlen($data['source'])>500 || parse_url($data['source'],PHP_URL_SCHEME)!=='https'
            || !in_array($host,['www.dian.gov.co','dian.gov.co','normograma.dian.gov.co'],true)) {
            throw new \InvalidArgumentException('Indica la dirección HTTPS de la fuente oficial DIAN de la UVT.');
        }
        if (mb_strlen($data['norma'])<10 || mb_strlen($data['norma'])>500) { throw new \InvalidArgumentException('Describe la norma que respalda los rangos y topes (10 a 500 caracteres).'); }
        foreach (ParametrosAnuales::LIMITS as $key=>[$label,$default]) {
            $data[$key]=self::integer($post[$key]??null,0,str_ends_with($key,'_pct')?100:100000,$label);
        }
        $rows=$post['tramos']??null;
        if (!is_array($rows) || count($rows)!==7 || array_keys($rows)!==range(0,6)) { throw new \InvalidArgumentException('Completa los siete rangos de la tabla laboral.'); }
        $lower=0;$data['tramos']=[];
        foreach ($rows as $i=>$row) {
            if (!is_array($row)) { throw new \InvalidArgumentException('Revisa los campos de cada rango.'); }
            $upper=$i===6?null:self::integer($row['hasta']??null,$lower+1,1000000,'Límite superior del rango '.($i+1));
            $rate=self::integer($row['tarifa']??null,0,100,'Tarifa marginal');
            $fixed=self::integer($row['fijo']??null,0,1000000,'Componente fijo UVT');
            if ($i===0 && ($rate!==0 || $fixed!==0)) { throw new \InvalidArgumentException('El primer rango de este motor debe tener tarifa y fijo cero.'); }
            $data['tramos'][]=[$lower,$upper,$rate,$fixed];$lower=$upper;
        }
        if (\input($post,'confirmado')!=='1') { throw new \InvalidArgumentException('Confirma que contrastaste los valores con las normas del año antes de publicarlos.'); }
        return $data;
    }
    private static function integer(mixed $raw,int $min,int $max,string $label): int
    {
        if (!is_string($raw) || !preg_match('/^(0|[1-9][0-9]{0,6})$/D',$raw) || (int)$raw<$min || (int)$raw>$max) {
            throw new \InvalidArgumentException($label.': escribe un entero entre '.$min.' y '.$max.', sin separadores.');
        }
        return (int)$raw;
    }
}
