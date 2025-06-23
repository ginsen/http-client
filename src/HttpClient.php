<?php

namespace Ginsen\HttpClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Utils;
use Http\Adapter\Guzzle7\Client as PsrAdapterClient;
use Http\Adapter\Guzzle7\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient implements HttpClientInterface
{
    private PsrAdapterClient $client;

    public function __construct(iterable $guzzleOptions = [])
    {
        $this->client = new PsrAdapterClient(
            $this->initGuzzleClient($guzzleOptions)
        );
    }


    /**
     * @param RequestInterface ...$requests
     * @return ResponseInterface|ResponseInterface[]
     * @throws
     */
    public function sendRequest(RequestInterface ...$requests): ResponseInterface|iterable
    {
        if (count($requests) === 1) {
            return $this->client->sendRequest($requests[0]);
        }

        if (empty($requests)) {
            return [];
        }

        $promises = $this->makePromises(...$requests);

        return $this->extractResponses(...$promises);
    }


    /**
     * @param RequestInterface ...$requests
     * @return Promise[]
     * @throws
     */
    private function makePromises(RequestInterface ...$requests): iterable
    {
        $promises = [];
        foreach ($requests as $id => $request) {
            $promises[] = $this->client->sendAsyncRequest($request)->then(
                fn(ResponseInterface $response) => [
                    'id'       => $id,
                    'response' => $response,
                ],
                fn (\Throwable $reason) => [
                    'id'    => $id,
                    'error' => $reason,
                ]
            );
        }

        return $promises;
    }


    /**
     * @param Promise ...$promises
     * @return ResponseInterface[]
     */
    private function extractResponses(Promise ...$promises): iterable
    {
        $results = [];
        foreach (Utils::settle($promises)->wait() as $item) {
            if (isset($item['value']['response'])) {
                $results[$item['value']['id']] = $item['value']['response'];
            }
            if (isset($item['value']['error'])) {
                $results[$item['value']['id']] = $item['value']['error'];
            }
        }

        return $results;
    }


    private function initGuzzleClient(array $config): GuzzleClientInterface
    {
        $defaultConfig = [
            'allow_redirects' => true,
            'http_errors'     => false,
            'handler'         => HandlerStack::create(new CurlMultiHandler()),
        ];

        $config = array_merge($defaultConfig, $config);

        return new GuzzleClient($config);
    }
}
