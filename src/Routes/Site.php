<?php

declare(strict_types=1);

namespace Legend\Routes;

use HttpSoft\Message\Response;
use HttpSoft\Message\Stream;
use Legend\Helper;
use Noodlehaus\Config;
use Oct8pus\NanoRouter\NanoRouter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Site extends Routes
{
    /**
     * Constructor
     *
     * @param NanoRouter $router
     * @param Config     $config
     */
    public function __construct(NanoRouter $router, Config $config)
    {
        parent::__construct($router, $config);
    }

    /**
     * Add routes
     *
     * @return self
     */
    public function addRoutes() : self
    {
        parent::addRoutes();

        $this->router->addRouteRegex('GET', '~^.*$~', function (ServerRequestInterface $request) : ResponseInterface {
            $path = $request->getUri()->getPath();

            $file = Helper::publicDir() . $path;

            if (is_dir($file)) {
                $options = [
                    'index.html',
                    'index.htm',
                    'index.php',
                ];

                foreach ($options as $option) {
                    if (file_exists($file . $option)) {
                        $file = $file . $option;
                    }
                }
            } elseif (!file_exists($file)) {
                return new Response(404);
            }

            $stream = new Stream();

            $source = file_get_contents($file);

            $source = str_replace('</head>', $this->addTracking('G-EQKHHME6YK') . '</head>', $source);

            $stream->write($source);

            return new Response(200, ['content-type' => 'text/html'], $stream);
        });

/*
        $this->router->addRoute('GET', '/robots.txt', function (ServerRequestInterface $request) : ResponseInterface {
            return (new Robots($request))
                ->run();
        });

        $this->router->addMiddleware('GET', '~^/robots\.txt$~', 'pre', function (ServerRequestInterface $request) : ?ResponseInterface {
            return (new RedirectHost($request, $this->config->get('host'), $this->config->get('host')))
                ->run();
        });

        $this->router->addRoute('GET', '/sitemap.xml', function () : ResponseInterface {
            return (new Sitemap())
                ->run();
        });

        $this->router->addMiddleware('GET', '~^/sitemap\.xml$~', 'pre', function (ServerRequestInterface $request) : ?ResponseInterface {
            return (new RedirectHost($request, $this->config->get('host'), $this->config->get('host')))
                ->run();
        });

        $this->router->addRoute('GET', '/.well-known/traffic-advice', function () : ResponseInterface {
            return (new TrafficAdvice())
                ->run();
        });

        $this->router->addRoute('GET', '/.well-known/security.txt', function () : ResponseInterface {
            $stream = new Stream();
            $stream->write(<<<'TEXT'
            Contact: hello@octopuslabs.io

            TEXT);

            return new Response(200, ['Content-Type' => 'text/plain'], $stream);
        });
*/

        return $this;
    }

    private function addTracking(string $tag) : string
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
}
