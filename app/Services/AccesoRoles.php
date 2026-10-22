<?php
declare(strict_types=1);
namespace App\Services;

final class AccesoRoles
{
    public static function socio(array $user): bool { return ($user['_acceso']['perfil']??'')==='socio' && !empty($user['_acceso']['activo']); }
    public static function edit(array $user, array $record): bool
    {
        $a=$user['_acceso']??[]; $id=(int)($record['contribuyente_id']??$record['id']??0);
        return self::socio($user) || (!empty($a['activo']) && (int)($a['contribuyente_id']??0)===$id && $id>0);
    }
    public static function writable(array $user, array $record): bool
    {
        if (self::edit($user,$record)) { return true; }
        http_response_code(403); echo 'Acceso de consulta. No puedes modificar la documentación de este contribuyente.'; return false;
    }
}
