<?php

namespace FixRefs\Tests\APIServices;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Services\Api\check_commons_image_exists;

class CommonsApiTest extends bootstrap
{
    /**
     * Check if we can reach the Wikimedia Commons API
     */
    protected function setUp(): void
    {
        $this->markTestSkipped('skipping newwork tests for now');
        // Check if commons.wikimedia.org is accessible
        if (!$this->isCommonsAvailable()) {
            $this->markTestSkipped('MDWiki API unavailable - skipping tests');
        }
    }

    private function isCommonsAvailable(): bool
    {
        $socket = @fsockopen('commons.wikimedia.org', 443, $errno, $errstr, 5);
        if ($socket) {
            fclose($socket);
            return true;
        }
        return false;
    }
    /**
     * Test that check_commons_image_exists returns true for a known existing image
     */
    public function testCheckCommonsImageExists()
    {
        if (!$this->isCommonsAvailable()) {
            $this->markTestSkipped('Cannot reach Wikimedia Commons API');
        }

        // Test with a well-known Commons image that should exist
        $result = check_commons_image_exists('AwareLogo.png');
        $this->assertTrue($result, 'AwareLogo.png should exist on Commons');
    }

    /**
     * Test that check_commons_image_exists returns false for non-existent image
     */
    public function testCheckCommonsImageNotExists()
    {
        if (!$this->isCommonsAvailable()) {
            $this->markTestSkipped('Cannot reach Wikimedia Commons API');
        }

        // Test with an image that definitely doesn't exist
        $result = check_commons_image_exists('NonExistentImageFileNameThatDoesNotExist12345678901234567890.png');
        $this->assertFalse($result, 'Non-existent image should return false');
    }

    /**
     * Test that empty filename returns false
     */
    public function testCheckCommonsImageEmptyFilename()
    {
        $this->assertFalse(check_commons_image_exists(''));
        $this->assertFalse(check_commons_image_exists('   '));
    }

    /**
     * Test with File: prefix (should handle it)
     */
    public function testCheckCommonsImageWithFilePrefix()
    {
        if (!$this->isCommonsAvailable()) {
            $this->markTestSkipped('Cannot reach Wikimedia Commons API');
        }

        $result = check_commons_image_exists('File:AwareLogo.png');
        $this->assertTrue($result);
    }

    /**
     * Test with Image: prefix (should handle it)
     */
    public function testCheckCommonsImageWithImagePrefix()
    {
        if (!$this->isCommonsAvailable()) {
            $this->markTestSkipped('Cannot reach Wikimedia Commons API');
        }

        $result = check_commons_image_exists('Image:AwareLogo.png');
        $this->assertTrue($result);
    }

    /**
     * Test with filename containing spaces
     */
    public function testCheckCommonsImageWithSpaces()
    {
        if (!$this->isCommonsAvailable()) {
            $this->markTestSkipped('Cannot reach Wikimedia Commons API');
        }

        // Test handling of spaces in filename
        $result = check_commons_image_exists('  AwareLogo.png  ');
        $this->assertTrue($result);
    }

    /**
     * Test with special characters in filename
     */
    public function testCheckCommonsImageWithSpecialCharacters()
    {
        if (!$this->isCommonsAvailable()) {
            $this->markTestSkipped('Cannot reach Wikimedia Commons API');
        }

        // Test with underscores and hyphens
        $result = check_commons_image_exists('Test-image_2020.png');
        // This may or may not exist, just verify it returns a boolean
        $this->assertIsBool($result);
    }

    /**
     * Test return type is always boolean
     */
    public function testCheckCommonsImageReturnsBoolean()
    {
        $result = check_commons_image_exists('');
        $this->assertIsBool($result);

        if ($this->isCommonsAvailable()) {
            $result2 = check_commons_image_exists('AwareLogo.png');
            $this->assertIsBool($result2);
        }
    }

    /**
     * Test with mixed case prefix
     */
    public function testCheckCommonsImageWithMixedCasePrefix()
    {
        if (!$this->isCommonsAvailable()) {
            $this->markTestSkipped('Cannot reach Wikimedia Commons API');
        }

        $result = check_commons_image_exists('file:AwareLogo.png');
        $this->assertTrue($result);
    }

    /**
     * Test with very long filename
     */
    public function testCheckCommonsImageWithLongFilename()
    {
        if (!$this->isCommonsAvailable()) {
            $this->markTestSkipped('Cannot reach Wikimedia Commons API');
        }

        $longFilename = str_repeat('Long_', 50) . '.png';
        $result = check_commons_image_exists($longFilename);
        // Should return false for this unlikely filename
        $this->assertIsBool($result);
    }

    /**
     * Test with invalid characters
     */
    public function testCheckCommonsImageWithInvalidCharacters()
    {
        if (!$this->isCommonsAvailable()) {
            $this->markTestSkipped('Cannot reach Wikimedia Commons API');
        }

        $result = check_commons_image_exists('Test<>|.png');
        // Should handle gracefully
        $this->assertIsBool($result);
    }
}