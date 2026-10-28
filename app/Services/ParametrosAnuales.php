<?php
declare(strict_types=1);
namespace App\Services;

/** Catálogo por año cargado una vez por solicitud. Los valores anteriores siguen versionados. */
final class ParametrosAnuales
{
    private static array $saved=[];
    public const LIMITS=[
        'vivienda_uvt'=>['Intereses de vivienda · máximo mensual en UVT',100],
        'prepagada_uvt'=>['Prepagada · máximo mensual en UVT',16],
        'dependientes_uvt'=>['Dependientes · máximo mensual en UVT',32],
        'dependientes_pct'=>['Dependientes · porcentaje del ingreso bruto',10],
        'exencion_uvt'=>['Exención laboral · máximo anual en UVT',790],
        'exencion_pct'=>['Exención laboral · porcentaje',25],
        'conjunto_uvt'=>['Beneficios conjuntos · máximo anual en UVT',1340],
        'conjunto_pct'=>['Beneficios conjuntos · porcentaje máximo',40],
    ];
    public static function load(array $saved): void { self::$saved=$saved; }
    public static function defaults(int $year): array
    {
        $base=match($year) {
            2025=>['uvt'=>49799,'source'=>'https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0193_2024.htm'],
            2026=>['uvt'=>52374,'source'=>'https://normograma.dian.gov.co/dian/compilacion/docs/resolucion_dian_0238_2025.htm'],
            default=>throw new \InvalidArgumentException('No hay parámetros confirmados para ese año.'),
        };
        return $base+['revision'=>0,'norma'=>'ET 206.10, 383, 387 y 388; decreto 1625/2016, art. 1.2.4.1.6.',
            'tramos'=>RetencionTarifa::BRACKETS]+array_map(static fn($field)=>$field[1],self::LIMITS);
    }
    public static function year(int $year): array { return self::$saved[$year]??self::defaults($year); }
    public static function years(): array { $years=array_unique([2025,2026,...array_keys(self::$saved)]);sort($years);return $years; }
    public static function fingerprint(int $year): string { return self::hash(self::year($year)); }
    public static function originalFingerprint(int $year): string
    {
        return in_array($year,[2025,2026],true)?self::hash(self::defaults($year)):'';
    }
    private static function hash(array $rules): string { return hash('sha256',json_encode($rules,JSON_THROW_ON_ERROR)); }
}
