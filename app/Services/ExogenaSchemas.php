<?php
declare(strict_types=1);

namespace App\Services;

final class ExogenaSchemas
{
    public const CODES=['1003','1005','1006','1008','1009','1010','2276','5249','5250','5251','5252'];
    private static array $cache=[];

    public static function has(string|int $code): bool { return in_array((string)$code,self::CODES,true); }

    public static function get(string|int $code): array
    {
        $code=(string)$code;
        if (!self::has($code)) { throw new \InvalidArgumentException('Formato no disponible.'); }
        return self::$cache[$code]??=require dirname(__DIR__,2).'/resources/exogena/'.$code.'.php';
    }

    public static function all(): array
    {
        $result=[];
        foreach (self::CODES as $code) { $result[$code]=self::get($code); }
        return $result;
    }

    public static function identity(array $schema, array $record): string
    {
        if ($schema['code']==='2276') { return trim($record['pno'].' '.$record['ono'].' '.$record['pap'].' '.$record['sap']); }
        return $record['raz']?:trim($record['nom1'].' '.$record['nom2'].' '.$record['apl1'].' '.$record['apl2']);
    }

    public static function sections(array $schema): array
    {
        $ranges=match($schema['code']) {
            '2276'=>[[0,11,'Beneficiario y ubicación'],[11,18,'Pagos e ingresos'],[29,11,'Aportes, retenciones y otros valores'],[40,5,'Dependiente y contrato']],
            '1010'=>[[0,12,'Socio o accionista y ubicación'],[12,4,'Aportes y participación']],
            '5249'=>[[0,8,'Contrato e informado'],[8,2,'IVA descontable y devoluciones'],[10,4,'Participante y datos del contrato']],
            '5250'=>[[0,8,'Contrato e informado'],[8,3,'IVA generado y consumo'],[11,4,'Participante y datos del contrato']],
            '5251','5252'=>[[0,13,'Contrato, informado y ubicación'],[13,1,'Saldo al cierre'],[14,4,'Participante y datos del contrato']],
            default=>[[0,count($schema['fields']), 'Datos del informado y valores']],
        };
        $result=[];
        foreach ($ranges as [$start,$length,$name]) {
            $result[]=['name'=>$name,'fields'=>array_slice($schema['fields'],$start,$length,true)];
        }
        return $result;
    }

    public static function money(string $value): string
    {
        return (string)preg_replace('/(?<=\d)(?=(\d{3})+$)/','.',$value);
    }
}
