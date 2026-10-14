<?php
declare(strict_types=1);

namespace App\Services;

final class Exogena5247Fields
{
    public const STRUCTURE = 'plantilla-2024';
    public const DEFAULT_YEAR = '2025';

    public static function all(): array
    {
        // Orden de las columnas A–V de la hoja 5247. Solo estructura, sin datos reales.
        return [
            'tipo_contrato'=>['A','Tipo de contrato',2,'Código del contrato al que corresponde la operación. Revisar catálogo del año.'],
            'concepto'=>['B','Concepto',4,'Código del concepto de pago o abono; no es una tarifa de retención.'],
            'documento_tipo'=>['C','Tipo de documento',2,'Código DIAN del documento del tercero informado.'],
            'identificacion'=>['D','Número identificación',20,'Documento del informado. Conservar letras y ceros cuando corresponda; sin separadores.'],
            'primer_apellido'=>['E','Primer apellido del informado',60,'Para una persona natural. Para una empresa, diligenciar razón social.'],
            'segundo_apellido'=>['F','Segundo apellido del informado',60,'Segundo apellido, cuando exista.'],
            'primer_nombre'=>['G','Primer nombre del informado',60,'Primer nombre de la persona natural informada.'],
            'otros_nombres'=>['H','Otros nombres del informado',60,'Nombres adicionales, cuando existan.'],
            'razon_social'=>['I','Razón social informado',450,'Nombre de la entidad informada; distinguirla de la empresa que presenta el reporte.'],
            'direccion'=>['J','Dirección',200,'Dirección del tercero informado. Completar según país y reglas del año.'],
            'departamento'=>['K','Código dpto.',2,'Código de departamento como texto de dos dígitos; conservar ceros.'],
            'municipio'=>['L','Código mcp',3,'Código municipal de tres dígitos de la plantilla; no perder ceros iniciales.'],
            'pais'=>['M','País de residencia o domicilio',3,'Código DIAN de país; no escribir el nombre del país en este campo.'],
            'pago'=>['N','Pago o abono en cuenta',15,'Valor reportado en COP. Registrar cero expresamente cuando corresponda.'],
            'iva_costo'=>['O','IVA mayor valor del costo o gasto',15,'IVA reportado como mayor valor del costo o gasto; no confundir con IVA descontable.'],
            'retencion_renta'=>['P','Retención en la fuente practicada en renta',15,'Retención practicada, en COP; no es el valor del pago.'],
            'retencion_renta_asumida'=>['Q','Retención en la fuente asumida en renta',15,'Retención de renta asumida, separada de la practicada.'],
            'retencion_iva'=>['R','Retención en la fuente practicada IVA régimen común',15,'Nombre conservado de la plantilla 2024. Revisar denominación y alcance del año a reportar.'],
            'retencion_no_domiciliados'=>['S','Retención en la fuente asumida no domiciliados',15,'Valor de la retención asumida para no domiciliados según el formato aplicable.'],
            'fideicomiso'=>['T','Identificación del fideicomiso',20,'Identificación cuando la operación se realiza a través de un fideicomiso. No asignar cero automáticamente.'],
            'participante_tipo'=>['U','Tipo de documento participante en contrato',2,'Código del documento del participante, distinto del documento del tercero informado.'],
            'participante_identificacion'=>['V','Identificación participante en contrato',20,'Documento del participante en el contrato; conservarlo como texto.'],
        ];
    }

    public static function money(): array
    {
        return ['pago','iva_costo','retencion_renta','retencion_renta_asumida','retencion_iva','retencion_no_domiciliados'];
    }
}
