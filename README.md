# HttpClient

Esta librería permite hacer varias llamadas Http en paralelo, reduciendo el tiempo del conjunto de llamadas.

Podemos usarla para una única llamada cumpliendo el [PSR Http Client](), ver el fragmento de test.

```php
<?php

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

O podemos parar una colección de `Psr\Http\Message\RequestInterface` y recuperar un array de 
`Psr\Http\Message\ResponseInterface`, ver test.

```php
<?php

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