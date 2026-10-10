<?php

declare (strict_types = 1);

namespace Tests\Services\Html;

use MDWiki\NewHtml\Services\Api\SegmentApiService;
use MDWiki\NewHtml\Services\Html\HtmlToSegmentsService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(HtmlToSegmentsService::class)]
final class HtmlToSegmentsServiceTest extends TestCase
{
    private string $tmpDir;
    private string $segFile;
    private HtmlToSegmentsService $service;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/seg_test_' . uniqid('', true);
        mkdir($this->tmpDir, 0777, true);
        $this->segFile = $this->tmpDir . '/seg.html';
        unset($_GET['new']);
    }

    protected function tearDown(): void
    {
        unset($_GET['new']);

        foreach (glob($this->tmpDir . '/*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->tmpDir);
    }

    /**
     * Build the service with a stubbed SegmentApiService returning $payload.
     * Returns a counter so tests can assert how many calls were made.
     *
     * @param array<string, mixed> $payload
     */
    private function fakeApi(array $payload): \stdClass
    {
        $counter        = new \stdClass();
        $counter->calls = 0;
        $counter->args  = [];

        $api = $this->createStub(SegmentApiService::class);
        $api->method('HtmltoSegments')->willReturnCallback(
            function (string $text) use ($payload, $counter): array {
                $counter->calls++;
                $counter->args[] = $text;
                return $payload;
            }
        );

        $this->service = new HtmlToSegmentsService($api);

        return $counter;
    }

    // ---------------------- Cache behaviour ----------------------

    public function testReturnsCachedContentWithoutCallingApi(): void
    {
        file_put_contents($this->segFile, '<section>cached</section>');
        $calls = $this->fakeApi(['result' => '<section>fresh</section>']);

        [$text, $fromCache] = $this->service->load('<p>x</p>', $this->segFile);

        $this->assertSame('<section>cached</section>', $text);
        $this->assertTrue($fromCache);
        $this->assertSame(0, $calls->calls);
    }

    public function testCallsApiAndWritesCacheWhenFileMissing(): void
    {
        $calls = $this->fakeApi(['result' => '<section>fresh</section>']);

        [$text, $fromCache] = $this->service->load('<p>x</p>', $this->segFile);

        $this->assertSame('<section>fresh</section>', $text);
        $this->assertFalse($fromCache);
        $this->assertSame(1, $calls->calls);
        $this->assertSame(['<p>x</p>'], $calls->args);
        $this->assertFileExists($this->segFile);
        $this->assertSame('<section>fresh</section>', file_get_contents($this->segFile));
    }

    public function testEmptyCacheFileTriggersApiCall(): void
    {
        file_put_contents($this->segFile, '');
        $calls = $this->fakeApi(['result' => 'new content']);

        [$text, $fromCache] = $this->service->load('<p>x</p>', $this->segFile);

        $this->assertSame('new content', $text);
        $this->assertFalse($fromCache);
        $this->assertSame(1, $calls->calls);
    }

    public function testNewQueryParamBypassesCacheAndOverwritesIt(): void
    {
        file_put_contents($this->segFile, 'old cached');
        $_GET['new'] = '1';
        $calls       = $this->fakeApi(['result' => 'refreshed']);

        [$text, $fromCache] = $this->service->load('<p>x</p>', $this->segFile);

        $this->assertSame('refreshed', $text);
        $this->assertFalse($fromCache);
        $this->assertSame(1, $calls->calls);
        $this->assertSame('refreshed', file_get_contents($this->segFile));
    }

    // ---------------------- API failure / empty ----------------------

    public function testReturnsEmptyAndDoesNotWriteCacheWhenApiReturnsNoResult(): void
    {
        $this->fakeApi(['error' => 'boom']);

        [$text, $fromCache] = $this->service->load('<p>x</p>', $this->segFile);

        $this->assertSame('', $text);
        $this->assertFalse($fromCache);
        $this->assertFileDoesNotExist($this->segFile);
    }

    public function testReturnsEmptyWhenApiResultIsEmptyString(): void
    {
        $this->fakeApi(['result' => '']);

        [$text, $fromCache] = $this->service->load('<p>x</p>', $this->segFile);

        $this->assertSame('', $text);
        $this->assertFalse($fromCache);
        $this->assertFileDoesNotExist($this->segFile);
    }

    /** @return array<string, array{0: string}> */
    public static function emptyMessageProvider(): array
    {
        return [
            'empty content message'     => ['Content for translate is not given or is empty'],
            'sectionwrap error message' => ['Sectionwrap: Attempting to remove a non-section tag: undefined'],
        ];
    }

    #[DataProvider('emptyMessageProvider')]
    public function testKnownEmptyMessagesAreTreatedAsNoContent(string $message): void
    {
        $this->fakeApi(['result' => $message]);

        [$text, $fromCache] = $this->service->load('<p>x</p>', $this->segFile);

        $this->assertSame('', $text);
        $this->assertFalse($fromCache);
        $this->assertFileDoesNotExist($this->segFile, 'Error messages must never be cached.');
    }

    public function testEmptyMessageEmbeddedInLongerTextIsNotTreatedAsEmpty(): void
    {
        $result = 'prefix Content for translate is not given or is empty suffix';
        $this->fakeApi(['result' => $result]);

        [$text] = $this->service->load('<p>x</p>', $this->segFile);

        $this->assertSame($result, $text);
    }

    // ---------------------- URL replacement ----------------------

    /** @return array<string, array{0: string, 1: string}> */
    public static function urlReplacementProvider(): array
    {
        return [
            'md path'              => [
                '<a href="https://medwiki.toolforge.org/md/index.php?title=X">x</a>',
                '<a href="https://en.wikipedia.org/w/index.php?title=X">x</a>',
            ],
            'w path'               => [
                '<a href="https://medwiki.toolforge.org/w/index.php">x</a>',
                '<a href="https://en.wikipedia.org/w/index.php">x</a>',
            ],
            'wiki path'            => [
                '<a href="https://medwiki.toolforge.org/wiki/Heart">x</a>',
                '<a href="https://en.wikipedia.org/wiki/Heart">x</a>',
            ],
            'multiple in one text' => [
                '<a href="https://medwiki.toolforge.org/wiki/A">a</a><a href="https://medwiki.toolforge.org/wiki/B">b</a>',
                '<a href="https://en.wikipedia.org/wiki/A">a</a><a href="https://en.wikipedia.org/wiki/B">b</a>',
            ],
            'no match unchanged'   => [
                '<a href="https://example.org/wiki/A">a</a>',
                '<a href="https://example.org/wiki/A">a</a>',
            ],
        ];
    }

    #[DataProvider('urlReplacementProvider')]
    public function testUrlsAreRewrittenInApiResult(string $apiResult, string $expected): void
    {
        $this->fakeApi(['result' => $apiResult]);

        [$text] = $this->service->load('<p>x</p>', $this->segFile);

        $this->assertSame($expected, $text);
    }

    public function testRewrittenContentIsWhatGetsCached(): void
    {
        $this->fakeApi(['result' => '<a href="https://medwiki.toolforge.org/wiki/Heart">x</a>']);

        $this->service->load('<p>x</p>', $this->segFile);

        $this->assertSame(
            '<a href="https://en.wikipedia.org/wiki/Heart">x</a>',
            file_get_contents($this->segFile)
        );
    }

    public function testCachedContentIsReturnedAsIsWithoutUrlRewriting(): void
    {
        $cached = '<a href="https://medwiki.toolforge.org/wiki/Heart">x</a>';
        file_put_contents($this->segFile, $cached);
        $this->fakeApi(['result' => 'unused']);

        [$text, $fromCache] = $this->service->load('<p>x</p>', $this->segFile);

        $this->assertSame($cached, $text);
        $this->assertTrue($fromCache);
    }

    public function testReplaceUrlsConstantTargetsEnglishWikipedia(): void
    {
        foreach (HtmlToSegmentsService::REPLACE_URLS as $from => $to) {
            $this->assertStringStartsWith('https://medwiki.toolforge.org/', $from);
            $this->assertStringStartsWith('https://en.wikipedia.org/', $to);
        }
    }
}
