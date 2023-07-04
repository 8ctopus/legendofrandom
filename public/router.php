<?php

declare(strict_types=1);

namespace Legend;

use HttpSoft\Emitter\SapiEmitter;
use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequestFactory;
use HttpSoft\ServerRequest\ServerRequestCreator;
use Legend\Routes\Site;
use Legend\Routes\Routes;
use Noodlehaus\Config;
use Oct8pus\NanoRouter\NanoRouter;
use Oct8pus\NanoTimer\NanoTimer;

$startTime = hrtime(true);

require_once __DIR__ . '/../vendor/autoload.php';

$timer = new NanoTimer($startTime);
$timer->measure('autoload');

$config = Config::load(__DIR__ . '/../.env.php');

$timer
    ->measure('config')
    ->logSlowerThan($config->get('timer.threshold'))
    ->logMemoryPeakUse();

$router = new NanoRouter(Response::class, ServerRequestFactory::class, Routes::routeExceptionHandler(...), Routes::exceptionHandler(...));

$timer->measure('router');

$serverRequest = ServerRequestCreator::createFromGlobals($_SERVER, $_FILES, $_COOKIE, $_GET, $_POST);

$timer->measure('request');

(new Site($router, $config))
    ->addRoutes();

$timer->measure('add routes');

$response = $router->resolve($serverRequest);

$path = $serverRequest->getUri()->getPath();

$timer
    ->measure('resolve')
    ->setLabel("delta - {$path}");

(new SapiEmitter())
    ->emit($response);

$timer->measure('emit');

if ($config->get('router.statsEnabled')) {
    // log statistics
    (new RouteStatistics($serverRequest, $config->get('router.file'), '', ''))
        ->add($path, $serverRequest->getMethod(), http_response_code(), (int) round((hrtime(true) - $startTime) / 1000000, 0, PHP_ROUND_HALF_UP), $serverRequest->getServerParams()['REMOTE_ADDR']);
}
