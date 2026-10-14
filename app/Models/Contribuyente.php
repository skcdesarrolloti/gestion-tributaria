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
            'SELECT * FROM contribuyentes WHERE funcionario_id = ? ORDER BY nombre, id'
        );
        $query->execute([$owner]);
        return array_map([$this, 'decode'], $query->fetchAll());
    }

    public function findFor(int $id, int $owner): ?array
    {
        $query = $this->database->connection()->prepare('SELECT * FROM contribuyentes WHERE id = ? AND funcionario_id = ?');
        $query->execute([$id, $owner]);
        $row = $query->fetch();
        return $row ? $this->decode($row) : null;
    }

    public function saveFor(array $data, int $owner, ?int $id): void
    {
        $values = [$data['nombre'], $data['tipo'], $data['documento_tipo'], $data['documento_numero'],
            $data['dv'] === '' ? null : $data['dv'], json_encode(array_values(array_unique($data['fuentes'])), JSON_THROW_ON_ERROR)];
        if ($id === null) {
            $sql = 'INSERT INTO contribuyentes (nombre, tipo, documento_tipo, documento_numero, dv, fuentes, funcionario_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?)';
            $values[] = $owner;
        } else {
            $sql = 'UPDATE contribuyentes SET nombre = ?, tipo = ?, documento_tipo = ?, documento_numero = ?, dv = ?,
                    fuentes = ? WHERE id = ? AND funcionario_id = ?';
            array_push($values, $id, $owner);
        }
        $this->database->connection()->prepare($sql)->execute($values);
    }

    private function decode(array $row): array
    {
        $row['fuentes'] = json_decode($row['fuentes'], true, 512, JSON_THROW_ON_ERROR);
        $row['dv'] ??= '';
        return $row;
    }
}
