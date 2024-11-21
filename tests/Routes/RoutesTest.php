<?php

declare(strict_types=1);

namespace Tests\Routes;

use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequestFactory;
use Legend\Helper;
use Legend\Routes\Routes;
use Oct8pus\NanoRouter\NanoRouter;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Routes\RoutesTestCase;

#[CoversClass(Routes::class)]
final class RoutesTest extends RoutesTestCase
{
    private static NanoRouter $router;
    private static string $domain;

    public static function setUpBeforeClass() : void
    {
        self::$domain = Helper::protocolHost();

        $router = new NanoRouter(Response::class, ServerRequestFactory::class, false, false);

        (new Routes($router, null))
            ->addRoutes();

        self::$router = $router;
    }

    public function testAddRoutes() : void
    {
        $router = new NanoRouter(Response::class, ServerRequestFactory::class, false, false);

        (new Routes($router, null))
            ->addRoutes();

        self::assertTrue(true);
    }

    public function testRouteStatsRequireAuth() : void
    {
        $uri = self::$domain . '/dashboard/route-stats/';

        $request = self::mockRequest('GET', $uri, [
            'payload' => '{}',
        ]);

        $response = self::$router->resolve($request);

        self::assertSame(401, $response->getStatusCode());
    }

    public function testRouteStatsRequireHttps() : void
    {
        $uri = self::$domain . '/dashboard/route-stats/';

        $request = self::mockRequest('GET', str_replace('https', 'http', $uri), [
            'payload' => '{}',
        ]);

        $response = self::$router->resolve($request);

        self::assertSame(301, $response->getStatusCode());
        self::assertSame($uri, $response->getHeaderLine('location'));
    }
}
