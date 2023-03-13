<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Oct8pus\NanoRouter\NanoRouter;
use Oct8pus\NanoRouter\Response;

final class RouterTest extends TestCase
{
    private NanoRouter $router;

    public function setUp() : void
    {
        parent::setUp();

        $router = new NanoRouter();

        $router->addRouteRegex('GET', '~^(/[a-zA-Z0-9\-]*)*(/index.html?)?$~', function (array $matches) : Response {
            $dir = $matches[1];
            $file = $matches[2] ?? '';

            if (empty($file)) {
                $file = $this->findFile($dir);
            }

            $path = __DIR__ . '/../public' . $dir . $file;

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
        $this->mockRequest('GET', '/');

        $response = $this->router
            ->resolve();

        static::assertEquals(200, $response->status());
        static::assertStringEqualsFile(__DIR__ . '/../public/index.html', $response->body());
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
            $file = __DIR__ . '/../public'. $dir . $option;

            if (file_exists($file)) {
                return $option;
            }
        }

        return '';
    }
}
