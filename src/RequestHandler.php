<?php

declare(strict_types=1);

namespace Codin\Healthchecks;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait RequestHandler
{
    protected ClientInterface $httpClient;

    protected function handleRequest(RequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            if (!method_exists($e, 'getResponse')) {
                throw new Exceptions\FailureError($e->getMessage(), $e->getCode(), $e);
            }

            $response = $e->getResponse();

            if ($response->getStatusCode() === Constants::HTTP_BAD_REQUEST) {
                throw new Exceptions\InvalidPayloadError($e->getMessage(), $e->getCode(), $e);
            }

            if ($response->getStatusCode() === Constants::HTTP_UNAUTHORIZED) {
                throw new Exceptions\UnauthorisedError($e->getMessage(), $e->getCode(), $e);
            }

            if ($response->getStatusCode() === Constants::HTTP_FORBIDDEN) {
                throw new Exceptions\AccountLimitReached($e->getMessage(), $e->getCode(), $e);
            }

            if ($response->getStatusCode() === Constants::HTTP_NOT_FOUND) {
                throw new Exceptions\UuidNotFound($e->getMessage(), $e->getCode(), $e);
            }

            throw new Exceptions\FailureError($e->getMessage(), $e->getCode(), $e);
        }

        return $response;
    }
}
