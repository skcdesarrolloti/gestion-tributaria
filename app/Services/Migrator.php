<?php
declare(strict_types=1);

namespace App\Services;

use PDO;

final class Migrator
{
    public function __construct(private PDO $db, private string $directory) {}

    public function run(): void
    {
        $lock = 'mvc-schema-' . hash('sha1', (string) $this->db->query('SELECT DATABASE()')->fetchColumn());
        $query = $this->db->prepare('SELECT GET_LOCK(?, 15)');
        $query->execute([$lock]);
        if ((int) $query->fetchColumn() !== 1) {
            throw new \RuntimeException('Otra actualización está en curso. Reintenta en unos segundos.');
        }
        try {
            $this->db->exec(file_get_contents($this->directory . '/schema_migrations.sql'));
            $applied = $this->db->query('SELECT version, checksum FROM schema_migrations')->fetchAll(PDO::FETCH_KEY_PAIR);
            $files = glob($this->directory . '/migrations/[0-9]*.sql');
            sort($files, SORT_STRING);
            foreach ($files as $file) {
                $version = basename($file, '.sql');
                $sql = file_get_contents($file);
                $checksum = hash('sha256', $sql);
                if (isset($applied[$version])) {
                    if (!hash_equals($applied[$version], $checksum)) {
                        throw new \RuntimeException('Se modificó una migración ya aplicada: ' . $version);
                    }
                    continue;
                }
                // Cada archivo contiene una sentencia SQL. El DDL confirma implícitamente.
                // Si hubo un corte después de aplicar DDL, se verifica su postcondición.
                $checkFile = $this->directory . '/checks/' . $version . '.sql';
                if (!is_file($checkFile)) {
                    throw new \RuntimeException('Falta la comprobación de migración: ' . $version);
                }
                $check = file_get_contents($checkFile);
                if (!(bool) $this->db->query($check)->fetchColumn()) {
                    $this->db->exec($sql);
                }
                if (!(bool) $this->db->query($check)->fetchColumn()) {
                    throw new \RuntimeException('No se confirmó la migración: ' . $version);
                }
                $insert = $this->db->prepare('INSERT INTO schema_migrations (version, checksum) VALUES (?, ?)');
                $insert->execute([$version, $checksum]);
            }
        } finally {
            $query = $this->db->prepare('SELECT RELEASE_LOCK(?)');
            $query->execute([$lock]);
        }
    }
}
