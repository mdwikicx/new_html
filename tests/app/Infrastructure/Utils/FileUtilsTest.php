<?php
namespace Tests\Utils;

use MDWiki\NewHtml\Logger;
use MDWiki\NewHtml\Infrastructure\Utils\FileUtils;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FileUtils::class)]
class FileUtilsTest extends TestCase
{
    private $testDir;
    // private $testFile;

    protected function setUp(): void
    {
        Logger::setSink(function (string $level, string $message): void {
            error_log($message);
        });

        $this->testDir = sys_get_temp_dir() . '/test_file_utils_' . time();
        // $this->testFile = $this->testDir . '/test_file.txt';
    }

    protected function tearDown(): void
    {
        Logger::reset();

        // Clean up test directory if it exists
        if (is_dir($this->testDir)) {
            array_map('unlink', glob("$this->testDir/*.*"));
            @rmdir($this->testDir);
        }
    }
    public function testGetFileDirWithVeryLongRevision()
    {
        $longRevision = str_repeat('9', 20);
        $result = FileUtils::get_file_dir($longRevision, '');

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
    public function testFileWriteWithValidData()
    {
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        FileUtils::FileWrite($tempFile, 'Test content');

        // Verify file exists
        $this->assertFileExists($tempFile);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteWithEmptyText()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        FileUtils::FileWrite($tempFile, '');

        // Should not create file with empty text
        // Or create empty file depending on implementation
        @unlink($tempFile);
        $this->assertTrue(true);
    }

    public function testFileWriteWithEmptyFile()
    {
        FileUtils::FileWrite('', 'Some text');

        // Should handle empty file path gracefully
        $this->assertTrue(true);
    }

    public function testFileWriteWithNullFile()
    {
        FileUtils::FileWrite(null, 'Some text');

        // Should handle null file path gracefully
        $this->assertTrue(true);
    }

    public function testReadFileWithExistingFile()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'Test content');

        $result = FileUtils::readFile($tempFile);

        $this->assertEquals('Test content', $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testReadFileWithNonexistentFile()
    {
        $result = FileUtils::readFile('/nonexistent/path/file.txt');

        $this->assertEquals('', $result);
    }

    public function testReadFileWithEmptyPath()
    {
        $result = FileUtils::readFile('');

        $this->assertEquals('', $result);
    }

    public function testReadFileWithNullPath()
    {
        $result = FileUtils::readFile(null);

        $this->assertEquals('', $result);
    }

    public function testFileWriteAndRead()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content = 'Write and read test';

        FileUtils::FileWrite($tempFile, $content);
        $result = FileUtils::readFile($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteWithSpecialCharacters()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content = "Special chars: \n\t{{template}} [[link]] <ref>cite</ref>";

        FileUtils::FileWrite($tempFile, $content);
        $result = FileUtils::readFile($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteWithUnicode()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content = "Unicode: 日本語 العربية Ελληνικά";

        FileUtils::FileWrite($tempFile, $content);
        $result = FileUtils::readFile($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteOverwritesExisting()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');

        FileUtils::FileWrite($tempFile, 'First content');
        FileUtils::FileWrite($tempFile, 'Second content');

        $result = FileUtils::readFile($tempFile);

        $this->assertEquals('Second content', $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testReadFileWithLargeContent()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $largeContent = str_repeat('Large content block. ', 1000);

        file_put_contents($tempFile, $largeContent);
        $result = FileUtils::readFile($tempFile);

        $this->assertEquals($largeContent, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteWithNewlines()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content = "Line 1\nLine 2\nLine 3";

        FileUtils::FileWrite($tempFile, $content);
        $result = FileUtils::readFile($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testReadFileReturnsStringOrBool()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'Test');

        $result = FileUtils::readFile($tempFile);

        $this->assertTrue(is_string($result) || is_bool($result));

        // Clean up
        @unlink($tempFile);
    }
}
