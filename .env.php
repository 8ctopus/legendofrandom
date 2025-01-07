<?php

declare(strict_types=1);

namespace Legend;

use Legend\Helper;

return [
    'authentication' => [
        'password' => Helper::rootDir() . '/../../auth/.htpasswd',
        'group' => Helper::rootDir() . '/../../auth/.htgroup',
        'require' => 'LV_users',
    ],
    'router' => [
        'banned' => [
        ],
        'statsEnabled' => true,
        'statsFile' => Helper::storageDir() . '/route-stats.db',
        // maximum requests per hour
        'throttleThreshold' => 100,
        'scanThreshold' => 10,
        'scanScore' => 2,
        // log requests slower than threshold to error log (ms)
        'timerThreshold' => 100,
        'whitelist' => [
            'localhost' => '127.0.0.1',
        ],
    ],
];
