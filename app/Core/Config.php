<?php
declare(strict_types=1);

namespace App\Core;

final class Config
{
    public static function load(string $root): array
    {
        $config = require $root . '/config/app.php';
        if (is_file($root . '/config/local.php')) {
            $config = array_replace_recursive($config, require $root . '/config/local.php');
        }
        $base = rtrim((string) $config['base_path'], '/');
        if ($base !== '' && !preg_match('~^/(?!/)[a-zA-Z0-9/_-]+$~D', $base)) {
            throw new \RuntimeException('base_path debe ser una ruta local, sin dominio.');
        }
        $config['base_path'] = $base;
        return $config;
    }
}
