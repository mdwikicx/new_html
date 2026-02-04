<?php

namespace FixRefs\Tests\WikiTextFixes;

use FixRefs\Tests\bootstrap;

use function Fixes\FixCats\remove_categories;

class FixCatsTest extends bootstrap
{
    public function testRemoveCategoriesWithSingleCategory()
    {
        $text = 'Article content [[Category:Medicine]] more text';
        $result = remove_categories($text);

        $this->assertStringNotContainsString('[[Category:Medicine]]', $result);
        $this->assertStringContainsString('Article content', $result);
        $this->assertStringContainsString('more text', $result);
    }

    public function testRemoveCategoriesWithMultipleCategories()
    {
        $text = '[[Category:Health]] Content [[Category:Science]] [[Category:Medicine]]';
        $result = remove_categories($text);

        $this->assertStringNotContainsString('[[Category:Health]]', $result);
        $this->assertStringNotContainsString('[[Category:Science]]', $result);
        $this->assertStringNotContainsString('[[Category:Medicine]]', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function testRemoveCategoriesWithSortKeys()
    {
        $text = 'Text [[Category:People|Smith, John]] more';
        $result = remove_categories($text);

        $this->assertStringNotContainsString('[[Category:People|Smith, John]]', $result);
        $this->assertStringContainsString('Text', $result);
        $this->assertStringContainsString('more', $result);
    }

    public function testRemoveCategoriesWithNoCategories()
    {
        $text = 'Plain article text without categories';
        $result = remove_categories($text);

        $this->assertEquals($text, $result);
    }

    public function testRemoveCategoriesWithEmptyText()
    {
        $result = remove_categories('');

        $this->assertEquals('', $result);
    }

    public function testRemoveCategoriesPreservesOtherLinks()
    {
        $text = '[[Article link]] and [[Category:Medicine]] and [[Another link]]';
        $result = remove_categories($text);

        $this->assertStringContainsString('[[Article link]]', $result);
        $this->assertStringContainsString('[[Another link]]', $result);
        $this->assertStringNotContainsString('[[Category:Medicine]]', $result);
    }

    public function testRemoveCategoriesWithCaseVariations()
    {
        $text = '[[Category:Test]] [[category:Test2]] [[CATEGORY:Test3]]';
        $result = remove_categories($text);

        $this->assertStringNotContainsString('Category:Test', $result);
        $this->assertStringNotContainsString('category:Test2', $result);
        $this->assertStringNotContainsString('CATEGORY:Test3', $result);
    }

    public function testRemoveCategoriesWithWhitespace()
    {
        $text = '[[  Category  :  Medicine  ]] content';
        $result = remove_categories($text);

        $this->assertStringNotContainsString('Category', $result);
        $this->assertStringContainsString('content', $result);
    }

    public function testRemoveCategoriesAtEndOfArticle()
    {
        $text = "Article content.\n\n[[Category:Medicine]]\n[[Category:Health]]\n[[Category:Science]]";
        $result = remove_categories($text);

        $this->assertStringContainsString('Article content.', $result);
        $this->assertStringNotContainsString('[[Category:', $result);
    }

    public function testRemoveCategoriesWithMultipleSortKeys()
    {
        $text = '[[Category:People|Smith]] [[Category:Authors|Smith, John]]';
        $result = remove_categories($text);

        $this->assertStringNotContainsString('[[Category:People|Smith]]', $result);
        $this->assertStringNotContainsString('[[Category:Authors|Smith, John]]', $result);
    }

    public function testRemoveCategoriesWithSpecialCharacters()
    {
        $text = 'Content [[Category:Articles with special-characters_and.spaces]]';
        $result = remove_categories($text);

        $this->assertStringNotContainsString('[[Category:', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function testRemoveCategoriesWithNewlines()
    {
        $text = "Content\n[[Category:First]]\n[[Category:Second]]\nMore content";
        $result = remove_categories($text);

        $this->assertStringContainsString("Content\n", $result);
        $this->assertStringContainsString("More content", $result);
        $this->assertStringNotContainsString('[[Category:', $result);
    }

    public function testRemoveCategoriesWithDuplicates()
    {
        $text = '[[Category:Test]] content [[Category:Test]]';
        $result = remove_categories($text);

        // Both occurrences should be removed
        $this->assertStringNotContainsString('[[Category:Test]]', $result);
        $this->assertStringContainsString('content', $result);
    }

    public function testRemoveCategoriesWithComplexSortKey()
    {
        $text = '[[Category:Articles|*Special sort key with spaces and symbols!@#]]';
        $result = remove_categories($text);

        $this->assertStringNotContainsString('[[Category:', $result);
    }

    public function testRemoveCategoriesPreservesTemplates()
    {
        $text = '{{Template|param=value}} [[Category:Test]] {{Another template}}';
        $result = remove_categories($text);

        $this->assertStringContainsString('{{Template|param=value}}', $result);
        $this->assertStringContainsString('{{Another template}}', $result);
        $this->assertStringNotContainsString('[[Category:Test]]', $result);
    }

    public function testRemoveCategoriesWithMultipleSpaces()
    {
        $text = 'Text [[Category:Test1]]  [[Category:Test2]]   [[Category:Test3]]';
        $result = remove_categories($text);

        $this->assertStringNotContainsString('[[Category:', $result);
        $this->assertStringContainsString('Text', $result);
    }

    public function testRemoveCategoriesWithInlineCategories()
    {
        $text = 'Start [[Category:Inline]] middle [[Category:Another]] end';
        $result = remove_categories($text);

        $this->assertStringContainsString('Start', $result);
        $this->assertStringContainsString('middle', $result);
        $this->assertStringContainsString('end', $result);
        $this->assertStringNotContainsString('[[Category:', $result);
    }

    public function testRemoveCategoriesWithEmptyCategory()
    {
        $text = 'Content [[Category:]] more';
        $result = remove_categories($text);

        // Empty category name should still be caught
        $this->assertStringContainsString('Content', $result);
        $this->assertStringContainsString('more', $result);
    }
}
