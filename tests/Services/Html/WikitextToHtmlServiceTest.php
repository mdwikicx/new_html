<?php

declare(strict_types=1);

namespace MDWiki\NewHtml\Tests\Services\Html;

use MDWiki\NewHtml\Services\Api\TransformApiService;
use MDWiki\NewHtml\Services\Html\WikitextToHtmlService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WikitextToHtmlServiceTest extends TestCase
{
    /** @var TransformApiService&MockObject */
    private TransformApiService $transform;

    private WikitextToHtmlService $service;

    private string $tmpDir;

    protected function setUp(): void
    {
        $this->transform = $this->createMock(TransformApiService::class);
        $this->service = new WikitextToHtmlService($this->transform);

        $this->tmpDir = sys_get_temp_dir() . '/wikitext_html_test_' . uniqid();
        mkdir($this->tmpDir, 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tmpDir . '/*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->tmpDir);
    }

    // ------------------------------------------------------------------
    // constructor
    // ------------------------------------------------------------------

    public function testConstructorWorksWithoutArguments(): void
    {
        $service = new WikitextToHtmlService();

        $this->assertInstanceOf(WikitextToHtmlService::class, $service);
    }

    // ------------------------------------------------------------------
    // convert()
    // ------------------------------------------------------------------

    public function testConvertReturnsEmptyStringForEmptyWikitext(): void
    {
        $this->transform->expects($this->never())->method('convert');

        $this->assertSame('', $this->service->convert('', 'Title'));
    }

    public function testConvertReplacesSpacesWithUnderscoresInTitle(): void
    {
        $this->transform
            ->expects($this->once())
            ->method('convert')
            ->with('some text', 'My_Page_Title')
            ->willReturn(['result' => '<p>ok</p>']);

        $this->service->convert('some text', 'My Page Title');
    }

    public function testConvertReturnsHtmlOnSuccess(): void
    {
        $this->transform
            ->method('convert')
            ->willReturn(['result' => '<p>Hello</p>']);

        $html = $this->service->convert("'''Hello'''", 'Title');

        $this->assertStringContainsString('Hello', $html);
    }

    public function testConvertReturnsEmptyStringWhenResultIsEmpty(): void
    {
        $this->transform
            ->method('convert')
            ->willReturn(['result' => '']);

        $this->assertSame('', $this->service->convert('text', 'Title'));
    }

    public function testConvertReturnsEmptyStringWhenResultKeyIsMissing(): void
    {
        $this->transform
            ->method('convert')
            ->willReturn(['error' => 'API failure']);

        $this->assertSame('', $this->service->convert('text', 'Title'));
    }

    public function testConvertReturnsEmptyStringWhenApiReturnsEmptyArray(): void
    {
        $this->transform
            ->method('convert')
            ->willReturn([]);

        $this->assertSame('', $this->service->convert('text', 'Title'));
    }

    // ------------------------------------------------------------------
    // convertWithCache()
    // ------------------------------------------------------------------

    public function testCacheIsUsedWhenNotNewAndFileHasContent(): void
    {
        $file = $this->tmpDir . '/cached.html';
        file_put_contents($file, '<p>cached</p>');

        $this->transform->expects($this->never())->method('convert');

        [$html, $fromCache] = $this->service->convertWithCache('text', $file, 'Title', false);

        $this->assertSame('<p>cached</p>', $html);
        $this->assertTrue($fromCache);
    }

    public function testApiIsCalledAndFileWrittenWhenCacheIsMissing(): void
    {
        $file = $this->tmpDir . '/fresh.html';

        $this->transform
            ->expects($this->once())
            ->method('convert')
            ->willReturn(['result' => '<p>fresh</p>']);

        [$html, $fromCache] = $this->service->convertWithCache('text', $file, 'Title', false);

        $this->assertStringContainsString('fresh', $html);
        $this->assertFalse($fromCache);
        $this->assertFileExists($file);
        $this->assertSame($html, file_get_contents($file));
    }

    public function testApiIsCalledWhenCacheFileIsEmpty(): void
    {
        $file = $this->tmpDir . '/empty.html';
        file_put_contents($file, '');

        $this->transform
            ->expects($this->once())
            ->method('convert')
            ->willReturn(['result' => '<p>new</p>']);

        [$html, $fromCache] = $this->service->convertWithCache('text', $file, 'Title', false);

        $this->assertStringContainsString('new', $html);
        $this->assertFalse($fromCache);
    }

    public function testNewFlagIgnoresExistingCache(): void
    {
        $file = $this->tmpDir . '/force.html';
        file_put_contents($file, '<p>old</p>');

        $this->transform
            ->expects($this->once())
            ->method('convert')
            ->willReturn(['result' => '<p>updated</p>']);

        [$html, $fromCache] = $this->service->convertWithCache('text', $file, 'Title', true);

        $this->assertStringContainsString('updated', $html);
        $this->assertFalse($fromCache);
        $this->assertStringContainsString('updated', (string) file_get_contents($file));
    }

    public function testEmptyWikitextReturnsEmptyAndDoesNotWriteFile(): void
    {
        $file = $this->tmpDir . '/none.html';

        $this->transform->expects($this->never())->method('convert');

        [$html, $fromCache] = $this->service->convertWithCache('', $file, 'Title', true);

        $this->assertSame('', $html);
        $this->assertFalse($fromCache);
        $this->assertFileDoesNotExist($file);
    }

    public function testApiFailureReturnsEmptyAndDoesNotWriteFile(): void
    {
        $file = $this->tmpDir . '/failed.html';

        $this->transform
            ->method('convert')
            ->willReturn(['error' => 'timeout']);

        [$html, $fromCache] = $this->service->convertWithCache('text', $file, 'Title', false);

        $this->assertSame('', $html);
        $this->assertFalse($fromCache);
        $this->assertFileDoesNotExist($file);
    }

    public function testEmptyWikitextStillReturnsCacheWhenNotNew(): void
    {
        $file = $this->tmpDir . '/cached2.html';
        file_put_contents($file, '<p>cached</p>');

        [$html, $fromCache] = $this->service->convertWithCache('', $file, 'Title', false);

        $this->assertSame('<p>cached</p>', $html);
        $this->assertTrue($fromCache);
    }
}
