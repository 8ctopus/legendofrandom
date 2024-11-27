<?php

declare(strict_types=1);

namespace Legend\Routes;

use HttpSoft\Message\Response;
use Legend\Helper;
use Legend\Traits\Twig;
use Oct8pus\NanoRouter\RouteException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ExceptionViewer
{
    use Twig;

    private readonly Throwable $exception;

    /**
     * Handle exception
     *
     * @param Throwable              $exception
     * @param ServerRequestInterface $request
     *
     * @return ?ResponseInterface
     */
    public static function handle(Throwable $exception, ServerRequestInterface $request) : ?ResponseInterface
    {
        return (new self($exception, $request))
            ->run();
    }

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function run() : ?ResponseInterface
    {
        $code = $this->exception->getCode();

        if (!$this->exception instanceof RouteException) {
            // PDOExceptions code can be string
            if (is_string($code) && is_numeric($code)) {
                $code = (int) $code;
            }

            if (is_string($code) || (is_int($code) && $code < 200 || $code > 500)) {
                $code = 500;
            }
        }

        $trace = $this->exception->getTrace();

        if ($code !== 404 && !($code === 429 && rand(0, 100) !== 0)) {
            $message = "[{$code}] {$this->exception->getMessage()}";

            // do not log all hammering in order to keep clean apache logs
            if (count($trace)) {
                $where = array_key_exists('class', $trace[0]) ? $trace[0]['class'] : $trace[0]['function'];
            }

            Helper::errorLog($where ?? '', $message, false);
        }

        if (!in_array($code, [400, 401, 404, 405, 429, 500], true)) {
            return null;
        }

        $stream = self::renderToStream('ExceptionViewer.twig', [
            'title' => "error {$code}",
            'error' => "error {$code}",
            'message' => Helper::production() ? '' : $this->exception->getMessage(),
            'trace' => Helper::production() ? '' : $this->exception->getTraceAsString(),
        ]);

        return new Response($code, ['Content-Type' => 'text/html'], $stream);
    }
}
