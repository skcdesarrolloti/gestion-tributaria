<?php
declare(strict_types=1);

namespace App\Services;

final class RentaTarifa
{
    // Art. 241: no redondear UVT para elegir el tramo. Operación racional en COP.
    public static function calculate(int $base, int $uvt): array
    {
        $table=[[31000,39,10352],[18970,37,5901],[8670,35,2296],[4100,33,788],[1700,28,116],[1090,19,0],[0,0,0]];
        foreach ($table as [$threshold,$rate,$fixed]) {
            if ($base>$threshold*$uvt || $threshold===0) {
                $numerator=($base-$threshold*$uvt)*$rate+$fixed*$uvt*100;
                return ['impuesto'=>intdiv($numerator+50,100),'umbral_uvt'=>$threshold,'tarifa'=>$rate,'fijo_uvt'=>$fixed];
            }
        }
        throw new \LogicException('Tramo no disponible.');
    }

    public static function thousand(int $value): int { return intdiv(max(0,$value)+500,1000)*1000; }
    public static function percent(int $value, int $rate): int { return intdiv($value*$rate+50,100); }
}
