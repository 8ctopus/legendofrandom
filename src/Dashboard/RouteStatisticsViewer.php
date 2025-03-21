<?php

declare(strict_types=1);

namespace Legend\Dashboard;

use DateTime;
use HttpSoft\Message\Response;
use Legend\Env;
use Legend\IPLocation\Location;
use Legend\Routes\RouteStatistics;
use Legend\Traits\Twig;
use Oct8pus\NanoRouter\RouteException;
use Oct8pus\NanoTimer\NanoTimer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RouteStatisticsViewer extends RouteStatistics
{
    use Twig;

    private readonly ServerRequestInterface $request;
    private readonly string $database;

    /**
     * Constructor
     *
     * @param ServerRequestInterface $request
     * @param string                 $database
     */
    public function __construct(ServerRequestInterface $request, string $database)
    {
        $this->request = $request;
        $this->database = $database;

        parent::__construct($database);
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
                $params = $this->request->getQueryParams();
                $ip = $params['ip'] ?? null;

                return $ip ? $this->filter($ip) : $this->show();

            case 'truncate':
                $this->truncate();
                return new Response(200);

            default:
                throw new RouteException("unknown route - {$path}", 400);
        }
    }

    private function show() : ResponseInterface
    {
        // get browser timezone using cookie
        $cookies = $this->request->getCookieParams();

        $offset = array_key_exists('timezone', $cookies) ? (int) $cookies['timezone'] : 0;
        $offsetHours = round($offset / 60 * -1, 0, PHP_ROUND_HALF_DOWN);
        $offsetMinutes = $offset % 60;

        $timezone = sprintf('+%02d:%02d', $offsetHours, $offsetMinutes);

        $timer = new NanoTimer();

        $timer->logMemoryPeakUse();

        $queries = [
            'errors' => <<<'SQL'
            SELECT
                COUNT(*) AS 'count',
                SUBSTR(`uri`, 1, 150) AS 'path',
                `method`,
                `status`,
                ROUND(AVG(`time`), 0) AS 'AVG time',
                MIN(`time`) AS 'min',
                MAX(`time`) AS 'max'
            FROM
                `stats`
            WHERE
                `status` >= 500 AND
                `date` >= DATETIME('now', '-7 day')
            GROUP BY
                `uri`,
                `method`,
                `status`
            ORDER BY
                `count` DESC
            LIMIT 200
            SQL,

            '4xx' => <<<'SQL'
            SELECT
                COUNT(*) AS 'count',
                SUBSTR(`uri`, 1, 150) AS 'path',
                `method`,
                `status`,
                ROUND(AVG(`time`), 0) AS 'AVG time',
                MIN(`time`) AS 'min',
                MAX(`time`) AS 'max'
            FROM
                `stats`
            WHERE
                `status` BETWEEN 400 AND 499 AND
                `date` >= DATETIME('now', '-7 day')
            GROUP BY
                `uri`,
                `method`,
                `status`
            ORDER BY
                `count` DESC
            LIMIT 20
            SQL,

            'most popular' => <<<'SQL'
            SELECT
                COUNT(*) AS 'count',
                SUBSTR(`uri`, 1, 150) AS 'path',
                `method`,
                `status`,
                ROUND(AVG(`time`), 0) AS 'AVG time',
                MIN(`time`) AS 'min',
                MAX(`time`) AS 'max'
            FROM
                `stats`
            WHERE
                `status` BETWEEN 200 AND 399 AND
                `date` >= DATETIME('now', '-7 day')
            GROUP BY
                `uri`,
                `method`,
                `status`
            ORDER BY
                `count` DESC
            LIMIT 200
            SQL,

            'latest' => <<<SQL
            SELECT
                DATETIME(`date`, '{$timezone}') AS 'date',
                `ip`,
                SUBSTR(`uri`, 1, 150) AS 'path',
                `method`,
                `status`,
                `time`
            FROM
                `stats`
            ORDER BY
                `rowid` DESC
            LIMIT 1000
            SQL,
        ];

        $tables = [];

        foreach ($queries as $title => $sql) {
            $query = $this->db->prepare($sql);
            $query->execute();
            $tables[$title] = $query->fetchAll();
            $timer->measure($title);
        }

        $host = $this->request->getUri()->getHost();
        $uri = (string) $this->request->getUri();

        if (str_starts_with($host, 'ch2')) {
            $serverName = 'us2';
            $serverUri = str_replace('ch2', 'us2', $uri);
        } else {
            $serverName = 'ch2';
            $serverUri = str_replace('us2', 'ch2', $uri);
        }

        $env = Env::instance();

        $tablesSmall = $this->stats(null);
        $timer->measure('stats');

        $stream = $this->renderToStream('Dashboard/RouteStatisticsViewer.twig', [
            'host' => $host,
            'truncate' => $this->request->getUri()->getPath() . 'truncate',
            'serverName' => $serverName,
            'serverUri' => $serverUri,
            'tables' => $tables,
            'tablesSmall' => $tablesSmall,
            'performance' => $timer->table(),
            'timerThreshold' => $env['router.timerThreshold'],
            'whitelist' => json_encode($env['router.whitelist']),
        ]);

        $headers = [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ];

        return new Response(200, $headers, $stream);
    }

    private function filter(string $ip) : ResponseInterface
    {
        // get browser timezone using cookie
        $cookies = $this->request->getCookieParams();

        $offset = array_key_exists('timezone', $cookies) ? (int) $cookies['timezone'] : 0;
        $offsetHours = round($offset / 60 * -1, 0, PHP_ROUND_HALF_DOWN);
        $offsetMinutes = $offset % 60;

        $timezone = sprintf('+%02d:%02d', $offsetHours, $offsetMinutes);

        $timer = new NanoTimer();
        $timer->logMemoryPeakUse();

        $queries = [
            'latest' => <<<SQL
            SELECT
                DATETIME(`date`, '{$timezone}') AS 'date',
                `ip`,
                SUBSTR(`uri`, 1, 150) AS 'path',
                `method`,
                `status`,
                `time`
            FROM
                `stats`
            WHERE
                `ip` = :ip
            ORDER BY
                `rowid` DESC
            LIMIT 1000
            SQL,
        ];

        $tables = [];

        foreach ($queries as $title => $sql) {
            $query = $this->db->prepare($sql);
            $query->execute([
                'ip' => $ip,
            ]);

            $tables[$title] = $query->fetchAll();
            $timer->measure($title);
        }

        $host = $this->request->getUri()->getHost();
        $uri = (string) $this->request->getUri();

        if (str_starts_with($host, 'ch2')) {
            $serverName = 'us2';
            $serverUri = str_replace('ch2', 'us2', $uri);
        } else {
            $serverName = 'ch2';
            $serverUri = str_replace('us2', 'ch2', $uri);
        }

        $env = Env::instance();

        $stream = $this->renderToStream('Dashboard/RouteStatisticsViewer.twig', [
            'host' => $host,
            'truncate' => $this->request->getUri()->getPath() . 'truncate',
            'serverName' => $serverName,
            'serverUri' => $serverUri,
            'tables' => $tables,
            'tablesSmall' => $this->stats($ip),
            'performance' => $timer->table(),
            'timerThreshold' => $env['router.timerThreshold'],
            'whitelist' => json_encode($env['router.whitelist']),
            'ip' => $ip,
        ]);

        $headers = [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ];

        return new Response(200, $headers, $stream);
    }

    private function stats(?string $ip = null) : array
    {
        $tables = [];

        if (!$ip) {
            $size = filesize($this->database);
            $size /= 1024 * 1024;
            $size = sprintf('%0.1f Mb', $size);

            // uptime request date and total requests count
            // cast(JulianDay(DATETIME('now', '{$timezone}')) - JulianDay(MIN(DATETIME(date, '{$timezone}'))) * 24 AS int) AS 'diff'
            $sql = <<<SQL
            SELECT
                MIN(DATETIME(`date`)) AS 'uptime',
                COUNT(*) AS 'total',
                SUM(CASE WHEN `date` >= DATETIME('now', '-1 day') THEN 1 ELSE 0 END) AS 'last day',
                SUM(CASE WHEN `date` >= DATETIME('now', '-1 day') THEN 1 ELSE 0 END) / 24 AS 'hourly average',
                SUM(CASE WHEN DATETIME(`date`) >= DATETIME('now', '-1 hour') THEN 1 ELSE 0 END) AS 'last hour',
                '{$size}' AS 'database size'
            FROM
                `stats`
            SQL;

            $query = $this->db->prepare($sql);
            $query->execute();
            $table = $query->fetchAll();

            // transpose?
            $transposed = [];

            foreach ($table[0] as $key => $value) {
                if ($key === 'uptime') {
                    $uptime = DateTime::createFromFormat('Y-m-d H:i:s', $value);

                    $value = (new DateTime('now'))
                        ->diff($uptime)
                        ->format('%a days, %H:%I:%S ago');
                }

                $transposed[] = [
                    'type' => $key,
                    'value' => $value,
                ];
            }

            $tables['stats'] = $transposed;
        }

        $sql = <<<'SQL'
        SELECT
            COUNT(*) AS 'count',
            `ip`
        FROM
            `stats`
        WHERE
            `date` >= DATETIME('now', '-1 day')
        GROUP BY
            `ip`
        ORDER BY
            `count` DESC
        LIMIT 10
        SQL;

        $query = $this->db->prepare($sql);
        $query->execute();
        $table = $query->fetchAll();

        if ($ip) {
            $table[0]['country'] = (new Location())
                ->country($ip);
        }

        $tables['ips'] = $table;

        return $tables;
    }
}
