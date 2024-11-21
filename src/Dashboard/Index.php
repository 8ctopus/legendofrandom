<?php

declare(strict_types=1);

namespace Legend\Dashboard;

use HttpSoft\Message\Response;
use Legend\Helper;
use Legend\Traits\Twig;
use Psr\Http\Message\ResponseInterface;

class Index
{
    use Twig;

    /**
     * Run
     *
     * @return ResponseInterface
     */
    public function run() : ResponseInterface
    {
        $stream = $this->renderToStream('Dashboard/Index.twig', [
            'country' => Helper::country(),
            'host' => Helper::host(),
        ]);

        $headers = [
            'cache-control' => 'no-store',
            'content-type' => 'text/html',
        ];

        return new Response(200, $headers, $stream);
    }
}
