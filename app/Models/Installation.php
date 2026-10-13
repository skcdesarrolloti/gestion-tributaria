<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Installation
{
    public function __construct(private Database $database) {}

    public function needed(): bool
    {
        return (int) $this->database->connection()->query('SELECT COUNT(*) FROM wp_jet_cct_funcionarios')->fetchColumn() === 0;
    }

    public function createAdministrator(string $name, string $username, string $password): void
    {
        $db = $this->database->connection();
        $lock = 'mvc-install-' . hash('sha1', (string) $db->query('SELECT DATABASE()')->fetchColumn());
        $query = $db->prepare('SELECT GET_LOCK(?, 10)');
        $query->execute([$lock]);
        if ((int) $query->fetchColumn() !== 1) {
            throw new \RuntimeException('Otra instalación está en curso.');
        }
        try {
            if (!$this->needed()) {
                throw new \RuntimeException('El administrador inicial ya existe.');
            }
            $query = $db->prepare("INSERT INTO wp_jet_cct_funcionarios
                (nombre, user_others_apss, pass_others_apss, rol, activo) VALUES (?, ?, ?, 'administrador', 'Si')");
            $query->execute([$name, $username, password_hash($password, PASSWORD_DEFAULT)]);
        } finally {
            $query = $db->prepare('SELECT RELEASE_LOCK(?)');
            $query->execute([$lock]);
        }
    }
}
