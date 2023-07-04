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
            // david
            '176.204.218.50',
            // loadster
            '18.232.125.104/22',
            '3.235.177.230/22',
            '44.210.239.6/22',
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
