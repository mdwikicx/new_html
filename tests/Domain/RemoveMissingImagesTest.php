<?php

namespace FixRefs\Tests\Domain;

use FixRefs\Tests\bootstrap;
use MDWiki\NewHtml\Domain\Fixes\Media\RemoveMissingImagesService;
use MDWiki\NewHtml\Services\Interfaces\CommonsImageServiceInterface;

class RemoveMissingImagesTest extends bootstrap
{
    private ?RemoveMissingImagesService $service;
    private ?CommonsImageServiceInterface $mockImageService;

    protected function setUp(): void
    {
        // Create a mock image service
        $this->mockImageService = $this->createMock(CommonsImageServiceInterface::class);
        $this->service = new RemoveMissingImagesService($this->mockImageService);
    }

    /**
     * Helper to configure the mock to return specific values for filenames
     *
     * @param array<string, bool> $fileExistsMap Map of filename => exists
     */
    private function setupMockImageExists(array $fileExistsMap): void
    {
        $this->mockImageService
            ->method('imageExists')
            ->willReturnCallback(function (string $filename) use ($fileExistsMap) {
                $filename = preg_replace('/^(File|Image):/i', '', $filename);
                return $fileExistsMap[$filename] ?? false;
            });
    }

    /**
     * TEST 1: Infobox image exists - no changes
     */
    public function testInfoboxImageExists()
    {
        $this->setupMockImageExists([
            'AwareLogo.png' => true
        ]);

        $input = "|name ={{PAGENAME}}\n|image =AwareLogo.png\n|caption =This is a valid image\n|specialty =[[Orthopedics]]";

        $result = $this->service->removeMissingInfoboxImages($input);

        // Should not change since AwareLogo.png exists
        $this->assertEquals($input, $result);
    }

