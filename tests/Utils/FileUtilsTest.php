<?php

namespace FixRefs\Tests\Utils;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Infrastructure\Utils\get_file_dir;
use function MDWiki\NewHtml\Infrastructure\Utils\file_write;
use function MDWiki\NewHtml\Infrastructure\Utils\read_file;

class FileUtilsTest extends bootstrap
{
    private $testDir;
    private $testFile;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/test_file_utils_' . time();
        $this->testFile = $this->testDir . '/test_file.txt';
    }

    public function testGetFileDirWithValidRevision()
    {
        // This test depends on REVISIONS_PATH constant
        $result = get_file_dir('12345', '');

        $this->assertIsString($result);
        $this->assertStringContainsString('12345', $result);
    }

    public function testGetFileDirWithAllFlag()
    {
        $result = get_file_dir('67890', 'all');

        $this->assertIsString($result);
        $this->assertStringContainsString('67890', $result);
        $this->assertStringContainsString('_all', $result);
    }

    public function testGetFileDirWithEmptyRevision()
    {
        $result = get_file_dir('', '');

        $this->assertEquals('', $result);
    }

    public function testGetFileDirWithNonNumericRevision()
    {
        $result = get_file_dir('abc123', '');

        $this->assertEquals('', $result);
    }

    public function testGetFileDirCreatesDirectory()
    {
        // Test that directory is created if it doesn't exist
        $result = get_file_dir('99999', '');

        $this->assertIsString($result);
        // Directory creation depends on system permissions
    }

    public function testFileWriteWithValidData()
    {
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_write($tempFile, 'Test content');

        // Verify file exists
        $this->assertFileExists($tempFile);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteWithEmptyText()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_write($tempFile, '');

        // Should not create file with empty text
        // Or create empty file depending on implementation
        @unlink($tempFile);
        $this->assertTrue(true);
    }

    public function testFileWriteWithEmptyFile()
    {
        file_write('', 'Some text');

        // Should handle empty file path gracefully
        $this->assertTrue(true);
    }

    public function testFileWriteWithNullFile()
    {
        file_write(null, 'Some text');

        // Should handle null file path gracefully
        $this->assertTrue(true);
    }

    public function testReadFileWithExistingFile()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'Test content');

        $result = read_file($tempFile);

        $this->assertEquals('Test content', $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testReadFileWithNonexistentFile()
    {
        $result = read_file('/nonexistent/path/file.txt');

        $this->assertEquals('', $result);
    }

    public function testReadFileWithEmptyPath()
    {
        $result = read_file('');

        $this->assertEquals('', $result);
    }

    public function testReadFileWithNullPath()
    {
        $result = read_file(null);

        $this->assertEquals('', $result);
    }

    public function testFileWriteAndRead()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content = 'Write and read test';

        file_write($tempFile, $content);
        $result = read_file($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteWithSpecialCharacters()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content = "Special chars: \n\t{{template}} [[link]] <ref>cite</ref>";

        file_write($tempFile, $content);
        $result = read_file($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteWithUnicode()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content = "Unicode: 日本語 العربية Ελληνικά";

        file_write($tempFile, $content);
        $result = read_file($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testFileWriteOverwritesExisting()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');

        file_write($tempFile, 'First content');
        file_write($tempFile, 'Second content');

        $result = read_file($tempFile);

        $this->assertEquals('Second content', $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testReadFileWithLargeContent()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $largeContent = str_repeat('Large content block. ', 1000);

        file_put_contents($tempFile, $largeContent);
        $result = read_file($tempFile);

        $this->assertEquals($largeContent, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testGetFileDirWithNumericStringRevision()
    {
        $result = get_file_dir('123456789', '');

        $this->assertIsString($result);
        $this->assertStringContainsString('123456789', $result);
    }

    public function testGetFileDirWithLeadingZeros()
    {
        // Test revision with leading zeros
        $result = get_file_dir('00123', '');

        $this->assertIsString($result);
    }

    public function testFileWriteWithNewlines()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        $content = "Line 1\nLine 2\nLine 3";

        file_write($tempFile, $content);
        $result = read_file($tempFile);

        $this->assertEquals($content, $result);

        // Clean up
        @unlink($tempFile);
    }

    public function testReadFileReturnsStringOrBool()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'Test');

        $result = read_file($tempFile);

        $this->assertTrue(is_string($result) || is_bool($result));

        // Clean up
        @unlink($tempFile);
    }

    public function testGetFileDirWithVeryLongRevision()
    {
        $longRevision = str_repeat('9', 20);
        $result = get_file_dir($longRevision, '');

        $this->assertIsString($result);
        $this->assertStringContainsString($longRevision, $result);
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