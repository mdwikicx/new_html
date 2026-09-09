<?php

namespace FixRefs\Tests\APIServices;

use FixRefs\Tests\bootstrap;

use MDWiki\NewHtml\Services\Api\CommonsImageService;

class CommonsApiRealTest extends bootstrap
{
    /**
     * Check if we can reach the Wikimedia Commons API
     */
    protected function setUp(): void
    {
        // Skip network tests unless RUN_NETWORK_TESTS=true is set
        if (!RUN_NETWORK_TESTS) {
            $this->markTestSkipped('Network tests disabled. Set RUN_NETWORK_TESTS=true to run them.');
        }
        // Check if commons.wikimedia.org is accessible
        if (!$this->isCommonsAvailable()) {
            $this->markTestSkipped('Commons API unavailable - skipping tests');
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
     * Test that CommonsImageService returns true for a known existing image
     */
    public function testCheckCommonsImageExists()
    {
        // Test with a well-known Commons image that should exist
        $service = new CommonsImageService();
        $result = $service->imageExists('Logo.png');
        $this->assertTrue($result, 'Logo.png should exist on Commons');
    }

    /**
     * Test that CommonsImageService returns false for non-existent image
     */
    public function testCheckCommonsImageNotExists()
    {
        // Test with an image that definitely doesn't exist
        $service = new CommonsImageService();
        $result = $service->imageExists('NonExistentImageFileNameThatDoesNotExist12345678901234567890.png');
        $this->assertFalse($result, 'Non-existent image should return false');
    }

    /**
     * Test that empty filename returns false
     */
    public function testCheckCommonsImageEmptyFilename()
    {
        $service = new CommonsImageService();
        $this->assertFalse($service->imageExists(''));
        $this->assertFalse($service->imageExists('   '));
    }
}
