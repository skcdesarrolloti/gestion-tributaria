<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;

final class AccesoTributario
{
    public function __construct(private Database $database) {}
    public function forUser(array $user): array
    {
        $db=$this->database->connection(); $hash=hash('sha256',mb_strtolower(trim($user['user_others_apss'])));
        // Solo vincula la autorización inicial explícita; no crea una cuenta ni deduce permisos del rol central.
        $q=$db->prepare('UPDATE accesos_tributarios SET funcionario_id=? WHERE funcionario_id=0 AND login_hash=? AND activo=1');
        $q->execute([(int)$user['_ID'],$hash]);
        $q=$db->prepare('SELECT * FROM accesos_tributarios WHERE funcionario_id=?');
        $q->execute([(int)$user['_ID']]); return $q->fetch()?:[];
    }
    public function rows(): array
    {
        return $this->database->connection()->query('SELECT a.*,c.nombre AS contribuyente FROM accesos_tributarios a LEFT JOIN contribuyentes c ON c.id=a.contribuyente_id WHERE a.funcionario_id>0 ORDER BY a.funcionario_id')->fetchAll();
    }
    public function save(int $actor, array $target, string $role, ?int $taxpayer, bool $active, int $revision): void
    {
        if (!in_array($role,['socio','usuario'],true) || ($role==='usuario' && $active && !$taxpayer)) { throw new \InvalidArgumentException('Un Usuario activo necesita su contribuyente asignado.'); }
        $db=$this->database->connection(); $db->beginTransaction();
        try {
            $all=$db->query('SELECT * FROM accesos_tributarios ORDER BY funcionario_id FOR UPDATE')->fetchAll(\PDO::FETCH_UNIQUE);
            if (($all[$actor]['perfil']??'')!=='socio' || empty($all[$actor]['activo'])) { throw new \DomainException('Solo un Socio puede administrar accesos.'); }
            $id=(int)$target['_ID']; $old=$all[$id]??null;
            $hash=hash('sha256',mb_strtolower(trim($target['user_others_apss'])));
            foreach ($all as $otherId=>$assignment) {
                if ((int)$otherId!==$id && hash_equals($assignment['login_hash'],$hash)) { throw new \InvalidArgumentException('Ese usuario institucional ya está vinculado a otra cuenta. Revisa su identificación central.'); }
            }
            if ((int)($old['revision']??0)!==$revision) { throw new \DomainException('La asignación cambió en otra ventana. Recarga antes de guardar.'); }
            if ($old && $old['perfil']==='socio' && $old['activo'] && (!$active || $role!=='socio')) {
                $partners=array_filter($all,static fn($r)=>$r['perfil']==='socio' && $r['activo']);
                if (count($partners)<=1) { throw new \InvalidArgumentException('Debe permanecer al menos un Socio activo.'); }
            }
            if ($taxpayer) { $q=$db->prepare('SELECT id FROM contribuyentes WHERE id=? FOR UPDATE'); $q->execute([$taxpayer]); if (!$q->fetchColumn()) { throw new \InvalidArgumentException('Selecciona un contribuyente existente.'); } }
            $new=['perfil'=>$role,'contribuyente_id'=>$taxpayer,'activo'=>(int)$active];
            $q=$db->prepare('INSERT INTO accesos_tributarios (funcionario_id,login_hash,perfil,contribuyente_id,activo,asignado_por) VALUES (?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE login_hash=VALUES(login_hash),perfil=VALUES(perfil),contribuyente_id=VALUES(contribuyente_id),activo=VALUES(activo),asignado_por=VALUES(asignado_por),revision=revision+1');
            $q->execute([$id,$hash,$role,$taxpayer,(int)$active,$actor]);
            $q=$db->prepare('INSERT INTO historial_accesos (actor_id,funcionario_id,anterior,nuevo) VALUES (?,?,?,?)');
            $q->execute([$actor,$id,$old?json_encode($old,JSON_THROW_ON_ERROR):null,json_encode($new,JSON_THROW_ON_ERROR)]);
            $db->commit();
        } catch (\Throwable $e) { if ($db->inTransaction()) { $db->rollBack(); } throw $e; }
    }
}
