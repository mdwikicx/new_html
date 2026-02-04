<?php

namespace FixRefs\Tests\WikiParse;

use PHPUnit\Framework\TestCase;
use WikiParse\Template\Template;
use WikiParse\Template\ParserTemplate;
use WikiParse\Template\ParserTemplates;

use function WikiParse\Template\getTemplates;

class ParserTemplatesTest extends TestCase
{
    public function testTemplateConstructor()
    {
        $template = new Template('Infobox', ['1' => 'value1', 'param2' => 'value2']);

        $this->assertEquals('Infobox', $template->getName());
        $this->assertEquals(['1' => 'value1', 'param2' => 'value2'], $template->getParameters());
    }

    public function testTemplateGetStripName()
    {
        $template = new Template('Test_Template', []);

        $this->assertEquals('Test Template', $template->getStripName());
    }

    public function testTemplateGetParameter()
    {
        $template = new Template('Test', ['key1' => 'value1', 'key2' => 'value2']);

        $this->assertEquals('value1', $template->getParameter('key1'));
        $this->assertEquals('value2', $template->getParameter('key2'));
        $this->assertEquals('', $template->getParameter('nonexistent'));
        $this->assertEquals('default', $template->getParameter('nonexistent', 'default'));
    }

    public function testTemplateSetParameter()
    {
        $template = new Template('Test', []);
        $template->setParameter('new_key', 'new_value');

        $this->assertEquals('new_value', $template->getParameter('new_key'));
    }

    public function testTemplateDeleteParameter()
    {
        $template = new Template('Test', ['key1' => 'value1', 'key2' => 'value2']);
        $template->deleteParameter('key1');

        $this->assertEquals('', $template->getParameter('key1'));
        $this->assertEquals('value2', $template->getParameter('key2'));
    }

    public function testTemplateChangeParameterName()
    {
        $template = new Template('Test', ['old_name' => 'value']);
        $template->changeParameterName('old_name', 'new_name');

        $this->assertEquals('', $template->getParameter('old_name'));
        $this->assertEquals('value', $template->getParameter('new_name'));
    }

    public function testTemplateChangeParametersNames()
    {
        $template = new Template('Test', ['param1' => 'value1', 'param2' => 'value2', 'param3' => 'value3']);
        $template->changeParametersNames(['param1' => 'new1', 'param3' => 'new3']);

        $this->assertEquals('value1', $template->getParameter('new1'));
        $this->assertEquals('value2', $template->getParameter('param2'));
        $this->assertEquals('value3', $template->getParameter('new3'));
    }

    public function testTemplateToString()
    {
        $template = new Template('Cite', ['1' => 'value1', 'author' => 'John']);
        $result = $template->toString();

        $this->assertStringContainsString('{{Cite', $result);
        $this->assertStringContainsString('|value1', $result);
        $this->assertStringContainsString('|author=John', $result);
        $this->assertStringContainsString('}}', $result);
    }

    public function testTemplateToStringWithNewLine()
    {
        $template = new Template('Cite', ['author' => 'John', 'title' => 'Book']);
        $result = $template->toString(true);

        $this->assertStringContainsString("\n", $result);
        $this->assertStringContainsString('{{Cite', $result);
        $this->assertStringContainsString('}}', $result);
    }

    public function testTemplateToStringWithPositionalParameter()
    {
        $template = new Template('Template', [1 => 'first', 2 => 'second']);
        $result = $template->toString();

        $this->assertStringContainsString('|first', $result);
        $this->assertStringContainsString('|second', $result);
        $this->assertStringNotContainsString('1=', $result);
    }

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

    public function testTemplateSetTempName()
    {
        $template = new Template('OldName', []);
        $template->setTempName('NewName');

        $this->assertEquals('NewName', $template->getName());
    }

    public function testTemplateToStringWithLjust()
    {
        $template = new Template('Test', ['a' => 'value1', 'author' => 'value2']);
        $result = $template->toString(false, 10);

        $this->assertStringContainsString('|a         =value1', $result);
        $this->assertStringContainsString('|author    =value2', $result);
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

    public function testTemplateGetTemplateText()
    {
        $templateText = '{{Test|param=value}}';
        $template = new Template('Test', ['param' => 'value'], $templateText);

        $this->assertEquals($templateText, $template->getTemplateText());
    }

    public function testParserTemplatesWithComplexNesting()
    {
        $text = '{{Level1|param={{Level2|inner={{Level3}}}}}}';
        $parser = new ParserTemplates($text);
        $templates = $parser->getTemplates();

        $this->assertIsArray($templates);
        $this->assertGreaterThanOrEqual(1, count($templates));
    }

    public function testTemplateToStringNew()
    {
        $template = new Template('Test', ['param' => 'value']);
        $result = $template->toString_new();

        $this->assertStringContainsString('{{Test', $result);
        $this->assertStringContainsString('|param=value', $result);
        $this->assertStringContainsString('}}', $result);
    }

    public function testTemplateDeleteNonexistentParameter()
    {
        $template = new Template('Test', ['key1' => 'value1']);
        $template->deleteParameter('nonexistent');

        // Should not throw error
        $this->assertEquals('value1', $template->getParameter('key1'));
    }

    public function testTemplateChangeParameterNamePreservesOrder()
    {
        $template = new Template('Test', ['first' => 'a', 'second' => 'b', 'third' => 'c']);
        $template->changeParameterName('second', 'middle');

        $params = $template->getParameters();
        $keys = array_keys($params);
        $this->assertEquals(['first', 'middle', 'third'], $keys);
    }
}