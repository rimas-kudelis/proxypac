<?php

namespace App\Service;

use GuzzleHttp\Client;

class ProxyListService
{
    const TYPE_HTTP = 'http';
    const TYPE_HTTPS = 'https';
    const TYPE_SOCKS4 = 'socks4';
    const TYPE_SOCKS5 = 'socks5';

    const ANON_TRANSPARENT = 'transparent';
    const ANON_ANONYMOUS = 'anonymous';
    const ANON_ELITE = 'elite';

    const BASE_URL = 'https://www.proxy-list.download/api/v1/';

    const RETURN_LIMIT = 20;

    /** @var Client */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetchProxies(string $type, ?string $anon = null, ?string $country = null)
    {
        $response = $this->client->get($this->getUrl($type, $anon, $country));

        $responseBody = (string)$response->getBody();
        // $proxies = explode("\n", $responseBody);
        $proxies = preg_split("/\r?\n|\r?\n/", $responseBody);

        shuffle($proxies);

        return array_slice($proxies, 0, self::RETURN_LIMIT);
    }

    protected function getUrl(string $type, ?string $anon = null, ?string $country = null): string
    {
        $args = [
            'type' => $type,
        ];

        if ($anon !== null) {
            $args['anon'] = $anon;
        }

        if ($country !== null) {
            $args['country'] = $country;
        }

        $url = sprintf('%sget?%s', self::BASE_URL, http_build_query($args));

        return $url;
    }
}
