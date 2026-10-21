<?php
declare(strict_types=1);
namespace App\Services;

final class RutUpload
{
    public function __construct(private string $storage) {}

    public function directory(): string
    {
        $directory=rtrim($this->storage,'/\\').'/rut-importaciones';
        if (!is_dir($directory) && !mkdir($directory,0700,true) && !is_dir($directory)) { throw new \RuntimeException('No se pudo preparar el almacenamiento privado.'); }
        if (!is_file($directory.'/.htaccess')) { file_put_contents($directory.'/.htaccess',"Require all denied\n"); }
        return $directory;
    }

    public function receive(array $file): array
    {
        if (!isset($file['error'],$file['tmp_name'],$file['name']) || is_array($file['error']) || $file['error']!==UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('Selecciona un PDF y verifica el límite de carga del servidor (máximo de la aplicación: 5 MB).');
        }
        if (!is_string($file['tmp_name']) || !is_string($file['name']) || !is_uploaded_file($file['tmp_name']) || strtolower(pathinfo($file['name'],PATHINFO_EXTENSION))!=='pdf') {
            throw new \InvalidArgumentException('Selecciona un archivo PDF cargado desde este formulario.');
        }
        $key=bin2hex(random_bytes(32)).'.pending'; $path=$this->path($key);
        if (!move_uploaded_file($file['tmp_name'],$path)) { throw new \RuntimeException('No se pudo recibir el archivo.'); }
        chmod($path,0600);
        if (function_exists('set_time_limit')) { set_time_limit(20); }
        try { $parsed=RutPdfForm::extract(RutPdfReader::read($path)); }
        catch (\Throwable $e) { $this->remove($key); throw $e; }
        return ['key'=>$key,'titulo'=>mb_substr(basename(str_replace('\\','/',$file['name'])),0,160),
            'sha256'=>hash_file('sha256',$path),'bytes'=>filesize($path),'parsed'=>$parsed];
    }

    public function path(string $key): string
    {
        if (!preg_match('/^[a-f0-9]{64}\.(?:pdf|pending)$/D',$key)) { throw new \InvalidArgumentException('Referencia de documento inválida.'); }
        return $this->directory().'/'.$key;
    }

    public function remove(string $key): void
    {
        $path=$this->path($key); if (is_file($path)) { unlink($path); }
    }

    public function cleanup(): void
    {
        $count=0;
        foreach (glob($this->directory().'/*.pending')?:[] as $path) {
            if (++$count>100) { break; }
            if (preg_match('/^[a-f0-9]{64}\.pending$/D',basename($path)) && filemtime($path)<time()-7200) { unlink($path); }
        }
    }
}
