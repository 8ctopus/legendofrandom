<?php

declare(strict_types=1);

namespace Legend\IPLocation;

use Legend\HttpClientFactory;
use HttpSoft\Message\Request;

abstract class Driver
{
    /**
     * Get country code
     *
     * @param string $ip
     *
     * @return string country or null on failure
     */
    public function countryCode(string $ip) : ?string
    {
        $result = $this->query($ip);

        if (!$result) {
            return null;
        }

        return $this->countryCodeSub();
    }

    /**
     * Get url content using curl
     *
     * @param string $url
     *
     * @return string
     */
    protected function get(string $url) : string
    {
        $client = HttpClientFactory::create([
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $request = new Request('GET', $url, []);

        $response = $client->sendRequest($request);

        return (string) $response->getBody();
    }

    /**
     * Query api
     *
     * @param string $ip
     *
     * @return bool true on success, otherwise false
     */
    abstract protected function query(string $ip) : bool;

    /**
     * Get country code
     *
     * @return string country code
     */
    abstract protected function countryCodeSub() : ?string;

    /**
     * Get api url
     *
     * @param string $ip
     *
     * @return string
     */
    abstract protected function url(string $ip) : string;
}
