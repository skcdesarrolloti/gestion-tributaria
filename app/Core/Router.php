<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function add(string $method, string $route, callable $handler): void
    {
        $this->routes[$route][$method] = $handler;
    }

    public function dispatch(string $method, string $route): void
    {
        if (!isset($this->routes[$route])) {
            http_response_code(404);
            echo 'Página no encontrada.';
            return;
        }
        if (!isset($this->routes[$route][$method])) {
            http_response_code(405);
            header('Allow: ' . implode(', ', array_keys($this->routes[$route])));
            echo 'Método no permitido.';
            return;
        }
        ($this->routes[$route][$method])();
    }
}
