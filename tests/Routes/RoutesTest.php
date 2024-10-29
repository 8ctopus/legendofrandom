<?php

declare(strict_types=1);

namespace Tests\Routes;

use Legend\Routes\Routes;
use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequestFactory;
use Noodlehaus\Config;
use Oct8pus\NanoRouter\NanoRouter;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Routes\RoutesTestCase;

#[CoversClass(Routes::class)]
final class RoutesTest extends RoutesTestCase
{
    private static NanoRouter $router;
    private static Config $config;
    private static string $domain;

    public static function setUpBeforeClass() : void
    {
        self::$config = Config::load(__DIR__ . '/../../.env.php');

        self::$domain = 'https://' . self::$config->get('host');

        $router = new NanoRouter(Response::class, ServerRequestFactory::class, false, false);

        (new Routes($router, self::$config))
            ->addRoutes();

        self::$router = $router;
    }

    public function testAddRoutes() : void
    {
        $router = new NanoRouter(Response::class, ServerRequestFactory::class, false, false);

        (new Routes($router, self::$config))
            ->addRoutes();

        self::assertTrue(true);
    }

    public function testRouteStatsRequireAuth() : void
    {
        $uri = self::$domain . '/plugins/route-stats/';

        $request = self::mockRequest('GET', $uri, [
            'payload' => '{}',
        ]);

        $response = self::$router->resolve($request);

        self::assertSame(401, $response->getStatusCode());
    }

    public function testRouteStatsRequireHttps() : void
    {
        $uri = self::$domain . '/plugins/route-stats/';

        $request = self::mockRequest('GET', str_replace('https', 'http', $uri), [
            'payload' => '{}',
        ]);

        $response = self::$router->resolve($request);

        self::assertSame(301, $response->getStatusCode());
        self::assertSame($uri, $response->getHeaderLine('location'));
    }
}
