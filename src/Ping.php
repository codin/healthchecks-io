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

    final public function __construct(
        string $uuid = 'none',
        string $url = 'https://hc-ping.com',
        ?RequestBuilder $requestBuilder = null,
        ?ClientInterface $httpClient = null
    ) {
        $this->uuid = $uuid;
        $this->url = $url;
        $factory = new Psr17Factory();
        $this->requestBuilder = $requestBuilder ?? new RequestBuilder($factory, $factory);
        $this->httpClient = $httpClient ?? new HttpClient($factory, $factory);
    }

    public function withUuid(string $uuid): self
    {
        return new self($uuid, $this->url, $this->requestBuilder, $this->httpClient);
    }

    protected function ping(string $action = ''): bool
    {
        $request = $this->requestBuilder->build(
            'get',
            $action ?
            sprintf('%s/%s/%s', $this->url, $this->uuid, $action) :
            sprintf('%s/%s', $this->url, $this->uuid)
        );

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
    public function fail(int $exitCode = 0): bool
    {
        return $this->ping($exitCode > 0 ? (string) $exitCode : 'fail');
    }

    /**
     * Send start
     */
    public function start(): bool
    {
        return $this->ping('start');
    }
}
