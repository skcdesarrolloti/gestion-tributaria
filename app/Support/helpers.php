<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function input(array $source, string $key, string $default = ''): string
{
    return isset($source[$key]) && is_string($source[$key]) ? $source[$key] : $default;
}

function url(array $config, string $route = 'inicio'): string
{
    return $config['base_path'] . '/index.php?route=' . rawurlencode($route);
}

function asset(array $config, string $path): string
{
    return $config['base_path'] . '/assets/' . $path;
}

function redirect(array $config, string $route): never
{
    header('Location: ' . url($config, $route), true, 303);
    exit;
}
