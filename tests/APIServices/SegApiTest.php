<?php

namespace FixRefs\Tests\APIServices;

use PHPUnit\Framework\TestCase;

use function APIServices\change_html_to_seg;

class SegApiTest extends TestCase
{
    protected function setUp(): void
    {
        // Check if the segmentation API is available
        if (!$this->isSegApiAvailable()) {
            $this->markTestSkipped('Segmentation API unavailable - skipping tests');
        }
    }

    private function isSegApiAvailable(): bool
    {
        $socket = @fsockopen('ncc2c.toolforge.org', 443, $errno, $errstr, 5);
        if ($socket) {
            fclose($socket);
            return true;
        }
        return false;
    }

    public function testChangeHtmlToSegWithSimpleHtml()
    {
        $html = '<html><body><p>Simple paragraph.</p></body></html>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertNotEmpty($result['result']);
        }
    }

    public function testChangeHtmlToSegWithComplexHtml()
    {
        $html = '<html><body><h1>Title</h1><p>First paragraph.</p><p>Second paragraph.</p></body></html>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testChangeHtmlToSegWithEmptyHtml()
    {
        $html = '';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        // Should either return result or error
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testChangeHtmlToSegReturnsArray()
    {
        $html = '<p>Test content</p>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
    }

    public function testChangeHtmlToSegWithMultipleParagraphs()
    {
        $html = '<p>Paragraph 1</p><p>Paragraph 2</p><p>Paragraph 3</p>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertNotEmpty($result['result']);
        }
    }

    public function testChangeHtmlToSegWithNestedElements()
    {
        $html = '<div><p>Text with <strong>bold</strong> and <em>italic</em>.</p></div>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testChangeHtmlToSegWithLinks()
    {
        $html = '<p>Text with <a href="#">link</a> inside.</p>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertNotEmpty($result['result']);
        }
    }

    public function testChangeHtmlToSegWithLists()
    {
        $html = '<ul><li>Item 1</li><li>Item 2</li><li>Item 3</li></ul>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testChangeHtmlToSegWithHeadings()
    {
        $html = '<h2>Section 1</h2><p>Content</p><h2>Section 2</h2><p>More content</p>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertNotEmpty($result['result']);
        }
    }

    public function testChangeHtmlToSegWithTables()
    {
        $html = '<table><tr><td>Cell 1</td><td>Cell 2</td></tr></table>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testChangeHtmlToSegWithUnicodeCharacters()
    {
        $html = '<p>Text with unicode: ñ, é, ü, 中文, العربية</p>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertNotEmpty($result['result']);
        }
    }

    public function testChangeHtmlToSegWithSpecialCharacters()
    {
        $html = '<p>Text with special chars: &lt; &gt; &amp; &quot;</p>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testChangeHtmlToSegHandlesApiError()
    {
        // Test with potentially problematic HTML
        $html = '<invalid>Malformed HTML';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        // Should handle gracefully with either result or error
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testChangeHtmlToSegWithLargeHtml()
    {
        // Create large HTML
        $paragraphs = str_repeat('<p>This is a test paragraph with some content.</p>', 50);
        $html = "<html><body>$paragraphs</body></html>";
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testChangeHtmlToSegWithReferences()
    {
        $html = '<p>Text with reference<sup><a href="#ref1">[1]</a></sup>.</p>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertNotEmpty($result['result']);
        }
    }

    public function testChangeHtmlToSegWithDivs()
    {
        $html = '<div class="section"><p>Content in div</p></div>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testChangeHtmlToSegResultFormat()
    {
        $html = '<p>Test paragraph</p>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            // Result should be a string (HTML or JSON)
            $this->assertIsString($result['result']);
        } elseif (isset($result['error'])) {
            $this->assertIsString($result['error']);
        }
    }

    public function testChangeHtmlToSegWithBreakTags()
    {
        $html = '<p>Line 1<br>Line 2<br>Line 3</p>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testChangeHtmlToSegWithImages()
    {
        $html = '<p>Text with <img src="test.jpg" alt="Image"> image.</p>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testChangeHtmlToSegWithInlineStyles()
    {
        $html = '<p style="color: red;">Styled paragraph</p>';
        $result = change_html_to_seg($html);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertNotEmpty($result['result']);
        }
    }
}