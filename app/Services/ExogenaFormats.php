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
            '1001'=>[
                'route'=>'exogena/1001',
                'name'=>'Pagos o Abonos en Cuenta y Retenciones Practicadas',
                'description'=>'Pagos deducibles y no deducibles, IVA como mayor valor y retenciones por beneficiario. Año 2025 · Versión 10.',
            ],
            '1007'=>[
                'route'=>'exogena/1007',
                'name'=>'Ingresos Propios',
                'description'=>'Ingresos brutos y devoluciones, rebajas y descuentos por tercero informado.',
            ],
            '5247'=>[
                'route'=>'exogena/5247',
                'name'=>'Pagos o Abonos en cuenta',
                'description'=>'Pagos o abonos en cuenta y retenciones practicadas en contratos de colaboración empresarial y otros contratos.',
            ],
        ];
    }
}
