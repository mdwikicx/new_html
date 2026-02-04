<?php

namespace FixRefs\Tests\WikiTextFixes;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Domain\Fixes\Media\remove_images;
use function MDWiki\NewHtml\Domain\Fixes\Media\remove_videos;

class FixImagesTest extends bootstrap
{
    public function testRemoveImagesWithSimpleImage()
    {
        $text = 'Text [[File:Example.png|thumb|Description]] more text';
        $result = remove_images($text);

        $this->assertStringContainsString('{{subst:#ifexist:File:Example.png|', $result);
        $this->assertStringContainsString('[[File:Example.png|thumb|Description]]}}', $result);
    }

    public function testRemoveImagesWithMultipleImages()
    {
        $text = '[[File:Image1.jpg|thumb]] text [[File:Image2.png|Description]]';
        $result = remove_images($text);

        $this->assertStringContainsString('{{subst:#ifexist:File:Image1.jpg|', $result);
        $this->assertStringContainsString('{{subst:#ifexist:File:Image2.png|', $result);
    }

    public function testRemoveImagesWithComplexParameters()
    {
        $text = '[[File:Test.jpg|thumb|upright=1.3|alt=Alt text|Caption with [[link]]]]';
        $result = remove_images($text);

        $this->assertStringContainsString('{{subst:#ifexist:File:Test.jpg|', $result);
        $this->assertStringContainsString('[[link]]', $result);
    }

    public function testRemoveImagesWithNoImages()
    {
        $text = 'Plain text without images';
        $result = remove_images($text);

        $this->assertEquals($text, $result);
    }

    public function testRemoveImagesWithEmptyText()
    {
        $result = remove_images('');

        $this->assertEquals('', $result);
    }

    public function testRemoveImagesPreservesNonFileLinks()
    {
        $text = '[[Article link]] and [[File:Image.jpg|thumb]] and [[Category:Test]]';
        $result = remove_images($text);

        $this->assertStringContainsString('[[Article link]]', $result);
        $this->assertStringContainsString('[[Category:Test]]', $result);
        $this->assertStringContainsString('{{subst:#ifexist:', $result);
    }

    public function testRemoveVideosWithWebmFile()
    {
        $text = 'Text [[File:Video.webm|frameless|Description]] more';
        $result = remove_videos($text);

        $this->assertStringNotContainsString('[[File:Video.webm', $result);
        $this->assertStringContainsString('Text', $result);
        $this->assertStringContainsString('more', $result);
    }

    public function testRemoveVideosWithOgvFile()
    {
        $text = '[[File:Video.ogv|thumb|Video description]]';
        $result = remove_videos($text);

        $this->assertStringNotContainsString('[[File:Video.ogv', $result);
    }

    public function testRemoveVideosWithOggFile()
    {
        $text = '[[File:Audio.ogg|Description]]';
        $result = remove_videos($text);

        $this->assertStringNotContainsString('[[File:Audio.ogg', $result);
    }

    public function testRemoveVideosWithMp4File()
    {
        $text = '[[File:Video.mp4|thumb|upright=1.36|Description]]';
        $result = remove_videos($text);

        $this->assertStringNotContainsString('[[File:Video.mp4', $result);
    }

    public function testRemoveVideosPreservesImages()
    {
        $text = '[[File:Image.jpg|thumb]] and [[File:Video.webm|Video]]';
        $result = remove_videos($text);

        $this->assertStringContainsString('[[File:Image.jpg|thumb]]', $result);
        $this->assertStringNotContainsString('[[File:Video.webm', $result);
    }

    public function testRemoveVideosWithMultipleVideos()
    {
        $text = '[[File:V1.webm|Video 1]] [[File:V2.ogv|Video 2]] [[File:V3.mp4|Video 3]]';
        $result = remove_videos($text);

        $this->assertStringNotContainsString('[[File:V1.webm', $result);
        $this->assertStringNotContainsString('[[File:V2.ogv', $result);
        $this->assertStringNotContainsString('[[File:V3.mp4', $result);
    }

    public function testRemoveVideosWithCaseVariations()
    {
        $text = '[[File:Video.WEBM|Uppercase]] [[File:Video.Ogv|Mixed]]';
        $result = remove_videos($text);

        $this->assertStringNotContainsString('[[File:Video.WEBM', $result);
        $this->assertStringNotContainsString('[[File:Video.Ogv', $result);
    }

    public function testRemoveVideosWithNoVideos()
    {
        $text = '[[File:Image.jpg|thumb]] [[File:Photo.png|Description]]';
        $result = remove_videos($text);

        // Images should be preserved
        $this->assertStringContainsString('[[File:Image.jpg|thumb]]', $result);
        $this->assertStringContainsString('[[File:Photo.png|Description]]', $result);
    }

    public function testRemoveVideosWithEmptyText()
    {
        $result = remove_videos('');

        $this->assertEquals('', $result);
    }

    public function testRemoveImagesWithNestedLinks()
    {
        $text = '[[File:Test.jpg|Caption with [[nested link|display]]]]';
        $result = remove_images($text);

        $this->assertStringContainsString('{{subst:#ifexist:File:Test.jpg|', $result);
        $this->assertStringContainsString('[[nested link|display]]', $result);
    }

    public function testRemoveVideosWithComplexParameters()
    {
        $text = '[[File:Video.webm|frameless|upright=1.36|thumbtime=2:25|Video explanation]]';
        $result = remove_videos($text);

        $this->assertStringNotContainsString('[[File:Video.webm', $result);
    }

    public function testRemoveImagesWithUpright()
    {
        $text = '[[File:Logo.png|thumb|upright=1.3|Logo description]]';
        $result = remove_images($text);

        $this->assertStringContainsString('{{subst:#ifexist:File:Logo.png|', $result);
        $this->assertStringContainsString('upright=1.3', $result);
    }

    public function testRemoveVideosWithNestedTemplate()
    {
        $text = '[[File:Video.webm|Description with {{template}}]]';
        $result = remove_videos($text);

        $this->assertStringNotContainsString('[[File:Video.webm', $result);
    }

    public function testRemoveImagesMultipleOccurrencesSameFile()
    {
        $text = '[[File:Same.jpg|thumb]] text [[File:Same.jpg|Different caption]]';
        $result = remove_images($text);

        // Both should be wrapped
        $count = substr_count($result, '{{subst:#ifexist:File:Same.jpg|');
        $this->assertEquals(2, $count);
    }

    public function testRemoveVideosPreservesTextAround()
    {
        $text = 'Before video [[File:Test.webm|Video]] after video';
        $result = remove_videos($text);

        $this->assertStringContainsString('Before video', $result);
        $this->assertStringContainsString('after video', $result);
        $this->assertStringNotContainsString('[[File:Test.webm', $result);
    }

    public function testRemoveImagesWithSpecialCharactersInFilename()
    {
        $text = '[[File:Test-image_2020 (1).png|Description]]';
        $result = remove_images($text);

        $this->assertStringContainsString('{{subst:#ifexist:File:Test-image_2020 (1).png|', $result);
    }

    public function testRemoveVideosDoesNotAffectNonVideoExtensions()
    {
        $text = '[[File:Document.pdf|Document]] [[File:Audio.mp3|Audio]]';
        $result = remove_videos($text);

        // Non-video files should remain
        $this->assertStringContainsString('[[File:Document.pdf', $result);
        $this->assertStringContainsString('[[File:Audio.mp3', $result);
    }
}
