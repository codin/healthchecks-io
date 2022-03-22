<?php

declare(strict_types=1);

namespace spec\Codin\Healthchecks;

use Codin\Healthchecks\Exceptions;
use Codin\Healthchecks\Ping;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument\Token\TypeToken;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PingSpec extends ObjectBehavior
{
    public function it_should_perform_start(ClientInterface $httpClient, ResponseInterface $response)
    {
        $httpClient->sendRequest(new TypeToken(RequestInterface::class))->shouldBeCalled()->willReturn($response);
        $this->beConstructedWith('none', 'https://foo.dev', null, $httpClient);
        $this->start();
    }

    public function it_should_perform_success(ClientInterface $httpClient, ResponseInterface $response)
    {
        $httpClient->sendRequest(new TypeToken(RequestInterface::class))->shouldBeCalled()->willReturn($response);
        $this->beConstructedWith('none', 'https://foo.dev', null, $httpClient);
        $this->success();
    }

    public function it_should_perform_fail(ClientInterface $httpClient, ResponseInterface $response)
    {
        $httpClient->sendRequest(new TypeToken(RequestInterface::class))->shouldBeCalled()->willReturn($response);
        $this->beConstructedWith('none', 'https://foo.dev', null, $httpClient);
        $this->fail();
    }

    public function it_should_change_uuid(ClientInterface $httpClient, ResponseInterface $response)
    {
        $this->withUuid('test')->shouldReturnAnInstanceOf(Ping::class);
    }

    public function it_should_handle_HTTP_BAD_REQUEST(ClientInterface $httpClient, ResponseInterface $response, ClientExceptionInterface $error)
    {
        $httpClient->sendRequest(new TypeToken(RequestInterface::class))->shouldBeCalled()
            ->willThrow(new class('test', $response->getWrappedObject()) extends \Exception implements ClientExceptionInterface {
                protected ResponseInterface $response;
                public function __construct(string $message, ResponseInterface $response)
                {
                    $this->response = $response;
                    parent::__construct($message);
                }
                public function getResponse(): ResponseInterface
                {
                    return $this->response;
                }
            })
        ;
        $response->getStatusCode()->shouldBeCalled()->willReturn(400);
        $this->beConstructedWith('none', 'https://foo.dev', null, $httpClient);
        $this->shouldThrow(Exceptions\InvalidPayloadError::class)->duringStart();
    }

    public function it_should_handle_HTTP_UNAUTHORIZED(ClientInterface $httpClient, ResponseInterface $response, ClientExceptionInterface $error)
    {
        $httpClient->sendRequest(new TypeToken(RequestInterface::class))->shouldBeCalled()
            ->willThrow(new class('test', $response->getWrappedObject()) extends \Exception implements ClientExceptionInterface {
                protected ResponseInterface $response;
                public function __construct(string $message, ResponseInterface $response)
                {
                    $this->response = $response;
                    parent::__construct($message);
                }
                public function getResponse(): ResponseInterface
                {
                    return $this->response;
                }
            })
        ;
        $response->getStatusCode()->shouldBeCalled()->willReturn(401);
        $this->beConstructedWith('none', 'https://foo.dev', null, $httpClient);
        $this->shouldThrow(Exceptions\UnauthorisedError::class)->duringStart();
    }

    public function it_should_handle_HTTP_FORBIDDEN(ClientInterface $httpClient, ResponseInterface $response, ClientExceptionInterface $error)
    {
        $httpClient->sendRequest(new TypeToken(RequestInterface::class))->shouldBeCalled()
            ->willThrow(new class('test', $response->getWrappedObject()) extends \Exception implements ClientExceptionInterface {
                protected ResponseInterface $response;
                public function __construct(string $message, ResponseInterface $response)
                {
                    $this->response = $response;
                    parent::__construct($message);
                }
                public function getResponse(): ResponseInterface
                {
                    return $this->response;
                }
            })
        ;
        $response->getStatusCode()->shouldBeCalled()->willReturn(403);
        $this->beConstructedWith('none', 'https://foo.dev', null, $httpClient);
        $this->shouldThrow(Exceptions\AccountLimitReached::class)->duringStart();
    }

    public function it_should_handle_HTTP_NOT_FOUND(ClientInterface $httpClient, ResponseInterface $response, ClientExceptionInterface $error)
    {
        $httpClient->sendRequest(new TypeToken(RequestInterface::class))->shouldBeCalled()
            ->willThrow(new class('test', $response->getWrappedObject()) extends \Exception implements ClientExceptionInterface {
                protected ResponseInterface $response;
                public function __construct(string $message, ResponseInterface $response)
                {
                    $this->response = $response;
                    parent::__construct($message);
                }
                public function getResponse(): ResponseInterface
                {
                    return $this->response;
                }
            })
        ;
        $response->getStatusCode()->shouldBeCalled()->willReturn(404);
        $this->beConstructedWith('none', 'https://foo.dev', null, $httpClient);
        $this->shouldThrow(Exceptions\UuidNotFound::class)->duringStart();
    }

    public function it_should_handle_HTTP_5xx(ClientInterface $httpClient, ResponseInterface $response, ClientExceptionInterface $error)
    {
        $httpClient->sendRequest(new TypeToken(RequestInterface::class))->shouldBeCalled()
            ->willThrow(new class('test', $response->getWrappedObject()) extends \Exception implements ClientExceptionInterface {
                protected ResponseInterface $response;
                public function __construct(string $message, ResponseInterface $response)
                {
                    $this->response = $response;
                    parent::__construct($message);
                }
                public function getResponse(): ResponseInterface
                {
                    return $this->response;
                }
            })
        ;
        $response->getStatusCode()->shouldBeCalled()->willReturn(500);
        $this->beConstructedWith('none', 'https://foo.dev', null, $httpClient);
        $this->shouldThrow(Exceptions\FailureError::class)->duringStart();
    }

    public function it_should_handle_Generic_HTTP_5xx(ClientInterface $httpClient, ClientExceptionInterface $error)
    {
        $httpClient->sendRequest(new TypeToken(RequestInterface::class))->shouldBeCalled()
            ->willThrow(new class('test') extends \Exception implements ClientExceptionInterface {
            })
        ;
        $this->beConstructedWith('none', 'https://foo.dev', null, $httpClient);
        $this->shouldThrow(Exceptions\FailureError::class)->duringStart();
    }
}
