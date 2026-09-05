<?php

namespace FixRefs\Tests\WikiTextFixes;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Domain\Fixes\References\refs_expend_work;

class ExpendRefsTest extends bootstrap
{
    public function testRefsExpendWorkWithShortRefAndFullInAlltext()
    {
        $first = 'Lead text <ref name="cite" />';
        $alltext = 'Full article <ref name="cite">Full citation</ref>';

        $result = refs_expend_work($first, $alltext);

        $this->assertStringContainsString('<ref name="cite">Full citation</ref>', $result);
        $this->assertStringNotContainsString('<ref name="cite" />', $result);
    }

    public function testRefsExpendWorkWithFullRefAlreadyInFirst()
    {
        $first = 'Lead text <ref name="cite">Citation</ref> <ref name="cite" />';
        $alltext = 'Full article <ref name="cite">Citation</ref>';

        $result = refs_expend_work($first, $alltext);

        // Short ref should remain because full ref is already in first
        $this->assertStringContainsString('<ref name="cite">Citation</ref>', $result);
        $this->assertStringContainsString('<ref name="cite" />', $result);
    }

    public function testRefsExpendWorkWithNoMatchingFullRef()
    {
        $first = 'Lead text <ref name="orphan" />';
        $alltext = 'Full article <ref name="other">Other citation</ref>';

        $result = refs_expend_work($first, $alltext);

        // Short ref with no matching full ref should remain unchanged
        $this->assertStringContainsString('<ref name="orphan" />', $result);
    }

    public function testRefsExpendWorkWithEmptyAlltext()
    {
        $first = 'Lead text <ref name="cite" />';
        $alltext = '';

        $result = refs_expend_work($first, $alltext);

        // Should use first as alltext
        $this->assertStringContainsString('Lead text', $result);
    }

    public function testRefsExpendWorkWithMultipleShortRefs()
    {
        $first = '<ref name="a" /> <ref name="b" /> <ref name="c" />';
        $alltext = '<ref name="a">Cite A</ref> <ref name="b">Cite B</ref> <ref name="c">Cite C</ref>';

        $result = refs_expend_work($first, $alltext);

        $this->assertStringContainsString('<ref name="a">Cite A</ref>', $result);
        $this->assertStringContainsString('<ref name="b">Cite B</ref>', $result);
        $this->assertStringContainsString('<ref name="c">Cite C</ref>', $result);
    }

    public function testRefsExpendWorkWithNoShortRefs()
    {
        $first = 'Lead text <ref name="full">Full citation</ref>';
        $alltext = 'Full article <ref name="full">Full citation</ref>';

        $result = refs_expend_work($first, $alltext);

        // Should remain unchanged
        $this->assertEquals($first, $result);
    }

    public function testRefsExpendWorkWithEmptyFirst()
    {
        $result = refs_expend_work('', 'Some alltext');

        $this->assertEquals('', $result);
    }

    public function testRefsExpendWorkWithShortRefWithoutName()
    {
        $first = 'Text <ref /> without name';
        $alltext = 'Full <ref>Citation</ref>';

        $result = refs_expend_work($first, $alltext);

        // Should handle gracefully (empty name)
        $this->assertStringContainsString('Text', $result);
    }

    public function testRefsExpendWorkPreservesOtherContent()
    {
        $first = 'Lead paragraph. <ref name="cite" /> More content.';
        $alltext = '<ref name="cite">Full citation</ref>';

        $result = refs_expend_work($first, $alltext);

        $this->assertStringContainsString('Lead paragraph.', $result);
        $this->assertStringContainsString('More content.', $result);
        $this->assertStringContainsString('<ref name="cite">Full citation</ref>', $result);
    }

    public function testRefsExpendWorkWithMixedRefs()
    {
        $first = '<ref name="has_full" /> and <ref name="no_full" />';
        $alltext = '<ref name="has_full">Citation</ref>';

        $result = refs_expend_work($first, $alltext);

        $this->assertStringContainsString('<ref name="has_full">Citation</ref>', $result);
        $this->assertStringContainsString('<ref name="no_full" />', $result);
    }

    public function testRefsExpendWorkWithComplexCitation()
    {
        $first = 'Text <ref name="complex" />';
        $alltext = '<ref name="complex">{{cite journal|author=Smith|title=Paper|year=2020}}</ref>';

        $result = refs_expend_work($first, $alltext);

        $this->assertStringContainsString('{{cite journal|author=Smith|title=Paper|year=2020}}', $result);
        $this->assertStringNotContainsString('<ref name="complex" />', $result);
    }

    public function testRefsExpendWorkWithWhitespaceVariations()
    {
        $first = 'Text <ref name="cite"  />';
        $alltext = '<ref name="cite" >Full citation</ref>';

        $result = refs_expend_work($first, $alltext);

        $this->assertStringContainsString('<ref name="cite" >Full citation</ref>', $result);
    }

    public function testRefsExpendWorkDoesNotReplaceIfFullRefExists()
    {
        $first = '<ref name="cite">Already here</ref> and <ref name="cite" />';
        $alltext = '<ref name="cite">Different citation</ref>';

        $result = refs_expend_work($first, $alltext);

        // Should not replace because full ref already exists in first
        $this->assertStringContainsString('<ref name="cite">Already here</ref>', $result);
        $this->assertStringContainsString('<ref name="cite" />', $result);
        $this->assertStringNotContainsString('Different citation', $result);
    }

    public function testRefsExpendWorkWithSpecialCharactersInName()
    {
        $first = 'Text <ref name="author_2020:page_5" />';
        $alltext = '<ref name="author_2020:page_5">Citation content</ref>';

        $result = refs_expend_work($first, $alltext);

        $this->assertStringContainsString('<ref name="author_2020:page_5">Citation content</ref>', $result);
    }

    public function testRefsExpendWorkWithMultipleOccurrencesOfSameShortRef()
    {
        $first = '<ref name="cite" /> text <ref name="cite" /> more <ref name="cite" />';
        $alltext = '<ref name="cite">Full citation</ref>';

        $result = refs_expend_work($first, $alltext);

        // All short refs should be replaced
        $count = substr_count($result, '<ref name="cite">Full citation</ref>');
        $this->assertEquals(3, $count);
    }

    public function testRefsExpendWorkWithNestedContent()
    {
        $first = 'Text <ref name="nested" />';
        $alltext = '<ref name="nested">Citation with <span>nested</span> content</ref>';

        $result = refs_expend_work($first, $alltext);

        $this->assertStringContainsString('<span>nested</span>', $result);
    }
}
