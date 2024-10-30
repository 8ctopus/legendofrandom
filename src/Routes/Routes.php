<?php

declare(strict_types=1);

namespace Legend\Routes;

use HttpSoft\Message\Response;
use HttpSoft\Message\Stream;
use Legend\Dashboard\Index;
use Legend\Dashboard\RouteStatisticsViewer;
use Legend\Env;
use Legend\Helper;
use Legend\RouteStatistics;
use Legend\Middleware\HttpBasicAuth;
use Legend\Middleware\Https;
use Oct8pus\NanoIP\Range;
use Oct8pus\NanoRouter\MiddlewareType;
use Oct8pus\NanoRouter\NanoRouter;
use Oct8pus\NanoRouter\Route;
use Oct8pus\NanoRouter\RouteException;
use Oct8pus\NanoRouter\RouteType;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class Routes
{
    protected readonly NanoRouter $router;
    protected readonly Env $env;

    /**
     * Constructor
     *
     * @param NanoRouter $router
     */
    public function __construct(NanoRouter $router)
    {
        $this->router = $router;
        $this->env = Env::instance();
    }

    /**
     * Add routes
     *
     * @return self
     */
    public function addRoutes() : self
    {
        $this->router->addRoute(new Route(RouteType::Exact, 'GET', '/dashboard/', static function () : ResponseInterface {
            return (new Index())
                ->run();
        }));

        $this->router->addMiddleware(['GET', 'POST'], '~^/dashboard/~', MiddlewareType::Pre, function (ServerRequestInterface $request) : ?ResponseInterface {
            $response = (new Https($request))
                ->run();

            if ($response) {
                return $response;
            }

            return (new HttpBasicAuth($request, $this->env['authentication.password'], $this->env['authentication.group'], $this->env['authentication.require']))
                ->run();
        });

        $this->router->addRoute(new Route(RouteType::StartsWith, ['GET', 'POST'], '/dashboard/route-stats/', function (ServerRequestInterface $request) : ResponseInterface {
            return (new RouteStatisticsViewer($request, $this->env['router.statsFile']))
                ->run();
        }));

        // throttle, whitelist and ban middleware
        $this->router->addMiddleware('*', '~.*~', MiddlewareType::Pre, function (ServerRequestInterface $request) : ?ResponseInterface {
            $ip = $request->getServerParams()['REMOTE_ADDR'];

            // FIX ME - docker ip v6
            if ($ip === '::1') {
                return null;
            }

            $range = new Range($this->env['router.banned']);

            if ($range->contains($ip)) {
                throw new RouteException('banned', 403);
            }

            // ignore whitelisted ips
            $range = new Range($this->env['router.whitelist']);

            if ($range->contains($ip)) {
                return null;
            }

            if (!$this->env['router.statsEnabled']) {
                return null;
            }

            $count = (new RouteStatistics($this->env['router.statsFile']))
                ->count($ip);

            if ($count > $this->env['router.throttleThreshold']) {
                throw new RouteException('too many requests', 429);
            }

            return null;
        });

        return $this;
    }

    /**
     * Handle route exception
     *
     * @param RouteException $exception
     *
     * @return void
     */
    public static function routeExceptionHandler(RouteException $exception) : void
    {
        if ($exception->getCode() === 429 && rand(0, 100) !== 0) {
            // do not log all hammering in order to keep clean apache logs
            return;
        }

        $trace = $exception->getTrace();

        if (count($trace)) {
            $where = array_key_exists('class', $trace[0]) ? $trace[0]['class'] : $trace[0]['function'];
        }

        Helper::errorLog($where ?? '', "[{$exception->getCode()}] {$exception->getMessage()}", false);
    }

    /**
     * Handle exception
     *
     * @param Throwable $exception
     *
     * @return ?ResponseInterface
     */
    public static function exceptionHandler(Throwable $exception) : ?ResponseInterface
    {
        $trace = $exception->getTrace();

        if (count($trace)) {
            $where = array_key_exists('class', $trace[0]) ? $trace[0]['class'] : $trace[0]['function'];
        }

        $code = $exception->getCode();

        // PDOExceptions code can be string
        if (is_string($code)) {
            $code = (int) $code;
        }

        $message = "[{$code}] {$exception->getMessage()}";

        Helper::errorLog($where ?? '', $message, false);

        if ($code < 200 || $code > 500) {
            $code = 500;
        }

        if (Helper::production()) {
            return new Response($code);
        }

        $stream = new Stream();
        $stream->write($message);

        return new Response($code, ['content-type' => 'text/plain'], $stream);
    }
}
