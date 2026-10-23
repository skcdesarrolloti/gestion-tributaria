<?php
declare(strict_types=1);
namespace App\Services;

final class RegistroNavegacion
{
    public const CEDULAS=['general'=>'Cédula general','pensiones'=>'Pensiones','dividendos'=>'Dividendos y participaciones'];
    public const RENTAS=['trabajo'=>'Trabajo','servicios'=>'Servicios personales','capital'=>'Capital','no_laborales'=>'No laborales'];
    public const SECCIONES_110=['ordinarios'=>'Actividades ordinarias','financieros'=>'Ingresos financieros','dividendos'=>'Dividendos y participaciones','otros'=>'Otros ingresos','ganancias'=>'Ganancias ocasionales'];
    private const BLOCKS=['trabajo'=>'Trabajo','servicios'=>'Servicios personales con costos','capital'=>'Capital','no_laborales'=>'No laborales'];
    public static function state(array $source): array
    {
        $state=['anio'=>\input($source,'anio','2026'),'componente'=>\input($source,'componente','ingresos'),
            'cedula'=>\input($source,'cedula','general'),'renta'=>\input($source,'renta','trabajo'),
            'seccion'=>\input($source,'seccion','ordinarios')];
        if (!in_array($state['anio'],['2025','2026'],true) || !in_array($state['componente'],['ingresos','costos'],true)
            || (!isset(self::CEDULAS[$state['cedula']]) && $state['cedula']!=='ganancias')
            || !isset(self::RENTAS[$state['renta']],self::SECCIONES_110[$state['seccion']])) { throw new \InvalidArgumentException('Selecciona año, componente y cédula disponibles.'); }
        return $state;
    }
    public static function concepts(array $all,array $profile,array $state): array
    {
        return array_values(array_filter($all,static function($c) use($profile,$state) {
            if ($c['clase']==='calculado') { return false; }
            $cost=$profile['formulario']==='210'?in_array((int)$c['casilla'],[45,60,77],true):$c['bloque']==='Costos y deducciones';
            if ($cost!==($state['componente']==='costos')) { return false; }
            if (!IngresoData::allowed($c,$profile)) { return false; }
            if ($profile['formulario']!=='210') {
                if ($cost) { return true; }
                $codes=match($state['seccion']) {
                    'ordinarios'=>[47,59,60], 'financieros'=>[48], 'dividendos'=>range(49,56), 'otros'=>[57], 'ganancias'=>[80],
                };
                return in_array((int)$c['casilla'],$codes,true);
            }
            if ($state['cedula']==='general') { return $c['bloque']===self::BLOCKS[$state['renta']]; }
            return $c['bloque']===match($state['cedula']) { 'pensiones'=>'Pensiones','dividendos'=>'Dividendos y participaciones','ganancias'=>'Ganancias ocasionales' };
        }));
    }
    public static function query(array $state,int $taxpayer): string
    {
        return '&'.http_build_query(['contribuyente'=>$taxpayer]+$state);
    }
}
