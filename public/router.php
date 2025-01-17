<?php

declare(strict_types=1);

namespace Legend;

use HttpSoft\Emitter\SapiEmitter;
use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequestFactory;
use HttpSoft\ServerRequest\ServerRequestCreator;
use Legend\Routes\ExceptionViewer;
use Legend\Routes\RouteStatistics;
use Legend\Routes\Site;
use Oct8pus\NanoRouter\NanoRouter;
use Oct8pus\NanoTimer\NanoTimer;

$startTime = hrtime(true);

require_once __DIR__ . '/../vendor/autoload.php';

$timer = new NanoTimer($startTime);
//$timer->measure('autoload');

$env = Env::instance();

$timer
    //->measure('env')
    ->logSlowerThan($env['router.timerThreshold'])
    ->logMemoryPeakUse();

$router = new NanoRouter(Response::class, ServerRequestFactory::class, ExceptionViewer::handle(...), ExceptionViewer::handle(...));
//$timer->measure('router');

$serverRequest = ServerRequestCreator::createFromGlobals($_SERVER, $_FILES, $_COOKIE, $_GET, $_POST);
//$timer->measure('request');

$stats = $env['router.statsEnabled'] ? new RouteStatistics($env['router.statsFile']) : null;
//$timer->measure('stats');

(new Site($router, $stats, $timer))
    ->addRoutes();

//$timer->measure('add routes');

$response = $router->resolve($serverRequest);

$uri = $serverRequest->getUri();
$path = $uri->getPath();

$timer
    ->measure('resolve')
    ->setLabel("delta - {$path}");

(new SapiEmitter())
    ->emit($response);

$timer
    //->measure('emit')
    ->autoLog(true);

$stats?->add($path, $serverRequest->getMethod(), $response->getStatusCode(), (int) round((hrtime(true) - $startTime) / 1000000, 0, PHP_ROUND_HALF_UP), $serverRequest->getServerParams()['REMOTE_ADDR']);
