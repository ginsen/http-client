<?php

declare(strict_types=1);

namespace Ginsen\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    /**
     * @return ResponseInterface|ResponseInterface[]
     */
    public function sendRequest(RequestInterface ...$requests): ResponseInterface|iterable;
}
