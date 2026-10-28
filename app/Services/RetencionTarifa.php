<?php
declare(strict_types=1);
namespace App\Services;

/** ET 383: pesos con centavos, sin redondear previamente la base a UVT. */
final class RetencionTarifa
{
    /** Desde (exclusivo salvo cero), hasta (incluido), porcentaje marginal y fijo UVT. */
    public const BRACKETS=[[0,95,0,0],[95,150,19,0],[150,360,28,10],[360,640,33,69],
        [640,945,35,162],[945,2300,37,268],[2300,null,39,770]];

    public static function calculate(int $base, int $uvt,?array $brackets=null): int
    {
        if ($base<0 || $uvt<=0) { throw new \InvalidArgumentException('Base o UVT inválida.'); }
        foreach (array_reverse($brackets??self::BRACKETS) as [$threshold,$upper,$rate,$fixed]) {
            if ($base>$threshold*$uvt*100) {
                return intdiv(($base-$threshold*$uvt*100)*$rate+50,100)+$fixed*$uvt*100;
            }
        }
        return 0;
    }
    public static function rounded(int $cents, string $rule): int
    {
        $unit=$rule==='miles'?100000:100;
        return intdiv($cents+intdiv($unit,2),$unit)*$unit;
    }
}
