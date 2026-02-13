<?php

namespace FixRefs\Tests\APIServices;

use FixRefs\Tests\bootstrap;
use MDWiki\NewHtml\Services\Api\SegmentApiService;
use MDWiki\NewHtml\Services\Interfaces\HttpClientInterface;

class SegApiTest extends bootstrap
{
    private ?SegmentApiService $service;
    private ?HttpClientInterface $mockHttpClient;

    protected function setUp(): void
    {
        // Create a mock HTTP client
        $this->mockHttpClient = $this->createMock(HttpClientInterface::class);
        $this->service = new SegmentApiService($this->mockHttpClient);
    }

    /**
     * Helper method to setup mock response
     *
     * @param string $response The JSON response to return
     */
    private function setupMockResponse(string $response): void
    {
        $this->mockHttpClient
            ->method('request')
            ->willReturn($response);
    }

    /**
     * Helper to create a successful API response
     *
     * @param string $result The result HTML/content
     * @return string JSON encoded response
     */
    private function createSuccessResponse(string $result): string
    {
        return json_encode(['result' => $result]);
    }

    /**
     * Helper to create an error API response
     *
     * @param string $error The error message
     * @return string JSON encoded response
     */
    private function createErrorResponse(string $error): string
    {
        return json_encode(['error' => $error]);
    }

    public function testChangeHtmlToSegWithSimpleHtml()
    {
        $html = '<html><body><p>Simple paragraph.</p></body></html>';
        $expectedResult = '<seg>Simple paragraph.</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertEquals($expectedResult, $result['result']);
    }

    public function testChangeHtmlToSegWithComplexHtml()
    {
        $html = '<html><body><h1>Title</h1><p>First paragraph.</p><p>Second paragraph.</p></body></html>';
        $expectedResult = '<seg>Title</seg><seg>First paragraph.</seg><seg>Second paragraph.</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertEquals($expectedResult, $result['result']);
    }

    public function testChangeHtmlToSegWithEmptyHtml()
    {
        $html = '';
        $expectedResult = '';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertEquals($expectedResult, $result['result']);
    }

    public function testChangeHtmlToSegReturnsArray()
    {
        $html = '<p>Test content</p>';

        $this->setupMockResponse($this->createSuccessResponse('<seg>Test content</seg>'));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testChangeHtmlToSegWithMultipleParagraphs()
    {
        $html = '<p>Paragraph 1</p><p>Paragraph 2</p><p>Paragraph 3</p>';
        $expectedResult = '<seg>Paragraph 1</seg><seg>Paragraph 2</seg><seg>Paragraph 3</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertNotEmpty($result['result']);
    }

    public function testChangeHtmlToSegWithNestedElements()
    {
        $html = '<div><p>Text with <strong>bold</strong> and <em>italic</em>.</p></div>';
        $expectedResult = '<seg>Text with <strong>bold</strong> and <em>italic</em>.</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testChangeHtmlToSegWithLinks()
    {
        $html = '<p>Text with <a href="#">link</a> inside.</p>';
        $expectedResult = '<seg>Text with <a href="#">link</a> inside.</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertNotEmpty($result['result']);
    }

    public function testChangeHtmlToSegWithLists()
    {
        $html = '<ul><li>Item 1</li><li>Item 2</li><li>Item 3</li></ul>';
        $expectedResult = '<seg>Item 1</seg><seg>Item 2</seg><seg>Item 3</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testChangeHtmlToSegWithHeadings()
    {
        $html = '<h2>Section 1</h2><p>Content</p><h2>Section 2</h2><p>More content</p>';
        $expectedResult = '<seg>Section 1</seg><seg>Content</seg><seg>Section 2</seg><seg>More content</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertNotEmpty($result['result']);
    }

    public function testChangeHtmlToSegWithTables()
    {
        $html = '<table><tr><td>Cell 1</td><td>Cell 2</td></tr></table>';
        $expectedResult = '<seg>Cell 1</seg><seg>Cell 2</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testChangeHtmlToSegWithUnicodeCharacters()
    {
        $html = '<p>Text with unicode: ñ, é, ü, 中文, العربية</p>';
        $expectedResult = '<seg>Text with unicode: ñ, é, ü, 中文, العربية</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertNotEmpty($result['result']);
    }

    public function testChangeHtmlToSegWithSpecialCharacters()
    {
        $html = '<p>Text with special chars: < > & "</p>';
        $expectedResult = '<seg>Text with special chars: < > & "</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testChangeHtmlToSegHandlesApiError()
    {
        $html = '<invalid>Malformed HTML';
        $errorMessage = 'Invalid HTML structure';

        $this->setupMockResponse($this->createErrorResponse($errorMessage));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString($errorMessage, $result['error']);
    }

    public function testChangeHtmlToSegHandlesNetworkError()
    {
        $html = '<p>Test content</p>';

        // Simulate network failure (empty response)
        $this->mockHttpClient
            ->method('request')
            ->willReturn('');

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Could not reach API', $result['error']);
    }

    public function testChangeHtmlToSegHandlesUnexpectedResponseFormat()
    {
        $html = '<p>Test content</p>';

        // Simulate unexpected response format (missing both result and error)
        $this->mockHttpClient
            ->method('request')
            ->willReturn(json_encode(['unexpected_key' => 'value']));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Unexpected response format', $result['error']);
    }

    public function testChangeHtmlToSegWithLargeHtml()
    {
        $paragraphs = str_repeat('<p>This is a test paragraph with some content.</p>', 50);
        $html = "<html><body>$paragraphs</body></html>";
        $expectedResult = '<seg>Large content</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testChangeHtmlToSegWithReferences()
    {
        $html = '<p>Text with reference<sup><a href="#ref1">[1]</a></sup>.</p>';
        $expectedResult = '<seg>Text with reference<sup><a href="#ref1">[1]</a></sup>.</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertNotEmpty($result['result']);
    }

    public function testChangeHtmlToSegWithDivs()
    {
        $html = '<div class="section"><p>Content in div</p></div>';
        $expectedResult = '<seg>Content in div</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testChangeHtmlToSegResultFormat()
    {
        $html = '<p>Test paragraph</p>';
        $expectedResult = '<seg>Test paragraph</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertIsString($result['result']);
    }

    public function testChangeHtmlToSegWithBreakTags()
    {
        $html = '<p>Line 1<br>Line 2<br>Line 3</p>';
        $expectedResult = '<seg>Line 1<br>Line 2<br>Line 3</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testChangeHtmlToSegWithImages()
    {
        $html = '<p>Text with <img src="test.jpg" alt="Image"> image.</p>';
        $expectedResult = '<seg>Text with <img src="test.jpg" alt="Image"> image.</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testChangeHtmlToSegWithInlineStyles()
    {
        $html = '<p style="color: red;">Styled paragraph</p>';
        $expectedResult = '<seg style="color: red;">Styled paragraph</seg>';

        $this->setupMockResponse($this->createSuccessResponse($expectedResult));

        $result = $this->service->changeHtmlToSeg($html);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertNotEmpty($result['result']);
    }
}
