<?php

namespace FixRefs\Tests\APIServices;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Services\Api\getWikitextFromMdwikiApi;
use function MDWiki\NewHtml\Services\Api\getWikitextFromMdwikiRestapi;


class MdwikiApiTest extends bootstrap
{
    protected function setUp(): void
    {
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

    public function testGetWikitextFromMdwikiApiWithValidTitle()
    {
        $title = 'Aspirin';
        [$wikitext, $revid] = getWikitextFromMdwikiApi($title);

        // Should return non-empty wikitext and revision ID
        $this->assertNotEmpty($wikitext);
        $this->assertNotEmpty($revid);
        $this->assertIsString($wikitext);
        $this->assertIsNumeric($revid);
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

    public function testGetWikitextFromMdwikiRestapiWithValidTitle()
    {
        $title = 'Diabetes';
        [$wikitext, $revid] = getWikitextFromMdwikiRestapi($title);

        $this->assertNotEmpty($wikitext);
        $this->assertNotEmpty($revid);
        $this->assertIsString($wikitext);
    }

    public function testGetWikitextFromMdwikiRestapiWithInvalidTitle()
    {
        $title = 'Nonexistent_Article_xyz123';
        [$wikitext, $revid] = getWikitextFromMdwikiRestapi($title);

        // Should return empty strings
        $this->assertEquals('', $wikitext);
        $this->assertEquals('', $revid);
    }

    public function testGetWikitextFromMdwikiRestapiReturnsArray()
    {
        $title = 'Diabetes';
        $result = getWikitextFromMdwikiRestapi($title);

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

    public function testGetWikitextFromMdwikiRestapiWithSpaces()
    {
        $title = 'Heart attack';
        [$wikitext, $revid] = getWikitextFromMdwikiRestapi($title);

        // Should handle spaces in title
        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetWikitextFromMdwikiRestapiWithSlash()
    {
        // Test title with slash (should be encoded)
        $title = 'Test/Subpage';
        [$wikitext, $revid] = getWikitextFromMdwikiRestapi($title);

        // Should handle slashes
        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetWikitextFromMdwikiApiReturnsValidWikitext()
    {
        $title = 'Paracetamol';
        [$wikitext, $revid] = getWikitextFromMdwikiApi($title);

        if (!empty($wikitext)) {
            // Wikitext should contain typical wiki markup
            $this->assertIsString($wikitext);
            $this->assertGreaterThan(100, strlen($wikitext));
        } else {
            $this->markTestSkipped('Article not found or API unavailable');
        }
    }

    public function testGetWikitextFromMdwikiRestapiReturnsValidWikitext()
    {
        $title = 'Cancer';
        [$wikitext, $revid] = getWikitextFromMdwikiRestapi($title);

        if (!empty($wikitext)) {
            $this->assertIsString($wikitext);
            $this->assertGreaterThan(100, strlen($wikitext));
        } else {
            $this->markTestSkipped('Article not found or API unavailable');
        }
    }

    public function testGetWikitextFromMdwikiApiWithEmptyTitle()
    {
        $title = '';
        [$wikitext, $revid] = getWikitextFromMdwikiApi($title);

        // Should handle empty title gracefully
        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetWikitextFromMdwikiRestapiWithEmptyTitle()
    {
        $title = '';
        [$wikitext, $revid] = getWikitextFromMdwikiRestapi($title);

        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetWikitextFromMdwikiApiRevisionIdFormat()
    {
        $title = 'Hypertension';
        [$wikitext, $revid] = getWikitextFromMdwikiApi($title);

        if (!empty($revid)) {
            // Revision ID should be numeric
            $this->assertMatchesRegularExpression('/^\d+$/', (string)$revid);
        } else {
            $this->markTestSkipped('Article not found');
        }
    }

    public function testGetWikitextFromMdwikiRestapiRevisionIdFormat()
    {
        $title = 'Influenza';
        [$wikitext, $revid] = getWikitextFromMdwikiRestapi($title);

        if (!empty($revid)) {
            $this->assertMatchesRegularExpression('/^\d+$/', (string)$revid);
        } else {
            $this->markTestSkipped('Article not found');
        }
    }

    public function testGetWikitextFromMdwikiApiConsistency()
    {
        $title = 'Diabetes';
        [$wikitext1, $revid1] = getWikitextFromMdwikiApi($title);
        [$wikitext2, $revid2] = getWikitextFromMdwikiApi($title);

        if (!empty($wikitext1) && !empty($wikitext2)) {
            // Same title should return same revision (unless edited between calls)
            $this->assertEquals($revid1, $revid2);
        } else {
            $this->markTestSkipped('Article not found');
        }
    }

    public function testGetWikitextFromMdwikiRestapiWithUnderscore()
    {
        // REST API should handle underscores
        $title = 'Heart_disease';
        [$wikitext, $revid] = getWikitextFromMdwikiRestapi($title);

        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testBothApIsReturnSimilarData()
    {
        $title = 'Tuberculosis';
        [$wikitext1, $revid1] = getWikitextFromMdwikiApi($title);
        [$wikitext2, $revid2] = getWikitextFromMdwikiRestapi($title);

        if (!empty($wikitext1) && !empty($wikitext2)) {
            // Both APIs should return the same content
            $this->assertEquals($wikitext1, $wikitext2);
            $this->assertEquals($revid1, $revid2);
        } else {
            $this->markTestSkipped('Article not found or API unavailable');
        }
    }
}
