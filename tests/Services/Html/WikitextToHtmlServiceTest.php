<?php

declare(strict_types=1);

namespace MDWiki\NewHtml\Tests\Services\Html;

use MDWiki\NewHtml\Services\Api\TransformApiService;
use MDWiki\NewHtml\Services\Html\WikitextToHtmlService;
use PHPUnit\Framework\TestCase;

class WikitextToHtmlServiceTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
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
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Stub: only the value returned by the API matters; how it is called is not verified.
     */
    private function serviceReturning(array $response): WikitextToHtmlService
    {
        $stub = $this->createStub(TransformApiService::class);
        $stub->method('convert')->willReturn($response);

        return new WikitextToHtmlService($stub);
    }

    /**
     * Mock: the API is expected to never be called.
     */
    private function serviceThatMustNotCallApi(): WikitextToHtmlService
    {
        $mock = $this->createMock(TransformApiService::class);
        $mock->expects($this->never())->method('convert');

        return new WikitextToHtmlService($mock);
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
    // convert(): the returned value matters (Stub)
    // ------------------------------------------------------------------

    public function testConvertReturnsHtmlOnSuccess(): void
    {
        $service = $this->serviceReturning(['result' => '<p>Hello</p>']);

        $html = $service->convert("'''Hello'''", 'Title');

        $this->assertStringContainsString('Hello', $html);
    }

    public function testConvertReturnsEmptyStringWhenResultIsEmpty(): void
    {
        $service = $this->serviceReturning(['result' => '']);

        $this->assertSame('', $service->convert('text', 'Title'));
    }

    public function testConvertReturnsEmptyStringWhenResultKeyIsMissing(): void
    {
        $service = $this->serviceReturning(['error' => 'API failure']);

        $this->assertSame('', $service->convert('text', 'Title'));
    }

    public function testConvertReturnsEmptyStringWhenApiReturnsEmptyArray(): void
    {
        $service = $this->serviceReturning([]);

        $this->assertSame('', $service->convert('text', 'Title'));
    }

    // ------------------------------------------------------------------
    // convert(): how the API is called matters (Mock)
    // ------------------------------------------------------------------

    public function testConvertReturnsEmptyStringForEmptyWikitextWithoutCallingApi(): void
    {
        $service = $this->serviceThatMustNotCallApi();

        $this->assertSame('', $service->convert('', 'Title'));
    }

    public function testConvertReplacesSpacesWithUnderscoresInTitle(): void
    {
        $mock = $this->createMock(TransformApiService::class);
        $mock->expects($this->once())
            ->method('convert')
            ->with('some text', 'My_Page_Title')
            ->willReturn(['result' => '<p>ok</p>']);

        (new WikitextToHtmlService($mock))->convert('some text', 'My Page Title');
    }

    // ------------------------------------------------------------------
    // convertWithCache(): result only (Stub)
    // ------------------------------------------------------------------

    public function testApiResultIsReturnedAndFileWrittenWhenCacheIsMissing(): void
    {
        $file = $this->tmpDir . '/fresh.html';
        $service = $this->serviceReturning(['result' => '<p>fresh</p>']);

        [$html, $fromCache] = $service->convertWithCache('text', $file, 'Title', false);

        $this->assertStringContainsString('fresh', $html);
        $this->assertFalse($fromCache);
        $this->assertFileExists($file);
        $this->assertSame($html, file_get_contents($file));
    }

    public function testApiResultIsUsedWhenCacheFileIsEmpty(): void
    {
        $file = $this->tmpDir . '/empty.html';
        file_put_contents($file, '');
        $service = $this->serviceReturning(['result' => '<p>new</p>']);

        [$html, $fromCache] = $service->convertWithCache('text', $file, 'Title', false);

        $this->assertStringContainsString('new', $html);
        $this->assertFalse($fromCache);
    }

    public function testNewFlagIgnoresExistingCache(): void
    {
        $file = $this->tmpDir . '/force.html';
        file_put_contents($file, '<p>old</p>');
        $service = $this->serviceReturning(['result' => '<p>updated</p>']);

        [$html, $fromCache] = $service->convertWithCache('text', $file, 'Title', true);

        $this->assertStringContainsString('updated', $html);
        $this->assertStringNotContainsString('old', $html);
        $this->assertFalse($fromCache);
        $this->assertStringContainsString('updated', (string) file_get_contents($file));
    }

    public function testApiFailureReturnsEmptyAndDoesNotWriteFile(): void
    {
        $file = $this->tmpDir . '/failed.html';
        $service = $this->serviceReturning(['error' => 'timeout']);

        [$html, $fromCache] = $service->convertWithCache('text', $file, 'Title', false);

        $this->assertSame('', $html);
        $this->assertFalse($fromCache);
        $this->assertFileDoesNotExist($file);
    }

    // ------------------------------------------------------------------
    // convertWithCache(): avoiding the API is the requirement (Mock)
    // ------------------------------------------------------------------

    public function testCacheIsUsedWhenNotNewAndFileHasContent(): void
    {
        $file = $this->tmpDir . '/cached.html';
        file_put_contents($file, '<p>cached</p>');
        $service = $this->serviceThatMustNotCallApi();

        [$html, $fromCache] = $service->convertWithCache('text', $file, 'Title', false);

        $this->assertSame('<p>cached</p>', $html);
        $this->assertTrue($fromCache);
    }

    public function testEmptyWikitextStillReturnsCacheWhenNotNew(): void
    {
        $file = $this->tmpDir . '/cached2.html';
        file_put_contents($file, '<p>cached</p>');
        $service = $this->serviceThatMustNotCallApi();

        [$html, $fromCache] = $service->convertWithCache('', $file, 'Title', false);

        $this->assertSame('<p>cached</p>', $html);
        $this->assertTrue($fromCache);
    }

    public function testEmptyWikitextReturnsEmptyAndDoesNotWriteFile(): void
    {
        $file = $this->tmpDir . '/none.html';
        $service = $this->serviceThatMustNotCallApi();

        [$html, $fromCache] = $service->convertWithCache('', $file, 'Title', true);

        $this->assertSame('', $html);
        $this->assertFalse($fromCache);
        $this->assertFileDoesNotExist($file);
    }
}
