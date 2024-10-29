<?php

declare(strict_types=1);

namespace Legend\Routes;

use HttpSoft\Message\Response;
use Legend\Helper;
use Legend\RouteStatistics;
use Legend\Middleware\HttpAuthenticate;
use Legend\Middleware\Https;
use Noodlehaus\Config;
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
    protected NanoRouter $router;
    protected Config $config;

    /**
     * Constructor
     *
     * @param NanoRouter $router
     * @param Config     $config
     */
    public function __construct(NanoRouter $router, Config $config)
    {
        $this->router = $router;
        $this->config = $config;
    }

    /**
     * Add routes
     *
     * @return self
     */
    public function addRoutes() : self
    {
        // route statistics viewer
        $this->router->addRoute(new Route(RouteType::StartsWith, ['GET', 'POST'], '/plugins/route-stats/', function (ServerRequestInterface $request) : ResponseInterface {
            return (new RouteStatistics($request, $this->config->get('router.file'), $this->config->get('twig.views'), $this->config->get('twig.cache')))
                ->run();
        }));

        // route statistics middleware
        $this->router->addMiddleware(['GET', 'POST'], '~^/plugins/route-stats/~', MiddlewareType::Pre, function (ServerRequestInterface $request) : ?ResponseInterface {
            $response = (new Https($request))
                ->run();

            if ($response) {
                return $response;
            }

            return (new HttpAuthenticate($request, $this->config->get('authentication.password'), $this->config->get('authentication.group'), 'LV_users'))
                ->run();
        });

        // throttle, whitelist and ban middleware
        $this->router->addMiddleware('*', '~.*~', MiddlewareType::Pre, function (ServerRequestInterface $request) : ?ResponseInterface {
            $ip = $request->getServerParams()['REMOTE_ADDR'];

            $range = new Range($this->config->get('router.banned'));

            if ($range->contains($ip)) {
                throw new RouteException('banned', 403);
            }

            // ignore whitelisted ips
            $range = new Range($this->config->get('router.whitelist'));

            if ($range->contains($ip)) {
                return null;
            }

            if (!$this->config->get('router.statsEnabled')) {
                return null;
            }

            $count = (new RouteStatistics($request, $this->config->get('router.file'), '', ''))
                ->count($ip);

            if ($count > $this->config->get('router.throttleThreshold')) {
                throw new RouteException('too many requests', 429);
            }

            return null;
        });

        return $this;
    }

    /**
     * Handle route exceptions
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
     * Handle exceptions
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

        Helper::errorLog($where ?? '', "[{$code}] {$exception->getMessage()}", false);

        if ($code < 200 || $code > 500) {
            $code = 500;
        }

        return new Response($code);
    }
}
