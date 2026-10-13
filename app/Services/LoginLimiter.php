<?php
declare(strict_types=1);

namespace App\Services;

final class LoginLimiter
{
    public function __construct(private string $directory) {}

    /** Reserva un intento de forma atómica. Devuelve los segundos de bloqueo. */
    public function consume(string $key, int $limit, int $window = 900): int
    {
        $handle = fopen($this->directory . '/' . hash('sha256', $key) . '.json', 'c+');
        if (!$handle) {
            throw new \RuntimeException('storage/rate-limits no tiene permiso de escritura.');
        }
        try {
            if (!flock($handle, LOCK_EX)) {
                throw new \RuntimeException('No se pudo reservar el intento de acceso.');
            }
            $times = json_decode(stream_get_contents($handle) ?: '[]', true);
            $times = array_values(array_filter(is_array($times) ? $times : [],
                static fn ($time): bool => is_int($time) && $time > time() - $window));
            if (count($times) >= $limit) {
                return max(1, min($times) + $window - time());
            }
            $times[] = time();
            rewind($handle);
            if (!ftruncate($handle, 0) || fwrite($handle, json_encode($times)) === false) {
                throw new \RuntimeException('No se pudo guardar el intento de acceso.');
            }
            fflush($handle);
            return 0;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
