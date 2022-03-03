<?php

declare(strict_types=1);

namespace Codin\Healthchecks;

use Codin\HttpClient\HttpClient;
use Codin\HttpClient\RequestBuilder;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;

class Ping
{
    use RequestHandler;

    protected string $uuid;

    protected string $url;

    protected RequestBuilder $requestBuilder;

    public function __construct(
        string $uuid,
        string $url = 'https://hc-ping.com/',
        ?RequestBuilder $requestBuilder = null,
        ?ClientInterface $httpClient = null
    ) {
        $this->uuid = $uuid;
        $this->url = $url;
        $factory = new Psr17Factory();
        $this->requestBuilder = $requestBuilder ?? new RequestBuilder($factory, $factory);
        $this->httpClient = $httpClient ?? new HttpClient($factory, $factory);
    }

    protected function ping(string $action = ''): bool
    {
        $request = $this->requestBuilder->build('get', sprintf('%s/%s/', $this->url, $action));

        $this->handleRequest($request);

        return true;
    }

    /**
     * Send success
     */
    public function success(): bool
    {
        return $this->ping();
    }

    /**
     * Send fail
     */
    public function fail(): bool
    {
        return $this->ping('fail');
    }

    /**
     * Send start
     */
    public function start(): bool
    {
        return $this->ping('start');
    }
}
