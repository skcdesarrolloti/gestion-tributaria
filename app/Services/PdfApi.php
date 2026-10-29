<?php
declare(strict_types=1);
namespace App\Services;

final class PdfApi
{
    public static function send(array $data,int $status=200): void
    { http_response_code($status);header('Content-Type: application/json; charset=UTF-8');echo json_encode($data,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE); }
    public static function file(array $files,string $key): array
    {
        $file=$files[$key]??[];
        if (isset($file['name']) && is_array($file['name'])) {
            if (count($file['name'])!==1) { throw new \InvalidArgumentException('Para cargar varios PDF usa «Subir seleccionados» con JavaScript habilitado. No se ha guardado ninguno.'); }
            $file=array_map(static fn($values)=>$values[0]??null,$file);
        }
        return $file;
    }
}
