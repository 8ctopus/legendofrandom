<?php

namespace Legend;

use Oct8pus\NanoRouter\NanoRouter;
use Oct8pus\NanoRouter\Response;

class RouterHelper
{
    private string $dir;
    private NanoRouter $router;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
        $router = new NanoRouter();

        $router->addRouteRegex('GET', '~^(/[a-zA-Z0-9\-]*)*(/index.html?)?$~', function (array $matches) : Response {
            $dir = $matches[1] ?? '/';
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

            $file = $this->dir . $file;

            if (!file_exists($file)) {
                return new Response(404);
            }

            return new Response(200, file_get_contents($file));
        });

        $this->router = $router;

        // resolve route
        $this->router
            ->resolve()
            ->send();
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
