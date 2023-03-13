<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Legend\RouterHelper;

final class RouterTest extends TestCase
{
    private RouterHelper $router;

    public function setUp() : void
    {
        parent::setUp();

        $this->router = new RouterHelper(__DIR__ . '/../public');
    }

    public function testRouter() : void
    {
        $this->setUp();

        $this->mockRequest('GET', '/index.php');
        $response = $this->router
            ->resolve();

        static::assertEquals(404, $response->status());

        $pages = [
            '',
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
            $this->mockRequest('GET', $page);

            $response = $this->router
                ->resolve();

            static::assertEquals(200, $response->status());

            if (file_exists($this->router->dir . $page . '/index.html')) {
                $file = $this->router->dir . $page . '/index.html';
            } else {
                $file = $this->router->dir . $page . '/index.htm';
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
            static::assertStringEqualsFile($this->router->dir . $page, $response->body());
        }
    }

    private function mockRequest($method, $uri) : void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
    }
}
