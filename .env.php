<?php

return [
    'documentRoot' => __DIR__,
    'host' => 'legend.octopuslabs.io',
    'router' => [
        'statsEnabled' => true,
        'file' => __DIR__ . '/../storage/route-stats.db',
        // maximum requests per hour
        'throttleThreshold' => 150,
        'whitelist' => [
            '127.0.0.1',
            // docker
            '172.17.0.1',
            // 8ctopus
            '176.204.21.189',
            // togh
            '223.205.25.25',
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
    'authentication' => [
        'password' => __DIR__ . '/../../auth/.htpasswd',
        'group' => __DIR__ . '/../../auth/.htgroup',
    ],
];
