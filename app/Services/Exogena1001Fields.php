<?php
declare(strict_types=1);

namespace App\Services;

final class Exogena1001Fields
{
    public const STRUCTURE = '1001-v10-ag2025';
    public const DEFAULT_YEAR = '2025';
    public const SOURCE = 'https://www.dian.gov.co/normatividad/Normatividad/Resoluci%C3%B3n%20000162%20de%2031-10-2023.pdf';

    public static function all(): array
    {
        return [
            'concepto'=>['A','Concepto',4,'Código de pago del año. Los pagos en especie se identifican por concepto; no son una columna adicional del anexo v10.'],
            'documento_tipo'=>['B','Tipo de documento',2,'Código DIAN del documento del beneficiario del pago.'],
            'identificacion'=>['C','Número identificación',20,'Documento del beneficiario como texto, sin separadores y conservando ceros.'],
            'primer_apellido'=>['D','Primer apellido del informado',60,'Para un beneficiario persona natural. Para una entidad, diligenciar razón social.'],
            'segundo_apellido'=>['E','Segundo apellido del informado',60,'Segundo apellido, cuando exista.'],
            'primer_nombre'=>['F','Primer nombre del informado',60,'Primer nombre de la persona natural beneficiaria.'],
            'otros_nombres'=>['G','Otros nombres del informado',60,'Nombres adicionales, cuando existan.'],
            'razon_social'=>['H','Razón social informado',450,'Nombre de la entidad beneficiaria, distinto de la empresa informante.'],
            'direccion'=>['I','Dirección',200,'Dirección del beneficiario. Obligatoria si el país de residencia es Colombia.'],
            'departamento'=>['J','Código departamento',2,'Código DANE de dos dígitos. Obligatorio para Colombia; conservar ceros.'],
            'municipio'=>['K','Código municipio',3,'Código DANE municipal de tres dígitos. Obligatorio para Colombia; conservar ceros.'],
            'pais'=>['L','País de residencia o domicilio',4,'Código DIAN del país, hasta cuatro dígitos. No se asigna país automáticamente.'],
            'pago_deducible'=>['M','Pago o abono en cuenta deducible',18,'Valor fiscalmente deducible reportado en COP. La captura no acredita por sí sola su procedencia.'],
            'pago_no_deducible'=>['N','Pago o abono en cuenta NO deducible',18,'Parte reportada como no deducible; separarla del pago deducible.'],
            'iva_deducible'=>['O','IVA mayor valor del costo o gasto deducible',18,'IVA que integra el costo o gasto deducible. No confundir con IVA descontable.'],
            'iva_no_deducible'=>['P','IVA mayor valor del costo o gasto no deducible',18,'IVA que integra el costo o gasto no deducible.'],
            'retencion_renta'=>['Q','Retención en la fuente practicada Renta',18,'Retención practicada a título de renta, en COP; no es una tarifa.'],
            'retencion_renta_asumida'=>['R','Retención en la fuente asumida Renta',18,'Retención de renta asumida, separada de la practicada.'],
            'retencion_iva'=>['S','Retención en la fuente practicada IVA a responsables del IVA',18,'Retención de IVA practicada a responsables del IVA.'],
            'retencion_iva_exterior'=>['T','Retención en la fuente practicada IVA a no residentes o no domiciliados',18,'Retención de IVA practicada a no residentes o no domiciliados; no denominarla retención asumida.'],
        ];
    }

    public static function money(): array
    {
        return ['pago_deducible','pago_no_deducible','iva_deducible','iva_no_deducible',
            'retencion_renta','retencion_renta_asumida','retencion_iva','retencion_iva_exterior'];
    }
}
