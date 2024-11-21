<?php

declare(strict_types=1);

namespace Legend;

use HttpSoft\Message\Response;
use HttpSoft\Message\Stream;
use Legend\Helper;
use Oct8pus\NanoRouter\RouteException;
use Psr\Http\Message\ResponseInterface;

class Sitemap
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
        $host = Helper::protocolHost(true);

        $stream = new Stream();

        $stream->write(<<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <urlset
                xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
                    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
            <url>
                <loc>{$host}</loc>
            </url>
        </urlset>

        XML);

        return new Response(200, ['content-type' => 'application/xml'], $stream);
    }
}
