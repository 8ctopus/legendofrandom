<?php

declare(strict_types=1);

namespace Legend;

class Helper
{
    /**
     * Get root directory
     *
     * @return string
     */
    public static function rootDir() : string
    {
        return realpath(str_replace('\\', '/', __DIR__ . '/../'));
    }

    /**
     * Get public directory
     *
     * @return string
     */
    public static function publicDir() : string
    {
        return self::rootDir() . '/public';
    }

    /**
     * Get storage directory
     *
     * @return string
     */
    public static function storageDir() : string
    {
        return self::rootDir() . '../storage';
    }

    /**
     * Check if running in development environment (docker or local)
     *
     * @return bool
     */
    public static function isDevelopment() : bool
    {
        if (self::isPhpunit()) {
            return true;
        }

        return in_array(gethostname(), ['en-web', 'Kokosikos', 'studio'], true);
    }

    /**
     * Log to error log
     *
     * @param string $class
     * @param string $str
     * @param bool suppress
     *
     * @return void
     */
    public static function errorLog(string $class, string $str, bool $suppress = false) : void
    {
        $position = strrpos($class, '\\');

        if ($position !== false) {
            $class = substr($class, $position + 1);
        }

        $str = $class . ' - ' . $str;

        if (static::isPhpunit()) {
            if (!$suppress) {
                echo $str;
            }
        } else {
            error_log($str);
        }
    }

    /**
     * Check if phpunit is running
     *
     * @return bool
     */
    public static function isPhpunit() : bool
    {
        $argv = $_SERVER['argv'] ?? null;

        if (!$argv || !array_key_exists(0, $argv)) {
            return false;
        }

        return str_ends_with($argv[0], 'phpunit');
    }

    /**
     * Get country host
     *
     * @return string
     */
    public static function host() : string
    {
        return 'legend.octopuslabs.io';
    }

    /**
     * Get protocol and host
     *
     * @param bool        $https
     *
     * @return string
     */
    public static function protocolHost(bool $https = true) : string
    {
        $protocol = $https ? 'https://' : 'http://';

        return $protocol . self::host();
    }
}
