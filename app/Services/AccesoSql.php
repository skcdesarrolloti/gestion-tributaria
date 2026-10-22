<?php
declare(strict_types=1);
namespace App\Services;

/** Un único parámetro: ID central del actor autenticado, nunca el propietario solicitado. */
final class AccesoSql
{
    public static function condition(string $alias, bool $write=false): string
    {
        if (!in_array($alias,['c','contribuyentes'],true)) { throw new \InvalidArgumentException('Alias de acceso inválido.'); }
        $scope=$write?"(permiso.perfil='socio' AND $alias.funcionario_id=permiso.funcionario_id)":"permiso.perfil='socio'";
        return "EXISTS (SELECT 1 FROM accesos_tributarios permiso WHERE permiso.funcionario_id=? AND permiso.activo=1
            AND ($scope OR permiso.contribuyente_id=$alias.id))";
    }
}
