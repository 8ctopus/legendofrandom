<?php

namespace Legend;

use Closure;
use Oct8pus\NanoRouter\NanoRouter;
use Oct8pus\NanoRouter\Response;

class RouterHelper extends NanoRouter
{
    public readonly string $dir;
    private readonly ?Closure $callback;

    public function __construct(string $dir, ?Closure $callback = null)
    {
        parent::__construct();

        $this->dir = $dir;
        $this->callback = $callback;

        // deal with index pages
        $this->addRouteRegex('GET', '~^([/a-zA-Z0-9\-]+)*(index.html?)?$~', function (array $matches) : Response {
            $dir = $matches[1] ?? '/';
            $file = $matches[2] ?? '';

            if (empty($file)) {
                $file = $this->findFile($dir);
            }

            $path = $this->dir . $dir . $file;

            if (!file_exists($path)) {
                return new Response(404);
            }

            if (!isset($this->callback)) {
                return new Response(200, file_get_contents($path));
            } else {
                return ($this->callback)(file_get_contents($path));
            }
        });

        // deal with other pages
        $this->addRouteRegex('GET', '~(/[a-zA-Z0-9/\-]*\.(htm|html|php)$)~', function (array $matches) : Response {
            $file = $matches[1];

            if ($file === '/index.php') {
                return new Response(404);
            }

            $file = $this->dir . $file;

            if (!file_exists($file)) {
                return new Response(404);
            }

            if (!$this->callback) {
                return new Response(200, file_get_contents($file));
            } else {
                return ($this->callback)(file_get_contents($file));
            }
        });
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