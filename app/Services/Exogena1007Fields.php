<?php
declare(strict_types=1);

namespace App\Services;

final class Exogena1007Fields
{
    public const STRUCTURE = '1007-v9';
    public const DEFAULT_YEAR = '2025';

    public static function all(): array
    {
        return [
            'concepto'=>['A','Concepto',4,'Código de la clase de ingreso según el catálogo aplicable al año. No es una cuenta contable.'],
            'documento_tipo'=>['B','Tipo de documento',2,'Código DIAN del documento de quien se devengó el ingreso.'],
            'identificacion'=>['C','Número identificación',20,'Identificación del tercero informado, conservada como texto y sin separadores.'],
            'primer_apellido'=>['D','Primer apellido del informado',60,'Para un tercero persona natural. Para una empresa, diligenciar razón social.'],
            'segundo_apellido'=>['E','Segundo apellido del informado',60,'Segundo apellido de la persona natural, cuando exista.'],
            'primer_nombre'=>['F','Primer nombre del informado',60,'Primer nombre de la persona natural informada.'],
            'otros_nombres'=>['G','Otros nombres del informado',60,'Nombres adicionales, cuando existan.'],
            'razon_social'=>['H','Razón social informado',450,'Nombre de la entidad de quien se devengó el ingreso.'],
            'pais'=>['I','País de residencia o domicilio',4,'Código DIAN del país del tercero. No se presume Colombia ni se escribe el nombre del país.'],
            'ingresos_brutos'=>['J','Ingresos brutos recibidos',18,'Importe bruto en COP, antes de devoluciones, rebajas y descuentos. Revisar reconocimiento y concepto del año.'],
            'devoluciones'=>['K','Devoluciones, rebajas y descuentos',18,'Importe separado de las reducciones del ingreso. Registrar cero expresamente si corresponde.'],
        ];
    }

    public static function money(): array { return ['ingresos_brutos','devoluciones']; }
}
