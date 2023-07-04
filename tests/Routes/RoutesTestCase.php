<?php

declare(strict_types=1);

namespace Tests\Routes;

use HttpSoft\ServerRequest\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

abstract class RoutesTestCase extends TestCase
{
    public function mockRequest(string $method = 'GET', string $uri = '', array $params = [], string $ip = '127.0.0.1') : ServerRequestInterface
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        $parts = parse_url($uri);

        $_SERVER['REQUEST_SCHEME'] = $parts['scheme'];
        $_SERVER['HTTP_HOST'] = $parts['host'];
        $_SERVER['REQUEST_URI'] = $parts['path']; // . (isset($parts['query']) ? '?' . $parts['query'] : '');
        $_SERVER['QUERY_STRING'] = $parts['query'] ?? '';

        $_SERVER['REQUEST_PROTOCOL'] = '1.1';
        $_SERVER['REMOTE_ADDR'] = $ip;
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36 Edg/112.0.1722.58';

        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_COOKIE = [];

        switch ($method) {
            case 'HEAD':
            case 'GET':
                $_GET = $params;
                break;

            case 'POST':
                $_POST = $params;
                break;

            default:
                break;
        }

        return ServerRequestCreator::createFromGlobals($_SERVER, $_FILES, $_COOKIE, $_GET, $_POST);
    }
}
