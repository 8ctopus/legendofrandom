<?php

declare(strict_types=1);

namespace Tests\Routes;

use Legend\Helper;
use Legend\Routes\Routes;
use Legend\Routes\Site as SiteBase;
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

    public function testRoutes() : void
    {
        $pages = [
            // FIX ME
            //'',
            '/',
            '/forum/',
            '/archives/category/beginner/',
            '/archives/category/tutorials/',
            '/archives/date/2012/06/',
            '/archives/date/2012/07/',
            '/archives/date/2012/08/',
            '/archives/date/2012/09/',
            '/archives/date/2012/10/',
        ];

        foreach ($pages as $page) {
            $request = $this->mockRequest('GET', self::$domain . $page);

            $response = self::$router
                ->resolve($request);

            self::assertSame(200, $response->getStatusCode());

            if (file_exists(Helper::publicDir() . $page . '/index.html')) {
                $file = Helper::publicDir() . $page . '/index.html';
            } else {
                $file = Helper::publicDir() . $page . '/index.htm';
            }

            self::assertStringEqualsFile($file, (string) $response->getBody());
        }

        $pages = [
            '/challenges.html',
            '/contact-2.html',
            '/forum.html',
            '/hint6.html',
            '/sample-page.html',
            '/tools.html',
            '/archives/7ee88.html',
            '/archives/category/beginner.html',
            '/page/2.html',
        ];

        foreach ($pages as $page) {
            $request = $this->mockRequest('GET', self::$domain . $page);

            $response = self::$router
                ->resolve($request);

            self::assertSame(200, $response->getStatusCode());
            self::assertStringEqualsFile(Helper::publicDir() . $page, (string) $response->getBody());
        }
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

class Site extends SiteBase
{
    protected function tracking(string $tag) : string
    {
        return '';
    }
}
