# HttpClient

This library allows you to make several HTTP requests in parallel, reducing the overall time of the set of requests.

You can use it for a single request by following the [PSR-18 HTTP Client](https://www.php-fig.org/psr/psr-18/), see the
test snippet.

```php
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
```

Or you can send a collection of `Psr\Http\Message\RequestInterface` and retrieve an array of 
`Psr\Http\Message\ResponseInterface`, see the test.

```php
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
```
