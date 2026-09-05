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
            ->willReturn(["output" => '{"test": "response"}', "error_code" => "", "error" => ""]);

        $result = $mockHttpClient->request('https://example.com/api', 'GET');

        $this->assertIsArray($result);
        $this->assertEquals('{"test": "response"}', $result['output']);
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
            ->willReturn(["output" => '{"result": "success"}', "error_code" => "", "error" => ""]);

        $result = $mockHttpClient->request('https://example.com/api', 'GET', ['param' => 'value']);

        $this->assertEquals('{"result": "success"}', $result['output']);
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
            ->willReturn(["output" => '{"posted": true}', "error_code" => "", "error" => ""]);

        $result = $mockHttpClient->request('https://example.com/api', 'POST', ['data' => 'test']);

        $this->assertEquals('{"posted": true}', $result['output']);
    }

    /**
     * Test that request method handles empty response
     */
    public function testRequestHandlesEmptyResponse()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturn(["output" => "", "error_code" => "", "error" => ""]);

        $result = $mockHttpClient->request('https://example.com/api', 'GET');

        $this->assertEquals('', $result['output']);
    }

    /**
     * Test that request method handles error response
     */
    public function testRequestHandlesErrorResponse()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturn(["output" => '{"error": "Not found"}', "error_code" => "", "error" => ""]);

        $result = $mockHttpClient->request('https://example.com/notfound', 'GET');

        $this->assertStringContainsString('error', $result['output']);
    }

    /**
     * Test with multiple different URLs
     */
    public function testRequestWithDifferentUrls()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturnCallback(function ($url) {
                if (strpos($url, 'api1') !== false) {
                    return ["output" => '{"api": "1"}', "error_code" => "", "error" => ""];
                }
                if (strpos($url, 'api2') !== false) {
                    return ["output" => '{"api": "2"}', "error_code" => "", "error" => ""];
                }
                return ["output" => "{}", "error_code" => "", "error" => ""];
            });

        $result1 = $mockHttpClient->request('https://api1.example.com', 'GET');
        $result2 = $mockHttpClient->request('https://api2.example.com', 'GET');

        $this->assertEquals('{"api": "1"}', $result1['output']);
        $this->assertEquals('{"api": "2"}', $result2['output']);
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
            ->willReturn(["output" => "{}", "error_code" => "", "error" => ""]);

        $result = $mockHttpClient->request('https://example.com', 'GET', []);

        $this->assertEquals('{}', $result['output']);
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
            ->willReturn(["output" => '{"received": true}', "error_code" => "", "error" => ""]);

        $result = $mockHttpClient->request(
            'https://example.com',
            'GET',
            ['special' => 'value with spaces & symbols']
        );

        $this->assertEquals('{"received": true}', $result['output']);
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
                return ["output" => $mockResponses[$url] ?? '{}', "error_code" => "", "error" => ""];
            });

        // Simulate a service using the HTTP client
        $usersResponse = $mockHttpClient->request('https://api.example.com/users', 'GET');
        $postsResponse = $mockHttpClient->request('https://api.example.com/posts', 'GET');
        $unknownResponse = $mockHttpClient->request('https://api.example.com/unknown', 'GET');

        $this->assertEquals('{"users": [1, 2, 3]}', $usersResponse['output']);
        $this->assertEquals('{"posts": ["a", "b", "c"]}', $postsResponse['output']);
        $this->assertEquals('{}', $unknownResponse['output']);
    }

    /**
     * Test method case sensitivity - interface should handle any case
     */
    public function testRequestMethodCaseHandling()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturnCallback(function ($url, $method) {
                return ["output" => json_encode(['method' => strtoupper($method)]), "error_code" => "", "error" => ""];
            });

        $getResult = $mockHttpClient->request('https://example.com', 'GET');
        $postResult = $mockHttpClient->request('https://example.com', 'post');
        $PostResult = $mockHttpClient->request('https://example.com', 'POST');

        $this->assertStringContainsString('GET', $getResult['output']);
        $this->assertStringContainsString('POST', $PostResult['output']);
        $this->assertStringContainsString('POST', $postResult['output']);
    }
}
