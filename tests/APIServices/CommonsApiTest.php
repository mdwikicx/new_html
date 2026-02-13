<?php

namespace FixRefs\Tests\APIServices;

use FixRefs\Tests\bootstrap;
use MDWiki\NewHtml\Services\Api\CommonsImageService;
use MDWiki\NewHtml\Services\Interfaces\HttpClientInterface;

class CommonsApiTest extends bootstrap
{
    private ?CommonsImageService $service;
    private ?HttpClientInterface $mockHttpClient;

    protected function setUp(): void
    {
        // Create a mock HTTP client
        $this->mockHttpClient = $this->createMock(HttpClientInterface::class);
        $this->service = new CommonsImageService($this->mockHttpClient);
    }

    /**
     * Helper to create a successful API response (image exists)
     *
     * @param string $filename The filename
     * @return string JSON encoded response
     */
    private function createImageExistsResponse(string $filename): string
    {
        return json_encode([
            'query' => [
                'pages' => [
                    [
                        'pageid' => 12345,
                        'ns' => 6,
                        'title' => 'File:' . $filename
                    ]
                ]
            ]
        ]);
    }

    /**
     * Helper to create a response for missing image
     *
     * @param string $filename The filename
     * @return string JSON encoded response
     */
    private function createImageMissingResponse(string $filename): string
    {
        return json_encode([
            'query' => [
                'pages' => [
                    [
                        'ns' => 6,
                        'title' => 'File:' . $filename,
                        'missing' => ''
                    ]
                ]
            ]
        ]);
    }

    /**
     * Test that imageExists returns true for a known existing image
     */
    public function testCheckCommonsImageExists()
    {
        $filename = 'Logo.png';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createImageExistsResponse($filename));

        $result = $this->service->imageExists($filename);

        $this->assertTrue($result, 'Logo.png should exist on Commons');
    }

    /**
     * Test that imageExists returns false for non-existent image
     */
    public function testCheckCommonsImageNotExists()
    {
        $filename = 'NonExistentImageFileNameThatDoesNotExist12345678901234567890.png';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createImageMissingResponse($filename));

        $result = $this->service->imageExists($filename);

        $this->assertFalse($result, 'Non-existent image should return false');
    }

    /**
     * Test that empty filename returns false
     */
    public function testCheckCommonsImageEmptyFilename()
    {
        $this->mockHttpClient
            ->method('request')
            ->willReturn('');

        $this->assertFalse($this->service->imageExists(''));
        $this->assertFalse($this->service->imageExists('   '));
    }

    /**
     * Test that imageExists handles File: prefix
     */
    public function testCheckCommonsImageWithFilePrefix()
    {
        $filename = 'Logo.png';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createImageExistsResponse($filename));

        // Should handle File: prefix
        $result = $this->service->imageExists('File:' . $filename);

        $this->assertTrue($result);
    }

    /**
     * Test that imageExists handles Image: prefix
     */
    public function testCheckCommonsImageWithImagePrefix()
    {
        $filename = 'Logo.png';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createImageExistsResponse($filename));

        // Should handle Image: prefix
        $result = $this->service->imageExists('Image:' . $filename);

        $this->assertTrue($result);
    }

    /**
     * Test that imageExists handles API failure gracefully
     */
    public function testCheckCommonsImageHandlesApiFailure()
    {
        $filename = 'Logo.png';

        $this->mockHttpClient
            ->method('request')
            ->willReturn(''); // Empty response simulates API failure

        // On API failure, should return true (assumes exists)
        $result = $this->service->imageExists($filename);

        $this->assertTrue($result, 'Should return true on API failure');
    }

    /**
     * Test that imageExists sends correct API parameters
     */
    public function testCheckCommonsImageSendsCorrectParameters()
    {
        $filename = 'Logo.png';

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->stringContains('commons.wikimedia.org/w/api.php'),
                $this->equalTo('GET'),
                $this->callback(function ($params) use ($filename) {
                    return $params['action'] === 'query'
                        && $params['titles'] === 'File:' . $filename
                        && $params['format'] === 'json';
                })
            )
            ->willReturn($this->createImageExistsResponse($filename));

        $this->service->imageExists($filename);
    }

    /**
     * Test that imageExists handles whitespace in filename
     */
    public function testCheckCommonsImageHandlesWhitespace()
    {
        $filename = 'Logo.png';

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->createImageExistsResponse($filename));

        $result = $this->service->imageExists('  ' . $filename . '  ');

        $this->assertTrue($result);
    }
}
