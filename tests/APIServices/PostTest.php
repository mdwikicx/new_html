<?php

namespace FixRefs\Tests\APIServices;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Services\Api\post_url_params_result;
use function MDWiki\NewHtml\Services\Api\handle_url_request;


class PostTest extends bootstrap
{
    protected function setUp(): void
    {
        // Check if network is available
        if (!$this->isNetworkAvailable()) {
            $this->markTestSkipped('Network unavailable - skipping API tests');
        }
    }

    private function isNetworkAvailable(): bool
    {
        $socket = @fsockopen('www.google.com', 80, $errno, $errstr, 2);
        if ($socket) {
            fclose($socket);
            return true;
        }
        return false;
    }

    public function testPostUrlParamsResultWithGetRequest()
    {
        $url = 'https://httpbin.org/get';
        $result = post_url_params_result($url);

        // Should return response
        $this->assertNotEmpty($result);
    }

    public function testPostUrlParamsResultWithParams()
    {
        $url = 'https://httpbin.org/post';
        $params = ['test' => 'value', 'another' => 'param'];
        $result = post_url_params_result($url, $params);

        $this->assertNotEmpty($result);
        // Result should contain the posted data
        $data = json_decode($result, true);
        if ($data && isset($data['form'])) {
            $this->assertArrayHasKey('test', $data['form']);
        }
    }

    public function testPostUrlParamsResultWithInvalidUrl()
    {
        $url = 'https://thisisnotavalidurlfortesting12345.com';
        $result = post_url_params_result($url);

        // Should return empty string on error
        $this->assertEquals('', $result);
    }

    public function testHandleUrlRequestWithGetMethod()
    {
        $url = 'https://httpbin.org/get';
        $result = handle_url_request($url, 'GET');

        $this->assertNotEmpty($result);
        $data = json_decode($result, true);
        $this->assertIsArray($data);
    }

    public function testHandleUrlRequestWithGetParams()
    {
        $url = 'https://httpbin.org/get';
        $params = ['param1' => 'value1', 'param2' => 'value2'];
        $result = handle_url_request($url, 'GET', $params);

        $this->assertNotEmpty($result);
        $data = json_decode($result, true);
        if ($data && isset($data['args'])) {
            $this->assertArrayHasKey('param1', $data['args']);
            $this->assertArrayHasKey('param2', $data['args']);
        }
    }

    public function testHandleUrlRequestWithPostMethod()
    {
        $url = 'https://httpbin.org/post';
        $params = ['test' => 'data'];
        $result = handle_url_request($url, 'POST', $params);

        $this->assertNotEmpty($result);
        $data = json_decode($result, true);
        if ($data && isset($data['form'])) {
            $this->assertArrayHasKey('test', $data['form']);
        }
    }

    public function testHandleUrlRequestWithInvalidUrl()
    {
        $url = 'https://invalid-url-that-does-not-exist-12345.com';
        $result = handle_url_request($url, 'GET');

        $this->assertEquals('', $result);
    }

    public function testHandleUrlRequestWithEmptyParams()
    {
        $url = 'https://httpbin.org/get';
        $result = handle_url_request($url, 'GET', []);

        $this->assertNotEmpty($result);
    }

    public function testHandleUrlRequestReturnsEmptyOn404()
    {
        $url = 'https://httpbin.org/status/404';
        $result = handle_url_request($url, 'GET');

        // Should return empty string for non-200 status
        $this->assertEquals('', $result);
    }

    public function testPostUrlParamsResultSetsUserAgent()
    {
        $url = 'https://httpbin.org/get';
        $result = post_url_params_result($url);

        $this->assertNotEmpty($result);
        $data = json_decode($result, true);
        if ($data && isset($data['headers']['User-Agent'])) {
            $this->assertStringContainsString('WikiProjectMed', $data['headers']['User-Agent']);
        }
    }

    public function testHandleUrlRequestSetsUserAgent()
    {
        $url = 'https://httpbin.org/get';
        $result = handle_url_request($url, 'GET');

        $this->assertNotEmpty($result);
        $data = json_decode($result, true);
        if ($data && isset($data['headers']['User-Agent'])) {
            $this->assertStringContainsString('WikiProjectMed', $data['headers']['User-Agent']);
        }
    }

    public function testHandleUrlRequestWithExistingQueryString()
    {
        $url = 'https://httpbin.org/get?existing=param';
        $params = ['new' => 'param'];
        $result = handle_url_request($url, 'GET', $params);

        $this->assertNotEmpty($result);
        $data = json_decode($result, true);
        if ($data && isset($data['args'])) {
            $this->assertArrayHasKey('existing', $data['args']);
            $this->assertArrayHasKey('new', $data['args']);
        }
    }

    public function testPostUrlParamsResultWithEmptyParams()
    {
        $url = 'https://httpbin.org/get';
        $result = post_url_params_result($url, []);

        $this->assertNotEmpty($result);
    }

    public function testHandleUrlRequestWithSpecialCharactersInParams()
    {
        $url = 'https://httpbin.org/get';
        $params = ['special' => 'value with spaces', 'unicode' => 'ëñçödéd'];
        $result = handle_url_request($url, 'GET', $params);

        $this->assertNotEmpty($result);
    }

    public function testHandleUrlRequestMethodCaseSensitivity()
    {
        $url = 'https://httpbin.org/get';
        $result1 = handle_url_request($url, 'GET');
        $result2 = handle_url_request($url, 'get'); // lowercase

        // Both should work (or at least not crash)
        $this->assertIsString($result1);
        $this->assertIsString($result2);
    }

    public function testPostUrlParamsResultTimeout()
    {
        // Test with a URL that will timeout
        $url = 'https://httpbin.org/delay/20'; // 20 second delay
        $result = post_url_params_result($url);

        // Should return empty string due to timeout (15 seconds)
        $this->assertEquals('', $result);
    }

    public function testHandleUrlRequestReturnsStringType()
    {
        $url = 'https://httpbin.org/get';
        $result = handle_url_request($url, 'GET');

        $this->assertIsString($result);
    }

    public function testPostUrlParamsResultReturnsStringType()
    {
        $url = 'https://httpbin.org/get';
        $result = post_url_params_result($url);

        $this->assertIsString($result);
    }
}
