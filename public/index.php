<?php

use Oct8pus\NanoRouter\NanoRouter;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new NanoRouter();

$router->addErrorHandler(404, function (string $requestPath) {
    http_response_code(404);
    echo "page not found {$requestPath}";
});

$router->addRouteRegex('GET', '~^(/[a-zA-Z0-9\-]*)*(/index.html?)?$~', function (array $matches) {
    $dir = $matches[1];
    $file = $matches[2] ?? '';

    if (empty($file)) {
        $file = findFile($dir);
    }

    if (!file_exists(__DIR__ . $dir . $file)) {
        http_response_code(404);
        return;
    }

    displayPage($dir . $file);
});

$router->addRouteRegex('GET', '~(/[a-zA-Z0-9/\-]*\.(htm|html|php)$)~', function (array $matches) {
    $file = $matches[1];

    if ($file === '/index.php') {
        http_response_code(404);
        return;
    }

    displayPage($file);
});

// resolve routes
$router->resolve();

function displayPage(string $file) : void
{
    echo file_get_contents(__DIR__ . $file);
    error_log("tracking: {$file}");
}

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
