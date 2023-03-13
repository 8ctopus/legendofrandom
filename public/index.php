<?php

use Oct8pus\NanoRouter\NanoRouter;
use Oct8pus\NanoRouter\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new NanoRouter();

$router->addRouteRegex('GET', '~^(/[a-zA-Z0-9\-]*)*(/index.html?)?$~', function (array $matches) : Response {
    $dir = $matches[1];
    $file = $matches[2] ?? '';

    if (empty($file)) {
        $file = findFile($dir);
    }

    $path = $dir . $file;

    if (!file_exists(__DIR__ . $path)) {
        return new Response(404, "page not found {$path}");
    }

    error_log("tracking: {$path}");
    return new Response(200, file_get_contents(__DIR__ . $path));
});

$router->addRouteRegex('GET', '~(/[a-zA-Z0-9/\-]*\.(htm|html|php)$)~', function (array $matches) : Response {
    $file = $matches[1];

    if ($file === '/index.php') {
        return new Response(404);
    } elseif (!file_exists(__DIR__ . $file)) {
        return new Response(404);
    }

    error_log("tracking: {$file}");
    return new Response(200, file_get_contents(__DIR__ . $file));
});

// resolve route
$router
    ->resolve()
    ->send();


function findFile(string $dir) : string
{
    $options = [
        'index.html',
        'index.htm',
        'index.php',
    ];

    foreach ($options as $option) {
        $file = __DIR__ . $dir . $option;

        if (file_exists($file)) {
            return $option;
        }
    }

    return '';
}
