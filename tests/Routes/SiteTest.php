<?php

declare(strict_types=1);

namespace Tests\Routes;

use Legend\Routes\Routes;
use Legend\Routes\Site;
use Exception;
use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequestFactory;
use Noodlehaus\Config;
use Oct8pus\NanoRouter\NanoRouter;
use Tests\Routes\RoutesTestCase;

/**
 * @internal
 *
 * @covers \Legend\Routes\Site
 */
final class SiteTest extends RoutesTestCase
{
    private static NanoRouter $router;
    private static Config $config;
    private static string $domain;
    private static string $ch2;
    private static string $us2;

    public static function setUpBeforeClass() : void
    {
        self::$config = Config::load(__DIR__ . '/../../.env.php');

        self::$domain = 'https://' . self::$config->get('host');

        $router = new NanoRouter(Response::class, ServerRequestFactory::class, Routes::routeExceptionHandler(...), Routes::exceptionHandler(...));

        (new Site($router, self::$config))
            ->addRoutes();

        self::$router = $router;
    }

    public function testAddRoutes() : void
    {
        $config = Config::load(__DIR__ . '/../../.env.php');

        $router = new NanoRouter(Response::class, ServerRequestFactory::class, Routes::routeExceptionHandler(...), Routes::exceptionHandler(...));

        (new Site($router, $config))
            ->addRoutes();

        self::assertTrue(true);
    }

    public function testRobots() : void
    {
        $request = self::mockRequest('GET', self::$domain . '/robots.txt');
        $response = self::$router->resolve($request);

        $body = <<<BODY
        User-agent: *
        Sitemap: https://legend.octopuslabs.io/sitemap.xml

        BODY;

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('text/plain', $response->getHeaderLine('content-type'));
        self::assertSame($body, (string) $response->getBody());
    }

    public function testSitemap() : void
    {
        $request = self::mockRequest('GET', self::$domain . '/sitemap.xml');
        $response = self::$router->resolve($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/xml', $response->getHeaderLine('content-type'));
        self::assertNotEmpty((string) $response->getBody());
    }
}
