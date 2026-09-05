<?php

namespace FixRefs\Tests\APIServices;

use FixRefs\Tests\bootstrap;
use MDWiki\NewHtml\Services\Api\MdwikiApiService;
use MDWiki\NewHtml\Services\Interfaces\HttpClientInterface;

class MdwikiApiTest extends bootstrap
{
    private ?MdwikiApiService $service;
    private ?HttpClientInterface $mockHttpClient;

    protected function setUp(): void
    {
        // Create a mock HTTP client
        $this->mockHttpClient = $this->createMock(HttpClientInterface::class);
        $this->service = new MdwikiApiService($this->mockHttpClient);
    }

    /**
     * Helper to create a successful API response
     *
     * @param string $content The wikitext content
     * @param int|string $revid The revision ID
     * @return string JSON encoded response
     */
    private function createApiResponse(string $content, $revid): string
    {
        return json_encode([
            'query' => [
                'pages' => [
                    [
                        'revisions' => [
                            [
                                'content' => $content,
                                'revid' => $revid
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Helper to create a successful REST API response
     *
     * @param string $source The wikitext source
     * @param int|string $revid The revision ID
     * @return string JSON encoded response
     */
    private function createRestApiResponse(string $source, $revid): string
    {
        return json_encode([
            'source' => $source,
            'latest' => [
                'id' => $revid
            ]
        ]);
    }

    public function testGetWikitextFromMdwikiApiWithValidTitle()
    {
        $title = 'Aspirin';
        $wikitext = '==Aspirin==\nAspirin is a medication.';
        $revid = '12345';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createApiResponse($wikitext, $revid), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiApi($title);

        $this->assertEquals($wikitext, $result["source"]);
        $this->assertEquals($revid, $result["revid"]);
    }

    public function testGetWikitextFromMdwikiApiWithInvalidTitle()
    {
        $title = 'This_Is_A_Nonexistent_Article_Title_12345';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => json_encode(['query' => ['pages' => [[]]]]), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiApi($title);

        // Should return empty strings for nonexistent article
        $this->assertEquals('', $result["source"]);
        $this->assertEquals('', $result["revid"]);
    }

    public function testGetWikitextFromMdwikiApiReturnsArray()
    {
        $title = 'Aspirin';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createApiResponse('Content', '12345'), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiApi($title);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetWikitextFromMdwikiRestApiWithValidTitle()
    {
        $title = 'Diabetes';
        $wikitext = '==Diabetes==\nDiabetes is a disease.';
        $revid = '67890';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createRestApiResponse($wikitext, $revid), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiRestApi($title);

        $this->assertEquals($wikitext, $result["source"]);
        $this->assertEquals($revid, $result["revid"]);
    }

    public function testGetWikitextFromMdwikiRestApiWithInvalidTitle()
    {
        $title = 'Nonexistent_Article_xyz123';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => '{}', "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiRestApi($title);

        // Should return empty strings
        $this->assertEquals('', $result["source"]);
        $this->assertEquals('', $result["revid"]);
    }

    public function testGetWikitextFromMdwikiRestApiReturnsArray()
    {
        $title = 'Diabetes';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createRestApiResponse('Content', '12345'), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiRestApi($title);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('revid', $result);
    }

    public function testGetWikitextFromMdwikiApiWithSpecialCharacters()
    {
        $title = 'Crohn\'s disease';
        $wikitext = '==Crohn\'s disease==\nContent';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createApiResponse($wikitext, '12345'), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiApi($title);

        // Should handle special characters
        $this->assertIsString($result["source"]);
        $this->assertIsString($result["revid"]);
    }

    public function testGetWikitextFromMdwikiRestApiWithSpaces()
    {
        $title = 'Heart attack';
        $wikitext = '==Heart attack==\nContent';

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->stringContains('Heart_attack'),
                $this->equalTo('GET'),
                $this->equalTo([])
            )
            ->willReturn(["output" => $this->createRestApiResponse($wikitext, '12345'), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiRestApi($title);

        // Should handle spaces in title (converted to underscores)
        $this->assertIsString($result["source"]);
        $this->assertIsString($result["revid"]);
    }

    public function testGetWikitextFromMdwikiRestApiWithSlash()
    {
        // Test title with slash (should be encoded)
        $title = 'Test/Subpage';
        $wikitext = '==Test/Subpage==\nContent';

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->stringContains('Test%2FSubpage'),
                $this->equalTo('GET'),
                $this->equalTo([])
            )
            ->willReturn(["output" => $this->createRestApiResponse($wikitext, '12345'), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiRestApi($title);

        // Should handle slashes (encoded as %2F)
        $this->assertIsString($result["source"]);
        $this->assertIsString($result["revid"]);
    }

    public function testGetWikitextFromMdwikiApiReturnsValidWikitext()
    {
        $title = 'Paracetamol';
        $wikitext = str_repeat('Wiki content here. ', 50); // Long wikitext

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createApiResponse($wikitext, '12345'), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiApi($title);

        // Wikitext should contain typical wiki markup
        $this->assertIsString($result["source"]);
        $this->assertGreaterThan(100, strlen($result["source"]));
    }

    public function testGetWikitextFromMdwikiRestApiReturnsValidWikitext()
    {
        $title = 'Cancer';
        $wikitext = str_repeat('Cancer content here. ', 50); // Long wikitext

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createRestApiResponse($wikitext, '67890'), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiRestApi($title);

        $this->assertIsString($result["source"]);
        $this->assertGreaterThan(100, strlen($result["source"]));
    }

    public function testGetWikitextFromMdwikiApiWithEmptyTitle()
    {
        $title = '';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createApiResponse('', ''), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiApi($title);

        // Should handle empty title gracefully
        $this->assertIsString($result["source"]);
        $this->assertIsString($result["revid"]);
    }

    public function testGetWikitextFromMdwikiRestApiWithEmptyTitle()
    {
        $title = '';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createRestApiResponse('', ''), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiRestApi($title);

        $this->assertIsString($result["source"]);
        $this->assertIsString($result["revid"]);
    }

    public function testGetWikitextFromMdwikiApiRevisionIdFormat()
    {
        $title = 'Hypertension';
        $revid = '54321';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createApiResponse('Content', $revid), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiApi($title);

        // Revision ID should be numeric
        $this->assertMatchesRegularExpression('/^\d+$/', (string)$result["revid"]);
    }

    public function testGetWikitextFromMdwikiRestApiRevisionIdFormat()
    {
        $title = 'Influenza';
        $revid = '98765';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createRestApiResponse('Content', $revid), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiRestApi($title);

        // Revision ID should be numeric
        $this->assertMatchesRegularExpression('/^\d+$/', (string)$result["revid"]);
    }

    public function testGetWikitextFromMdwikiApiConsistency()
    {
        $title = 'Diabetes';
        $wikitext = 'Consistent content';
        $revid = '11111';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => $this->createApiResponse($wikitext, $revid), "error_code" => "", "error" => ""]);

        $result1 = $this->service->getWikitextFromMdwikiApi($title);
        $result2 = $this->service->getWikitextFromMdwikiApi($title);

        $wikitext1 = $result1['source'];
        $revid1 = $result1['revid'];
        $wikitext2 = $result2['source'];
        $revid2 = $result2['revid'];

        // Same title should return same revision
        $this->assertEquals($revid1, $revid2);
        $this->assertEquals($wikitext1, $wikitext2);
    }

    public function testGetWikitextFromMdwikiRestApiWithUnderscore()
    {
        // REST API should handle underscores
        $title = 'Heart_disease';
        $wikitext = '==Heart disease==\nContent';

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->stringContains('Heart_disease'),
                $this->equalTo('GET'),
                $this->equalTo([])
            )
            ->willReturn(["output" => $this->createRestApiResponse($wikitext, '12345'), "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiRestApi($title);

        $this->assertIsString($result["source"]);
        $this->assertIsString($result["revid"]);
    }

    public function testBothApisReturnSimilarData()
    {
        $title = 'Tuberculosis';
        $wikitext = '==Tuberculosis==\nSame content from both APIs.';
        $revid = '99999';

        $this->mockHttpClient
            ->method('request')
            ->willReturnCallback(function ($url) use ($wikitext, $revid) {
                if (strpos($url, '/api.php') !== false) {
                    return ["output" => $this->createApiResponse($wikitext, $revid), "error_code" => "", "error" => ""];
                }
                return ["output" => $this->createRestApiResponse($wikitext, $revid), "error_code" => "", "error" => ""];
            });

        $result1 = $this->service->getWikitextFromMdwikiApi($title);
        $result2 = $this->service->getWikitextFromMdwikiRestApi($title);

        // Both APIs should return the same content
        $this->assertEquals($result1["source"], $result2["source"]);
        $this->assertEquals($result1["revid"], $result2["revid"]);
    }

    public function testGetWikitextFromMdwikiApiHandlesEmptyResponse()
    {
        $title = 'Aspirin';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => "", "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiApi($title);

        // Should return empty strings when API fails
        $this->assertEquals('', $result["source"]);
        $this->assertEquals('', $result["revid"]);
    }

    public function testGetWikitextFromMdwikiRestApiHandlesEmptyResponse()
    {
        $title = 'Diabetes';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(["output" => "", "error_code" => "", "error" => ""]);

        $result = $this->service->getWikitextFromMdwikiRestApi($title);

        // Should return empty strings when API fails
        $this->assertEquals('', $result["source"]);
        $this->assertEquals('', $result["revid"]);
    }
}
