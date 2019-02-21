<?php

namespace App\Controller;

use App\Service\ProxyListService;
use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    const RESPONSE_TEMPLATE = <<<PAC
function FindProxyForURL(url, host) {
    host = host.toLowerCase();

    if (url == 'https://www.crunchyroll.com/login') {
            return 'DIRECT';
    }

    if (dnsDomainIs(host, 'www.crunchyroll.com')) {
        return '%s'; // PROXY 40.114.109.214:3128; PROXY 40.117.231.19:3128
    }

    return 'DIRECT';
}
PAC;

    public function __construct(ProxyListService $service)
    {
        $this->service = $service;
    }

    public function index(
        string $type = ProxyListService::TYPE_HTTPS,
        ?string $anon = null,
        ?string $country = null,
        ?string $hosts = null,
        ?string $excludeUrls = null
    ) {
        $proxies = $this->service->fetchProxies($type);
        $proxyString = 'PROXY ' . implode('; PROXY ', $proxies);

        return new Response(
            sprintf(self::RESPONSE_TEMPLATE, $proxyString),
            200,
            ['Content-Type' => 'application/x-ns-proxy-autoconfig']
        );
    }
}
