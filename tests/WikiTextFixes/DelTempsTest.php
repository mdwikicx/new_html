<?php

namespace FixRefs\Tests\WikiTextFixes;

use FixRefs\Tests\bootstrap;

use function Fixes\DelTemps\remove_templates;
use function Fixes\DelTemps\remove_lead_templates;

class DelTempsTest extends bootstrap
{
    public function testRemoveTemplatesWithShortDescription()
    {
        $text = '{{Short description|Test article}} Article content';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('{{Short description|Test article}}', $result);
        $this->assertStringContainsString('Article content', $result);
    }

    public function testRemoveTemplatesWithMultipleDeleteTargets()
    {
        $text = '{{Featured article}} {{Good article}} Content {{Use dmy dates}}';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('{{Featured article}}', $result);
        $this->assertStringNotContainsString('{{Good article}}', $result);
        $this->assertStringNotContainsString('{{Use dmy dates}}', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function testRemoveTemplatesWithStubTemplate()
    {
        $text = 'Article content {{Biology-stub}}';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('{{Biology-stub}}', $result);
        $this->assertStringContainsString('Article content', $result);
    }

    public function testRemoveTemplatesWithPPTemplates()
    {
        $text = '{{pp-protected}} {{pp-semi}} Article content';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('{{pp-protected}}', $result);
        $this->assertStringNotContainsString('{{pp-semi}}', $result);
    }

    public function testRemoveTemplatesWithArticlesPattern()
    {
        $text = '{{Articles for deletion}} {{Articles needing cleanup}} Content';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('{{Articles for deletion}}', $result);
        $this->assertStringNotContainsString('{{Articles needing cleanup}}', $result);
    }

    public function testRemoveTemplatesPreservesOtherTemplates()
    {
        $text = '{{Short description|Test}} {{Infobox|param=value}} Content';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('{{Short description|Test}}', $result);
        $this->assertStringContainsString('{{Infobox|param=value}}', $result);
    }

    public function testRemoveTemplatesWithCaseInsensitive()
    {
        $text = '{{SHORT DESCRIPTION|Test}} {{Short Description|Test2}} Content';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('SHORT DESCRIPTION', $result);
        $this->assertStringNotContainsString('Short Description', $result);
    }

    public function testRemoveTemplatesWithUnlinkedWikibase()
    {
        $text = '{{#unlinkedwikibase:test}} Content';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('{{#unlinkedwikibase:test}}', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function testRemoveTemplatesWithUseSpellingTemplates()
    {
        $text = '{{Use American English}} {{Use British spelling}} Content';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('Use American English', $result);
        $this->assertStringNotContainsString('Use British spelling', $result);
    }

    public function testRemoveTemplatesWithNoMatchingTemplates()
    {
        $text = '{{Infobox|param=value}} {{Citation needed}} Content';
        $result = remove_templates($text);

        // These templates should not be removed
        $this->assertStringContainsString('{{Infobox|param=value}}', $result);
        $this->assertStringContainsString('{{Citation needed}}', $result);
    }

    public function testRemoveTemplatesWithEmptyText()
    {
        $result = remove_templates('');

        $this->assertEquals('', $result);
    }

    public function testRemoveTemplatesWithNoTemplates()
    {
        $text = 'Plain text without any templates';
        $result = remove_templates($text);

        $this->assertEquals($text, $result);
    }

    public function testRemoveLeadTemplatesFindsInfobox()
    {
        $text = 'Pre-infobox content {{Infobox medical condition|name=Test}} Article content';
        $result = remove_lead_templates($text);

        $this->assertStringNotContainsString('Pre-infobox content', $result);
        $this->assertStringStartsWith('{{Infobox medical condition', $result);
    }

    public function testRemoveLeadTemplatesFindsDrugbox()
    {
        $text = 'Header content {{Drugbox|name=Drug}} Main content';
        $result = remove_lead_templates($text);

        $this->assertStringNotContainsString('Header content', $result);
        $this->assertStringStartsWith('{{Drugbox', $result);
    }

    public function testRemoveLeadTemplatesFindsSpeciesbox()
    {
        $text = 'Pre content {{Speciesbox|name=Species}} Article';
        $result = remove_lead_templates($text);

        $this->assertStringNotContainsString('Pre content', $result);
        $this->assertStringStartsWith('{{Speciesbox', $result);
    }

    public function testRemoveLeadTemplatesWithNoTargetTemplate()
    {
        $text = 'Article content {{Other template}} more content';
        $result = remove_lead_templates($text);

        // Should return text as is (trimmed)
        $this->assertEquals(trim($text), $result);
    }

    public function testRemoveLeadTemplatesCaseInsensitive()
    {
        $text = 'Header {{INFOBOX medical condition|param=value}} Content';
        $result = remove_lead_templates($text);

        $this->assertStringNotContainsString('Header', $result);
        $this->assertStringContainsString('INFOBOX', $result);
    }

    public function testRemoveLeadTemplatesWithEmptyText()
    {
        $result = remove_lead_templates('');

        $this->assertEquals('', $result);
    }

    public function testRemoveLeadTemplatesTrimsResult()
    {
        $text = "   \n\n{{Infobox drug|name=Test}}   \n";
        $result = remove_lead_templates($text);

        $this->assertStringStartsWith('{{Infobox', $result);
        $this->assertEquals(trim($result), $result);
    }

    public function testRemoveTemplatesWithMultilineTemplate()
    {
        $text = "{{Short description\n|Test description\n}} Content";
        $result = remove_templates($text);

        $this->assertStringNotContainsString('Short description', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function testRemoveTemplatesWithNestedTemplates()
    {
        $text = '{{Use dmy dates}} {{Infobox|nested={{Short description|Test}}}} Content';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('{{Use dmy dates}}', $result);
        // Infobox should be preserved even with nested short description
        $this->assertStringContainsString('{{Infobox', $result);
    }

    public function testRemoveTemplatesWithRedirectTemplate()
    {
        $text = '{{Redirect|Test}} Article content';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('{{Redirect|Test}}', $result);
        $this->assertStringContainsString('Article content', $result);
    }

    public function testRemoveTemplatesWithSprotect()
    {
        $text = '{{Sprotect}} Content';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('{{Sprotect}}', $result);
    }

    public function testRemoveTemplatesWithDefaultsort()
    {
        $text = 'Content {{DEFAULTSORT:Sort Key}}';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('{{DEFAULTSORT:Sort Key}}', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function testRemoveTemplatesWithWikipediaArticlesPattern()
    {
        $text = '{{Wikipedia articles needing cleanup}} Content';
        $result = remove_templates($text);

        $this->assertStringNotContainsString('Wikipedia articles needing cleanup', $result);
    }

    public function testRemoveLeadTemplatesWithMultipleInfoboxes()
    {
        $text = 'Pre {{Infobox 1}} and {{Drugbox}} content';
        $result = remove_lead_templates($text);

        // Should find first matching template
        $this->assertStringNotContainsString('Pre', $result);
        $this->assertStringStartsWith('{{Infobox', $result);
    }
}
