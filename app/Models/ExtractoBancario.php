<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
use App\Services\AccesoSql;

final class ExtractoBancario
{
    public function __construct(private Database $database) {}

    public function listFor(int $taxpayer, int $owner, int $year): array
    {
        $q=$this->database->connection()->prepare('SELECT d.* FROM extractos_bancarios d JOIN contribuyentes c ON c.id=d.contribuyente_id
            WHERE c.id=? AND '.AccesoSql::condition('c').' AND d.anio=? ORDER BY d.banco,d.cuenta,d.mes');
        $q->execute([$taxpayer,$owner,$year]);return $q->fetchAll();
    }

    public function findFor(int $id, int $owner): ?array
    {
        $q=$this->database->connection()->prepare('SELECT d.* FROM extractos_bancarios d JOIN contribuyentes c ON c.id=d.contribuyente_id
            WHERE d.id=? AND '.AccesoSql::condition('c'));
        $q->execute([$id,$owner]);return $q->fetch()?:null;
    }

    public function saveFor(int $taxpayer, int $owner, array $data, array $file): int
    {
        $db=$this->database->connection();$db->beginTransaction();
        try {
            // Volver a comprobar el acceso dentro de la escritura, incluso si fue revocado tras abrir el formulario.
            $q=$db->prepare('SELECT id FROM contribuyentes WHERE id=? AND '.AccesoSql::condition('contribuyentes',true).' FOR UPDATE');
            $q->execute([$taxpayer,$owner]);if (!$q->fetch()) { throw new \DomainException('Ya no tienes permiso para guardar en esta ficha.'); }
            $q=$db->prepare('SELECT id FROM extractos_bancarios WHERE contribuyente_id=? AND (sha256=? OR (anio=? AND mes=? AND banco=? AND cuenta=?))');
            $q->execute([$taxpayer,$file['sha256'],$data['anio'],$data['mes'],$data['banco'],$data['cuenta']]);
            if ($q->fetch()) { throw new \DomainException('Este PDF o un extracto de esa cuenta y mes ya está guardado. Consúltalo abajo; no se ha duplicado.'); }
            $q=$db->prepare('INSERT INTO extractos_bancarios (contribuyente_id,anio,mes,banco,cuenta,titulo,archivo,sha256,bytes,actor_id,lectura) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
            $q->execute([$taxpayer,$data['anio'],$data['mes'],$data['banco'],$data['cuenta'],$file['titulo'],$file['archivo'],$file['sha256'],$file['bytes'],$owner,
                json_encode($file['lectura']??null,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE)]);
            $id=(int)$db->lastInsertId();$db->commit();return $id;
        } catch (\Throwable $e) { if ($db->inTransaction()) { $db->rollBack(); }throw $e; }
    }
}
