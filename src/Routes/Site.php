<?php

declare(strict_types=1);

namespace Legend\Routes;

use HttpSoft\Message\Response;
use HttpSoft\Message\Stream;
use Legend\Helper;
use Legend\Robots;
use Legend\Sitemap;
use Legend\TrafficAdvice;
use Oct8pus\NanoRouter\NanoRouter;
use Oct8pus\NanoRouter\Route;
use Oct8pus\NanoRouter\RouteException;
use Oct8pus\NanoRouter\RouteType;
use Oct8pus\NanoTimer\NanoTimer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Site extends Routes
{
    /**
     * Constructor
     *
     * @param NanoRouter $router
     * @param ?RouteStatistics $stats
     * @param ?NanoTimer $timer
     */
    public function __construct(NanoRouter $router, ?RouteStatistics $stats, ?NanoTimer $timer)
    {
        parent::__construct($router, $stats);

        $timer = $timer;
    }

    /**
     * Add routes
     *
     * @return self
     */
    public function addRoutes() : self
    {
        parent::addRoutes();

        $this->router->addRoute(new Route(RouteType::Exact, 'GET', '/robots.txt', static function (ServerRequestInterface $request) : ResponseInterface {
            return (new Robots($request))
                ->run();
        }));

        $this->router->addRoute(new Route(RouteType::Exact, 'GET', '/sitemap.xml', static function () : ResponseInterface {
            return (new Sitemap())
                ->run();
        }));

        $this->router->addRoute(new Route(RouteType::Exact, 'GET', '/.well-known/security.txt', static function () : ResponseInterface {
            $stream = new Stream();
            $stream->write(<<<'TEXT'
            Contact: hello@octopuslabs.io

            TEXT);

            return new Response(200, ['Content-Type' => 'text/plain'], $stream);
        }));

        $this->router->addRoute(new Route(RouteType::Exact, 'GET', '/.well-known/traffic-advice', static function () : ResponseInterface {
            return (new TrafficAdvice())
                ->run();
        }));

        $this->router->addRoute(new Route(RouteType::Regex, 'GET', '~^.*$~', function (ServerRequestInterface $request) : ResponseInterface {
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
                        $file .= $option;
                    }
                }
            }

            if (!file_exists($file) || is_dir($file)) {
                throw new RouteException('file not found', 404);
            }

            $stream = new Stream();

            $source = file_get_contents($file);

            // add google tracking
            $source = str_replace('</head>', $this->tracking('G-EQKHHME6YK') . '</head>', $source);

            // add banner
            $source = str_replace('<body>', '<body>' . self::banner(), $source);

            $stream->write($source);

            return new Response(200, ['Content-Type' => 'text/html'], $stream);
        }));

        return $this;
    }

    /**
     * Add github banner
     *
     * @return string
     */
    public static function banner() : string
    {
        return <<<'HTML'
        <div style="position: fixed; left: 0; top: 0; width: 100%; padding: 0.8rem; text-align: center; background: #343481; color: white;">
            This is static copy of The Legend of Random as it was on Thu, 19 Sep 2013. Some of the links are not functional. <a href="https://github.com/8ctopus/legendofrandom">View the source code on GitHub</a>.
        </div>

        HTML;
    }

    /**
     * Add google analytics tracking
     *
     * @param string $tag
     *
     * @return string
     */
    protected function tracking(string $tag) : string
    {
        return '';
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
