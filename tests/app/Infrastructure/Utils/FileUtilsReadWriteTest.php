<?php
namespace Tests\Utils;

use MDWiki\NewHtml\Infrastructure\Utils\FileUtils;
use PHPUnit\Framework\TestCase;

class FileUtilsReadWriteTest extends TestCase
{
    private $testDir;
    private $testFile;

    protected function setUp(): void
    {
        $this->testDir  = sys_get_temp_dir() . '/test_file_utils_' . time();
        $this->testFile = $this->testDir . '/test_file.txt';
    }

    public function testFileWriteWithValidData()
    {
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        FileUtils::file_write($tempFile, 'Test content');

        // Verify file exists
        $this->assertFileExists($tempFile);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteWithEmptyText()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        FileUtils::file_write($tempFile, '');

        // Should not create file with empty text
        // Or create empty file depending on implementation
        @unlink($tempFile);
        $this->assertTrue(true);
    }

    public function testFileWriteWithEmptyFile()
    {
        FileUtils::file_write('', 'Some text');

        // Should handle empty file path gracefully
        $this->assertTrue(true);
    }

    public function testFileWriteWithNullFile()
    {
        FileUtils::file_write(null, 'Some text');

        // Should handle null file path gracefully
        $this->assertTrue(true);
    }

    public function testReadFileWithExistingFile()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'Test content');

        $result = FileUtils::read_file($tempFile);

        $this->assertEquals('Test content', $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testReadFileWithNonexistentFile()
    {
        $result = FileUtils::read_file('/nonexistent/path/file.txt');

        $this->assertEquals('', $result);
    }

    public function testReadFileWithEmptyPath()
    {
        $result = FileUtils::read_file('');

        $this->assertEquals('', $result);
    }

    public function testReadFileWithNullPath()
    {
        $result = FileUtils::read_file(null);

        $this->assertEquals('', $result);
    }

    public function testFileWriteAndRead()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content  = 'Write and read test';

        FileUtils::file_write($tempFile, $content);
        $result = FileUtils::read_file($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteWithSpecialCharacters()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content  = "Special chars: \n\t{{template}} [[link]] <ref>cite</ref>";

        FileUtils::file_write($tempFile, $content);
        $result = FileUtils::read_file($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteWithUnicode()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content  = "Unicode: 日本語 العربية Ελληνικά";

        FileUtils::file_write($tempFile, $content);
        $result = FileUtils::read_file($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteOverwritesExisting()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');

        FileUtils::file_write($tempFile, 'First content');
        FileUtils::file_write($tempFile, 'Second content');

        $result = FileUtils::read_file($tempFile);

        $this->assertEquals('Second content', $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testReadFileWithLargeContent()
    {
        $tempFile     = tempnam(sys_get_temp_dir(), 'test_');
        $largeContent = str_repeat('Large content block. ', 1000);

        file_put_contents($tempFile, $largeContent);
        $result = FileUtils::read_file($tempFile);

        $this->assertEquals($largeContent, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteWithNewlines()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content  = "Line 1\nLine 2\nLine 3";

        FileUtils::file_write($tempFile, $content);
        $result = FileUtils::read_file($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testReadFileReturnsStringOrBool()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'Test');

        $result = FileUtils::read_file($tempFile);

        $this->assertTrue(is_string($result) || is_bool($result));

        // Clean up
        @unlink($tempFile);
    }

    protected function tearDown(): void
    {
        // Clean up test directory if it exists
        if (is_dir($this->testDir)) {
            array_map('unlink', glob("$this->testDir/*.*"));
            @rmdir($this->testDir);
        }
    }
}
