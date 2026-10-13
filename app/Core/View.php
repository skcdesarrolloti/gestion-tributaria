<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    public function __construct(private string $directory, private array $config) {}

    public function render(string $template, array $data = []): void
    {
        $config = $this->config;
        extract($data, EXTR_SKIP);
        require $this->directory . '/' . $template . '.php';
    }
}
