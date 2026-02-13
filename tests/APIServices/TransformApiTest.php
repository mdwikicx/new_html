<?php

namespace FixRefs\Tests\APIServices;

use FixRefs\Tests\bootstrap;
use MDWiki\NewHtml\Services\Api\TransformApiService;
use MDWiki\NewHtml\Services\Interfaces\HttpClientInterface;

class TransformApiTest extends bootstrap
{
    private ?TransformApiService $service;
    private ?HttpClientInterface $mockHttpClient;

    protected function setUp(): void
    {
        // Create a mock HTTP client
        $this->mockHttpClient = $this->createMock(HttpClientInterface::class);
        $this->service = new TransformApiService($this->mockHttpClient);
    }

    /**
     * Helper method to setup mock response
     *
     * @param string $response The HTML response to return
     */
    private function setupMockResponse(string $response): void
    {
        $this->mockHttpClient
            ->method('request')
            ->willReturn($response);
    }

    /**
     * Helper to create a successful API response (valid HTML)
     *
     * @param string $content The HTML content
     * @return string HTML response
     */
    private function createSuccessResponse(string $content): string
    {
        return '<html><body>' . $content . '</body></html>';
    }

    /**
     * Helper to create an error response (Wikimedia Error)
     *
     * @return string Error HTML response
     */
    private function createErrorResponse(): string
    {
        return '<html><body>Wikimedia Error</body></html>';
    }

    public function testConvertWikitextToHtmlWithSimpleText()
    {
        $wikitext = "Simple paragraph.";
        $title = "Test";
        $expectedHtml = '<p>Simple paragraph.</p>';

        $this->setupMockResponse($this->createSuccessResponse($expectedHtml));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('Simple paragraph', $result['result']);
    }

    public function testConvertWikitextToHtmlWithBoldText()
    {
        $wikitext = "'''Bold text'''";
        $title = "Test";
        $expectedHtml = '<p><b>Bold text</b></p>';

        $this->setupMockResponse($this->createSuccessResponse($expectedHtml));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('Bold text', $result['result']);
    }

    public function testConvertWikitextToHtmlWithItalicText()
    {
        $wikitext = "''Italic text''";
        $title = "Test";
        $expectedHtml = '<p><i>Italic text</i></p>';

        $this->setupMockResponse($this->createSuccessResponse($expectedHtml));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('Italic text', $result['result']);
    }

    public function testConvertWikitextToHtmlWithLinks()
    {
        $wikitext = "[[Article]]";
        $title = "Test";
        $expectedHtml = '<a href="/wiki/Article">Article</a>';

        $this->setupMockResponse($this->createSuccessResponse($expectedHtml));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('Article', $result['result']);
    }

    public function testConvertWikitextToHtmlWithHeading()
    {
        $wikitext = "==Heading==\nContent";
        $title = "Test";
        $expectedHtml = '<h2>Heading</h2><p>Content</p>';

        $this->setupMockResponse($this->createSuccessResponse($expectedHtml));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('Heading', $result['result']);
    }

