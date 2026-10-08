<?php
namespace Tests\Domain\Parser;

use MDWiki\NewHtml\Domain\Parser\CategoryParser;
use PHPUnit\Framework\TestCase;

class CategoryParserTest extends TestCase
{
    public function testGetCategoriesWithSingleCategory()
    {
        $text   = "Some text [[Category:Medicine]] more text";
        $result = CategoryParser::get_categories($text);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('Medicine', $result);
        $this->assertEquals('[[Category:Medicine]]', $result['Medicine']);
    }

    public function testGetCategoriesWithMultipleCategories()
    {
        $text   = "Text [[Category:Health]] and [[Category:Science]] content";
        $result = CategoryParser::get_categories($text);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('Health', $result);
        $this->assertArrayHasKey('Science', $result);
    }

    public function testGetCategoriesWithSortKey()
    {
        $text   = "[[Category:People|Smith, John]]";
        $result = CategoryParser::get_categories($text);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('People', $result);
        $this->assertEquals('[[Category:People|Smith, John]]', $result['People']);
    }

    public function testGetCategoriesWithWhitespace()
    {
        $text   = "[[  Category  :  Medicine  ]]";
        $result = CategoryParser::get_categories($text);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('Medicine', $result);
    }

    public function testGetCategoriesWithCaseInsensitive()
    {
        $text   = "[[category:Health]] [[CATEGORY:Science]]";
        $result = CategoryParser::get_categories($text);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetCategoriesWithNoCategories()
    {
        $text   = "Some text without any categories";
        $result = CategoryParser::get_categories($text);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetCategoriesWithEmptyText()
    {
        $result = CategoryParser::get_categories("");

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetCategoriesWithMultiplePipes()
    {
        $text   = "[[Category:Articles|Sort|Extra]]";
        $result = CategoryParser::get_categories($text);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Articles', $result);
    }

    public function testGetCategoriesWithSpecialCharacters()
    {
        $text   = "[[Category:Articles with special-characters_and.spaces]]";
        $result = CategoryParser::get_categories($text);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function testGetCategoriesWithDuplicateCategories()
    {
        $text   = "[[Category:Test]] some text [[Category:Test]]";
        $result = CategoryParser::get_categories($text);

        // Should only keep the last occurrence
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('Test', $result);
    }

    public function testGetCategoriesWithMultilineText()
    {
        $text   = "Line 1\n[[Category:First]]\nLine 2\n[[Category:Second]]\nLine 3";
        $result = CategoryParser::get_categories($text);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('First', $result);
        $this->assertArrayHasKey('Second', $result);
    }

    public function testGetCategoriesTrimsSpacesInCategoryName()
    {
        $text   = "[[Category:  Spaced Name  ]]";
        $result = CategoryParser::get_categories($text);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Spaced Name', $result);
    }
}
