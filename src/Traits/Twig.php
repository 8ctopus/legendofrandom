<?php

declare(strict_types=1);

namespace Legend\Traits;

use Legend\Helper;
use HttpSoft\Message\Stream;
use Psr\Http\Message\StreamInterface;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

trait Twig
{
    private Environment $environment;

    /**
     * Render template
     *
     * @param string $template
     * @param array  $params
     *
     * @return string
     */
    protected function render(string $template, array $params = []) : string
    {
        $pos = strrpos($template, '/');

        if ($pos) {
            $dir = substr($template, 0, $pos);
            $template = substr($template, $pos + 1);
        }

        $environment = $this->environment($dir ?? '');

        return $environment->render($template, $this->params($params));
    }

    /**
     * Render template to stream
     *
     * @param string $template
     * @param array  $params
     *
     * @return StreamInterface
     */
    protected function renderToStream(string $template, array $params = []) : StreamInterface
    {
        $output = $this->render($template, $params);

        $stream = new Stream();
        $stream->write($output);

        return $stream;
    }

    /**
     * Get params
     *
     * @parm array $params
     *
     * @return array
     */
    protected function params(array $params) : array
    {
        return array_merge($params, [
            'sandbox' => Helper::sandbox(),
        ]);
    }

    /**
     * Set environment dir
     *
     * @param string $dir
     *
     * @return Environment
     */
    private function environment(string $dir) : Environment
    {
        if (isset($this->environment)) {
            return $this->environment;
        }

        $namespaces = [
            '__main__' => '',
        ];

        if (!empty($dir)) {
            $namespaces[$dir] = $dir;
        }

        $loader = new FilesystemLoader($namespaces, Helper::viewsDir());

        $this->environment = new Environment($loader, [
            'auto_reload' => true,
            'cache' => Helper::storageDir() . '/twig',
            'debug' => false,
            'strict_variables' => true,
        ]);

        if (class_exists('\Twig\Extra\Intl\IntlExtension')) {
            // support for number formatting
            /** @disregard P1009 */
            $this->environment->addExtension(new IntlExtension());
        }

        return $this->environment;
    }
}
