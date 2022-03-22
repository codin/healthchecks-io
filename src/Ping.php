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
        string $uuid,
        string $url = 'https://hc-ping.com',
        ?ClientInterface $httpClient = null,
        ?RequestBuilder $requestBuilder = null
    ) {
        $this->uuid = $uuid;
        $this->url = $url;
        $factory = new Psr17Factory();
        $this->httpClient = $httpClient ?? new HttpClient($factory, $factory);
        $this->requestBuilder = $requestBuilder ?? new RequestBuilder($factory, $factory);
    }

    public function withUuid(string $uuid): self
    {
        return new self($uuid, $this->url, $this->httpClient, $this->requestBuilder);
    }

    public function withHttpClient(ClientInterface $httpClient): self
    {
        return new self($this->uuid, $this->url, $httpClient, $this->requestBuilder);
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
