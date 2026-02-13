<?php

namespace FixRefs\Tests\APIServices;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Services\Api\getWikitextFromMdwikiApi;
use function MDWiki\NewHtml\Services\Api\getWikitextFromMdwikiRestApi;


class MdwikiApiRealTest extends bootstrap
{
    protected function setUp(): void
    {
        // Skip network tests unless RUN_NETWORK_TESTS=true is set
        if (!RUN_NETWORK_TESTS) {
            $this->markTestSkipped('Network tests disabled. Set RUN_NETWORK_TESTS=true to run them.');
        }
        // Check if mdwiki.org is accessible
        if (!$this->isMdwikiAvailable()) {
            $this->markTestSkipped('MDWiki API unavailable - skipping tests');
        }
    }

    private function isMdwikiAvailable(): bool
    {
        $socket = @fsockopen('mdwiki.org', 443, $errno, $errstr, 5);
        if ($socket) {
            fclose($socket);
            return true;
        }
        return false;
    }

    public function testGetWikitextFromMdwikiApiWithInvalidTitle()
    {
        $title = 'This_Is_A_Nonexistent_Article_Title_12345';
        [$wikitext, $revid] = getWikitextFromMdwikiApi($title);

        // Should return empty strings for nonexistent article
        $this->assertEquals('', $wikitext);
        $this->assertEquals('', $revid);
    }

    public function testGetWikitextFromMdwikiApiReturnsArray()
    {
        $title = 'Aspirin';
        $result = getWikitextFromMdwikiApi($title);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetWikitextFromMdwikiRestApiWithInvalidTitle()
    {
        $title = 'Nonexistent_Article_xyz123';
        [$wikitext, $revid] = getWikitextFromMdwikiRestApi($title);

        // Should return empty strings
        $this->assertEquals('', $wikitext);
        $this->assertEquals('', $revid);
    }

    public function testGetWikitextFromMdwikiRestApiReturnsArray()
    {
        $title = 'Diabetes';
        $result = getWikitextFromMdwikiRestApi($title);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetWikitextFromMdwikiApiWithSpecialCharacters()
    {
        $title = 'Crohn\'s disease';
        [$wikitext, $revid] = getWikitextFromMdwikiApi($title);

        // Should handle special characters
        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetWikitextFromMdwikiRestApiWithSpaces()
    {
        $title = 'Heart attack';
        [$wikitext, $revid] = getWikitextFromMdwikiRestApi($title);

        // Should handle spaces in title
        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetWikitextFromMdwikiRestApiWithSlash()
    {
        // Test title with slash (should be encoded)
        $title = 'Test/Subpage';
        [$wikitext, $revid] = getWikitextFromMdwikiRestApi($title);

        // Should handle slashes
        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetWikitextFromMdwikiApiReturnsValidWikitext()
    {
        $title = 'Paracetamol';
        [$wikitext, $revid] = getWikitextFromMdwikiApi($title);

        // Wikitext should contain typical wiki markup
        $this->assertIsString($wikitext);
        $this->assertGreaterThan(100, strlen($wikitext));
    }

    public function testGetWikitextFromMdwikiRestApiReturnsValidWikitext()
    {
        $title = 'Cancer';
        [$wikitext, $revid] = getWikitextFromMdwikiRestApi($title);

        $this->assertIsString($wikitext);
        $this->assertGreaterThan(100, strlen($wikitext));
    }

    public function testGetWikitextFromMdwikiApiWithEmptyTitle()
    {
        $title = '';
        [$wikitext, $revid] = getWikitextFromMdwikiApi($title);

        // Should handle empty title gracefully
        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetWikitextFromMdwikiRestApiWithEmptyTitle()
    {
        $title = '';
        [$wikitext, $revid] = getWikitextFromMdwikiRestApi($title);

        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetWikitextFromMdwikiApiRevisionIdFormat()
    {
        $title = 'Hypertension';
        [$wikitext, $revid] = getWikitextFromMdwikiApi($title);

        // Revision ID should be numeric
        $this->assertMatchesRegularExpression('/^\d+$/', (string)$revid);
    }

    public function testGetWikitextFromMdwikiRestApiRevisionIdFormat()
    {
        $title = 'Influenza';
        [$wikitext, $revid] = getWikitextFromMdwikiRestApi($title);

        $this->assertMatchesRegularExpression('/^\d+$/', (string)$revid);
    }

    public function testGetWikitextFromMdwikiApiConsistency()
    {
        $title = 'Diabetes';
        [$wikitext1, $revid1] = getWikitextFromMdwikiApi($title);
        [$wikitext2, $revid2] = getWikitextFromMdwikiApi($title);

        // Same title should return same revision (unless edited between calls)
        $this->assertEquals($revid1, $revid2);
    }

    public function testGetWikitextFromMdwikiRestApiWithUnderscore()
    {
        // REST API should handle underscores
        $title = 'Heart_disease';
        [$wikitext, $revid] = getWikitextFromMdwikiRestApi($title);

        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testBothApIsReturnSimilarData()
    {
        $title = 'Tuberculosis';
        [$wikitext1, $revid1] = getWikitextFromMdwikiApi($title);
        [$wikitext2, $revid2] = getWikitextFromMdwikiRestApi($title);

        // Both APIs should return the same content
        $this->assertEquals($wikitext1, $wikitext2);
        $this->assertEquals($revid1, $revid2);
    }

    public function testGetWikitextFromMdwikiApiWithValidTitle()
    {
        $title = 'Aspirin';
        [$wikitext, $revid] = getWikitextFromMdwikiApi($title);

        // Should return non-empty wikitext and revision ID
        $this->assertNotEmpty($revid);
        $this->assertNotEmpty($wikitext);
        $this->assertIsString($wikitext);
        $this->assertIsNumeric($revid);
    }

    public function testGetWikitextFromMdwikiRestApiWithValidTitle()
    {
        $title = 'Diabetes';
        [$wikitext, $revid] = getWikitextFromMdwikiRestApi($title);

        $this->assertNotEmpty($revid);
        $this->assertNotEmpty($wikitext);
        $this->assertIsString($wikitext);
    }
}
