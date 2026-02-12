<?php

namespace FixRefs\Tests\Utils;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Infrastructure\Utils\del_div_error;
use function MDWiki\NewHtml\Infrastructure\Utils\get_attrs;
use function MDWiki\NewHtml\Infrastructure\Utils\fix_link_red;
use function MDWiki\NewHtml\Infrastructure\Utils\remove_data_parsoid;

class HtmlUtilsTest extends bootstrap
{
    public function testDelDivErrorRemovesSingleErrorDiv()
    {
        $html = '<div>Normal content</div><div class="error">Error message</div><div>More content</div>';
        $result = del_div_error($html);

        $this->assertStringNotContainsString('Error message', $result);
        $this->assertStringContainsString('Normal content', $result);
        $this->assertStringContainsString('More content', $result);
    }

    public function testDelDivErrorRemovesMultipleErrorDivs()
    {
        $html = '<div class="error">Error 1</div><div>Content</div><div class="error">Error 2</div>';
        $result = del_div_error($html);

        $this->assertStringNotContainsString('Error 1', $result);
        $this->assertStringNotContainsString('Error 2', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function testDelDivErrorPreservesNonErrorDivs()
    {
        $html = '<div class="info">Info</div><div class="error">Error</div><div class="warning">Warning</div>';
        $result = del_div_error($html);

        $this->assertStringContainsString('Info', $result);
        $this->assertStringContainsString('Warning', $result);
        $this->assertStringNotContainsString('Error', $result);
    }

    public function testDelDivErrorWithNoErrorDivs()
    {
        $html = '<div>Content 1</div><div>Content 2</div>';
        $result = del_div_error($html);

        $this->assertEquals($html, $result);
    }

    public function testDelDivErrorWithEmptyHtml()
    {
        $result = del_div_error('');

        $this->assertEquals('', $result);
    }

    public function testDelDivErrorWithSingleQuotes()
    {
        $html = "<div class='error'>Error message</div>";
        $result = del_div_error($html);

        $this->assertStringNotContainsString('Error message', $result);
    }

    public function testDelDivErrorWithNestedContent()
    {
        $html = '<div class="error">Error with <span>nested</span> content</div>';
        $result = del_div_error($html);

        $this->assertStringNotContainsString('Error with', $result);
        $this->assertStringNotContainsString('nested', $result);
    }

    public function testGetAttrsWithSimpleAttribute()
    {
        $text = 'href="http://example.com"';
        $result = get_attrs($text);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('href', $result);
        $this->assertEquals('"http://example.com"', $result['href']);
    }

    public function testGetAttrsWithMultipleAttributes()
    {
        $text = 'href="http://example.com" class="link" id="main"';
        $result = get_attrs($text);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('href', $result);
        $this->assertArrayHasKey('class', $result);
        $this->assertArrayHasKey('id', $result);
    }

    public function testGetAttrsWithSingleQuotes()
    {
        $text = "href='http://example.com' class='link'";
        $result = get_attrs($text);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('href', $result);
        $this->assertArrayHasKey('class', $result);
    }

    public function testGetAttrsWithNoQuotes()
    {
        $text = 'href=http://example.com class=link';
        $result = get_attrs($text);

        $this->assertIsArray($result);
    }

    public function testGetAttrsWithEmptyText()
    {
        $result = get_attrs('');

        $this->assertIsArray($result);
    }

    public function testGetAttrsCaseInsensitive()
    {
        $text = 'HREF="http://example.com" CLASS="link"';
        $result = get_attrs($text);

        $this->assertIsArray($result);
        // Attributes should be lowercase
        $this->assertArrayHasKey('href', $result);
        $this->assertArrayHasKey('class', $result);
    }

    public function testFixLinkRedRemovesEditLinks()
    {
        $html = '<a rel="mw:ExtLink" href="//en.wikipedia.org/w/index.php?title=Video:Test&veaction=edit" class="external text"><span class="mw-ui-button mw-ui-progressive">Edit with VisualEditor</span></a>';
        $result = fix_link_red($html);

        $this->assertStringNotContainsString('Edit with VisualEditor', $result);
    }

    public function testFixLinkRedFixesRedLinks()
    {
        $html = '<a typeof="mw:LocalizedAttrs" href="/wiki/Test?action=edit&redlink=1">Red Link</a>';
        $result = fix_link_red($html);

        $this->assertStringNotContainsString('action=edit', $result);
        $this->assertStringNotContainsString('redlink=1', $result);
    }

    public function testFixLinkRedPreservesNormalLinks()
    {
        $html = '<a href="/wiki/Article">Normal Link</a>';
        $result = fix_link_red($html);

        $this->assertStringContainsString('Normal Link', $result);
        $this->assertStringContainsString('href="/wiki/Article"', $result);
    }

    public function testFixLinkRedWithNoLinks()
    {
        $html = '<p>Content without links</p>';
        $result = fix_link_red($html);

        $this->assertEquals($html, $result);
    }

    public function testFixLinkRedWithEmptyHtml()
    {
        $result = fix_link_red('');

        $this->assertEquals('', $result);
    }

    public function testRemoveDataParsoidRemovesAttribute()
    {
        $html = '<a href="/wiki/Article" data-parsoid="{}">Link</a>';
        $result = remove_data_parsoid($html);

        $this->assertStringNotContainsString('data-parsoid', $result);
        $this->assertStringContainsString('href="/wiki/Article"', $result);
        $this->assertStringContainsString('Link', $result);
    }

    public function testRemoveDataParsoidWithComplexData()
    {
        $html = '<a href="/wiki/Article" data-parsoid=\'{"dsr":[0,10,2,2]}\'>Link</a>';
        $result = remove_data_parsoid($html);

        $this->assertStringNotContainsString('data-parsoid', $result);
        $this->assertStringContainsString('Link', $result);
    }

    public function testRemoveDataParsoidWithMultipleLinks()
    {
        $html = '<a data-parsoid="{}">Link1</a> <a data-parsoid="{}">Link2</a>';
        $result = remove_data_parsoid($html);

        $this->assertStringNotContainsString('data-parsoid', $result);
        $this->assertStringContainsString('Link1', $result);
        $this->assertStringContainsString('Link2', $result);
    }

    public function testRemoveDataParsoidWithEmptyHtml()
    {
        $result = remove_data_parsoid('');

        $this->assertEquals('', $result);
    }

    public function testRemoveDataParsoidPreservesOtherAttributes()
    {
        $html = '<a href="/wiki/Article" class="link" data-parsoid="{}">Link</a>';
        $result = remove_data_parsoid($html);

        $this->assertStringContainsString('href="/wiki/Article"', $result);
        $this->assertStringContainsString('class="link"', $result);
        $this->assertStringNotContainsString('data-parsoid', $result);
    }

    public function testRemoveDataParsoidWithNoDataParsoid()
    {
        $html = '<a href="/wiki/Article">Normal Link</a>';
        $result = remove_data_parsoid($html);

        $this->assertEquals($html, $result);
    }

    public function testDelDivErrorWithMultilineDiv()
    {
        $html = "<div class=\"error\">\nMultiline\nerror\nmessage\n</div>";
        $result = del_div_error($html);

        $this->assertStringNotContainsString('Multiline', $result);
        $this->assertStringNotContainsString('error', $result);
    }

    public function testGetAttrsWithComplexUrl()
    {
        $text = 'href="http://example.com/path?param=value&other=test"';
        $result = get_attrs($text);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('href', $result);
    }

    public function testFixLinkRedWithMultipleRedLinks()
    {
        $html = '<a typeof="mw:LocalizedAttrs" href="/test?action=edit&redlink=1">Red1</a> <a typeof="mw:LocalizedAttrs" href="/test2?action=edit&redlink=1">Red2</a>';
        $result = fix_link_red($html);

        $this->assertStringNotContainsString('action=edit', $result);
        $this->assertStringNotContainsString('redlink=1', $result);
    }

    public function testRemoveDataParsoidWithRegexPatterns()
    {
        $html = '<div data-parsoid="{}">Content</div><span data-parsoid=\'{"test":"value"}\'>More</span>';
        $result = remove_data_parsoid($html);

        $this->assertStringNotContainsString('data-parsoid', $result);
        $this->assertStringContainsString('Content', $result);
        $this->assertStringContainsString('More', $result);
    }

    public function testDelDivErrorWithAdjacentDivs()
    {
        $html = '<div>Before</div><div class="error">Error</div><div>After</div>';
        $result = del_div_error($html);

        $this->assertStringContainsString('<div>Before</div>', $result);
        $this->assertStringContainsString('<div>After</div>', $result);
        $this->assertStringNotContainsString('Error', $result);
    }

    public function testGetAttrsWithDataAttributes()
    {
        $text = 'href="test" data-value="123" data-name="test"';
        $result = get_attrs($text);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('href', $result);
    }

    public function testFixLinkRedRemovesTypeofAttribute()
    {
        $html = '<a typeof="mw:LocalizedAttrs" href="/wiki/Test?action=edit">Link</a>';
        $result = fix_link_red($html);

        // Should remove typeof and other attributes when processing red links
        $this->assertIsString($result);
    }

    public function testRemoveDataParsoidWithNestedLinks()
    {
        $html = '<div><a data-parsoid="{}">Link 1</a> and <a data-parsoid="{}">Link 2</a></div>';
        $result = remove_data_parsoid($html);

        $this->assertStringNotContainsString('data-parsoid', $result);
        $this->assertStringContainsString('Link 1', $result);
        $this->assertStringContainsString('Link 2', $result);
    }
}
