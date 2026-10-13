<?php
declare(strict_types=1);

namespace App\Services;

final class PasswordVerifier
{
    public static function verify(string $password, string $stored, bool $allowLegacy): bool
    {
        if ($password === '' || $stored === '') { return false; }
        $algorithm = password_get_info($stored)['algoName'] ?? 'unknown';
        if ($algorithm !== 'unknown') {
            // Un hash bcrypt solo distingue los primeros 72 bytes.
            if ($algorithm === 'bcrypt' && strlen($password) > 72) { return false; }
            return password_verify($password, $stored);
        }
        // Compatibilidad explícita con pass_others_apss del sistema comercial.
        // Nunca convertir, escribir ni reemplazar la contraseña central.
        return $allowLegacy && hash_equals($stored, $password);
    }
}
