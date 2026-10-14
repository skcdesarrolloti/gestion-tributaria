<?php
declare(strict_types=1);

namespace App\Services;

final class IngresoData
{
    public static function read(array $source, array $fields): array
    {
        $record = [];
        foreach ($fields as $field) { $record[$field] = trim(\input($source, $field)); }
        return $record;
    }

    public static function profileErrors(array $record, string $type = 'natural'): array
    {
        $errors = [];
        if (!in_array($record['anio'], ['2025', '2026'], true)) { $errors[] = 'Selecciona un año disponible: 2025 o 2026.'; }
        if (!in_array($record['residencia'], ['residente', 'no_residente'], true)) { $errors[] = 'Confirma la residencia fiscal.'; }
        if ($record['regimen'] !== 'ordinario') { $errors[] = 'El régimen seleccionado es solo de consulta: su cálculo está pendiente. No se crea un perfil ordinario en su lugar.'; }
        $options = PerfilGuidance::fields($type)['servicios']['options'];
        if (!isset($options[$record['servicios']])) { $errors[] = 'Revisa el tratamiento de servicios personales para este tipo de contribuyente.'; }
        if ($record['soporte_perfil'] === '' || mb_strlen($record['soporte_perfil']) > 500) {
            $errors[] = 'Indica la evidencia del perfil fiscal (máximo 500 caracteres).';
        }
        return $errors;
    }

    public static function allowed(array $concept, array $profile): bool
    {
        if ($concept['clase'] === 'calculado') { return false; }
        return $profile['formulario'] !== '210' || !in_array((int) $concept['casilla'], [43,44,45], true)
            || $profile['servicios'] === 'costos';
    }

    public static function movementErrors(array $record, array $profile, ?array $concept): array
    {
        $errors = [];
        if (!$concept || !self::allowed($concept, $profile)) { $errors[] = 'La casilla no es capturable o no corresponde al perfil.'; }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $record['fecha']);
        if (!$date || $date->format('Y-m-d') !== $record['fecha'] || $date->format('Y') !== (string) $profile['anio']) {
            $errors[] = 'La fecha debe ser válida y pertenecer al año del perfil.';
        }
        if (!in_array($record['estado'], ['real','proyectado'], true)) { $errors[] = 'Indica si el movimiento es real o proyectado.'; }
        if ($record['estado'] === 'real' && $record['fecha'] > date('Y-m-d')) { $errors[] = 'Un movimiento futuro debe marcarse como proyectado.'; }
        foreach (['descripcion'=>300,'tercero'=>150,'pais'=>80,'conversion'=>500,'soporte'=>500,'justificacion'=>500] as $field=>$max) {
            if (mb_strlen($record[$field]) > $max) { $errors[] = "El campo $field supera $max caracteres."; }
        }
        if ($record['descripcion'] === '' || $record['tercero'] === '' || $record['pais'] === '') {
            $errors[] = 'Completa concepto, tercero y país de origen.';
        }
        if (!preg_match('/^[1-9][0-9]{0,11}$/D', $record['valor_cop'])) { $errors[] = 'El valor fiscal debe ser positivo, en pesos enteros y hasta 12 dígitos.'; }
        if (!preg_match('/^[A-Z]{3}$/D', $record['moneda']) || !preg_match('/^[0-9]{1,14}(\.[0-9]{1,2})?$/D', $record['valor_original'])
            || (float) $record['valor_original'] <= 0) { $errors[] = 'Revisa moneda de tres letras y valor original positivo (punto para decimales).'; }
        if ($record['moneda'] === 'COP') {
            if ($record['valor_original'] !== $record['valor_cop']) { $errors[] = 'En COP el valor original debe coincidir con el valor fiscal entero.'; }
        } elseif ($record['conversion'] === '') { $errors[] = 'Explica tasa, fecha y soporte de la conversión fiscal a COP.'; }
        if ($record['estado'] === 'real' && $record['soporte'] === '') { $errors[] = 'Los movimientos reales requieren referencia del soporte.'; }
        if ($concept && $concept['clase'] === 'reduccion' && $record['justificacion'] === '') {
            $errors[] = 'La partida que se resta requiere norma particular, condiciones y justificación.';
        }
        return $errors;
    }
}
