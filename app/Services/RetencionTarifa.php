<?php
declare(strict_types=1);
namespace App\Services;

/** ET 383: pesos con centavos, sin redondear previamente la base a UVT. */
final class RetencionTarifa
{
    public static function calculate(int $base, int $uvt): int
    {
        if ($base<0 || $uvt<=0) { throw new \InvalidArgumentException('Base o UVT inválida.'); }
        foreach ([[2300,39,770],[945,37,268],[640,35,162],[360,33,69],[150,28,10],[95,19,0]] as [$threshold,$rate,$fixed]) {
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
