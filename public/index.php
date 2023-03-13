<?php

require_once __DIR__ . '/../vendor/autoload.php';

$router = (new Legend\RouterHelper(__DIR__));

$router
    ->resolve()
    ->send();
