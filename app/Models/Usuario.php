<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;

final class Usuario
{
    public function __construct(private Database $database) {}
    public function findActiveByLogin(string $login): ?array { return $this->find('u.usuario',mb_strtolower(trim($login))); }
    public function findActiveById(int $id): ?array { return $this->find('u.id',$id); }
    public function activeDirectory(): array
    {
        // Los alias conservan los identificadores históricos sin consultar funcionarios.
        return $this->database->connection()->query('SELECT id AS _ID,nombre,usuario AS user_others_apss FROM usuarios_tributarios ORDER BY nombre,id')->fetchAll();
    }
    public function exists(): bool { return (bool)$this->database->connection()->query('SELECT 1 FROM usuarios_tributarios LIMIT 1')->fetchColumn(); }
    private function find(string $column,string|int $value): ?array
    {
        $q=$this->database->connection()->prepare("SELECT u.*,u.id AS _ID,u.usuario AS user_others_apss FROM usuarios_tributarios u
            JOIN accesos_tributarios a ON a.funcionario_id=u.id AND a.activo=1 WHERE $column=? LIMIT 1");
        $q->execute([$value]);return $q->fetch()?:null;
    }
    public function changePassword(int $id,string $current,string $password,int $version): int
    {
        \App\Services\ClaveCuenta::validate($password);
        $user=$this->findActiveById($id);
        if (!$user || !password_verify($current,$user['clave_hash'])) { throw new \InvalidArgumentException('La contraseña actual no es correcta.'); }
        if (password_verify($password,$user['clave_hash'])) { throw new \InvalidArgumentException('Elige una contraseña diferente de la actual.'); }
        $q=$this->database->connection()->prepare('UPDATE usuarios_tributarios SET clave_hash=?,cambiar_clave=0,clave_version=clave_version+1 WHERE id=? AND clave_version=?');
        $q->execute([password_hash($password,PASSWORD_DEFAULT),$id,$version]);
        if (!$q->rowCount()) { throw new \DomainException('La contraseña cambió en otra sesión. Vuelve a ingresar.'); }
        return $version+1;
    }
}
