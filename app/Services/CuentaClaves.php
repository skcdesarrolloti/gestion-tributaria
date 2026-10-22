<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Database;

final class CuentaClaves
{
    public function __construct(private Database $database) {}
    public function reset(int $actor,int $target,string $current,string $password): void
    {
        ClaveCuenta::validate($password);$db=$this->database->connection();$db->beginTransaction();
        try {
            $q=$db->prepare("SELECT 1 FROM accesos_tributarios WHERE funcionario_id=? AND perfil='socio' AND activo=1 FOR UPDATE");$q->execute([$actor]);
            if (!$q->fetchColumn()) { throw new \DomainException('Solo un Socio puede restablecer contraseñas.'); }
            $q=$db->prepare('SELECT clave_hash FROM usuarios_tributarios WHERE id=?');$q->execute([$actor]);
            if (!password_verify($current,$q->fetchColumn()?:'')) { throw new \InvalidArgumentException('Tu contraseña actual no es correcta.'); }
            if ($actor===$target) { throw new \InvalidArgumentException('Cambia tu propia contraseña desde Mi perfil.'); }
            $q=$db->prepare('UPDATE usuarios_tributarios SET clave_hash=?,cambiar_clave=1,clave_version=clave_version+1 WHERE id=?');
            $q->execute([password_hash($password,PASSWORD_DEFAULT),$target]);
            if (!$q->rowCount()) { throw new \InvalidArgumentException('La cuenta no existe.'); }
            $db->prepare('INSERT INTO historial_accesos (actor_id,funcionario_id,anterior,nuevo) VALUES (?,?,NULL,?)')->execute([$actor,$target,json_encode(['evento'=>'clave_restablecida'],JSON_THROW_ON_ERROR)]);
            $db->commit();
        } catch (\Throwable $e) { if ($db->inTransaction()) { $db->rollBack(); }throw $e; }
    }
}
