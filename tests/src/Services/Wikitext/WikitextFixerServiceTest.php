<?php

namespace Tests\Services\Wikitext;

use PHPUnit\Framework\TestCase;

use function MDWiki\NewHtml\Services\Wikitext\fix_wikitext;

/**
 * @covers \MDWiki\NewHtml\Services\Wikitext\fix_wikitext
 */
class WikitextFixerServiceTest extends TestCase
{
    private function loadFixture(string $name): string
    {
        $path = __DIR__ . '/data/' . $name;
        $this->assertFileExists($path, "Fixture file missing: {$path}");

        $content = file_get_contents($path);
        $this->assertNotFalse($content, "Unable to read fixture file: {$path}");

        return $content;
    }

    public function testFixWikitextMatchesResultFixture()
    {
        $source = $this->loadFixture('source-1.wiki');
        $expected = $this->loadFixture('result-1.wiki');

        $result = fix_wikitext($source, "PLACEHOLDER_TITLE");

        $this->assertSame($expected, $result);
    }

    public function testFixWikitextIsDeterministic()
    {
        $source = $this->loadFixture('source-1.wiki');

        $first = fix_wikitext($source, "PLACEHOLDER_TITLE");
        $second = fix_wikitext($source, "PLACEHOLDER_TITLE");

        $this->assertSame($first, $second);
    }

    public function testFixWikitextWithEmptyInputReturnsEmpty()
    {
        $result = fix_wikitext('', "PLACEHOLDER_TITLE");

        $this->assertSame('', $result);
    }
}
