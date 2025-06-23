<?php

namespace Ginsen\HttpClient\Tests;

use Ginsen\HttpClient\HttpClient;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(HttpClient::class)]
class HttpClientTest extends TestCase
{
    #[Test]
    public function it_should_handler_one_request_as_psr_18(): void
    {
        $client = new HttpClient();

        $response = $client->sendRequest(
            new Request('GET', 'https://jsonplaceholder.typicode.com/todos/1')
        );

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertSame(200, $response->getStatusCode());
    }


    #[Test]
    public function it_should_handler_several_requests(): void
    {
        $client = new HttpClient();

        $requests['todos'] = new Request('GET', 'https://jsonplaceholder.typicode.com/todos/1');
        $requests['posts'] = new Request('GET', 'https://jsonplaceholder.typicode.com/posts/1');

        $responses = $client->sendRequest(...$requests);

        self::assertInstanceOf(ResponseInterface::class, $responses['todos']);
        self::assertInstanceOf(ResponseInterface::class, $responses['posts']);

        self::assertSame(200, $responses['todos']->getStatusCode());
        self::assertSame(200, $responses['posts']->getStatusCode());
    }
}
