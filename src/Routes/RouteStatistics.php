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

    public function scan(string $ip) : int
    {
        $score = 0;

        $suspicious = [
            '/.env',
            '/.env.js',
            '/.env.php',
            '/.env.save',
            '/1.php',
            '/admin.php',
            '/autoload_classmap.php',
            '/backup',
            '/bak.php',
            '/bc',
            '/bk',
            '/config.php',
            '/db.php',
            '/default.php',
            '/defaults.php',
            '/file.php',
            '/good.php',
            '/home',
            '/home.php',
            '/info.php',
            '/install.php',
            '/license.php',
            '/main',
            '/menu.php',
            '/network.php',
            '/new',
            '/old',
            '/options.php',
            '/phpinfo',
            '/phpinfo.php',
            '/plugin.php',
            '/readme.php',
            '/session.php',
            '/shell.php',
            '/test.php',
            '/update.php',
            '/upgrade.php',
            '/user.php',
            '/version.php',
            '/web.php',
            '/wordpress',
            '/wp',
            '/wp-activate.php',
            '/wp-config-sample.php',
            '/wp-login.php',
        ];

        $startsWith = [
            '/wp-admin/',
            '/wp-includes/',
        ];

        $endsWith = [
            '/eval-stdin.php',
        ];

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
                if (in_array($row['uri'], $suspicious, true)) {
                    $score += 5;
                } else {
                    foreach ($startsWith as $needle) {
                        if (str_starts_with($needle, $row['uri'])) {
                            $found = true;
                            $score += 5;
                            break;
                        }
                    }

                    if (isset($found)) {
                        break;
                    }

                    foreach ($endsWith as $needle) {
                        if (str_ends_with($needle, $row['uri'])) {
                            $score += 5;
                            break;
                        }
                    }
                }

                if ($row['status'] === 200) {
                    $score -= 3;
                } elseif ($row['status'] === 404 || $row['status'] === 429) {
                    $score += 2;
                }

                /*
                if ($row['method'] !== 'GET') {
                    ++$score;
                }
                */
            }
        } catch (PDOException) {
            Helper::errorLog(self::class, 'PDOException', false);
        }

        return $score;
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
