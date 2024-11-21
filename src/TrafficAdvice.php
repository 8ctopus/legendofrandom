<?php

declare(strict_types=1);

namespace Legend;

use HttpSoft\Message\Response;
use HttpSoft\Message\Stream;
use Oct8pus\NanoRouter\RouteException;
use Psr\Http\Message\ResponseInterface;

class TrafficAdvice
{
    /**
     * Run
     *
     * @return ResponseInterface
     *
     * @throws RouteException
     */
    public function run() : ResponseInterface
    {
        $stream = new Stream();

        $stream->write(<<<'JSON'
        [{
          "user_agent": "prefetch-proxy",
          "google_prefetch_proxy_eap": {
            "fraction": 1.0
          }
        }]

        JSON);

        return new Response(200, ['Content-Type' => 'application/json'], $stream);
    }
}
