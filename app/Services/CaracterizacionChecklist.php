<?php
declare(strict_types=1);
namespace App\Services;

final class CaracterizacionChecklist
{
    public static function review(array $taxpayer, array $blocks, array $schemas, ?array $annual=null): array
    {
        $result=['blocks'=>[],'alerts'=>[],'reviewed'=>0]; $data=[];
        foreach ($schemas as $key=>$schema) {
            $d=CaracterizacionData::read($blocks[$key]['datos']??[],$schema); $data[$key]=$d;
            $missing=CaracterizacionData::missing($d,$schema,$taxpayer['tipo']);
            $status=isset($blocks[$key])?CaracterizacionData::status($d,$schema,$taxpayer['tipo']):'Pendiente de diligenciar';
            $result['blocks'][$key]=compact('missing','status');
            if ($status==='Contrastado por usuario') { $result['reviewed']++; }
        }
        $c=static fn($block,$field)=>$data[$block]['campos'][$field]??'';
        $codes=array_column($data['responsabilidades']['filas'],'codigo');
        if (in_array('47',$codes,true) && $c('fiscal','regimen')==='ordinario') { $result['alerts'][]='Diferencia: RUT con código 47 SIMPLE y régimen del año marcado Ordinario.'; }
        if (in_array('48',$codes,true) && $c('fiscal','iva')==='no_responsable') { $result['alerts'][]='Diferencia: código 48 Responsable de IVA y condición anual No responsable.'; }
        if (in_array('48',$codes,true) && in_array('49',$codes,true)) { $result['alerts'][]='Revisa códigos de IVA 48 y 49 registrados simultáneamente.'; }
        if ($c('ingresos','pensiones')==='si' && $c('seguridad','tipo_pension')==='no') { $result['alerts'][]='Diferencia: hay ingresos por pensión, pero en seguridad social se indicó que no recibe pensión.'; }
        if ($c('beneficios','dependientes')==='si' && !in_array('dependiente',array_column($data['relacionados']['filas'],'rol'),true)) {
            $result['alerts'][]='Falta detallar los dependientes y sus soportes en Representantes, familia y socios.';
        }
        if ($c('patrimonio','exterior')==='si' && !$data['patrimonio']['filas']) { $result['alerts'][]='Falta detallar las cuentas y activos del exterior.'; }
        if ($c('ingresos','exterior')==='si' || $c('ingresos','ece')==='si' || $c('ingresos','ocasionales')==='si') {
            $result['alerts'][]='Hay hechos internacionales o ganancias ocasionales: revisar reglas particulares antes de utilizar el simulador común.';
        }
        if ($annual) {
            foreach (['residencia','regimen','formulario'] as $field) {
                if ($c('fiscal',$field)!=='' && $c('fiscal',$field)!==$annual[$field]) { $result['alerts'][]='Diferencia en '.$field.' frente al perfil de Ingresos por año; no se ha modificado ese perfil.'; }
            }
        } else { $result['alerts'][]='El perfil de Ingresos por año aún no existe. Completar esta ficha no lo crea automáticamente.'; }
        return $result;
    }
}
