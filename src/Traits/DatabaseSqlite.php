<?php

declare(strict_types=1);

namespace Legend\Traits;

use Exception;
use Legend\Helper;
use PDO;

trait DatabaseSqlite
{
    protected readonly PDO $db;

    /**
     * Initialize
     *
     * @param string $database
     *
     * @return void
     */
    private function initialize(string $database) : void
    {
        if (file_exists($database)) {
            // uncomment when you make breaking changes
            //unlink($database);

            $this->connect($database);

            if (Helper::phpunit()) {
                $this->migrate();
            }

            return;
        }

        if (!touch($database)) {
            throw new Exception('create database');
        }

        $this
            ->connect($database)
            ->createMigrationTable()
            ->migrate();
    }

    /**
     * Connect to database
     *
     * @param string $database
     *
     * @return self
     */
    private function connect(string $database) : self
    {
        $this->db = new PDO("sqlite:{$database}", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // better prevention against sql injections
            // https://stackoverflow.com/questions/134099/are-pdo-prepared-statements-sufficient-to-prevent-sql-injection/
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $this;
    }

    /**
     * Create migration table
     *
     * @return self
     */
    private function createMigrationTable() : self
    {
        // only create migrations table the first time
        $sql = <<<'SQL'
        CREATE TABLE IF NOT EXISTS `migrations` (
            `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `id` VARCHAR(32) NOT NULL UNIQUE
        )
        SQL;

        $this->db->exec($sql);

        return $this;
    }

    /**
     * Migrate
     *
     * @return self
     */
    private function migrate() : self
    {
        $sql = <<<'SQL'
        SELECT
            `id`
        FROM
            `migrations`
        SQL;

        $query = $this->db->query($sql);

        $migrated = $query->fetchAll(PDO::FETCH_COLUMN, 0);

        $migrations = $this->migrations();

        foreach ($migrations as $id => $migration) {
            // skip migrated methods
            if (in_array((string) $id, $migrated, true)) {
                continue;
            }

            $this->db->exec($migration);

            $sql = <<<'SQL'
            INSERT INTO `migrations`
                (`date`, `id`)
            VALUES
                (datetime('now'), :id)
            SQL;

            $query = $this->db->prepare($sql);
            $query->execute([
                'id' => $id,
            ]);
        }

        return $this;
    }
}
