<?php
namespace Tests\WikiTextFixes;

use MDWiki\NewHtml\Domain\Fixes\Structure\FixCategoriesFixture;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FixCategoriesFixture::class)]
class FixCategoriesFixtureTest extends TestCase
{
    public function testRemoveCategoriesWithSingleCategory()
    {
        $text   = 'Article content [[Category:Medicine]] more text';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringNotContainsString('[[Category:Medicine]]', $result);
        $this->assertStringContainsString('Article content', $result);
        $this->assertStringContainsString('more text', $result);
    }

    public function testRemoveCategoriesWithMultipleCategories()
    {
        $text   = '[[Category:Health]] Content [[Category:Science]] [[Category:Medicine]]';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringNotContainsString('[[Category:Health]]', $result);
        $this->assertStringNotContainsString('[[Category:Science]]', $result);
        $this->assertStringNotContainsString('[[Category:Medicine]]', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function testRemoveCategoriesWithSortKeys()
    {
        $text   = 'Text [[Category:People|Smith, John]] more';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringNotContainsString('[[Category:People|Smith, John]]', $result);
        $this->assertStringContainsString('Text', $result);
        $this->assertStringContainsString('more', $result);
    }

    public function testRemoveCategoriesWithNoCategories()
    {
        $text   = 'Plain article text without categories';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertEquals($text, $result);
    }

    public function testRemoveCategoriesWithEmptyText()
    {
        $result = FixCategoriesFixture::removeCategories('');

        $this->assertEquals('', $result);
    }

    public function testRemoveCategoriesPreservesOtherLinks()
    {
        $text   = '[[Article link]] and [[Category:Medicine]] and [[Another link]]';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringContainsString('[[Article link]]', $result);
        $this->assertStringContainsString('[[Another link]]', $result);
        $this->assertStringNotContainsString('[[Category:Medicine]]', $result);
    }

    public function testRemoveCategoriesWithCaseVariations()
    {
        $text   = '[[Category:Test]] [[category:Test2]] [[CATEGORY:Test3]]';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringNotContainsString('Category:Test', $result);
        $this->assertStringNotContainsString('category:Test2', $result);
        $this->assertStringNotContainsString('CATEGORY:Test3', $result);
    }

    public function testRemoveCategoriesWithWhitespace()
    {
        $text   = '[[  Category  :  Medicine  ]] content';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringNotContainsString('Category', $result);
        $this->assertStringContainsString('content', $result);
    }

    public function testRemoveCategoriesAtEndOfArticle()
    {
        $text   = "Article content.\n\n[[Category:Medicine]]\n[[Category:Health]]\n[[Category:Science]]";
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringContainsString('Article content.', $result);
        $this->assertStringNotContainsString('[[Category:', $result);
    }

    public function testRemoveCategoriesWithMultipleSortKeys()
    {
        $text   = '[[Category:People|Smith]] [[Category:Authors|Smith, John]]';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringNotContainsString('[[Category:People|Smith]]', $result);
        $this->assertStringNotContainsString('[[Category:Authors|Smith, John]]', $result);
    }

    public function testRemoveCategoriesWithSpecialCharacters()
    {
        $text   = 'Content [[Category:Articles with special-characters_and.spaces]]';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringNotContainsString('[[Category:', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function testRemoveCategoriesWithNewlines()
    {
        $text   = "Content\n[[Category:First]]\n[[Category:Second]]\nMore content";
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringContainsString("Content\n", $result);
        $this->assertStringContainsString("More content", $result);
        $this->assertStringNotContainsString('[[Category:', $result);
    }

    public function testRemoveCategoriesWithDuplicates()
    {
        $text   = '[[Category:Test]] content [[Category:Test]]';
        $result = FixCategoriesFixture::removeCategories($text);

        // Both occurrences should be removed
        $this->assertStringNotContainsString('[[Category:Test]]', $result);
        $this->assertStringContainsString('content', $result);
    }

    public function testRemoveCategoriesWithComplexSortKey()
    {
        $text   = '[[Category:Articles|*Special sort key with spaces and symbols!@#]]';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringNotContainsString('[[Category:', $result);
    }

    public function testRemoveCategoriesPreservesTemplates()
    {
        $text   = '{{Template|param=value}} [[Category:Test]] {{Another template}}';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringContainsString('{{Template|param=value}}', $result);
        $this->assertStringContainsString('{{Another template}}', $result);
        $this->assertStringNotContainsString('[[Category:Test]]', $result);
    }

    public function testRemoveCategoriesWithMultipleSpaces()
    {
        $text   = 'Text [[Category:Test1]]  [[Category:Test2]]   [[Category:Test3]]';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringNotContainsString('[[Category:', $result);
        $this->assertStringContainsString('Text', $result);
    }

    public function testRemoveCategoriesWithInlineCategories()
    {
        $text   = 'Start [[Category:Inline]] middle [[Category:Another]] end';
        $result = FixCategoriesFixture::removeCategories($text);

        $this->assertStringContainsString('Start', $result);
        $this->assertStringContainsString('middle', $result);
        $this->assertStringContainsString('end', $result);
        $this->assertStringNotContainsString('[[Category:', $result);
    }

    public function testRemoveCategoriesWithEmptyCategory()
    {
        $text   = 'Content [[Category:]] more';
        $result = FixCategoriesFixture::removeCategories($text);

        // Empty category name should still be caught
        $this->assertStringContainsString('Content', $result);
        $this->assertStringContainsString('more', $result);
    }
}
