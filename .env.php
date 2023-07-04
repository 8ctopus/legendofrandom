<?php

return [
    'documentRoot' => __DIR__,
    'router' => [
        'statsEnabled' => true,
        'file' => __DIR__ . '/../storage/route-stats.db',
        // maximum requests per hour
        'throttleThreshold' => 150,
        'whitelist' => [
            '127.0.0.1',
            // david
            '94.57.49.68',
        ],
        'banned' => [
        ],
    ],
    'timer' => [
        // logging to error log threshold
        'threshold' => 50,
    ],
    'twig' => [
        'views' => __DIR__ . '/views',
        'cache' => __DIR__ . '/../storage/twig',
    ],
];
