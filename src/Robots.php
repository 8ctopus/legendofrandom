<?php

declare(strict_types=1);

namespace Legend;

use Legend\Helper;
use HttpSoft\Message\Response;
use HttpSoft\Message\Stream;
use Oct8pus\NanoRouter\RouteException;
use Psr\Http\Message\ResponseInterface;

class Robots
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

        $stream->write($this->content());

        return new Response(200, ['content-type' => 'text/plain'], $stream);
    }

    protected function content() : string
    {
        $host = Helper::protocolHost(true);

        return <<<TEXT
        User-agent: *
        Sitemap: {$host}/sitemap.xml

        TEXT;
    }
}
