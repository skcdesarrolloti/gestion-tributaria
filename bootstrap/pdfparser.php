<?php
declare(strict_types=1);

// Biblioteca aislada de lectura PDF; fuente y licencia en third_party/pdfparser.
spl_autoload_register(static function (string $class): void {
    $prefix='Smalot\\PdfParser\\';
    if (!str_starts_with($class,$prefix)) { return; }
    $relative=substr($class,strlen($prefix));
    if (!preg_match('/^[A-Za-z0-9_\\\\]+$/D',$relative)) { return; }
    $path=dirname(__DIR__).'/third_party/pdfparser/src/Smalot/PdfParser/'.str_replace('\\','/',$relative).'.php';
    if (is_file($path)) { require $path; }
});
