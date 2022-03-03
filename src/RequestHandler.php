<?php

declare(strict_types=1);

namespace Codin\Healthchecks;

use Codin\HttpClient\Exceptions\ClientError;
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
        } catch (ClientError $e) {
            $response = $e->getResponse();
        }

        if ($response->getStatusCode() !== Constants::HTTP_OK) {
            if ($response->getStatusCode() === Constants::HTTP_BAD_REQUEST) {
                throw new Exceptions\InvalidPayloadError($e->getMessage());
            }

            if ($response->getStatusCode() === Constants::HTTP_UNAUTHORIZED) {
                throw new Exceptions\UnauthorisedError($e->getMessage());
            }

            if ($response->getStatusCode() === Constants::HTTP_FORBIDDEN) {
                throw new Exceptions\AccountLimitReached($e->getMessage());
            }

            if ($response->getStatusCode() === Constants::HTTP_NOT_FOUND) {
                throw new Exceptions\UuidNotFound($e->getMessage());
            }

            throw new Exceptions\FailureError($e->getMessage());
        }

        return $response;
    }
}
