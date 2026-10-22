<?php
declare(strict_types=1);
namespace App\Services;

/** Presentación del perfil; conserva los bloques históricos y separa sus personas por función. */
final class PerfilContribuyente
{
    public static function tabs(string $type): array
    {
        $tabs=[
            'identificacion'=>['Identificación','identificacion'], 'ubicacion'=>['Contacto','ubicacion'],
            'actividades'=>['Actividades económicas','actividades'], 'responsabilidades'=>['Responsabilidades','responsabilidades'],
            'entidad'=>[$type==='sucesion'?'Sucesión':'Sociedad / constitución','entidad'],
            'representantes'=>['Representantes','relacionados',['representante','apoderado','heredero']],
            'socios'=>['Socios y participaciones','relacionados',['socio','socio_junta']],
            'junta'=>['Junta directiva','relacionados',['junta']],
            'profesionales'=>['Contador y revisor','relacionados',['contador','revisor']],
            'familia'=>['Personas relacionadas','relacionados',['conyuge','dependiente','otro']],
            'personal'=>['Condiciones personales','seguridad'],
            'establecimientos'=>['Establecimientos','establecimientos'], 'aduanas'=>['Aduanas','aduanas'],
            'fiscal'=>['Condiciones tributarias','fiscal'], 'documentos'=>['RUT y documentos',''],
        ];
        if ($type==='natural') {
            $tabs['entidad'][0]='Situación personal / sucesión';
            $tabs['socios']=['Participaciones','relacionados',['participacion','socio','socio_junta']];
        }
        if ($type==='juridica') { unset($tabs['personal']); }
        return $tabs;
    }

    public static function schema(array $schema, array $roles): array
    {
        $allowed=in_array('socio_junta',$roles,true)?array_merge($roles,['junta']):$roles;
        if ($roles) { $schema['rows']['rol']['options']=array_intersect_key($schema['rows']['rol']['options'],array_flip($allowed)); }
        return $schema;
    }

    public static function subset(array $data, array $roles): array
    {
        if ($roles) { $data['filas']=array_values(array_filter($data['filas'],static fn($r)=>in_array($r['rol'],$roles,true))); }
        return $data;
    }

    public static function combine(array $saved, array $posted, array $roles): array
    {
        if (!$roles) { return $posted; }
        $allowed=in_array('socio_junta',$roles,true)?array_merge($roles,['junta']):$roles;
        foreach ($posted['filas'] as $row) {
            if (!in_array($row['rol'],$allowed,true)) { throw new \InvalidArgumentException('La relación no corresponde a esta pestaña.'); }
        }
        $others=array_values(array_filter($saved['filas'],static fn($r)=>!in_array($r['rol'],$roles,true)));
        $posted['filas']=array_merge($others,$posted['filas']);
        if ($posted['filas']) { $posted['aplicabilidad']='si'; }
        if (count($posted['filas'])>100) { throw new \InvalidArgumentException('El detalle de personas supera 100 renglones.'); }
        return $posted;
    }
}
