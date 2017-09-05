<?php
namespace juniorb2ss\LaravelExtendedValidationRules\Tests;

use PHPUnit_Framework_TestCase as BaseTestCase;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    protected $contentToHttpResponse = '{}';

    protected function middlewareResponse(callable $handler = null)
    {
        return (Middleware::mapResponse(function (ResponseInterface $response) use ($handler) {
            $response = $response->withStatus(200);
            $response = ($handler !== null) ? $handler($response) : $response;
            return $response;
        }));
    }

    protected function clientMock(string $body, callable $handlerResponse = null)
    {
        $response = new MockHandler([
            new GuzzleResponse(200, ['Content-Type' => 'application/json; charset=utf8'], $body)
        ]);
        $handlerStack = HandlerStack::create($response);
        $middleware = $this->middlewareResponse($handlerResponse);
        $handlerStack->push($middleware);
        $guzzle = new GuzzleClient([
            'handler' => $handlerStack
        ]);
        return $guzzle;
    }

    /**
     *
     * @param  string $content content or filename
     * @return string content
     */
    protected function getStubContent($content)
    {
        $base_path = dirname(__FILE__) . '/stubs/';
        if (is_file($base_path . $content)) {
            return file_get_contents($base_path . $content);
        }
        return $content;
    }
}
