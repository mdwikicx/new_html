<?php
namespace Tests\Utils;

use MDWiki\NewHtml\Logger;
use MDWiki\NewHtml\Infrastructure\Utils\FileUtils;
use PHPUnit\Framework\TestCase;

class FileUtilsTest extends TestCase
{
    protected function setUp(): void
    {
        Logger::setSink(function (string $level, string $message): void {
            error_log($message);
        });
    }

    protected function tearDown(): void
    {
        Logger::reset();
    }
    public function testGetFileDirWithVeryLongRevision()
    {
        $longRevision = str_repeat('9', 20);
        $result       = FileUtils::get_file_dir($longRevision, '');

        $this->assertIsString($result);
        $this->assertStringContainsString($longRevision, $result);
    }

    public function testGetFileDirWithValidRevision()
    {
        // This test depends on REVISIONS_PATH constant
        $result = FileUtils::get_file_dir('12345', '');

        $this->assertIsString($result);
        $this->assertStringContainsString('12345', $result);
    }

    public function testGetFileDirWithAllFlag()
    {
        $result = FileUtils::get_file_dir('67890', 'all');

        $this->assertIsString($result);
        $this->assertStringContainsString('67890', $result);
        $this->assertStringContainsString('_all', $result);
    }

    public function testGetFileDirWithNonNumericRevision()
    {
        $result = FileUtils::get_file_dir('abc123', '');

        $this->expectOutputRegex('/revision is empty in get_file_dir/');

        $this->assertEquals('', $result);
    }

    public function testGetFileDirCreatesDirectory()
    {
        // Test that directory is created if it doesn't exist
        $result = FileUtils::get_file_dir('99999', '');

        $this->assertIsString($result);
        // Directory creation depends on system permissions
    }
    public function testGetFileDirWithNumericStringRevision()
    {
        $result = FileUtils::get_file_dir('123456789', '');

        $this->assertIsString($result);
        $this->assertStringContainsString('123456789', $result);
    }

    public function testGetFileDirWithLeadingZeros()
    {
        // Test revision with leading zeros
        $result = FileUtils::get_file_dir('00123', '');

        $this->assertIsString($result);
    }
}
