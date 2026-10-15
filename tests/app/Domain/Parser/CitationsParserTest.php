<?php
namespace Tests\Domain\Parser;

use MDWiki\NewHtml\Domain\Parser\CitationsParser;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CitationsParser::class)]
class CitationsParserTest extends TestCase
{
    public function testGetNameWithDoubleQuotes()
    {
        $options = ' name="test_ref" ';
        $result  = CitationsParser::get_ref_name($options);

        $this->assertEquals('test_ref', $result);
    }

    public function testGetNameWithSingleQuotes()
    {
        $options = " name='test_ref' ";
        $result  = CitationsParser::get_ref_name($options);

        $this->assertEquals('test_ref', $result);
    }

    public function testGetNameWithoutQuotes(): void
    {
        $options = ' name=test_ref ';
        $result  = CitationsParser::get_ref_name($options);

        $this->assertEquals('test_ref', $result);
    }

    public function testGetNameWithEmptyOptions()
    {
        $result = CitationsParser::get_ref_name('');

        $this->assertEquals('', $result);
    }

    public function testGetNameWithNoName()
    {
        $options = ' group="notes" ';
        $result  = CitationsParser::get_ref_name($options);

        $this->assertEquals('', $result);
    }

    public function testGetRegCitationsWithSingleRef()
    {
        $text   = 'Some text <ref name="test">Citation content</ref> more text';
        $result = CitationsParser::get_regex_citations($text);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Citation content', $result[0]['content']);
        $this->assertEquals('test', $result[0]['name']);
        $this->assertEquals('<ref name="test">Citation content</ref>', $result[0]['tag']);
    }

    public function testGetRegCitationsWithMultipleRefs()
    {
        $text   = '<ref name="ref1">Content 1</ref> and <ref name="ref2">Content 2</ref>';
        $result = CitationsParser::get_regex_citations($text);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Content 1', $result[0]['content']);
        $this->assertEquals('Content 2', $result[1]['content']);
    }

    public function testGetRegCitationsWithoutName()
    {
        $text   = '<ref>Anonymous citation</ref>';
        $result = CitationsParser::get_regex_citations($text);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Anonymous citation', $result[0]['content']);
        $this->assertEquals('', $result[0]['name']);
    }

    public function testGetRegCitationsWithMultilineContent()
    {
        $text = '<ref name="multi">Line 1
Line 2
Line 3</ref>';
        $result = CitationsParser::get_regex_citations($text);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertStringContainsString('Line 1', $result[0]['content']);
        $this->assertStringContainsString('Line 3', $result[0]['content']);
    }

    public function testGetRegCitationsWithNoRefs()
    {
        $text   = 'Text without any references';
        $result = CitationsParser::get_regex_citations($text);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetFullRefs()
    {
        $text   = '<ref name="ref1">Content 1</ref> text <ref name="ref2">Content 2</ref>';
        $result = CitationsParser::get_full_refs($text);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('ref1', $result);
        $this->assertArrayHasKey('ref2', $result);
        $this->assertEquals('<ref name="ref1">Content 1</ref>', $result['ref1']);
        $this->assertEquals('<ref name="ref2">Content 2</ref>', $result['ref2']);
    }

    public function testGetFullRefsWithAnonymousRef()
    {
        $text   = '<ref>Anonymous</ref>';
        $result = CitationsParser::get_full_refs($text);

        $this->assertIsArray($result);
        // Anonymous refs have empty string as key
        // ref without names are skipped now
        // $this->assertArrayHasKey('', $result);
    }

    public function testgetShortCitations()
    {
        $text   = 'Text <ref name="test" /> more text';
        $result = CitationsParser::get_short_citations($text);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('test', $result[0]['name']);
        $this->assertEquals('', $result[0]['content']);
        $this->assertEquals('<ref name="test" />', $result[0]['tag']);
    }

    public function testgetShortCitationsWithMultiple()
    {
        $text   = '<ref name="a"/> and <ref name="b" /> and <ref name="c"/>';
        $result = CitationsParser::get_short_citations($text);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertEquals('a', $result[0]['name']);
        $this->assertEquals('b', $result[1]['name']);
        $this->assertEquals('c', $result[2]['name']);
    }

    public function testgetShortCitationsWithSpaceVariations()
    {
        $text   = '<ref name="test"/><ref name="test2" /><ref name="test3"  />';
        $result = CitationsParser::get_short_citations($text);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }

    public function testgetShortCitationsWithNoShortRefs()
    {
        $text   = '<ref name="full">Content</ref>';
        $result = CitationsParser::get_short_citations($text);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetRegCitationsWithComplexAttributes()
    {
        $text   = '<ref name="test" group="notes">Complex citation</ref>';
        $result = CitationsParser::get_regex_citations($text);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('test', $result[0]['name']);
        $this->assertStringContainsString('group="notes"', $result[0]['options']);
    }

    public function testgetShortCitationsWithComplexAttributes()
    {
        $text   = '<ref name="test" group="notes" />';
        $result = CitationsParser::get_short_citations($text);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('test', $result[0]['name']);
    }

    public function testGetFullRefsPreservesLastOccurrence()
    {
        $text   = '<ref name="dup">First</ref> text <ref name="dup">Second</ref>';
        $result = CitationsParser::get_full_refs($text);

        $this->assertIsArray($result);
        // The last occurrence should overwrite
        $this->assertStringContainsString('Second', $result['dup']);
    }

    public function testGetRegCitationsWithNestedTags()
    {
        $text   = '<ref name="cite">Text with <span>nested tags</span></ref>';
        $result = CitationsParser::get_regex_citations($text);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertStringContainsString('nested tags', $result[0]['content']);
    }
}
