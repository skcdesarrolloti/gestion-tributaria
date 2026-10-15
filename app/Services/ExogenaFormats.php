<?php
declare(strict_types=1);

namespace App\Services;

final class ExogenaFormats
{
    // Catálogo compartido por el menú y la página de formatos.
    // Añadir aquí un formato únicamente cuando tenga una ruta disponible.
    public static function all(): array
    {
        $formats=[
            '1001'=>[
                'route'=>'exogena/1001',
                'name'=>'Pagos o Abonos en Cuenta y Retenciones Practicadas',
                'description'=>'Pagos deducibles y no deducibles, IVA como mayor valor y retenciones por beneficiario. Año 2025 · Versión 10.',
            ],
            '1007'=>[
                'route'=>'exogena/1007',
                'name'=>'Ingresos Recibidos',
                'description'=>'Ingresos brutos y devoluciones, rebajas y descuentos por tercero informado.',
            ],
            '5247'=>[
                'route'=>'exogena/5247',
                'name'=>'Pagos o Abonos en cuenta',
                'description'=>'Pagos o abonos en cuenta y retenciones practicadas en contratos de colaboración empresarial y otros contratos.',
            ],
        ];
        foreach (['1001'=>'pagos','1007'=>'ingresos','5247'=>'contratos'] as $code=>$group) { $formats[$code]['group']=$group; }
        foreach (ExogenaSchemas::all() as $code=>$schema) {
            $formats[$code]=['route'=>'exogena/'.$code,'name'=>$schema['name'],'group'=>$schema['group'],
                'description'=>'Estructura de '.count($schema['fields']).' campos · Referencia versión '.$schema['version'].' · Preparación 2025.'];
        }
        uksort($formats,static fn($a,$b)=>(int)$a<=>(int)$b);
        return $formats;
    }

    public static function groups(): array
    {
        return ['pagos'=>'Pagos y retenciones','iva'=>'IVA','ingresos'=>'Ingresos',
            'cuentas'=>'Cuentas y socios','laboral'=>'Rentas de trabajo y pensiones','contratos'=>'Contratos de colaboración'];
    }

    public static function grouped(): array
    {
        $result=[]; $formats=self::all();
        foreach (self::groups() as $key=>$label) {
            $result[$key]=['name'=>$label,'formats'=>array_filter($formats,static fn($format)=>$format['group']===$key)];
        }
        return $result;
    }
}
