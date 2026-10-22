<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Contribuyente
{
    public function __construct(private Database $database) {}

    public function listFor(int $owner): array
    {
        $query = $this->database->connection()->prepare(
            'SELECT * FROM contribuyentes WHERE '.\App\Services\AccesoSql::condition('contribuyentes',false).' ORDER BY nombre, id'
        );
        $query->execute([$owner]);
        return array_map([$this, 'decode'], $query->fetchAll());
    }

    public function findFor(int $id, int $owner): ?array
    {
        $query = $this->database->connection()->prepare('SELECT * FROM contribuyentes WHERE id = ? AND '.\App\Services\AccesoSql::condition('contribuyentes',false));
        $query->execute([$id, $owner]);
        $row = $query->fetch();
        return $row ? $this->decode($row) : null;
    }

    public function existsFromRut(array $parsed, int $owner): bool
    {
        return $this->findFromRutFor($parsed,$owner)!==null;
    }

    public function findFromRutFor(array $parsed, int $owner): ?array
    {
        $identity=$parsed['fields']['identificacion'];
        $q=$this->database->connection()->prepare('SELECT * FROM contribuyentes WHERE '.\App\Services\AccesoSql::condition('contribuyentes',false).' AND
            ((documento_tipo="NIT" AND documento_numero=?) OR (documento_tipo=? AND documento_numero=?))
            ORDER BY (documento_tipo="NIT") DESC,id LIMIT 1');
        $q->execute([$owner,$parsed['nit'],$identity['identificacion_tipo']??'',$identity['identificacion_numero']??'']);
        $row=$q->fetch(); return $row?$this->decode($row):null;
    }

    public function saveFor(array $data, int $owner, ?int $id): int
    {
        $values = [$data['nombre'], $data['tipo'], $data['documento_tipo'], $data['documento_numero'],
            $data['dv'] === '' ? null : $data['dv'], json_encode(array_values(array_unique($data['fuentes'])), JSON_THROW_ON_ERROR)];
        if ($id === null) {
            $q=$this->database->connection()->prepare("SELECT 1 FROM accesos_tributarios WHERE funcionario_id=? AND perfil='socio' AND activo=1");
            $q->execute([$owner]); if (!$q->fetchColumn()) { throw new \DomainException('Solo un Socio puede registrar contribuyentes.'); }
            $sql = 'INSERT INTO contribuyentes (nombre, tipo, documento_tipo, documento_numero, dv, fuentes, funcionario_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?)';
            $values[] = $owner;
        } else {
            $sql = 'UPDATE contribuyentes SET nombre = ?, tipo = ?, documento_tipo = ?, documento_numero = ?, dv = ?,
                    fuentes = ? WHERE id = ? AND '.\App\Services\AccesoSql::condition('contribuyentes',true);
            array_push($values, $id, $owner);
        }
        $this->database->connection()->prepare($sql)->execute($values);
        return $id ?? (int)$this->database->connection()->lastInsertId();
    }

    private function decode(array $row): array
    {
        $row['fuentes'] = json_decode($row['fuentes'], true, 512, JSON_THROW_ON_ERROR);
        $row['dv'] ??= '';
        return $row;
    }
}
