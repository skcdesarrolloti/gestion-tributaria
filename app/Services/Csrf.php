<?php
declare(strict_types=1);

namespace App\Services;

final class Csrf
{
    public static function token(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    }

    public static function valid(string $token): bool
    {
        return $token !== '' && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
    }
}
