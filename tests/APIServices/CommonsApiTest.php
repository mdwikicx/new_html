<?php

use FixRefs\Tests\bootstrap;

use function APIServices\check_commons_image_exists;

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
}
