<?php

namespace FixRefs\Tests\WikiParse;

use FixRefs\Tests\bootstrap;

use function Lead\get_lead_section;

class LeadSectionTest extends bootstrap
{
    public function testGetLeadSectionWithSections()
    {
        $wikitext = "Lead paragraph content.\n\n==Section 1==\nSection content.\n\n==Section 2==\nMore content.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString('Lead paragraph content.', $result);
        $this->assertStringNotContainsString('Section 1', $result);
        $this->assertStringNotContainsString('Section content.', $result);
        $this->assertStringContainsString('==References==', $result);
        $this->assertStringContainsString('<references />', $result);
    }

    public function testGetLeadSectionWithNoSections()
    {
        $wikitext = "Only lead content without any sections.";
        $result = get_lead_section($wikitext);

        $this->assertEquals($wikitext, $result);
    }

    public function testGetLeadSectionWithEmptyText()
    {
        $result = get_lead_section('');

        $this->assertEquals('', $result);
    }
    public function testGetLeadSectionWithMultipleLevelHeadings()
    {
        $wikitext = "Lead text.\n\n==Level 2==\nContent.\n\n===Level 3===\nMore content.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString('Lead text.', $result);
        $this->assertStringNotContainsString('Level 2', $result);
        $this->assertStringNotContainsString('Level 3', $result);
    }

    public function testGetLeadSectionAddsReferencesSection()
    {
        $wikitext = "Lead with citation.<ref>Source</ref>\n\n==Body==\nContent.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString('Lead with citation.<ref>Source</ref>', $result);
        $this->assertStringContainsString("\n==References==\n", $result);
        $this->assertStringContainsString('<references />', $result);
    }

    public function testGetLeadSectionWithComplexLead()
    {
        $wikitext = "First paragraph.\n\nSecond paragraph.\n\nThird paragraph.\n\n==First Section==\nShould not appear.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString('First paragraph.', $result);
        $this->assertStringContainsString('Second paragraph.', $result);
        $this->assertStringContainsString('Third paragraph.', $result);
        $this->assertStringNotContainsString('Should not appear.', $result);
    }

    public function testGetLeadSectionWithTemplatesInLead()
    {
        $wikitext = "{{Infobox|param=value}}\n\nLead text.\n\n==Section==\nContent.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString('{{Infobox|param=value}}', $result);
        $this->assertStringContainsString('Lead text.', $result);
        $this->assertStringNotContainsString('Content.', $result);
    }

    public function testGetLeadSectionPreservesFormatting()
    {
        $wikitext = "'''Bold text''' and ''italic text''.\n\n==Section==\nContent.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString("'''Bold text'''", $result);
        $this->assertStringContainsString("''italic text''", $result);
    }

    public function testGetLeadSectionWithLinksInLead()
    {
        $wikitext = "Text with [[link]] and [[link|display text]].\n\n==Section==\nContent.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString('[[link]]', $result);
        $this->assertStringContainsString('[[link|display text]]', $result);
    }

    public function testGetLeadSectionWithWhitespaceAroundHeadings()
    {
        $wikitext = "Lead text.\n\n  ==Section==  \nContent.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString('Lead text.', $result);
        $this->assertStringNotContainsString('Content.', $result);
    }

    public function testGetLeadSectionWithReferencesInLead()
    {
        $wikitext = "Text with citation.<ref>Full citation</ref> More text.\n\n==Section==\nContent.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString('<ref>Full citation</ref>', $result);
        $this->assertStringContainsString('<references />', $result);
    }

    public function testGetLeadSectionDoesNotDoubleAddReferences()
    {
        $wikitext = "Lead text.\n\n==Section==\nContent.";
        $result = get_lead_section($wikitext);

        // Count occurrences of ==References==
        $count = substr_count($result, '==References==');
        $this->assertEquals(1, $count);
    }

    public function testGetLeadSectionWithLongLead()
    {
        $lead = str_repeat("Paragraph. ", 100);
        $wikitext = $lead . "\n\n==Section==\nContent.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString('Paragraph.', $result);
        $this->assertStringNotContainsString('Content.', $result);
    }


    public function testGetLeadSectionWithHeadingWithEquals()
    {
        $wikitext = "Lead.\n\n==Section with = sign==\nContent.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString('Lead.', $result);
        $this->assertStringNotContainsString('Section with = sign', $result);
    }

    public function testGetLeadSectionEmptyLeadWithSections()
    {
        $wikitext = "==First Section==\nContent.";
        $result = get_lead_section($wikitext);

        // When lead is empty, should add references section
        $this->assertStringNotContainsString('First Section', $result);
        $this->assertEquals("", trim($result));
    }

    public function testGetLeadSectionWithHeadingAtStart()
    {
        $wikitext = "==Introduction==\nIntro content.\n\n==Body==\nBody content.";
        $result = get_lead_section($wikitext);

        $this->assertStringNotContainsString('Introduction', $result);
        $this->assertStringNotContainsString('Intro content', $result);
    }
    public function testGetLeadSectionWithFalsePositiveHeadings()
    {
        // Test with == inside code or template
        $wikitext = "Lead text with == in code.\n\n==Real Section==\nContent.";
        $result = get_lead_section($wikitext);

        $this->assertStringContainsString('Lead text with == in code.', $result);
    }

    public function testGetLeadSectionWithOnlyHeading()
    {
        $wikitext = "==Heading==";
        $result = get_lead_section($wikitext);

        $this->assertStringNotContainsString('Heading', $result);
        $this->assertEquals("", trim($result));
    }
}
