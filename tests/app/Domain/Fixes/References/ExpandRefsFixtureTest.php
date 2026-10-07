<?php

namespace FixRefs\Tests\WikiTextFixes;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Domain\Fixes\References\expand_text_refs;

class ExpendRefsTest extends bootstrap
{
    public function testExpandTextRefsWithShortRefAndFullInAlltext()
    {
        $first = 'Lead text <ref name="cite" />';
        $alltext = 'Full article <ref name="cite">Full citation</ref>';

        $result = expand_text_refs($first, $alltext);

        $this->assertStringContainsString('<ref name="cite">Full citation</ref>', $result);
        $this->assertStringNotContainsString('<ref name="cite" />', $result);
    }

    public function testExpandTextRefsWithFullRefAlreadyInFirst()
    {
        $first = 'Lead text <ref name="cite">Citation</ref> <ref name="cite" />';
        $alltext = 'Full article <ref name="cite">Citation</ref>';

        $result = expand_text_refs($first, $alltext);

        // Short ref should remain because full ref is already in first
        $this->assertStringContainsString('<ref name="cite">Citation</ref>', $result);
        $this->assertStringContainsString('<ref name="cite" />', $result);
    }

    public function testExpandTextRefsWithNoMatchingFullRef()
    {
        $first = 'Lead text <ref name="orphan" />';
        $alltext = 'Full article <ref name="other">Other citation</ref>';

        $result = expand_text_refs($first, $alltext);

        // Short ref with no matching full ref should remain unchanged
        $this->assertStringContainsString('<ref name="orphan" />', $result);
    }

    public function testExpandTextRefsWithEmptyAlltext()
    {
        $first = 'Lead text <ref name="cite" />';
        $alltext = '';

        $result = expand_text_refs($first, $alltext);

        // Should use first as alltext
        $this->assertStringContainsString('Lead text', $result);
    }

    public function testExpandTextRefsWithMultipleShortRefs()
    {
        $first = '<ref name="a" /> <ref name="b" /> <ref name="c" />';
        $alltext = '<ref name="a">Cite A</ref> <ref name="b">Cite B</ref> <ref name="c">Cite C</ref>';

        $result = expand_text_refs($first, $alltext);

        $this->assertStringContainsString('<ref name="a">Cite A</ref>', $result);
        $this->assertStringContainsString('<ref name="b">Cite B</ref>', $result);
        $this->assertStringContainsString('<ref name="c">Cite C</ref>', $result);
    }

    public function testExpandTextRefsWithNoShortRefs()
    {
        $first = 'Lead text <ref name="full">Full citation</ref>';
        $alltext = 'Full article <ref name="full">Full citation</ref>';

        $result = expand_text_refs($first, $alltext);

        // Should remain unchanged
        $this->assertEquals($first, $result);
    }

    public function testExpandTextRefsWithEmptyFirst()
    {
        $result = expand_text_refs('', 'Some alltext');

        $this->assertEquals('', $result);
    }

    public function testExpandTextRefsWithShortRefWithoutName()
    {
        $first = 'Text <ref /> without name';
        $alltext = 'Full <ref>Citation</ref>';

        $result = expand_text_refs($first, $alltext);

        // Should handle gracefully (empty name)
        $this->assertStringContainsString('Text', $result);
    }

    public function testExpandTextRefsPreservesOtherContent()
    {
        $first = 'Lead paragraph. <ref name="cite" /> More content.';
        $alltext = '<ref name="cite">Full citation</ref>';

        $result = expand_text_refs($first, $alltext);

        $this->assertStringContainsString('Lead paragraph.', $result);
        $this->assertStringContainsString('More content.', $result);
        $this->assertStringContainsString('<ref name="cite">Full citation</ref>', $result);
    }

    public function testExpandTextRefsWithMixedRefs()
    {
        $first = '<ref name="has_full" /> and <ref name="no_full" />';
        $alltext = '<ref name="has_full">Citation</ref>';

        $result = expand_text_refs($first, $alltext);

        $this->assertStringContainsString('<ref name="has_full">Citation</ref>', $result);
        $this->assertStringContainsString('<ref name="no_full" />', $result);
    }

    public function testExpandTextRefsWithComplexCitation()
    {
        $first = 'Text <ref name="complex" />';
        $alltext = '<ref name="complex">{{cite journal|author=Smith|title=Paper|year=2020}}</ref>';

        $result = expand_text_refs($first, $alltext);

        $this->assertStringContainsString('{{cite journal|author=Smith|title=Paper|year=2020}}', $result);
        $this->assertStringNotContainsString('<ref name="complex" />', $result);
    }

    public function testExpandTextRefsWithWhitespaceVariations()
    {
        $first = 'Text <ref name="cite"  />';
        $alltext = '<ref name="cite" >Full citation</ref>';

        $result = expand_text_refs($first, $alltext);

        $this->assertStringContainsString('<ref name="cite" >Full citation</ref>', $result);
    }

    public function testExpandTextRefsDoesNotReplaceIfFullRefExists()
    {
        $first = '<ref name="cite">Already here</ref> and <ref name="cite" />';
        $alltext = '<ref name="cite">Different citation</ref>';

        $result = expand_text_refs($first, $alltext);

        // Should not replace because full ref already exists in first
        $this->assertStringContainsString('<ref name="cite">Already here</ref>', $result);
        $this->assertStringContainsString('<ref name="cite" />', $result);
        $this->assertStringNotContainsString('Different citation', $result);
    }

    public function testExpandTextRefsWithSpecialCharactersInName()
    {
        $first = 'Text <ref name="author_2020:page_5" />';
        $alltext = '<ref name="author_2020:page_5">Citation content</ref>';

        $result = expand_text_refs($first, $alltext);

        $this->assertStringContainsString('<ref name="author_2020:page_5">Citation content</ref>', $result);
    }

    public function testExpandTextRefsWithMultipleOccurrencesOfSameShortRef()
    {
        $first = '<ref name="cite" /> text <ref name="cite" /> more <ref name="cite" />';
        $alltext = '<ref name="cite">Full citation</ref>';

        $result = expand_text_refs($first, $alltext);

        // All short refs should be replaced
        $count = substr_count($result, '<ref name="cite">Full citation</ref>');
        $this->assertEquals(3, $count);
    }

    public function testExpandTextRefsWithNestedContent()
    {
        $first = 'Text <ref name="nested" />';
        $alltext = '<ref name="nested">Citation with <span>nested</span> content</ref>';

        $result = expand_text_refs($first, $alltext);

        $this->assertStringContainsString('<span>nested</span>', $result);
    }
}
