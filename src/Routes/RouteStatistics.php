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
                ':uri' => urldecode($uri),
                ':method' => $method,
                ':status' => $code,
                ':time' => $time,
                ':ip' => $ip,
            ]);
        } catch (PDOException) {
            Helper::errorLog(self::class, 'PDOException', false);
        }

        return $this;
    }

    /**
     * Count requests from ip within time interval
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
                ':ip' => $ip,
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
     * @param  string $ip
     *
     * @return float
     *
     * @note minimum score is -3 max score is +7
     */
    public function scan(string $ip) : float
    {
        $score = 0;

        $endsWith = [
            '/.env',
            '/.env.js',
            '/.env.local',
            '/.env.php',
            '/.env.production',
            '/.env.save',
            '/.env.staging',
            '/.git',
            '/.gitlab',
            '/admin.php',
            '/autoload_classmap.php',
            '/backup',
            '/bak.php',
            '/config.bak',
            '/config.old',
            '/config.php',
            '/config.php~',
            '/db.php',
            '/default.php',
            '/defaults.php',
            '/eval-stdin.php',
            '/home.php',
            '/info.php',
            '/install.php',
            '/menu.php',
            '/new',
            '/old',
            '/phpinfo',
            '/phpinfo.php',
            '/plugin.php',
            '/session.php',
            '/shell.php',
            '/test.php',
            '/update.php',
            '/upgrade.php',
            '/user.php',
            '/version.php',
            '/web.php',
            '/wordpress',
            '/wp-activate.php',
            '/wp-config-sample.php',
            '/wp-config.bak',
            '/wp-config.local.php',
            '/wp-config.txt',
            '/wp-login.php',
            //'/1.php',
            //'/bc',
            //'/bk',
            //'/file.php',
            //'/good.php',
            //'/home',
            //'/license.php',
            //'/main',
            //'/network.php',
            //'/options.php',
            //'/readme.php',
            //'/wp',
        ];

        // catch exceptions to avoid route to fail
        try {
            $sql = <<<'SQL'
            SELECT
                COUNT(*) AS `count`,
                `uri`,
                `method`,
                `status`
            FROM
                `stats`
            WHERE
                `ip` = :ip
            ORDER BY
                `date` DESC
            LIMIT 50
            SQL;

            $query = $this->db->prepare($sql);
            $query->execute([
                ':ip' => $ip,
            ]);

            $rows = $query->fetchAll();

            foreach ($rows as $row) {
                if (!isset($count)) {
                    $count = $row['count'];
                }

                if ($row['status'] === 200) {
                    $score -= 3;

                    // none of the suspicious pattern should exist on the server, so we can ignore the loops below
                    continue;
                }

                if ($row['status'] >= 400) {
                    $score += 2;
                }

                /*
                if ($row['method'] !== 'GET') {
                    $score += 1;
                }
                */

                foreach ($endsWith as $needle) {
                    if (str_ends_with($needle, $row['uri'])) {
                        $score += 5;
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
        $sql = <<<SQL
        DELETE FROM `stats`
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
