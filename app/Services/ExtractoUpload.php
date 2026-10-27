<?php
declare(strict_types=1);
namespace App\Services;

/** Conserva soportes privados; no presupone que un abono sea ingreso fiscal. */
final class ExtractoUpload
{
    public const MAX_BYTES=10485760;
    public function __construct(private string $storage) {}

    public function path(string $key): string
    {
        if (!preg_match('/^[a-f0-9]{64}\.pdf$/D',$key)) { throw new \InvalidArgumentException('Referencia de extracto inválida.'); }
        $dir=rtrim($this->storage,'/\\').'/extractos-bancarios';
        if (!is_dir($dir) && !mkdir($dir,0700,true) && !is_dir($dir)) { throw new \RuntimeException('Almacenamiento no disponible.'); }
        if (!is_file($dir.'/.htaccess') && file_put_contents($dir.'/.htaccess',"Require all denied\n")===false) {
            throw new \RuntimeException('Almacenamiento no disponible.');
        }
        return $dir.'/'.$key;
    }

    public static function validate(string $path): void
    {
        $bytes=filesize($path);
        if (!$bytes || $bytes>self::MAX_BYTES) { throw new \InvalidArgumentException('El extracto debe pesar como máximo 10 MB.'); }
        $h=fopen($path,'rb');$signature=fread($h,5);fclose($h);
        if ($signature!=='%PDF-' || (new \finfo(FILEINFO_MIME_TYPE))->file($path)!=='application/pdf') {
            throw new \InvalidArgumentException('El archivo no es un PDF válido.');
        }
    }

    public function receive(array $file): array
    {
        if (!isset($file['error'],$file['tmp_name'],$file['name']) || $file['error']!==UPLOAD_ERR_OK
            || !is_string($file['tmp_name']) || !is_string($file['name']) || !is_uploaded_file($file['tmp_name'])
            || strtolower(pathinfo($file['name'],PATHINFO_EXTENSION))!=='pdf') {
            throw new \InvalidArgumentException('Selecciona un PDF. Comprueba también el límite de carga del servidor (máximo aquí: 10 MB).');
        }
        self::validate($file['tmp_name']);
        if (function_exists('set_time_limit')) { set_time_limit(25); }
        $key=bin2hex(random_bytes(32)).'.pdf';$path=$this->path($key);
        if (!move_uploaded_file($file['tmp_name'],$path)) { throw new \RuntimeException('No se pudo conservar el documento.'); }
        chmod($path,0600);
        return ['archivo'=>$key,'titulo'=>mb_substr(basename(str_replace('\\','/',$file['name'])),0,160),
            'sha256'=>hash_file('sha256',$path),'bytes'=>filesize($path),'lectura'=>ExtractoPdfReader::read($path)];
    }

    public function remove(string $key): void
    {
        $path=$this->path($key);if (is_file($path)) { unlink($path); }
    }
}
