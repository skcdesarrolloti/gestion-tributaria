<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Database;

final class CuentaRegistro
{
    public function __construct(private Database $database) {}
    public function create(?int $actor,string $login,string $name,string $email,string $password,string $role,?int $taxpayer,bool $initial=false): int
    {
        [$login,$name,$email]=ClaveCuenta::identity($login,$name,$email);ClaveCuenta::validate($password);
        if (!in_array($role,['socio','usuario'],true) || ($role==='usuario' && !$taxpayer)) { throw new \InvalidArgumentException('Selecciona perfil y contribuyente para un Usuario.'); }
        if ($role==='socio') { $taxpayer=null; }
        $db=$this->database->connection();$db->beginTransaction();
        try {
            // Mismo bloqueo que la administración de permisos; serializa altas y activación inicial.
            $grants=$db->query('SELECT * FROM accesos_tributarios ORDER BY funcionario_id FOR UPDATE')->fetchAll(\PDO::FETCH_UNIQUE);
            if ($initial) {
                if ($db->query('SELECT 1 FROM usuarios_tributarios LIMIT 1')->fetchColumn()) { throw new \DomainException('La cuenta inicial ya fue activada. Inicia sesión.'); }
            } elseif (($grants[$actor]['perfil']??'')!=='socio' || empty($grants[$actor]['activo'])) { throw new \DomainException('Solo un Socio puede crear cuentas.'); }
            $q=$db->prepare('SELECT id FROM usuarios_tributarios WHERE usuario=?');$q->execute([$login]);
            if ($q->fetchColumn()) { throw new \InvalidArgumentException('Ese nombre de usuario ya existe.'); }
            if ($taxpayer) { $q=$db->prepare('SELECT id FROM contribuyentes WHERE id=? FOR UPDATE');$q->execute([$taxpayer]);if (!$q->fetchColumn()) { throw new \InvalidArgumentException('Selecciona un contribuyente existente.'); } }
            $hash=hash('sha256',$login);$previous=null;$id=0;
            foreach ($grants as $key=>$grant) { if (hash_equals($grant['login_hash'],$hash)) { $id=(int)$key;$previous=$grant;break; } }
            if ($initial && (!$previous || $previous['perfil']!=='socio' || !$previous['activo'])) { throw new \InvalidArgumentException('Usa el usuario del Socio previamente autorizado.'); }
            $pending=$previous && $id===0;
            if (!$id) { $id=1+max((int)$db->query('SELECT COALESCE(MAX(id),0) FROM usuarios_tributarios')->fetchColumn(),(int)$db->query('SELECT COALESCE(MAX(funcionario_id),0) FROM contribuyentes')->fetchColumn(),max(array_map('intval',array_keys($grants))?:[0])); }
            $q=$db->prepare('INSERT INTO usuarios_tributarios (id,usuario,nombre,correo,clave_hash,cambiar_clave,creado_por) VALUES (?,?,?,?,?,?,?)');
            $q->execute([$id,$login,$name,$email,password_hash($password,PASSWORD_DEFAULT),(int)!$initial,$actor]);
            if ($pending) { $db->prepare('DELETE FROM accesos_tributarios WHERE funcionario_id=0 AND login_hash=?')->execute([$hash]); }
            $q=$db->prepare('INSERT INTO accesos_tributarios (funcionario_id,login_hash,perfil,contribuyente_id,activo,asignado_por) VALUES (?,?,?,?,1,?) ON DUPLICATE KEY UPDATE perfil=VALUES(perfil),contribuyente_id=VALUES(contribuyente_id),activo=1,asignado_por=VALUES(asignado_por),revision=revision+1');
            $q->execute([$id,$hash,$role,$taxpayer,$actor]);
            $q=$db->prepare('INSERT INTO historial_accesos (actor_id,funcionario_id,anterior,nuevo) VALUES (?,?,?,?)');
            $q->execute([$actor??$id,$id,$previous?json_encode($previous,JSON_THROW_ON_ERROR):null,json_encode(['evento'=>$initial?'activacion_inicial':'cuenta_creada','perfil'=>$role,'contribuyente_id'=>$taxpayer],JSON_THROW_ON_ERROR)]);
            $db->commit();return $id;
        } catch (\Throwable $e) { if ($db->inTransaction()) { $db->rollBack(); }throw $e; }
    }
}
