<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { exit(1); }
$root = dirname(__DIR__);
$failed = false;
foreach (['app', 'bootstrap', 'config', 'routes', 'public', 'bin', 'tests'] as $folder) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $folder));
    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'php' || $file->getFilename() === 'local.php') { continue; }
        $output = [];
        exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname()) . ' 2>&1', $output, $code);
        if ($code !== 0) { echo implode("\n", $output) . "\n"; $failed = true; }
        if (count(file($file->getPathname())) > 250) {
            echo 'Archivo demasiado extenso: ' . $file->getPathname() . "\n";
            $failed = true;
        }
    }
}
echo $failed ? "Revisión fallida.\n" : "Sintaxis correcta; archivos PHP de hasta 250 líneas.\n";
exit($failed ? 1 : 0);
