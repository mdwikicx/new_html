<?php

namespace FixRefs\Tests\APIServices;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Services\Api\convert_wikitext_to_html;


class TransformApiTest extends bootstrap
{
    protected function setUp(): void
    {
        $this->markTestSkipped('skipping newwork tests for now');
        // Check if Wikipedia API is available
        if (!$this->isWikipediaApiAvailable()) {
            $this->markTestSkipped('Wikipedia Transform API unavailable - skipping tests');
        }
    }

    private function isWikipediaApiAvailable(): bool
    {
        $socket = @fsockopen('en.wikipedia.org', 443, $errno, $errstr, 5);
        if ($socket) {
            fclose($socket);
            return true;
        }
        return false;
    }

    public function testConvertWikitextToHtmlWithSimpleText()
    {
        $wikitext = "Simple paragraph.";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('Simple paragraph', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithBoldText()
    {
        $wikitext = "'''Bold text'''";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('Bold text', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithItalicText()
    {
        $wikitext = "''Italic text''";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('Italic text', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithLinks()
    {
        $wikitext = "[[Article]]";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('Article', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithHeading()
    {
        $wikitext = "==Heading==\nContent";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('Heading', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithTemplate()
    {
        $wikitext = "{{cite web|url=http://example.com|title=Example}}";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testConvertWikitextToHtmlWithEmptyText()
    {
        $wikitext = "";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        // Empty wikitext might return error or empty result
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testConvertWikitextToHtmlReturnsArray()
    {
        $wikitext = "Test content";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
    }

    public function testConvertWikitextToHtmlWithList()
    {
        $wikitext = "* Item 1\n* Item 2\n* Item 3";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('Item 1', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithNumberedList()
    {
        $wikitext = "# First\n# Second\n# Third";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('First', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithReferences()
    {
        $wikitext = "Text with citation.<ref>Reference content</ref>";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('citation', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithTitleSlash()
    {
        $wikitext = "Content";
        $title = "Test/Subpage";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        // Should handle slashes in title
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testConvertWikitextToHtmlWithComplexWikitext()
    {
        $wikitext = "==Section==\n'''Bold''' and ''italic''.\n* List item\n[[Link]]";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertNotEmpty($result['result']);
            $this->assertStringContainsString('<html', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlReturnsHtml()
    {
        $wikitext = "Simple text";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('<html', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithUnicodeCharacters()
    {
        $wikitext = "Text with unicode: ñ, é, ü, 中文";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertNotEmpty($result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithTable()
    {
        $wikitext = "{|\n|Cell 1||Cell 2\n|-\n|Cell 3||Cell 4\n|}";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('Cell', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithExternalLink()
    {
        $wikitext = "[http://example.com Example]";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('Example', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithCategory()
    {
        $wikitext = "Content [[Category:Test]]";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('Content', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlWithImage()
    {
        $wikitext = "[[File:Example.jpg|thumb|Caption]]";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        $this->assertTrue(isset($result['result']) || isset($result['error']));
    }

    public function testConvertWikitextToHtmlWithMultipleParagraphs()
    {
        $wikitext = "Paragraph 1\n\nParagraph 2\n\nParagraph 3";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertStringContainsString('Paragraph', $result['result']);
        }
    }

    public function testConvertWikitextToHtmlResultFormat()
    {
        $wikitext = "Test";
        $title = "Test";
        $result = convert_wikitext_to_html($wikitext, $title);

        $this->assertIsArray($result);
        if (isset($result['result'])) {
            $this->assertIsString($result['result']);
        } elseif (isset($result['error'])) {
            $this->assertIsString($result['error']);
        }
    }
}
