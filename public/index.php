<?php

require_once __DIR__ . '/../vendor/autoload.php';

function tracking(string $tag) : string
{
    return <<<HTML
        <script async src="https://www.googletagmanager.com/gtag/js?id={$tag}"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag() {
            dataLayer.push(arguments);
          }

          gtag('js', new Date());
          gtag('config', '{$tag}');
        </script>

    HTML;
}

$router = (new Legend\RouterHelper(__DIR__, function (string $source) : string {
    return str_replace('</head>', tracking('UA-XXXXX-Y') . '</head>', $source);
}));

$router
    ->resolve()
    ->send();
