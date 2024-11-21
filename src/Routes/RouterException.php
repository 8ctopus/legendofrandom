<?php

declare(strict_types=1);

namespace Legend\Routes;

use HttpSoft\Message\Response;
use Legend\Helper;
use Legend\Traits\Twig;
use Oct8pus\NanoRouter\RouteException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class RouterException
{
    use Twig;

    private readonly Throwable $exception;

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

        $message = "[{$code}] {$this->exception->getMessage()}";

        $trace = $this->exception->getTrace();

        if (count($trace)) {
            $where = array_key_exists('class', $trace[0]) ? $trace[0]['class'] : $trace[0]['function'];
        }

        if ($code === 404 || ($code === 429 && rand(0, 100) !== 0)) {
            // do not log all hammering in order to keep clean apache logs
        } else {
            Helper::errorLog($where ?? '', $message, false);
        }

        if (!in_array($code, [400, 401, 404, 405, 500], true)) {
            return null;
        }

        $stream = self::renderToStream('RouterException.twig', [
            'title' => "error {$code}",
            'error' => "error {$code}",
            'message' => Helper::production() ? '' : $this->exception->getMessage(),
            'trace' => Helper::production() ? '' : json_encode($trace, JSON_PRETTY_PRINT),
        ]);

        return new Response($code, ['Content-Type' => 'text/html'], $stream);
    }
}
