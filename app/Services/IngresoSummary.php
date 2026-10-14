<?php
declare(strict_types=1);

namespace App\Services;

final class IngresoSummary
{
    public static function build(array $concepts, array $movements): array
    {
        $totals = [];
        foreach ($concepts as $concept) { $totals[(int) $concept['casilla']] = ['real'=>0,'proyectado'=>0]; }
        foreach ($movements as $movement) {
            $code = (int) $movement['casilla'];
            $state = $movement['estado'];
            $amount = (int) $movement['valor_cop'];
            if ($totals[$code][$state] > PHP_INT_MAX - $amount) { throw new \RuntimeException('El total excede el rango de cálculo.'); }
            $totals[$code][$state] += $amount;
        }
        // El catálogo ordena las casillas de destino después de sus dependencias.
        foreach ($concepts as $concept) {
            if ($concept['clase'] !== 'calculado') { continue; }
            preg_match_all('/([+-]?)([0-9]+)/', $concept['formula'], $terms, PREG_SET_ORDER);
            $code = (int) $concept['casilla'];
            foreach (['real','proyectado'] as $state) {
                foreach ($terms as $term) { $totals[$code][$state] += ($term[1] === '-' ? -1 : 1) * $totals[(int) $term[2]][$state]; }
            }
        }
        return $totals;
    }
}
