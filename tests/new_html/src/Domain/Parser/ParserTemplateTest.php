<?php

namespace FixRefs\Tests\WikiParse;

use FixRefs\Tests\bootstrap;
use MDWiki\NewHtml\Domain\Parser\ParserTemplate;

class ParserTemplateTest extends bootstrap
{
    public function testParserTemplateSimple()
    {
        $templateText = '{{Infobox|param1=value1|param2=value2}}';
        $parser = new ParserTemplate($templateText);
        $template = $parser->getTemplate();

        $this->assertEquals('Infobox', $template->getName());
        $this->assertEquals('value1', $template->getParameter('param1'));
        $this->assertEquals('value2', $template->getParameter('param2'));
    }

    public function testParserTemplateWithPositionalParams()
    {
        $templateText = '{{Template|first|second|third}}';
        $parser = new ParserTemplate($templateText);
        $template = $parser->getTemplate();

        $this->assertEquals('Template', $template->getName());
        $this->assertEquals('first', $template->getParameter(1));
        $this->assertEquals('second', $template->getParameter(2));
        $this->assertEquals('third', $template->getParameter(3));
    }

    public function testParserTemplateWithNestedTemplate()
    {
        $templateText = '{{Outer|param={{Inner|value}}}}';
        $parser = new ParserTemplate($templateText);
        $template = $parser->getTemplate();

        $this->assertEquals('Outer', $template->getName());
        $this->assertStringContainsString('{{Inner|value}}', $template->getParameter('param'));
    }

    public function testParserTemplateWithNestedLink()
    {
        $templateText = '{{Template|link=[[Article|display text]]}}';
        $parser = new ParserTemplate($templateText);
        $template = $parser->getTemplate();

        $this->assertEquals('Template', $template->getName());
        $this->assertStringContainsString('[[Article|display text]]', $template->getParameter('link'));
    }


    public function testParserTemplateWithWhitespace()
    {
        $templateText = '{{ Template | param1 = value1 | param2 = value2 }}';
        $parser = new ParserTemplate($templateText);
        $template = $parser->getTemplate();

        $this->assertStringContainsString('Template', $template->getName());
        $this->assertEquals('value1', $template->getParameter('param1'));
    }

    public function testParserTemplateWithMultilineParameters()
    {
        $templateText = "{{Template
|param1=value1
|param2=value2
}}";
        $parser = new ParserTemplate($templateText);
        $template = $parser->getTemplate();

        $this->assertStringContainsString('Template', $template->getName());
        $this->assertEquals('value1', $template->getParameter('param1'));
        $this->assertEquals('value2', $template->getParameter('param2'));
    }
}
