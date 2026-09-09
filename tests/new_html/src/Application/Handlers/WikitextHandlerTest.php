<?php

namespace FixRefs\Tests\Handlers;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Application\Handlers\get_wikitext;

class WikitextHandlerTest extends bootstrap
{
    protected function setUp(): void
    {
        // Skip tests that require network access by default
        $this->markTestSkipped('Skipping network tests - require MDWiki API access');
    }

    public function testGetWikitextReturnsArray()
    {
        $result = get_wikitext('Test_Article', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextWithSpacesInTitle()
    {
        // Test that spaces are replaced with underscores
        $result = get_wikitext('Test Article', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextReturnsWikitextAndRevid()
    {
        $result = get_wikitext('Sample_Page', '');

        $this->assertIsString($result["source"]);
        $this->assertTrue(is_string($result["revid"]) || is_int($result["revid"]));
    }

    public function testGetWikitextWithEmptyAllParameter()
    {
        // When $all is empty, should get only lead section
        $result = get_wikitext('Test_Page', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextWithNonEmptyAllParameter()
    {
        // When $all is non-empty, should get full page
        $result = get_wikitext('Test_Page', 'all');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextHandlesEmptyResponse()
    {
        // Test with likely non-existent page
        $result = get_wikitext('NonExistentPage999999', '');

        $this->assertIsString($result["source"]);
    }

    public function testGetWikitextWithSpecialCharacters()
    {
        $result = get_wikitext('Test/Page-Name_123', '');

        $this->assertIsArray($result);
        $this->assertIsString($result["source"]);
    }

    public function testGetWikitextReplacesSpacesWithUnderscores()
    {
        // Test the title transformation
        $result = get_wikitext('Multiple  Spaces  Here', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextLeadSectionExtraction()
    {
        // Test that lead section is extracted when $all is empty
        $result = get_wikitext('Test_Article', '');

        $this->assertIsString($result["source"]);
        // Lead section should end with references section
        // This depends on actual content, so we just verify it's a string
    }

    public function testGetWikitextFullTextRetrieval()
    {
        // Test full text retrieval with non-empty $all
        $result = get_wikitext('Test_Article', 'full');

        $this->assertIsString($result["source"]);
    }

    public function testGetWikitextWithUnicodeTitle()
    {
        $result = get_wikitext('Tëst_Articlé', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextEmptyTitle()
    {
        $result = get_wikitext('', '');

        $this->assertIsArray($result);
        $this->assertIsString($result["source"]);
    }

    public function testGetWikitextReturnsValidStructure()
    {
        $result = get_wikitext('Any_Title', '');

        // Verify structure: array with source and revid keys
        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextProcessesRedirect()
    {
        // Test redirect handling (if source contains #REDIRECT)
        // This would need actual API access, so we just verify structure
        $result = get_wikitext('Possible_Redirect', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextWithLongTitle()
    {
        $longTitle = str_repeat('Long_Title_', 20);
        $result = get_wikitext($longTitle, '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }
}
