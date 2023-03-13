<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Oct8pus\NanoRouter\NanoRouter;
use Oct8pus\NanoRouter\Response;

final class RouterTest extends TestCase
{
    private string $dir;
    private NanoRouter $router;

    public function setUp() : void
    {
        parent::setUp();

        $this->dir = __DIR__ . '/../public';

        $router = new NanoRouter();

        $router->addRouteRegex('GET', '~^(/[a-zA-Z0-9\-]*)*(/index.html?)?$~', function (array $matches) : Response {
            $dir = $matches[1];
            $file = $matches[2] ?? '';

            if (empty($file)) {
                $file = $this->findFile($dir);
            }

            $path = $this->dir . $dir . $file;

            if (!file_exists($path)) {
                return new Response(404);
            }

            return new Response(200, file_get_contents($path));
        });

        $router->addRouteRegex('GET', '~(/[a-zA-Z0-9/\-]*\.(htm|html|php)$)~', function (array $matches) : Response {
            $file = $matches[1];

            if ($file === '/index.php') {
                return new Response(404);
            }

            $file = __DIR__ . '/../public/' . $file;

            if (!file_exists($file)) {
                return new Response(404);
            }

            return new Response(200, file_get_contents($file));
        });

        $this->router = $router;
    }

    public function testRouter() : void
    {
        $this->setUp();

        $this->mockRequest('GET', '/index.php');
        $response = $this->router
            ->resolve();

        static::assertEquals(404, $response->status());

        $pages = [
            '/',
            //'/forum/',
        ];

        foreach ($pages as $page) {
            $this->mockRequest('GET', $page);

            $response = $this->router
                ->resolve();

            static::assertEquals(200, $response->status());

            if (file_exists($this->dir . $page . '/index.html')) {
                $file = $this->dir . $page . '/index.html';
            } else {
                $file = $this->dir . $page . '/index.htm';
            }

            static::assertStringEqualsFile($file, $response->body());
        }

/*
        $this->mockRequest('GET', '/index.html');
        $response = $this->router
            ->resolve();
*/

        $pages = [
            '/challenges.html',
            '/contact-2.html',
            '/forum.html',
            '/hint6.html',
            '/sample-page.html',
            '/tools.html',
            //'/robots.txt',
            '/archives/7ee88.html',
            '/archives/category/beginner.html',
            '/page/2.html',
        ];

        foreach ($pages as $page) {
            $this->mockRequest('GET', $page);
            $response = $this->router
                ->resolve();

            static::assertEquals(200, $response->status());
            static::assertStringEqualsFile($this->dir . $page, $response->body());
        }
    }

    private function mockRequest($method, $uri) : void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
    }

    private function findFile(string $dir) : string
    {
        $options = [
            'index.html',
            'index.htm',
            'index.php',
        ];

        foreach ($options as $option) {
            $file = $this->dir. $dir . $option;

            if (file_exists($file)) {
                return $option;
            }
        }

        return '';
    }
}
