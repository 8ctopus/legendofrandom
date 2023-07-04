<?php

declare(strict_types=1);

namespace Legend;

use HttpSoft\Message\Response;
use HttpSoft\Message\Stream;
use Oct8pus\NanoRouter\RouteException;
use Oct8pus\NanoTimer\NanoTimer;
use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class RouteStatistics
{
    private readonly ServerRequestInterface $request;
    private readonly PDO $db;
    private readonly string $twigViews;
    private readonly string $twigCache;

    /**
     * Constructor
     *
     * @param ServerRequestInterface $request
     * @param string                 $databaseFile
     * @param string                 $twigViewsDir
     * @param string                 $twigCacheDir
     */
    public function __construct(ServerRequestInterface $request, string $databaseFile, string $twigViewsDir, string $twigCacheDir)
    {
        $this->request = $request;

        if (!file_exists($databaseFile)) {
            $create = true;
            touch($databaseFile);
        }

        $this->db = new PDO("sqlite:{$databaseFile}", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // better prevention against sql injections
            // https://stackoverflow.com/questions/134099/are-pdo-prepared-statements-sufficient-to-prevent-sql-injection/
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        if (isset($create)) {
            $sql = <<<'SQL'
            CREATE TABLE IF NOT EXISTS stats (
                date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                uri VARCHAR(40) NOT NULL,
                method VARCHAR(40) NOT NULL,
                status INTEGER NOT NULL,
                time INTEGER NOT NULL,
                ip VARCHAR(40) NOT NULL
            )
            SQL;

            $this->db->exec($sql);
        }

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

        $this->twigViews = $twigViewsDir . '/RouteStatistics';
        $this->twigCache = $twigCacheDir;
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
            INSERT INTO stats
            (date, uri, method, status, time, ip)
            VALUES (datetime('now'), :uri, :method, :status, :time, :ip)
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
     * Count requests from ip within last hour
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
            SELECT COUNT(*)
            FROM stats
            WHERE ip = :ip AND date >= datetime('now', '-1 hour')
            SQL;

            $query = $this->db->prepare($sql);
            $query->execute([
                ':ip' => $ip,
            ]);

            return $query->fetchColumn();
        } catch (PDOException) {
            Helper::errorLog(self::class, 'PDOException', false);
            return 0;
        }
    }

    /**
     * Run
     *
     * @return ResponseInterface
     *
     * @throws RouteException
     */
    public function run() : ResponseInterface
    {
        $path = basename($this->request->getUri()->getPath());

        switch ($path) {
            case 'route-stats':
                return $this->show();

            case 'truncate':
                $sql = <<<SQL
                DELETE FROM stats
                SQL;

                $query = $this->db->prepare($sql);
                $query->execute();

                return new Response(200);

            default:
                throw new RouteException("unknown route - {$path}", 400);
        }
    }

    private function show() : ResponseInterface
    {
        $timer = new NanoTimer();

        $timer->logMemoryPeakUse();

        // most popular
        $sql = <<<'SQL'
        SELECT COUNT(*) AS count, SUBSTR(uri, 1, 150) AS path, method, status, ROUND(AVG(time), 0) AS 'AVG time (ms)', MIN(time) AS 'min', MAX(time) AS 'max'
        FROM stats
        WHERE date >= datetime('now', '-7 day')
        GROUP BY uri, method, status
        ORDER BY count DESC
        LIMIT 200
        SQL;

        $query = $this->db->prepare($sql);
        $query->execute();
        $tableMostPopular = $query->fetchAll();

        $timer->measure('sql most popular');

        $sql = <<<'SQL'
        SELECT COUNT(*) AS count, SUBSTR(uri, 1, 150) AS path, method, status, ROUND(AVG(time), 0) AS 'AVG time (ms)', MIN(time) AS 'min', MAX(time) AS 'max'
        FROM stats
        WHERE
            status >= 400 AND
            date >= datetime('now', '-7 day')
        GROUP BY uri, method, status
        ORDER BY count DESC
        LIMIT 200
        SQL;

        $query = $this->db->prepare($sql);
        $query->execute();
        $tableMostPopularErrors = $query->fetchAll();

        $timer->measure('sql most popular only error');

        // get browser timezone using cookie
        $cookies = $this->request->getCookieParams();

        $offset = array_key_exists('timezone', $cookies) ? (int) $cookies['timezone'] : 0;
        $offsetHours = round($offset / 60 * -1, 0, PHP_ROUND_HALF_DOWN);
        $offsetMinutes = $offset % 60;

        $timezone = sprintf('+%02d:%02d', $offsetHours, $offsetMinutes);

        // last 1000 requests
        $sql = <<<SQL
        SELECT datetime(date, '{$timezone}') as date, ip, SUBSTR(uri, 1, 150) AS path, method, status, time AS 'time (ms)'
        FROM stats
        ORDER BY date DESC
        LIMIT 1000
        SQL;

        $query = $this->db->prepare($sql);
        $query->execute();
        $tableLastRequests = $query->fetchAll();

        $timer->measure('sql last requests');

        $sql = <<<'SQL'
        SELECT COUNT(*) AS count, ip
        FROM stats
        WHERE date >= datetime('now', '-1 day')
        GROUP BY ip
        ORDER BY count DESC
        LIMIT 10
        SQL;

        $query = $this->db->prepare($sql);
        $query->execute();
        $tableMostPopularIps = $query->fetchAll();

        $timer->measure('sql most popular ips');

        // requests in the last 24 hours
        $sql = <<<'SQL'
        SELECT COUNT(*) as 'count24', COUNT(*) / 24 as 'per hour'
        FROM stats
        WHERE date >= datetime('now', '-1 day')
        SQL;

        $query = $this->db->prepare($sql);
        $query->execute();
        $count24 = $query->fetch();

        $timer->measure('sql request in 24 hours');

        // requests in the last hour
        $sql = <<<'SQL'
        SELECT COUNT(*) as 'count'
        FROM stats
        WHERE date >= datetime('now', '-1 hour')
        SQL;

        $query = $this->db->prepare($sql);
        $query->execute();
        $countHour = $query->fetch();

        $timer->measure('sql request in last hour');

        $tableCount24 = [[
                '24 hours' => $count24['count24'],
                'last hour' => $countHour['count'],
                'per hour' => $count24['per hour'],
            ],
        ];

        // first request date and total requests count
        // cast(JulianDay(datetime('now', '{$timezone}')) - JulianDay(MIN(datetime(date, '{$timezone}'))) * 24 AS int) AS 'diff'
        $sql = <<<SQL
        SELECT MIN(datetime(date, '{$timezone}')) AS 'first', COUNT(*) as 'total'
        FROM stats
        SQL;

        $query = $this->db->prepare($sql);
        $query->execute();
        $tableCountAll = $query->fetchAll();

        $timer->measure('sql count all');

        $host = $this->request->getUri()->getHost();
        $uri = (string) $this->request->getUri();

        if (str_starts_with($this->request->getUri()->getHost(), 'ch2-')) {
            $serverName = 'us2';
            $serverUri = str_replace('ch2-', 'us2-', $uri);
        } else {
            $serverName = 'ch2';
            $serverUri = str_replace('us2-', 'ch2-', $uri);
        }

        $truncate = $this->request->getUri()->getPath() . 'truncate';

        $loader = new FilesystemLoader($this->twigViews);

        $twig = new Environment($loader, [
            'cache' => $this->twigCache,
            'auto_reload' => true,
        ]);

        $output = $twig->render('index.twig', [
            'host' => $host,
            'truncate' => $truncate,
            'serverName' => $serverName,
            'serverUri' => $serverUri,
            'tableMostPopular' => $tableMostPopular,
            'tableMostPopularErrors' => $tableMostPopularErrors,
            'tableLastRequests' => $tableLastRequests,
            'tableMostPopularIps' => $tableMostPopularIps,
            'tableCount24' => $tableCount24,
            'tableCountAll' => $tableCountAll,
        ]);

        $timer->measure('twig');

        $stream = new Stream();
        $stream->write($output);

        $stream->write('<pre>' . $timer->measure('stream')->table() . '</pre></body></html>');

        return new Response(200, ['content-type' => 'text/html'], $stream);
    }
}
