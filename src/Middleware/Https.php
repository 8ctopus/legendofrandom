<?php

declare(strict_types=1);

namespace Legend\Middleware;

use HttpSoft\Message\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Https
{
    private readonly ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Run
     *
     * @return ?ResponseInterface
     */
    public function run() : ?ResponseInterface
    {
        $uri = $this->request->getUri();

        if ($uri->getScheme() !== 'https') {
            return new Response(301, ['location' => (string) $uri->withScheme('https')]);
        }

        return null;
    }
}
