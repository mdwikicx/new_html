<?php

namespace FixRefs\Tests\WikiParse;

use FixRefs\Tests\bootstrap;
use MDWiki\NewHtml\Domain\Parser\ParserTemplates;

use function MDWiki\NewHtml\Domain\Parser\getTemplates;

class ParserTemplatesTest extends bootstrap
{

    public function testParserTemplatesMultiple()
    {
        $text = '{{Template1|param1}} text {{Template2|param2}}';
        $parser = new ParserTemplates($text);
        $templates = $parser->getTemplates();

        $this->assertIsArray($templates);
        $this->assertGreaterThanOrEqual(2, count($templates));
    }

    public function testParserTemplatesNested()
    {
        $text = '{{Outer|inner={{Inner|value}}}}';
        $parser = new ParserTemplates($text);
        $templates = $parser->getTemplates();

        $this->assertIsArray($templates);
        $this->assertGreaterThanOrEqual(1, count($templates));
    }

    public function testGetTemplatesFunction()
    {
        $text = '{{Template1}} and {{Template2|param=value}}';
        $templates = getTemplates($text);

        $this->assertIsArray($templates);
        $this->assertGreaterThanOrEqual(2, count($templates));
    }

    public function testGetTemplatesWithEmptyText()
    {
        $templates = getTemplates('');

        $this->assertIsArray($templates);
        $this->assertEmpty($templates);
    }

    public function testGetTemplatesWithNoTemplates()
    {
        $templates = getTemplates('Just plain text without templates');

        $this->assertIsArray($templates);
        $this->assertEmpty($templates);
    }

    public function testParserTemplatesWithComplexNesting()
    {
        $text = '{{Level1|param={{Level2|inner={{Level3}}}}}}';
        $parser = new ParserTemplates($text);
        $templates = $parser->getTemplates();

        $this->assertIsArray($templates);
        $this->assertGreaterThanOrEqual(1, count($templates));
    }
}