    public function testConvertWikitextToHtmlWithTemplate()
    {
        $wikitext = "{{cite web|url=http://example.com|title=Example}}";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse('<span class="cite">Example</span>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testConvertWikitextToHtmlWithEmptyText()
    {
        $wikitext = "";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse(''));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testConvertWikitextToHtmlReturnsArray()
    {
        $wikitext = "Test content";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse('<p>Test content</p>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
    }

    public function testConvertWikitextToHtmlWithList()
    {
        $wikitext = "* Item 1\n* Item 2\n* Item 3";
        $title = "Test";
        $expectedHtml = '<ul><li>Item 1</li><li>Item 2</li><li>Item 3</li></ul>';

        $this->setupMockResponse($this->createSuccessResponse($expectedHtml));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('Item 1', $result['result']);
    }

    public function testConvertWikitextToHtmlWithNumberedList()
    {
        $wikitext = "# First\n# Second\n# Third";
        $title = "Test";
        $expectedHtml = '<ol><li>First</li><li>Second</li><li>Third</li></ol>';

        $this->setupMockResponse($this->createSuccessResponse($expectedHtml));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('First', $result['result']);
    }

    public function testConvertWikitextToHtmlWithReferences()
    {
        $wikitext = "Text with citation.<ref>Reference content</ref>";
        $title = "Test";
        $expectedHtml = '<p>Text with citation.<sup class="reference">[1]</sup></p>';

        $this->setupMockResponse($this->createSuccessResponse($expectedHtml));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('citation', $result['result']);
    }

    public function testConvertWikitextToHtmlWithTitleSlash()
    {
        $wikitext = "Content";
        $title = "Test/Subpage";

        // Mock should receive the encoded title
        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->stringContains('Test%2FSubpage'),
                $this->equalTo('POST'),
                $this->anything()
            )
            ->willReturn($this->createSuccessResponse('<p>Content</p>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testConvertWikitextToHtmlWithComplexWikitext()
    {
        $wikitext = "==Section==\n'''Bold''' and ''italic''.\n* List item\n[[Link]]";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse('<h2>Section</h2><p><b>Bold</b> and <i>italic</i>.</p><ul><li>List item</li></ul><a href="/wiki/Link">Link</a>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertNotEmpty($result['result']);
        $this->assertStringContainsString('<html', $result['result']);
    }

    public function testConvertWikitextToHtmlReturnsHtml()
    {
        $wikitext = "Simple text";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse('<p>Simple text</p>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('<html', $result['result']);
    }

    public function testConvertWikitextToHtmlWithUnicodeCharacters()
    {
        $wikitext = "Text with unicode: ñ, é, ü, 中文";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse('<p>Text with unicode: ñ, é, ü, 中文</p>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertNotEmpty($result['result']);
    }

    public function testConvertWikitextToHtmlWithTable()
    {
        $wikitext = "{|\n|Cell 1||Cell 2\n|-\n|Cell 3||Cell 4\n|}";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse('<table><tr><td>Cell 1</td><td>Cell 2</td></tr><tr><td>Cell 3</td><td>Cell 4</td></tr></table>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('Cell', $result['result']);
    }

    public function testConvertWikitextToHtmlWithExternalLink()
    {
        $wikitext = "[http://example.com Example]";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse('<a href="http://example.com">Example</a>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('Example', $result['result']);
    }

    public function testConvertWikitextToHtmlWithCategory()
    {
        $wikitext = "Content [[Category:Test]]";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse('<p>Content </p>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('Content', $result['result']);
    }

    public function testConvertWikitextToHtmlWithImage()
    {
        $wikitext = "[[File:Example.jpg|thumb|Caption]]";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse('<figure><img src="Example.jpg" /><figcaption>Caption</figcaption></figure>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
    }

    public function testConvertWikitextToHtmlWithMultipleParagraphs()
    {
        $wikitext = "Paragraph 1\n\nParagraph 2\n\nParagraph 3";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse('<p>Paragraph 1</p><p>Paragraph 2</p><p>Paragraph 3</p>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('Paragraph', $result['result']);
    }

    public function testConvertWikitextToHtmlResultFormat()
    {
        $wikitext = "Test";
        $title = "Test";

        $this->setupMockResponse($this->createSuccessResponse('<p>Test</p>'));

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertIsString($result['result']);
    }

    public function testConvertWikitextToHtmlHandlesEmptyResponse()
    {
        $wikitext = "Test";
        $title = "Test";

        $this->mockHttpClient
            ->method('request')
            ->willReturn('');

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Could not reach API', $result['error']);
    }

    public function testConvertWikitextToHtmlHandlesWikimediaError()
    {
        $wikitext = "Test";
        $title = "Test";

        $this->mockHttpClient
            ->method('request')
            ->willReturn('<html><body>Wikimedia Error</body></html>');

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Wikipedia API returned an error', $result['error']);
    }

    public function testConvertWikitextToHtmlHandlesInvalidHtml()
    {
        $wikitext = "Test";
        $title = "Test";

        $this->mockHttpClient
            ->method('request')
            ->willReturn('Not valid HTML');

        $result = $this->service->convertWikitextToHtml($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('invalid HTML', $result['error']);
    }
}
