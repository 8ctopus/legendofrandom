<?php

declare(strict_types=1);

namespace Legend\IPLocation\Drivers;

use Legend\Helper;
use Legend\IPLocation\Driver;
use Exception;
use MaxMind\Db\Reader;

class MaxmindDb extends Driver
{
    private $reader;
    private $result;

    public function __construct()
    {
        $this->reader = new Reader(Helper::rootDir() . '/data/GeoLite2-Country.mmdb');
    }

    public function __destruct()
    {
        $this->reader->close();
    }

    /**
     * {@inheritdoc}
     */
    protected function query(string $ip) : bool
    {
        try {
            $this->result = $this->reader->get($ip);

            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function countryCodeSub() : ?string
    {
        if ($this->result === null) {
            return null;
        }

        return $this->result['country']['iso_code'] ?? $this->result['registered_country']['iso_code'];
    }

    /**
     * {@inheritdoc}
     */
    protected function url(string $ip) : string
    {
        return '';
    }
}
