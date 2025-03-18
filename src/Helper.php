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
        return str_replace('\\', '/', dirname(__DIR__, 1));
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
        return self::rootDir() . '/../storage';
    }

    /**
     * Common storage dir
     *
     * @return string
     */
    public static function commonStorageDir() : string
    {
        return self::rootDir() . '/../../storage';
    }

    /**
     * Get views directory
     *
     * @return string
     */
    public static function viewsDir() : string
    {
        return self::rootDir() . '/views';
    }

    /**
     * Get system temporary dir
     *
     * @return string
     */
    public static function tmpDir() : string
    {
        return str_replace('\\', '/', sys_get_temp_dir());
    }

    /**
     * Get cache directory
     *
     * @return string
     */
    public static function cacheDir() : string
    {
        return self::rootDir() . '/../cache';
    }

    /**
     * Log to error log
     *
     * @param string $class
     * @param string $str
     * @param bool   $suppress
     *
     * @return void
     */
    public static function errorLog(string $class, string $str, bool $suppress = false) : void
    {
        $str = self::className($class) . ' - ' . $str;

        if (self::phpunit()) {
            if (!$suppress) {
                echo $str;
            }
        } else {
            error_log($str);
        }
    }

    /**
     * Get class without namespace
     *
     * @param string $class
     *
     * @return string
     */
    public static function className(string $class) : string
    {
        $position = strrpos($class, '\\');

        if ($position !== false) {
            $class = substr($class, $position + 1);
        }

        return $class;
    }

    /**
     * Get country from request uri
     *
     * @return string
     *
     * @throws Exception when domain is not known
     */
    public static function country() : string
    {
        return 'legend';
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
     * @param bool    $https
     * @param ?string $country
     *
     * @return string
     */
    public static function protocolHost(bool $https = true, ?string $country = null) : string
    {
        return ($https ? 'https://' : 'http://') . self::host($country);
    }

    /**
     * Check if production
     *
     * @return bool
     */
    public static function production() : bool
    {
        if (!in_array(gethostname(), ['octopuslabs.io'], true)) {
            return false;
        }

        return !self::phpunit();
    }

    /**
     * Check if sandbox
     *
     * @return bool
     */
    public static function sandbox() : bool
    {
        return !self::production();
    }

    /**
     * Check if phpunit is running
     *
     * @return bool
     */
    public static function phpunit() : bool
    {
        $argv = $_SERVER['argv'] ?? null;

        if (!$argv || !array_key_exists(0, $argv)) {
            return false;
        }

        return str_ends_with($argv[0], 'phpunit');
    }
}
