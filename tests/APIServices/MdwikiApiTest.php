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
            ->willReturn($this->createApiResponse($wikitext, $revid));

        [$resultWikitext, $resultRevid] = $this->service->getWikitextFromMdwikiApi($title);

        $this->assertEquals($wikitext, $resultWikitext);
        $this->assertEquals($revid, $resultRevid);
    }

    public function testGetWikitextFromMdwikiApiWithInvalidTitle()
    {
        $title = 'This_Is_A_Nonexistent_Article_Title_12345';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(json_encode(['query' => ['pages' => [[]]]]));

        [$wikitext, $revid] = $this->service->getWikitextFromMdwikiApi($title);

        // Should return empty strings for nonexistent article
        $this->assertEquals('', $wikitext);
        $this->assertEquals('', $revid);
    }

    public function testGetWikitextFromMdwikiApiReturnsArray()
    {
        $title = 'Aspirin';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createApiResponse('Content', '12345'));

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
            ->willReturn($this->createRestApiResponse($wikitext, $revid));

        [$resultWikitext, $resultRevid] = $this->service->getWikitextFromMdwikiRestApi($title);

        $this->assertEquals($wikitext, $resultWikitext);
        $this->assertEquals($revid, $resultRevid);
    }

    public function testGetWikitextFromMdwikiRestApiWithInvalidTitle()
    {
        $title = 'Nonexistent_Article_xyz123';

        $this->mockHttpClient
            ->method('request')
            ->willReturn('{}');

        [$wikitext, $revid] = $this->service->getWikitextFromMdwikiRestApi($title);

        // Should return empty strings
        $this->assertEquals('', $wikitext);
        $this->assertEquals('', $revid);
    }

    public function testGetWikitextFromMdwikiRestApiReturnsArray()
    {
        $title = 'Diabetes';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createRestApiResponse('Content', '12345'));

        $result = $this->service->getWikitextFromMdwikiRestApi($title);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetWikitextFromMdwikiApiWithSpecialCharacters()
    {
        $title = 'Crohn\'s disease';
        $wikitext = '==Crohn\'s disease==\nContent';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createApiResponse($wikitext, '12345'));

        [$resultWikitext, $resultRevid] = $this->service->getWikitextFromMdwikiApi($title);

        // Should handle special characters
        $this->assertIsString($resultWikitext);
        $this->assertIsString($resultRevid);
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
            ->willReturn($this->createRestApiResponse($wikitext, '12345'));

        [$resultWikitext, $resultRevid] = $this->service->getWikitextFromMdwikiRestApi($title);

        // Should handle spaces in title (converted to underscores)
        $this->assertIsString($resultWikitext);
        $this->assertIsString($resultRevid);
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
            ->willReturn($this->createRestApiResponse($wikitext, '12345'));

        [$resultWikitext, $resultRevid] = $this->service->getWikitextFromMdwikiRestApi($title);

        // Should handle slashes (encoded as %2F)
        $this->assertIsString($resultWikitext);
        $this->assertIsString($resultRevid);
    }

    public function testGetWikitextFromMdwikiApiReturnsValidWikitext()
    {
        $title = 'Paracetamol';
        $wikitext = str_repeat('Wiki content here. ', 50); // Long wikitext

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createApiResponse($wikitext, '12345'));

        [$resultWikitext, $resultRevid] = $this->service->getWikitextFromMdwikiApi($title);

        // Wikitext should contain typical wiki markup
        $this->assertIsString($resultWikitext);
        $this->assertGreaterThan(100, strlen($resultWikitext));
    }

    public function testGetWikitextFromMdwikiRestApiReturnsValidWikitext()
    {
        $title = 'Cancer';
        $wikitext = str_repeat('Cancer content here. ', 50); // Long wikitext

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createRestApiResponse($wikitext, '67890'));

        [$resultWikitext, $resultRevid] = $this->service->getWikitextFromMdwikiRestApi($title);

        $this->assertIsString($resultWikitext);
        $this->assertGreaterThan(100, strlen($resultWikitext));
    }

    public function testGetWikitextFromMdwikiApiWithEmptyTitle()
    {
        $title = '';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createApiResponse('', ''));

        [$wikitext, $revid] = $this->service->getWikitextFromMdwikiApi($title);

        // Should handle empty title gracefully
        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetWikitextFromMdwikiRestApiWithEmptyTitle()
    {
        $title = '';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createRestApiResponse('', ''));

        [$wikitext, $revid] = $this->service->getWikitextFromMdwikiRestApi($title);

        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetWikitextFromMdwikiApiRevisionIdFormat()
    {
        $title = 'Hypertension';
        $revid = '54321';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createApiResponse('Content', $revid));

        [$wikitext, $resultRevid] = $this->service->getWikitextFromMdwikiApi($title);

        // Revision ID should be numeric
        $this->assertMatchesRegularExpression('/^\d+$/', (string)$resultRevid);
    }

    public function testGetWikitextFromMdwikiRestApiRevisionIdFormat()
    {
        $title = 'Influenza';
        $revid = '98765';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createRestApiResponse('Content', $revid));

        [$wikitext, $resultRevid] = $this->service->getWikitextFromMdwikiRestApi($title);

        // Revision ID should be numeric
        $this->assertMatchesRegularExpression('/^\d+$/', (string)$resultRevid);
    }

    public function testGetWikitextFromMdwikiApiConsistency()
    {
        $title = 'Diabetes';
        $wikitext = 'Consistent content';
        $revid = '11111';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createApiResponse($wikitext, $revid));

        [$wikitext1, $revid1] = $this->service->getWikitextFromMdwikiApi($title);
        [$wikitext2, $revid2] = $this->service->getWikitextFromMdwikiApi($title);

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
            ->willReturn($this->createRestApiResponse($wikitext, '12345'));

        [$resultWikitext, $resultRevid] = $this->service->getWikitextFromMdwikiRestApi($title);

        $this->assertIsString($resultWikitext);
        $this->assertIsString($resultRevid);
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
                    return $this->createApiResponse($wikitext, $revid);
                }
                return $this->createRestApiResponse($wikitext, $revid);
            });

        [$wikitext1, $revid1] = $this->service->getWikitextFromMdwikiApi($title);
        [$wikitext2, $revid2] = $this->service->getWikitextFromMdwikiRestApi($title);

        // Both APIs should return the same content
        $this->assertEquals($wikitext1, $wikitext2);
        $this->assertEquals($revid1, $revid2);
    }

    public function testGetWikitextFromMdwikiApiHandlesEmptyResponse()
    {
        $title = 'Aspirin';

        $this->mockHttpClient
            ->method('request')
            ->willReturn('');

        [$wikitext, $revid] = $this->service->getWikitextFromMdwikiApi($title);

        // Should return empty strings when API fails
        $this->assertEquals('', $wikitext);
        $this->assertEquals('', $revid);
    }

    public function testGetWikitextFromMdwikiRestApiHandlesEmptyResponse()
    {
        $title = 'Diabetes';

        $this->mockHttpClient
            ->method('request')
            ->willReturn('');

        [$wikitext, $revid] = $this->service->getWikitextFromMdwikiRestApi($title);

        // Should return empty strings when API fails
        $this->assertEquals('', $wikitext);
        $this->assertEquals('', $revid);
    }
}
