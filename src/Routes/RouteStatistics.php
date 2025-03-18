<?php

declare(strict_types=1);

namespace Legend\Routes;

use Legend\Helper;
use Legend\Traits\DatabaseSqlite;
use PDOException;

class RouteStatistics
{
    use DatabaseSqlite;

    /**
     * Constructor
     *
     * @param string $database
     */
    public function __construct(string $database)
    {
        $this->initialize($database);

        // https://www.sqlite.org/pragma.html#pragma_journal_mode
        $this->db->exec('PRAGMA journal_mode=OFF');

        // another potential optimization
        $this->db->exec('PRAGMA synchronous=OFF');

        /*
        // check journal mode
        $query = $this->db->prepare('PRAGMA journal_mode');
        $query->execute();
        $result = $query->fetchColumn();
        */
    }

    /**
     * Add route to statistics
     *
     * @param string $uri
     * @param string $method
     * @param int    $code
     * @param int    $time
     * @param string $ip
     *
     * @return self
     */
    public function add(string $uri, string $method, int $code, int $time, string $ip) : self
    {
        // catch exceptions to avoid route to fail
        try {
            $sql = <<<'SQL'
            INSERT INTO `stats`
                (`date`, `uri`, `method`, `status`, `time`, `ip`)
            VALUES
                (datetime('now'), :uri, :method, :status, :time, :ip)
            SQL;

            $query = $this->db->prepare($sql);
            $query->execute([
                'uri' => urldecode($uri),
                'method' => $method,
                'status' => $code,
                'time' => $time,
                'ip' => $ip,
            ]);
        } catch (PDOException) {
            Helper::errorLog(self::class, 'PDOException', false);
        }

        return $this;
    }

    /**
     * Count requests from ip within 1 hour time interval
     *
     * @param string $ip
     *
     * @return int
     */
    public function count(string $ip) : int
    {
        // catch exceptions to avoid route to fail
        try {
            $sql = <<<'SQL'
            SELECT
                COUNT(*)
            FROM
                `stats`
            WHERE
                `ip` = :ip AND
                `date` >= datetime('now', '-1 hour')
            SQL;

            $query = $this->db->prepare($sql);
            $query->execute([
                'ip' => $ip,
            ]);

            return $query->fetchColumn(0);
        } catch (PDOException) {
            Helper::errorLog(self::class, 'PDOException', false);
            return 0;
        }
    }

    /**
     * Check for OAT-014 vulnerability scanning
     *
     * @param string $ip
     *
     * @return float
     *
     * @note minimum score is -3 max score is +10
     * all 404 results gives +2 scores
     */
    public function scan(string $ip) : float
    {
        $score = 0;

        $extensions = [
            '.backup',
            '.bak',
            '.env',
            '.ini',
            '.js',
            '.json',
            '.local',
            '.log',
            '.old',
            '.php',
            '.py',
            '.txt',
            '.yaml',
            '.yml',
        ];

        $endsWith = [
            '/.aws/credentials',
            '/.env.example',
            '/.env.production',
            '/.env.save',
            '/.env.stage',
            '/.env.staging',
            '/.git',
            '/.git/config',
            '/.gitlab',
            '/admin',
            '/backup',
            '/backup/',
            '/config',
            '/env',
            '/forgot_password',
            '/getConfig',
            '/index.html',
            '/ismustmobile',
            '/login',
            '/login.action',
            '/login.html',
            '/new',
            '/new/',
            '/old',
            '/old/',
            '/owncloud',
            '/phpinfo',
            '/platform',
            '/register',
            '/server-info',
            '/server-status',
            '/signup',
            '/temp/',
            '/test/',
            '/wordpress',
            '/wp-json',
        ];

        $endsWith = array_merge($extensions, $endsWith);

        // catch exceptions to avoid route to fail
        try {
            $sql = <<<'SQL'
            SELECT
                `uri`,
                `method`,
                `status`
            FROM
                `stats`
            WHERE
                `ip` = :ip
            GROUP BY
                `uri`,
                `method`,
                `status`
            ORDER BY
                `rowid` DESC
            LIMIT 50
            SQL;

            $query = $this->db->prepare($sql);
            $query->execute([
                'ip' => $ip,
            ]);

            $rows = $query->fetchAll();

            $count = count($rows);

            foreach ($rows as $row) {
                if ($row['status'] < 400) {
                    $score -= 3;

                    // none of the suspicious patterns should exist on the server, so we can continue
                    continue;
                }

                if ($row['status'] >= 400) {
                    $score += 2;
                }

                foreach ($endsWith as $needle) {
                    if (str_ends_with($row['uri'], $needle)) {
                        $score += 8;
                        break;
                    }
                }
            }

            $score /= $count;
        } catch (PDOException) {
            Helper::errorLog(self::class, 'PDOException', false);
        }

        return $score;
    }

    public function truncate() : void
    {
        $sql = <<<'SQL'
        DELETE FROM `stats`;
        VACUUM;
        SQL;

        $this->db->exec($sql);
    }

    /**
     * Migrations
     *
     * @return array
     */
    private function migrations() : array
    {
        return [
            <<<'SQL'
            CREATE TABLE `stats` (
                `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `uri` VARCHAR(40) NOT NULL,
                `method` VARCHAR(40) NOT NULL,
                `status` INTEGER NOT NULL,
                `time` INTEGER NOT NULL,
                `ip` VARCHAR(40) NOT NULL
            )
            SQL,
        ];
    }
}
