<?php
declare(strict_types=1);
namespace App\Services;

final class RegistroNavegacion
{
    public const CEDULAS=['general'=>'Cédula general','pensiones'=>'Pensiones','dividendos'=>'Dividendos y participaciones'];
    public const RENTAS=['trabajo'=>'Trabajo','servicios'=>'Servicios personales','capital'=>'Capital','no_laborales'=>'No laborales'];
    private const BLOCKS=['trabajo'=>'Trabajo','servicios'=>'Servicios personales con costos','capital'=>'Capital','no_laborales'=>'No laborales'];
    public static function state(array $source): array
    {
        $state=['anio'=>\input($source,'anio','2026'),'componente'=>\input($source,'componente','ingresos'),
            'cedula'=>\input($source,'cedula','general'),'renta'=>\input($source,'renta','trabajo')];
        if (!in_array($state['anio'],['2025','2026'],true) || !in_array($state['componente'],['ingresos','costos'],true)
            || !isset(self::CEDULAS[$state['cedula']],self::RENTAS[$state['renta']])) { throw new \InvalidArgumentException('Selecciona año, componente y cédula disponibles.'); }
        return $state;
    }
    public static function concepts(array $all,array $profile,array $state): array
    {
        return array_values(array_filter($all,static function($c) use($profile,$state) {
            if ($c['clase']==='calculado') { return false; }
            $cost=$profile['formulario']==='210'?in_array((int)$c['casilla'],[45,60,77],true):$c['bloque']==='Costos y deducciones';
            if ($cost!==($state['componente']==='costos')) { return false; }
            return $profile['formulario']!=='210' || ($state['cedula']==='general' && $c['bloque']===self::BLOCKS[$state['renta']]);
        }));
    }
    public static function query(array $state,int $taxpayer): string
    {
        return '&'.http_build_query(['contribuyente'=>$taxpayer]+$state);
    }
}
