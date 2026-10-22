<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class EscenarioRenta
{
    public function __construct(private Database $db) {}

    public function profiles(int $owner): array
    {
        return $this->query('SELECT p.*, c.nombre FROM perfiles_renta p JOIN contribuyentes c ON c.id=p.contribuyente_id
            WHERE '.\App\Services\AccesoSql::condition('c',false).' AND c.tipo=\'natural\' AND p.tipo=\'natural\' AND p.residencia=\'residente\' AND p.regimen=\'ordinario\'
            ORDER BY c.nombre,p.anio DESC',[$owner])->fetchAll();
    }

    public function listFor(int $profile, int $owner): array
    {
        return $this->query('SELECT s.id,s.nombre,s.version,s.created_at FROM escenarios_renta s
            JOIN perfiles_renta p ON p.id=s.perfil_id JOIN contribuyentes c ON c.id=p.contribuyente_id
            WHERE p.id=? AND '.\App\Services\AccesoSql::condition('c',false).' ORDER BY s.id DESC',[$profile,$owner])->fetchAll();
    }

    public function findFor(int $id, int $owner): ?array
    {
        $row=$this->query('SELECT s.* FROM escenarios_renta s JOIN perfiles_renta p ON p.id=s.perfil_id
            JOIN contribuyentes c ON c.id=p.contribuyente_id WHERE s.id=? AND '.\App\Services\AccesoSql::condition('c',false),[$id,$owner])->fetch();
        if (!$row) { return null; }
        foreach (['entradas','resultado'] as $field) { $row[$field]=json_decode($row[$field],true,512,JSON_THROW_ON_ERROR); }
        return $row;
    }

    public function save(array $input, array $result, array $profile, int $owner): int
    {
        $query=$this->query('INSERT INTO escenarios_renta (perfil_id,nombre,version,entradas,resultado)
            SELECT p.id,?,?,?,? FROM perfiles_renta p JOIN contribuyentes c ON c.id=p.contribuyente_id
            WHERE p.id=? AND '.\App\Services\AccesoSql::condition('c',true).' AND p.tipo=\'natural\' AND p.residencia=\'residente\' AND p.regimen=\'ordinario\'',
            [$input['nombre']?:'Escenario sin nombre',$result['version'],json_encode($input,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE),
                json_encode($result,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE),$profile['id'],$owner]);
        if ($query->rowCount()!==1) { throw new \DomainException('Perfil no disponible.'); }
        return (int)$this->db->connection()->lastInsertId();
    }

    private function query(string $sql, array $values): \PDOStatement
    {
        $query=$this->db->connection()->prepare($sql); $query->execute($values); return $query;
    }
}
