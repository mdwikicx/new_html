<?php
namespace MDWiki\NewHtml\Tests\Services\Api;

use MDWiki\NewHtml\Services\Api\LocalRestJsonRepository;
use MDWiki\NewHtml\Services\Api\MdwikiApiService;
use MDWiki\NewHtml\Services\Api\MdwikiRestFallbackService;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class MdwikiRestFallbackServiceTest extends TestCase
{
    /** @var MdwikiApiService&MockObject */
    private $api;

    /** @var LocalRestJsonRepository&MockObject */
    private $local;

    private MdwikiRestFallbackService $service;

    protected function setUp(): void
    {
        $this->api     = $this->createMock(MdwikiApiService::class);
        $this->local   = $this->createMock(LocalRestJsonRepository::class);
        $this->service = new MdwikiRestFallbackService($this->api, $this->local);
    }

    public function testReturnsApiResultWhenApiSucceeds(): void
    {
        $this->api->expects($this->once())
            ->method('getWikitextFromMdwikiRestApi')
            ->with('Heart_failure')
            ->willReturn([
                'source' => 'API wikitext',
                'revid'  => 555,
                'error'  => '',
                'failed' => false,
            ]);

        // The local repository must not be touched when the API works
        $this->local->expects($this->never())->method('find');

        $result = $this->service->getWikitext('Heart_failure');

        $this->assertSame(
            ['source' => 'API wikitext', 'revid' => 555, 'error' => ''],
            $result
        );
    }

    public function testDoesNotFallBackWhenApiSucceedsWithEmptySource(): void
    {
        // The request worked but the page has no content: this is not a connection failure
        $this->api->method('getWikitextFromMdwikiRestApi')->willReturn([
            'source' => '',
            'revid'  => 10,
            'error'  => '',
            'failed' => false,
        ]);

        $this->local->expects($this->never())->method('find');

        $result = $this->service->getWikitext('Empty_page');

        $this->assertSame('', $result['source']);
        $this->assertSame(10, $result['revid']);
    }

    public function testFallsBackToLocalFileWhenApiFails(): void
    {
        $this->api->method('getWikitextFromMdwikiRestApi')->willReturn([
            'source' => '',
            'revid'  => '',
            'error'  => 'timeout',
            'failed' => true,
        ]);
        $this->local->expects($this->once())
            ->method('find')
            ->with('Heart_failure')
            ->willReturn([
                'source' => 'Local wikitext',
                'latest' => ['id' => 777],
            ]);

        $result = $this->service->getWikitext('Heart_failure');

        $this->assertSame('Local wikitext', $result['source']);
        $this->assertSame(777, $result['revid']);
        // The original API error is kept so callers can still see why the API failed
        $this->assertSame('timeout', $result['error']);
    }

    public function testFallbackHandlesLocalFileWithoutSourceOrRevision(): void
    {
        $this->api->method('getWikitextFromMdwikiRestApi')->willReturn([
            'source' => '',
            'revid'  => '',
            'error'  => 'http code 503',
            'failed' => true,
        ]);

        // A local file that is valid JSON but lacks the expected keys
        $this->local->method('find')->willReturn([]);

        $result = $this->service->getWikitext('Odd_file');

        $this->assertSame('', $result['source']);
        $this->assertSame('', $result['revid']);
        $this->assertSame('http code 503', $result['error']);
    }

    public function testReturnsEmptyResultWhenApiFailsAndNoLocalFileExists(): void
    {
        $this->api->method('getWikitextFromMdwikiRestApi')->willReturn([
            'source' => '',
            'revid'  => '',
            'error'  => 'cloudflare protection',
            'failed' => true,
        ]);

        $this->local->expects($this->once())
            ->method('find')
            ->with('Not_cached')
            ->willReturn(null);

        $result = $this->service->getWikitext('Not_cached');

        $this->assertSame(
            ['source' => '', 'revid' => '', 'error' => 'cloudflare protection'],
            $result
        );
    }

    public function testResultNeverExposesInternalFailedFlag(): void
    {
        $this->api->method('getWikitextFromMdwikiRestApi')->willReturn([
            'source' => '',
            'revid'  => '',
            'error'  => 'timeout',
            'failed' => true,
        ]);
        $this->local->method('find')->willReturn(null);

        $result = $this->service->getWikitext('Any');

        $this->assertSame(['source', 'revid', 'error'], array_keys($result));
    }

    public function testPassesTheSameTitleToBothApiAndLocalRepository(): void
    {
        $this->api->expects($this->once())
            ->method('getWikitextFromMdwikiRestApi')
            ->with('A/B title')
            ->willReturn([
                'source' => '',
                'revid'  => '',
                'error'  => 'timeout',
                'failed' => true,
            ]);

        $this->local->expects($this->once())
            ->method('find')
            ->with('A/B title')
            ->willReturn(null);

        $this->service->getWikitext('A/B title');
    }
}
