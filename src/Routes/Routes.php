<?php

declare(strict_types=1);

namespace Legend\Routes;

use Legend\Dashboard\Index;
use Legend\Dashboard\RouteStatisticsViewer;
use Legend\Env;
use Legend\Middleware\HttpBasicAuth;
use Legend\Middleware\Https;
use Legend\Routes\RouterException;
use Legend\Routes\RouteStatistics;
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
    public readonly RouteStatistics $stats;

    /**
     * Constructor
     *
     * @param NanoRouter $router
     */
    public function __construct(NanoRouter $router)
    {
        $this->router = $router;
        $this->env = Env::instance();

        if ($this->env['router.statsEnabled']) {
            $this->stats = new RouteStatistics($this->env['router.statsFile']);
        }
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

            $count = $this->stats->count($ip);

            if ($count > $this->env['router.throttleThreshold']) {
                throw new RouteException('too many requests', 429);
            }

            if ($count >= $this->env['router.scanThreshold'] && $this->stats->scan($ip) >= $this->env['router.scanScore']) {
                throw new RouteException('vulnerability scanner', 429);
            }

            return null;
        });

        return $this;
    }

    /**
     * Handle route exception
     *
     * @param RouteException         $exception
     * @param ServerRequestInterface $request
     *
     * @return ?ResponseInterface
     */
    public static function handleRouteException(RouteException $exception, ServerRequestInterface $request) : ?ResponseInterface
    {
        return (new RouterException($exception, $request))
            ->run();
    }

    /**
     * Handle exception
     *
     * @param Throwable              $exception
     * @param ServerRequestInterface $request
     *
     * @return ?ResponseInterface
     */
    public static function handleException(Throwable $exception, ServerRequestInterface $request) : ?ResponseInterface
    {
        return (new RouterException($exception, $request))
            ->run();
    }
}
