<?php

namespace FixRefs\Tests\WikiTextFixes;

use PHPUnit\Framework\TestCase;

use function Fixes\FixTemps\add_missing_title;

class FixTempsTest extends TestCase
{
    public function testAddMissingTitleWithDrugbox()
    {
        $text = '{{Drugbox|other_param=value}}';
        $result = add_missing_title($text, 'Aspirin');

        $this->assertStringContainsString('drug_name=Aspirin', $result);
        $this->assertStringContainsString('other_param=value', $result);
    }

    public function testAddMissingTitleWithInfoboxDrug()
    {
        $text = '{{Infobox drug|param=value}}';
        $result = add_missing_title($text, 'Paracetamol');

        $this->assertStringContainsString('drug_name=Paracetamol', $result);
    }

    public function testAddMissingTitleWithInfoboxMedicalCondition()
    {
        $text = '{{Infobox medical condition|symptoms=test}}';
        $result = add_missing_title($text, 'Diabetes');

        $this->assertStringContainsString('name=Diabetes', $result);
        $this->assertStringContainsString('symptoms=test', $result);
    }

    public function testAddMissingTitleWithInfoboxMedicalIntervention()
    {
        $text = '{{Infobox medical intervention|param=value}}';
        $result = add_missing_title($text, 'Surgery');

        $this->assertStringContainsString('name=Surgery', $result);
    }

    public function testAddMissingTitleDoesNotOverwriteExisting()
    {
        $text = '{{Drugbox|drug_name=Existing Name|param=value}}';
        $result = add_missing_title($text, 'New Name');

        $this->assertStringContainsString('drug_name=Existing Name', $result);
        $this->assertStringNotContainsString('drug_name=New Name', $result);
    }

    public function testAddMissingTitleWithEmptyName()
    {
        $text = '{{Drugbox|drug_name=}}';
        $result = add_missing_title($text, 'Medicine');

        $this->assertStringContainsString('drug_name=Medicine', $result);
    }

    public function testAddMissingTitleWithWhitespaceName()
    {
        $text = '{{Drugbox|drug_name=   }}';
        $result = add_missing_title($text, 'Medicine');

        $this->assertStringContainsString('drug_name=Medicine', $result);
    }

    public function testAddMissingTitleWithNoMatchingTemplate()
    {
        $text = '{{Other template|param=value}}';
        $result = add_missing_title($text, 'Title');

        // Should remain unchanged
        $this->assertEquals($text, $result);
    }

    public function testAddMissingTitleWithMultipleTemplates()
    {
        $text = '{{Drugbox}} {{Infobox medical condition}}';
        $result = add_missing_title($text, 'Test Title');

        $this->assertStringContainsString('drug_name=Test Title', $result);
        $this->assertStringContainsString('name=Test Title', $result);
    }

    public function testAddMissingTitleWithCaseInsensitive()
    {
        $text = '{{DRUGBOX|param=value}}';
        $result = add_missing_title($text, 'Medicine');

        $this->assertStringContainsString('drug_name=Medicine', $result);
    }

    public function testAddMissingTitleWithUnderscores()
    {
        $text = '{{Drug_box|param=value}}';
        $result = add_missing_title($text, 'Medicine');

        $this->assertStringContainsString('drug_name=Medicine', $result);
    }

    public function testAddMissingTitlePreservesOtherParameters()
    {
        $text = '{{Drugbox|param1=value1|param2=value2|param3=value3}}';
        $result = add_missing_title($text, 'Drug Name');

        $this->assertStringContainsString('param1=value1', $result);
        $this->assertStringContainsString('param2=value2', $result);
        $this->assertStringContainsString('param3=value3', $result);
        $this->assertStringContainsString('drug_name=Drug Name', $result);
    }

    public function testAddMissingTitleFormatsWithNewLine()
    {
        $text = '{{Drugbox|param=value}}';
        $result = add_missing_title($text, 'Medicine');

        // Should format with new lines
        $this->assertStringContainsString("\n", $result);
    }

    public function testAddMissingTitleWithEmptyText()
    {
        $result = add_missing_title('', 'Title');

        $this->assertEquals('', $result);
    }

    public function testAddMissingTitleWithNoTemplates()
    {
        $text = 'Plain text without templates';
        $result = add_missing_title($text, 'Title');

        $this->assertEquals($text, $result);
    }

    public function testAddMissingTitleWithMultilineTemplate()
    {
        $text = "{{Drugbox\n|param1=value1\n|param2=value2\n}}";
        $result = add_missing_title($text, 'Medicine');

        $this->assertStringContainsString('drug_name=Medicine', $result);
    }

    public function testAddMissingTitleWithNestedTemplates()
    {
        $text = '{{Drugbox|param={{nested|value}}}}';
        $result = add_missing_title($text, 'Medicine');

        $this->assertStringContainsString('drug_name=Medicine', $result);
        $this->assertStringContainsString('{{nested|value}}', $result);
    }

    public function testAddMissingTitleWithSpecialCharacters()
    {
        $text = '{{Drugbox|param=value}}';
        $result = add_missing_title($text, 'Medicine-123 (Test)');

        $this->assertStringContainsString('drug_name=Medicine-123 (Test)', $result);
    }

    public function testAddMissingTitleReplacesTemplate()
    {
        $text = 'Before {{Drugbox|old=param}} After';
        $result = add_missing_title($text, 'New Drug');

        $this->assertStringContainsString('Before', $result);
        $this->assertStringContainsString('After', $result);
        $this->assertStringContainsString('drug_name=New Drug', $result);
    }

    public function testAddMissingTitleWithLjustFormatting()
    {
        $text = '{{Drugbox|a=value1|longer_param=value2}}';
        $result = add_missing_title($text, 'Medicine');

        // The function uses ljust=17 for formatting
        $this->assertStringContainsString('drug_name=Medicine', $result);
    }

    public function testAddMissingTitleDoesNotAffectOtherTemplates()
    {
        $text = '{{Cite|title=Test}} {{Drugbox}} {{Another}}';
        $result = add_missing_title($text, 'Medicine');

        $this->assertStringContainsString('{{Cite|title=Test}}', $result);
        $this->assertStringContainsString('{{Another}}', $result);
        $this->assertStringContainsString('drug_name=Medicine', $result);
    }

    public function testAddMissingTitleWithMixedCase()
    {
        $text = '{{Infobox Medical Condition|param=value}}';
        $result = add_missing_title($text, 'Disease');

        $this->assertStringContainsString('name=Disease', $result);
    }

    public function testAddMissingTitlePreservesOrder()
    {
        $text = '{{Drugbox|first=1|second=2}}';
        $result = add_missing_title($text, 'Medicine');

        // New parameter should be added, existing order preserved
        $this->assertStringContainsString('drug_name=Medicine', $result);
        $this->assertStringContainsString('first=1', $result);
        $this->assertStringContainsString('second=2', $result);
    }
}