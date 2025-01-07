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

        $endsWith = [
            '/.aws/credentials',
            '/.env',
            '/.env.backup',
            '/.env.bak',
            '/.env.example',
            '/.env.js',
            '/.env.local',
            '/.env.old',
            '/.env.php',
            '/.env.production',
            '/.env.save',
            '/.env.stage',
            '/.env.staging',
            '/.env_example',
            '/.env_sample',
            '/.git',
            '/.git/config',
            '/.gitlab',
            '/about.php',
            '/access.php',
            '/admin',
            '/admin.php',
            '/application.properties',
            '/autoload_classmap.php',
            '/aws-secret.yaml',
            '/aws.yml',
            '/backup',
            '/backup.php',
            '/backup/',
            '/bak.php',
            '/bypass.php',
            '/cmd.php',
            '/conf.js',
            '/conf.json',
            '/config',
            '/config.bak',
            '/config.env',
            '/config.js',
            '/config.json',
            '/config.old',
            '/config.php',
            '/config.php~',
            '/constants.js',
            '/database.js',
            '/db.php',
            '/default.php',
            '/defaults.php',
            '/email_service.py',
            '/eval-stdin.php',
            '/evil.php',
            '/file.php',
            '/filemanager.php',
            '/forgot_password',
            '/function.php',
            '/getConfig',
            '/hehe.php',
            '/home.php',
            '/include.php',
            '/includes.php',
            '/index.html',
            '/index.php',
            '/info.php',
            '/init.php',
            '/install.php',
            '/ismustmobile',
            '/laravel.log',
            '/license.txt',
            '/load.php',
            '/login',
            '/login.html',
            '/main.js',
            '/manager.php',
            '/menu.php',
            '/minishell.php',
            '/network.php',
            '/new',
            '/new/',
            '/nodemailer.js',
            '/old',
            '/old/',
            '/owncloud',
            '/phpinfo',
            '/phpinfo.php',
            '/platform',
            '/plugin.php',
            '/r00t.php',
            '/register',
            '/root.php',
            '/s3cmd.ini',
            '/search.php',
            '/security.php',
            '/server-info',
            '/server-info.php',
            '/server-status',
            '/server.js',
            '/session.php',
            '/settings.py',
            '/shell.php',
            '/signup',
            '/status.php',
            '/sys.php',
            '/temp/',
            '/test.php',
            '/test/',
            '/tinyfilemanager.php',
            '/update.php',
            '/upfile.php',
            '/upgrade.php',
            '/upload.php',
            '/uploader.php',
            '/user.php',
            '/var.php',
            '/version.php',
            '/vuln.php',
            '/web.php',
            '/webadmin.php',
            '/wordpress',
            '/wp-activate.php',
            '/wp-config-sample.php',
            '/wp-config.bak',
            '/wp-config.local.php',
            '/wp-config.php.bak',
            '/wp-config.txt',
            '/wp-info.php',
            '/wp-json',
            '/wp-login.php',
            '/xleet-shell.php',
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
                if ($row['status'] === 200) {
                    $score -= 3;

                    // none of the suspicious patterns should exist on the server, so we can continue
                    continue;
                }

                if ($row['status'] >= 400) {
                    $score += 2;
                }

                foreach ($endsWith as $needle) {
                    if (str_ends_with($needle, $row['uri'])) {
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
