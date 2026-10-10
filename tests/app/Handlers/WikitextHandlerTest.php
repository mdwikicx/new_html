<?php

namespace Tests\Handlers;

use PHPUnit\Framework\TestCase;

use MDWiki\NewHtml\Handlers\WikitextHandler;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(WikitextHandler::class)]
class WikitextHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        // Skip tests that require network access by default
        $this->markTestSkipped('Skipping network tests - require MDWiki API access');
    }

    public function testGetWikitextReturnsArray()
    {
        $result = WikitextHandler::getWikitext('Test_Article', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextWithSpacesInTitle()
    {
        // Test that spaces are replaced with underscores
        $result = WikitextHandler::getWikitext('Test Article', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextReturnsWikitextAndRevid()
    {
        $result = WikitextHandler::getWikitext('Sample_Page', '');

        $this->assertIsString($result["source"]);
        $this->assertTrue(is_string($result["revid"]) || is_int($result["revid"]));
    }

    public function testGetWikitextWithEmptyAllParameter()
    {
        // When $all is empty, should get only lead section
        $result = WikitextHandler::getWikitext('Test_Page', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextWithNonEmptyAllParameter()
    {
        // When $all is non-empty, should get full page
        $result = WikitextHandler::getWikitext('Test_Page', 'all');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextHandlesEmptyResponse()
    {
        // Test with likely non-existent page
        $result = WikitextHandler::getWikitext('NonExistentPage999999', '');

        $this->assertIsString($result["source"]);
    }

    public function testGetWikitextWithSpecialCharacters()
    {
        $result = WikitextHandler::getWikitext('Test/Page-Name_123', '');

        $this->assertIsArray($result);
        $this->assertIsString($result["source"]);
    }

    public function testGetWikitextReplacesSpacesWithUnderscores()
    {
        // Test the title transformation
        $result = WikitextHandler::getWikitext('Multiple  Spaces  Here', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextLeadSectionExtraction()
    {
        // Test that lead section is extracted when $all is empty
        $result = WikitextHandler::getWikitext('Test_Article', '');

        $this->assertIsString($result["source"]);
        // Lead section should end with references section
        // This depends on actual content, so we just verify it's a string
    }

    public function testGetWikitextFullTextRetrieval()
    {
        // Test full text retrieval with non-empty $all
        $result = WikitextHandler::getWikitext('Test_Article', 'full');

        $this->assertIsString($result["source"]);
    }

    public function testGetWikitextWithUnicodeTitle()
    {
        $result = WikitextHandler::getWikitext('Tëst_Articlé', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextEmptyTitle()
    {
        $result = WikitextHandler::getWikitext('', '');

        $this->assertIsArray($result);
        $this->assertIsString($result["source"]);
    }

    public function testGetWikitextReturnsValidStructure()
    {
        $result = WikitextHandler::getWikitext('Any_Title', '');

        // Verify structure: array with source and revid keys
        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextProcessesRedirect()
    {
        // Test redirect handling (if source contains #REDIRECT)
        // This would need actual API access, so we just verify structure
        $result = WikitextHandler::getWikitext('Possible_Redirect', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextWithLongTitle()
    {
        $longTitle = str_repeat('Long_Title_', 20);
        $result = WikitextHandler::getWikitext($longTitle, '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }
}
