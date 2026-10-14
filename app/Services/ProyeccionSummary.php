<?php
declare(strict_types=1);

namespace App\Services;

final class ProyeccionSummary
{
    // Solo agrega partidas capturadas: los totales DIAN derivados no son nuevos ingresos.
    public static function build(array $concepts, array $movements, string $today): array
    {
        $classes = [];
        foreach ($concepts as $concept) { $classes[(int)$concept['casilla']] = $concept['clase']; }
        $empty = ['real'=>['ingreso'=>0,'reduccion'=>0], 'proyectado'=>['ingreso'=>0,'reduccion'=>0], 'registros'=>0];
        $months = array_fill(1,12,$empty);
        $total = $empty;
        $expired = 0;
        foreach ($movements as $movement) {
            $class = $classes[(int)$movement['casilla']] ?? null;
            if (!in_array($class,['ingreso','reduccion'],true)) { continue; }
            $month = (int)substr($movement['fecha'],5,2);
            $state = $movement['estado'];
            $amount = (int)$movement['valor_cop'];
            if ($total[$state][$class] > PHP_INT_MAX - $amount) {
                throw new \RuntimeException('El total excede el rango de cálculo.');
            }
            $months[$month][$state][$class] += $amount;
            $months[$month]['registros']++;
            $total[$state][$class] += $amount;
            $total['registros']++;
            if ($state === 'proyectado' && $movement['fecha'] < $today) { $expired++; }
        }
        return ['meses'=>$months,'total'=>$total,'proyecciones_por_revisar'=>$expired];
    }
}
