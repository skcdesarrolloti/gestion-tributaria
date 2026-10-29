<?php
declare(strict_types=1);
namespace App\Services;

final class CreditoCarga
{
    public static function read(array $file,ExtractoUpload $files,array $taxpayer,string $year): ?array
    {
        if (($file['lectura']['tipo_documento']??'')!=='credito') { return null; }
        if ($taxpayer['tipo']!=='natural') { throw new \InvalidArgumentException('La carga de créditos está habilitada para tu ficha personal, no para empresas.'); }
        $copy=array_intersect_key($file['lectura'],['archivo_consulta'=>true,'sha256_consulta'=>true]);
        $data=PrestamoCajaSocial::read($files->path($copy['archivo_consulta']??$file['archivo']),$taxpayer['nombre'])+$copy;
        if (max($data['pago'],$data['factura'])>date('Y-m-d') || $data['pago']>$data['factura']) { throw new \InvalidArgumentException('Revisa las fechas del pago y del corte del crédito.'); }
        if (substr($data['pago'],0,4)!==$year) { throw new \InvalidArgumentException('Abre el año del pago anterior: '.substr($data['pago'],0,4).'. No se toma la próxima cuota como pagada.'); }
        return $data;
    }
}
