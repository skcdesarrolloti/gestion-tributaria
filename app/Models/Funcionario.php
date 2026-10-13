<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Funcionario
{
    public function __construct(private Database $database) {}

    public function findActiveByLogin(string $login): ?array
    {
        return $this->find('user_others_apss', $login);
    }

    public function findActiveById(int $id): ?array
    {
        return $this->find('_ID', $id);
    }

    private function find(string $column, string|int $value): ?array
    {
        // La columna proviene únicamente de los dos métodos de esta clase.
        $sql = "SELECT _ID, id_empleado, nombre, rol, id_cargo, user_others_apss, pass_others_apss
                FROM wp_jet_cct_funcionarios WHERE $column = ? AND activo = 'Si' LIMIT 1";
        $query = $this->database->connection()->prepare($sql);
        $query->execute([$value]);
        return $query->fetch() ?: null;
    }
}
