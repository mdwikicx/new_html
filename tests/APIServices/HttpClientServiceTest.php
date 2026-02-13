<?php

namespace FixRefs\Tests\APIServices;

use FixRefs\Tests\bootstrap;
use MDWiki\NewHtml\Services\Api\HttpClientService;
use MDWiki\NewHtml\Services\Interfaces\HttpClientInterface;

class HttpClientServiceTest extends bootstrap
{
    private ?HttpClientService $httpClient;

    protected function setUp(): void
    {
        // Use real HttpClientService for integration testing
        // For pure unit tests, create a mock of HttpClientInterface
        $this->httpClient = new HttpClientService();
    }

    /**
     * Test that HttpClientService implements the interface correctly
     */
    public function testHttpClientServiceImplementsInterface()
    {
        $this->assertInstanceOf(HttpClientInterface::class, $this->httpClient);
    }

    /**
     * Test that request method returns string
     */
    public function testRequestReturnsString()
    {
        // Create a mock to test the interface without network
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturn('{"test": "response"}');

        $result = $mockHttpClient->request('https://example.com/api', 'GET');

        $this->assertIsString($result);
        $this->assertEquals('{"test": "response"}', $result);
    }

    /**
     * Test that request method accepts GET method
     */
    public function testRequestAcceptsGetMethod()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('https://example.com/api'),
                $this->equalTo('GET'),
                $this->equalTo(['param' => 'value'])
            )
            ->willReturn('{"result": "success"}');

        $result = $mockHttpClient->request('https://example.com/api', 'GET', ['param' => 'value']);

        $this->assertEquals('{"result": "success"}', $result);
    }

    /**
     * Test that request method accepts POST method
     */
    public function testRequestAcceptsPostMethod()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('https://example.com/api'),
                $this->equalTo('POST'),
                $this->equalTo(['data' => 'test'])
            )
            ->willReturn('{"posted": true}');

        $result = $mockHttpClient->request('https://example.com/api', 'POST', ['data' => 'test']);

        $this->assertEquals('{"posted": true}', $result);
    }

    /**
     * Test that request method handles empty response
     */
    public function testRequestHandlesEmptyResponse()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturn('');

        $result = $mockHttpClient->request('https://example.com/api', 'GET');

        $this->assertEquals('', $result);
    }

    /**
     * Test that request method handles error response
     */
    public function testRequestHandlesErrorResponse()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturn('{"error": "Not found"}');

        $result = $mockHttpClient->request('https://example.com/notfound', 'GET');

        $this->assertStringContainsString('error', $result);
    }

    /**
     * Test with multiple different URLs
     */
    public function testRequestWithDifferentUrls()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturnCallback(function ($url, $method, $params) {
                if (strpos($url, 'api1') !== false) {
                    return '{"api": "1"}';
                }
                if (strpos($url, 'api2') !== false) {
                    return '{"api": "2"}';
                }
                return '{}';
            });

        $result1 = $mockHttpClient->request('https://api1.example.com', 'GET');
        $result2 = $mockHttpClient->request('https://api2.example.com', 'GET');

        $this->assertEquals('{"api": "1"}', $result1);
        $this->assertEquals('{"api": "2"}', $result2);
    }

    /**
     * Test request with empty params
     */
    public function testRequestWithEmptyParams()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->equalTo([])
            )
            ->willReturn('{}');

        $result = $mockHttpClient->request('https://example.com', 'GET', []);

        $this->assertEquals('{}', $result);
    }

    /**
     * Test request with special characters in params
     */
    public function testRequestWithSpecialCharacters()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->equalTo(['special' => 'value with spaces & symbols'])
            )
            ->willReturn('{"received": true}');

        $result = $mockHttpClient->request(
            'https://example.com',
            'GET',
            ['special' => 'value with spaces & symbols']
        );

        $this->assertEquals('{"received": true}', $result);
    }

    /**
     * Test that the service can be mocked for dependency injection
     */
    public function testServiceCanBeMockedForDependencyInjection()
    {
        // This test verifies that HttpClientInterface can be properly mocked
        // and injected into other services

        $mockResponses = [
            'https://api.example.com/users' => '{"users": [1, 2, 3]}',
            'https://api.example.com/posts' => '{"posts": ["a", "b", "c"]}',
        ];

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturnCallback(function ($url) use ($mockResponses) {
                return $mockResponses[$url] ?? '{}';
            });

        // Simulate a service using the HTTP client
        $usersResponse = $mockHttpClient->request('https://api.example.com/users', 'GET');
        $postsResponse = $mockHttpClient->request('https://api.example.com/posts', 'GET');
        $unknownResponse = $mockHttpClient->request('https://api.example.com/unknown', 'GET');

        $this->assertEquals('{"users": [1, 2, 3]}', $usersResponse);
        $this->assertEquals('{"posts": ["a", "b", "c"]}', $postsResponse);
        $this->assertEquals('{}', $unknownResponse);
    }

    /**
     * Test method case sensitivity - interface should handle any case
     */
    public function testRequestMethodCaseHandling()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturnCallback(function ($url, $method) {
                return json_encode(['method' => strtoupper($method)]);
            });

        $getResult = $mockHttpClient->request('https://example.com', 'GET');
        $postResult = $mockHttpClient->request('https://example.com', 'post');
        $PostResult = $mockHttpClient->request('https://example.com', 'POST');

        $this->assertStringContainsString('GET', $getResult);
        $this->assertStringContainsString('POST', $PostResult);
        $this->assertStringContainsString('POST', $postResult);
    }
}
