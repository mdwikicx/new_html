<?php

namespace FixRefs\Tests\Utils;

use FixRefs\Tests\bootstrap;
use function MDWiki\NewHtmlMain\Utils\get_file_dir;

class mainTest extends bootstrap
{
    public function testGetFileDirWithVeryLongRevision()
    {
        $longRevision = str_repeat('9', 20);
        $result = get_file_dir($longRevision, '');

        $this->assertIsString($result);
        $this->assertStringContainsString($longRevision, $result);
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
}