    /**
     * TEST 2: Infobox image missing - remove image and caption
     */
    public function testInfoboxImageMissing()
    {
        $this->setupMockImageExists([
            'Non_existent_image_xyz789.png' => false
        ]);

        $input = "|name ={{PAGENAME}}\n|image =Non_existent_image_xyz789.png\n|caption =This caption should be removed\n|specialty =[[Orthopedics]]";

        $expected = "|name ={{PAGENAME}}\n|specialty =[[Orthopedics]]";

        $result = $this->service->removeMissingInfoboxImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 3: Empty infobox image field - remove both lines
     */
    public function testInfoboxEmptyImage()
    {
        $this->setupMockImageExists([]);

        $input = "|name ={{PAGENAME}}\n|image =\n|caption =Caption for empty image\n|specialty =[[Orthopedics]]";

        $expected = "|name ={{PAGENAME}}\n|specialty =[[Orthopedics]]";

        $result = $this->service->removeMissingInfoboxImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 4: Multiple infobox images - mixed existence
     */
    public function testInfoboxMultipleImagesMixed()
    {
        $this->setupMockImageExists([
            'AwareLogo.png' => true,
            'Missing_image_xyz123456.png' => false
        ]);

        $input = "|name ={{PAGENAME}}\n|image =AwareLogo.png\n|caption =Valid caption\n|image2 =Missing_image_xyz123456.png\n|caption2 =This should be removed\n|specialty =[[Orthopedics]]";

        $expected = "|name ={{PAGENAME}}\n|image =AwareLogo.png\n|caption =Valid caption\n|specialty =[[Orthopedics]]";

        $result = $this->service->removeMissingInfoboxImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 5: Inline image exists - no changes
     */
    public function testInlineImageExists()
    {
        $this->setupMockImageExists([
            'AwareLogo.png' => true
        ]);

        $input = "This is some text with an image:\n[[File:AwareLogo.png|thumb|A valid image caption]]\nMore text here.";

        $result = $this->service->removeMissingInlineImages($input);

        $this->assertEquals($input, $result);
    }

    /**
     * TEST 6: Inline image missing - remove entire block
     */
    public function testInlineImageMissing()
    {
        $this->setupMockImageExists([
            'Non_existent_image_xyz654.png' => false
        ]);

        $input = "This is some text with an image:\n[[File:Non_existent_image_xyz654.png|thumb|This should be removed]]\nMore text here.";

        $expected = "This is some text with an image:\n\nMore text here.";

        $result = $this->service->removeMissingInlineImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 7: Multiple inline images - mixed existence
     */
    public function testInlineMultipleImagesMixed()
    {
        $this->setupMockImageExists([
            'AwareLogo.png' => true,
            'Missing_file_xyz987.jpg' => false
        ]);

        $input = "Start of article.\n[[File:AwareLogo.png|thumb|Keep this image]]\nSome middle text.\n[[File:Missing_file_xyz987.jpg|left|200px|Remove this]]\nEnd of article.";

        $expected = "Start of article.\n[[File:AwareLogo.png|thumb|Keep this image]]\nSome middle text.\n\nEnd of article.";

        $result = $this->service->removeMissingInlineImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 8: Inline image with nested links in caption
     */
    public function testInlineImageNestedLinks()
    {
        $this->setupMockImageExists([
            'Missing_image_nested_xyz321.png' => false
        ]);

        $input = "[[File:Missing_image_nested_xyz321.png|thumb|See [[Orthopedics]] for more info]]";

        $expected = "";

        $result = $this->service->removeMissingInlineImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 9: Inline image using Image: prefix (alias) - missing
     */
    public function testInlineImagePrefixMissing()
    {
        $this->setupMockImageExists([
            'Non_existent_old_xyz111.png' => false
        ]);

        $input = "[[Image:Non_existent_old_xyz111.png|thumb|Old style image link]]";

        $expected = "";

        $result = $this->service->removeMissingInlineImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 10: Inline image exists using Image: prefix
     */
    public function testInlineImagePrefixExists()
    {
        $this->setupMockImageExists([
            'AwareLogo.png' => true
        ]);

        $input = "[[Image:AwareLogo.png|thumb|Old style but valid]]";

        $result = $this->service->removeMissingInlineImages($input);

        $this->assertEquals($input, $result);
    }

    /**
     * TEST 11: Both infobox and inline images - mixed
     */
    public function testCombinedMixed()
    {
        $this->setupMockImageExists([
            'Non_existent_infobox_xyz222.png' => false,
            'Gallstones.png' => true,
            'Another_missing_xyz333.jpg' => false
        ]);

        $input = "{{Infobox disease|name={{PAGENAME}}|image=Non_existent_infobox_xyz222.png|caption=Remove this caption|specialty=[[Orthopedics]]}}This article discusses the condition.[[File:Gallstones.png|thumb|right|A valid inline image]]More information here.[[File:Another_missing_xyz333.jpg|left|Remove this too]]End of article.";

        $expected = "{{Infobox disease|name={{PAGENAME}}|specialty=[[Orthopedics]]}}This article discusses the condition.[[File:Gallstones.png|thumb|right|A valid inline image]]More information here.End of article.";

        $result = $this->service->removeMissingImages($input);

        $this->assertEqualCompare($expected, $input, $result);

        // assert contains [[File:Gallstones.png|thumb|right|A valid inline image]]
        $this->assertStringContainsString('[[File:Gallstones.png|thumb|right|A valid inline image]]', $result);
        $this->assertStringNotContainsString('Non_existent_infobox_xyz222.png', $result);
    }

    /**
     * TEST 12: No images at all - no changes
     */
    public function testNoImages()
    {
        $this->setupMockImageExists([]);

        $input = "|name ={{PAGENAME}}\n|synonym =\n|specialty =[[Orthopedics]]\n\nThis is just plain text without any images.";

        $result = $this->service->removeMissingImages($input);

        $this->assertEquals($input, $result);
    }

    /**
     * TEST 13: Complex nested caption with existing image (returns empty due to domain logic)
     */
    public function testComplexNestedCaptionNccommons()
    {
        $this->setupMockImageExists([
            'AwareLogo.png' => false
        ]);

        $input = "[[File:AwareLogo.png|thumb|upright=1.3|Logo of the [[WHO]] Aware [[Classification]]]]__NOTOC__";

        $result = $this->service->removeMissingInlineImages($input);

        $this->assertEquals("__NOTOC__", $result);
    }

    public function testComplexNestedCaptionCommons()
    {
        $this->setupMockImageExists([
            'Gallstones.png' => true
        ]);

        $input = "[[File:Gallstones.png|thumb|upright=1.3|Gallstones typically form in the [[gallbladder]] and may result in symptoms if they block the biliary system.]]";

        $result = $this->service->removeMissingInlineImages($input);

        $this->assertEquals($input, $result);
    }
}
