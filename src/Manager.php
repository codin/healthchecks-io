<?php

declare(strict_types=1);

namespace Codin\Healthchecks;

use Codin\HttpClient\HttpClient;
use Codin\HttpClient\RequestBuilder;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;

class Manager
{
    use RequestHandler;

    protected string $apikey;

    protected string $url;

    protected RequestBuilder $requestBuilder;

    public function __construct(
        string $apikey,
        string $url = 'https://healthchecks.io/api/v1/',
        ?RequestBuilder $requestBuilder = null,
        ?ClientInterface $httpClient = null
    ) {
        $this->apikey = $apikey;
        $this->url = $url;
        $factory = new Psr17Factory();
        $this->requestBuilder = $requestBuilder ?? new RequestBuilder($factory, $factory);
        $this->httpClient = $httpClient ?? new HttpClient($factory, $factory);
    }

    protected function request(string $method, string $resource, array $options = []): array
    {
        $options['headers']['X-Api-Key'] = $this->apikey;

        $request = $this->requestBuilder->build($method, sprintf('%s/%s', $this->url, $resource), $options);

        $response = $this->handleRequest($request);

        $payload = (string) $response->getBody();

        $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new Exceptions\FailureError('Failed to decode response');
        }

        return $data;
    }

    public function listChecks(): array
    {
        $data = $this->request('get', 'checks');

        return $data['checks'];
    }

    public function getCheck(string $uuid): array
    {
        $data = $this->request('get', sprintf('checks/%s', $uuid));

        return $data;
    }

    public function pauseCheck(string $uuid): bool
    {
        $this->request('post', sprintf('checks/%s/pause', $uuid));

        return true;
    }

    public function resumeCheck(string $uuid): bool
    {
        (new Ping($uuid))->success();

        return true;
    }

    public function deleteCheck(string $uuid): bool
    {
        $this->request('delete', sprintf('checks/%s', $uuid));

        return true;
    }

    public function getCheckPings(string $uuid): array
    {
        $data = $this->request('get', sprintf('checks/%s/pings', $uuid));

        return $data;
    }

    public function getCheckStatusChanges(string $uuid): array
    {
        $data = $this->request('get', sprintf('checks/%s/flips', $uuid));

        return $data;
    }

    public function createCheck(Requests\Create $request) : array
    {
        $data = $this->request('post', 'checks', [
            'json' => $request->getParams(),
        ]);

        return $data;
    }

    public function updateCheck(string $uuid, Requests\Update $request): array
    {
        $data = $this->request('post', sprintf('checks/%s', $uuid), [
            'json' => $request->getParams(),
        ]);

        return $data;
    }
}
