<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\RutDocumento;

final class RutAplicacion
{
    public function __construct(private RutDocumento $documents, private RutUpload $files) {}
    public function apply(array $preview, array $selected, bool $replace, array $schemas): array
    {
        $old=$preview['file']['key']; $final=substr($old,0,64).'.pdf'; $path=$this->files->path($old);
        if (!is_file($path) || !hash_equals($preview['file']['sha256'],hash_file('sha256',$path))) { throw new \InvalidArgumentException('El archivo temporal no está disponible o cambió. Vuelve a subir el PDF.'); }
        if (!rename($path,$this->files->path($final))) { throw new \RuntimeException('No se pudo conservar el documento.'); }
        try { $result=$this->documents->applyFor($preview['id'],$preview['owner'],(int)$preview['year'],$preview,$selected,$replace,$schemas,$final); }
        catch (\Throwable $e) { rename($this->files->path($final),$path); throw $e; }
        if ($result['document']['archivo']!==$final) { $this->files->remove($final); }
        return $result;
    }
}
