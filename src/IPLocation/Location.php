<?php

declare(strict_types=1);

namespace Legend\IPLocation;

use Exception;
use League\ISO3166\ISO3166;

class Location
{
    private readonly Driver $driver;

    /**
     * Get ip country code
     *
     * @param string  $ip
     * @param ?string $driver
     *
     * @return string country code or null on failure
     */
    public function countryCode(string $ip, ?string $driver = null) : ?string
    {
        $class = static::class;

        $position = strrpos($class, '\\');

        if ($position === false) {
            throw new Exception('find namespace');
        }

        $namespace = substr($class, 0, $position);
        $namespace .= '\Drivers';

        if ($driver) {
            if (!isset($this->driver)) {
                $this->driver = new ("{$namespace}\\{$driver}")();
            }

            return $this->driver->countryCode($ip);
        }

        $drivers = $this->drivers();

        foreach ($drivers as $driver) {
            $driver = new ("{$namespace}\\{$driver}")();

            $location = $driver->countryCode($ip);

            if ($location !== null) {
                return $location;
            }
        }

        return null;
    }

    /**
     * Get ip country
     *
     * @param  string      $ip
     * @param  string|null $driver
     *
     * @return string
     */
    public function country(string $ip, ?string $driver = null) : string
    {
        $countryCode = $this->countryCode($ip, $driver);

        if (!$countryCode) {
            return 'unknown';
        }

        $iso = (new ISO3166())
            ->alpha2($countryCode);

        return $iso['name'];
    }

    /**
     * List drivers
     *
     * @return array
     */
    private function drivers() : array
    {
        return [
            'MaxmindDb',
        ];
    }
}
