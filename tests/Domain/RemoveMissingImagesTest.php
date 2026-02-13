<?php

namespace FixRefs\Tests;

use FixRefs\Tests\bootstrap;
use function MDWiki\NewHtml\Domain\Fixes\Media\removeMissingInfoboxImages;
use function MDWiki\NewHtml\Domain\Fixes\Media\removeMissingInlineImages;
use function MDWiki\NewHtml\Domain\Fixes\Media\removeMissingImages;

class RemoveMissingImagesTest extends bootstrap
{
    /**
     * TEST 1: Infobox image exists - no changes
     * Note: This test requires API access and may be skipped in environments without internet
     */
    public function testInfoboxImageExists()
    {
        $input = "|name ={{PAGENAME}}\n|image =AwareLogo.png\n|caption =This is a valid image\n|specialty =[[Orthopedics]]";

        $result = removeMissingInfoboxImages($input);

        // Should not change since AwareLogo.png exists
        $this->assertEquals($input, $result);
    }

    /**
     * TEST 2: Infobox image missing - remove image and caption
     * Note: This test requires API access and may be skipped in environments without internet
     */
    public function testInfoboxImageMissing()
    {
        $input = "|name ={{PAGENAME}}\n|image =Non_existent_image_xyz789.png\n|caption =This caption should be removed\n|specialty =[[Orthopedics]]";

        $expected = "|name ={{PAGENAME}}\n|specialty =[[Orthopedics]]";

        $result = removeMissingInfoboxImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 3: Empty infobox image field - remove both lines
     */
    public function testInfoboxEmptyImage()
    {
        $input = "|name ={{PAGENAME}}\n|image =\n|caption =Caption for empty image\n|specialty =[[Orthopedics]]";

        $expected = "|name ={{PAGENAME}}\n|specialty =[[Orthopedics]]";

        $result = removeMissingInfoboxImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 4: Multiple infobox images - mixed existence
     * Note: This test requires API access and may be skipped in environments without internet
     */
    public function testInfoboxMultipleImagesMixed()
    {
        $input = "|name ={{PAGENAME}}\n|image =AwareLogo.png\n|caption =Valid caption\n|image2 =Missing_image_xyz123456.png\n|caption2 =This should be removed\n|specialty =[[Orthopedics]]";

        $expected = "|name ={{PAGENAME}}\n|image =AwareLogo.png\n|caption =Valid caption\n|specialty =[[Orthopedics]]";

        $result = removeMissingInfoboxImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 5: Inline image exists - no changes
     * Note: This test requires API access and may be skipped in environments without internet
     */
    public function testInlineImageExists()
    {
        $input = "This is some text with an image:\n[[File:AwareLogo.png|thumb|A valid image caption]]\nMore text here.";

        $result = removeMissingInlineImages($input);

        $this->assertEquals($input, $result);
    }

    /**
     * TEST 6: Inline image missing - remove entire block
     * Note: This test requires API access and may be skipped in environments without internet
     */
    public function testInlineImageMissing()
    {
        $input = "This is some text with an image:\n[[File:Non_existent_image_xyz654.png|thumb|This should be removed]]\nMore text here.";

        $expected = "This is some text with an image:\n\nMore text here.";

        $result = removeMissingInlineImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 7: Multiple inline images - mixed existence
     * Note: This test requires API access and may be skipped in environments without internet
     */
    public function testInlineMultipleImagesMixed()
    {
        $input = "Start of article.\n[[File:AwareLogo.png|thumb|Keep this image]]\nSome middle text.\n[[File:Missing_file_xyz987.jpg|left|200px|Remove this]]\nEnd of article.";

        $expected = "Start of article.\n[[File:AwareLogo.png|thumb|Keep this image]]\nSome middle text.\n\nEnd of article.";

        $result = removeMissingInlineImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 8: Inline image with nested links in caption
     * Note: This test requires API access and may be skipped in environments without internet
     */
    public function testInlineImageNestedLinks()
    {
        $input = "[[File:Missing_image_nested_xyz321.png|thumb|See [[Orthopedics]] for more info]]";

        $expected = "";

        $result = removeMissingInlineImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 9: Inline image using Image: prefix (alias) - missing
     * Note: This test requires API access and may be skipped in environments without internet
     */
    public function testInlineImagePrefixMissing()
    {
        $input = "[[Image:Non_existent_old_xyz111.png|thumb|Old style image link]]";

        $expected = "";

        $result = removeMissingInlineImages($input);

        $this->assertEqualCompare($expected, $input, $result);
    }

    /**
     * TEST 10: Inline image exists using Image: prefix
     * Note: This test requires API access and may be skipped in environments without internet
     */
    public function testInlineImagePrefixExists()
    {
        $input = "[[Image:AwareLogo.png|thumb|Old style but valid]]";

        $result = removeMissingInlineImages($input);

        $this->assertEquals($input, $result);
    }

    /**
     * TEST 11: Both infobox and inline images - mixed
     * Note: This test requires API access and may be skipped in environments without internet
     */
    public function testCombinedMixed()
    {
        $input = "{{Infobox disease|name={{PAGENAME}}|image=Non_existent_infobox_xyz222.png|caption=Remove this caption|specialty=[[Orthopedics]]}}This article discusses the condition.[[File:Gallstones.png|thumb|right|A valid inline image]]More information here.[[File:Another_missing_xyz333.jpg|left|Remove this too]]End of article.";

        $expected = "{{Infobox disease|name={{PAGENAME}}|specialty=[[Orthopedics]]}}This article discusses the condition.[[File:Gallstones.png|thumb|right|A valid inline image]]More information here.End of article.";

        $result = removeMissingImages($input);

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
        $input = "|name ={{PAGENAME}}\n|synonym =\n|specialty =[[Orthopedics]]\n\nThis is just plain text without any images.";

        $result = removeMissingImages($input);

        $this->assertEquals($input, $result);
    }


    /**
     * TEST 13: Complex nested caption with existing image
     * Note: This test requires API access and may be skipped in environments without internet
     */
    public function testComplexNestedCaptionNccommons()
    {
        $input = "[[File:AwareLogo.png|thumb|upright=1.3|Logo of the [[WHO]] Aware [[Classification]]]]";

        $result = removeMissingInlineImages($input);

        $this->assertEquals($result, "");
    }
    public function testComplexNestedCaptionCommons()
    {
        $input = "[[File:Gallstones.png|thumb|upright=1.3|Gallstones typically form in the [[gallbladder]] and may result in symptoms if they block the biliary system.]]";

        $result = removeMissingInlineImages($input);

        $this->assertEquals($input, $result);
    }
}
