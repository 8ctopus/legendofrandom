<?php

declare(strict_types=1);

namespace Legend;

use ArrayAccess;
use Exception;

class Env implements ArrayAccess
{
    protected array $env;
    private static ?self $instance = null;

    private function __construct()
    {
        $this->env = require Helper::rootDir() . '/.env.php';
    }

    /**
     * Get instance
     *
     * @return self
     */
    public static function instance() : self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function offsetExists(mixed $offset) : bool
    {
        $path = explode('.', $offset);
        $last = array_pop($path);

        $tree = $this->env;

        foreach ($path as $element) {
            if (!array_key_exists($element, $tree)) {
                return false;
            }

            $tree = $tree[$element];
        }

        return array_key_exists($last, $tree);
    }

    public function offsetGet(mixed $offset) : mixed
    {
        $path = explode('.', $offset);
        $last = array_pop($path);

        $tree = $this->env;

        foreach ($path as $element) {
            if (!array_key_exists($element, $tree)) {
                throw new Exception("unknown key (1) - {$offset}");
            }

            $tree = $tree[$element];
        }

        if (!array_key_exists($last, $tree)) {
            throw new Exception("unknown key (2) - {$offset}");
        }

        return $tree[$last];
    }

    public function offsetSet(mixed $offset, mixed $value) : void
    {
        throw new Exception('not implemented');
    }

    public function offsetUnset(mixed $offset) : void
    {
        throw new Exception('not implemented');
    }
}
