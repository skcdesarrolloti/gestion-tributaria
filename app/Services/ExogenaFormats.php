<?php
declare(strict_types=1);

namespace App\Services;

final class ExogenaFormats
{
    // Catálogo compartido por el menú y la página de formatos.
    // Añadir aquí un formato únicamente cuando tenga una ruta disponible.
    public static function all(): array
    {
        return [
            '5247'=>[
                'route'=>'exogena/5247',
                'name'=>'Pagos y retenciones de contratos',
                'description'=>'Pagos o abonos en cuenta y retenciones practicadas en contratos de colaboración empresarial y otros contratos.',
            ],
        ];
    }
}
