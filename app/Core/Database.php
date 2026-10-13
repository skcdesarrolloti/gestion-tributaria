<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private ?PDO $connection = null;

    public function __construct(private array $config, private bool $allowCreate = true) {}

    public function connection(): PDO
    {
        if ($this->connection !== null) { return $this->connection; }
        $c = $this->config;
        if (!preg_match('/^[a-zA-Z0-9_]+$/D', $c['name'])) {
            throw new \RuntimeException('Nombre de base de datos inválido.');
        }
        $server = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $c['host'], $c['port']);
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
        ];
        try {
            $this->connection = new PDO($server . ';dbname=' . $c['name'], $c['username'], $c['password'], $options);
        } catch (PDOException $error) {
            if (!$this->allowCreate || (int) ($error->errorInfo[1] ?? 0) !== 1049) { throw $error; }
            // Si el hosting permite CREATE DATABASE, también se crea la base.
            $db = new PDO($server, $c['username'], $c['password'], $options);
            $sql = file_get_contents(dirname(__DIR__, 2) . '/database/000_create_database.sql');
            $db->exec(str_replace('{{database}}', $c['name'], $sql));
            $db->exec('USE ' . $c['name']);
            $this->connection = $db;
        }
        return $this->connection;
    }
}
