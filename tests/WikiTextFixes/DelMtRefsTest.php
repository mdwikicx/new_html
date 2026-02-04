<?php

namespace FixRefs\Tests\WikiTextFixes;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Domain\Fixes\References\del_empty_refs;

class DelMtRefsTest extends bootstrap
{
    public function testDelEmptyRefsWithValidShortRef()
    {
        $text = '<ref name="test">Full citation</ref> Some text <ref name="test" />';
        $result = del_empty_refs($text);

        // Short ref should remain as is because full ref exists
        $this->assertStringContainsString('<ref name="test">Full citation</ref>', $result);
        $this->assertStringContainsString('<ref name="test" />', $result);
    }

    public function testDelEmptyRefsWithOrphanShortRef()
    {
        $text = 'Some text <ref name="orphan" /> without full reference';
        $result = del_empty_refs($text);

        // Orphan short ref should be removed
        $this->assertStringNotContainsString('<ref name="orphan" />', $result);
        $this->assertStringContainsString('Some text', $result);
        $this->assertStringContainsString('without full reference', $result);
    }

    public function testDelEmptyRefsReplacesWithFullRef()
    {
        $text = 'Text <ref name="cite" /> more text. Later: <ref name="cite">Full content</ref>';
        $result = del_empty_refs($text);

        // The short ref at the beginning should be replaced with full ref if not already present
        $this->assertStringContainsString('<ref name="cite">Full content</ref>', $result);
    }

    public function testDelEmptyRefsWithMultipleShortRefs()
    {
        $text = '<ref name="a">Full A</ref> <ref name="a" /> <ref name="b" /> <ref name="a" />';
        $result = del_empty_refs($text);

        // Short refs for "a" should remain, short ref for "b" should be removed
        $this->assertStringContainsString('<ref name="a">Full A</ref>', $result);
        $this->assertStringNotContainsString('<ref name="b" />', $result);
    }

    public function testDelEmptyRefsWithNoShortRefs()
    {
        $text = '<ref name="one">Citation 1</ref> <ref name="two">Citation 2</ref>';
        $result = del_empty_refs($text);

        // Text should remain unchanged
        $this->assertEquals($text, $result);
    }

    public function testDelEmptyRefsWithNoRefs()
    {
        $text = 'Plain text without any references';
        $result = del_empty_refs($text);

        $this->assertEquals($text, $result);
    }

    public function testDelEmptyRefsWithEmptyText()
    {
        $result = del_empty_refs('');

        $this->assertEquals('', $result);
    }

    public function testDelEmptyRefsPreservesFullRefs()
    {
        $text = '<ref name="full1">Citation 1</ref> <ref name="full2">Citation 2</ref>';
        $result = del_empty_refs($text);

        $this->assertStringContainsString('<ref name="full1">Citation 1</ref>', $result);
        $this->assertStringContainsString('<ref name="full2">Citation 2</ref>', $result);
    }

    public function testDelEmptyRefsDoesNotDuplicateFullRef()
    {
        $text = '<ref name="cite">Full citation</ref> Text <ref name="cite" />';
        $result = del_empty_refs($text);

        // Should not duplicate the full ref if it's already in the text
        $count = substr_count($result, '<ref name="cite">Full citation</ref>');
        $this->assertEquals(1, $count);
    }

    public function testDelEmptyRefsWithMultipleOrphans()
    {
        $text = '<ref name="orphan1" /> <ref name="orphan2" /> <ref name="orphan3" />';
        $result = del_empty_refs($text);

        // All orphan refs should be removed
        $this->assertStringNotContainsString('<ref name="orphan1" />', $result);
        $this->assertStringNotContainsString('<ref name="orphan2" />', $result);
        $this->assertStringNotContainsString('<ref name="orphan3" />', $result);
    }

    public function testDelEmptyRefsWithMixedRefs()
    {
        $text = '<ref name="valid">Full</ref> <ref name="valid" /> <ref name="invalid" />';
        $result = del_empty_refs($text);

        $this->assertStringContainsString('<ref name="valid">Full</ref>', $result);
        $this->assertStringContainsString('<ref name="valid" />', $result);
        $this->assertStringNotContainsString('<ref name="invalid" />', $result);
    }

    public function testDelEmptyRefsWithAnonymousRefs()
    {
        $text = '<ref>Anonymous citation</ref> Text <ref name="named" />';
        $result = del_empty_refs($text);

        // Anonymous full ref should be preserved
        $this->assertStringContainsString('<ref>Anonymous citation</ref>', $result);
        // Named short ref without full should be removed
        $this->assertStringNotContainsString('<ref name="named" />', $result);
    }

    public function testDelEmptyRefsWithComplexNames()
    {
        $text = '<ref name="author_2020">Full citation</ref> <ref name="author_2020" />';
        $result = del_empty_refs($text);

        $this->assertStringContainsString('<ref name="author_2020">Full citation</ref>', $result);
        $this->assertStringContainsString('<ref name="author_2020" />', $result);
    }

    public function testDelEmptyRefsReplacesShortRefBeforeFullRef()
    {
        $text = 'Start <ref name="cite" /> middle. End <ref name="cite">Full content</ref>.';
        $result = del_empty_refs($text);

        // The short ref should be replaced with full ref if it appears before the full ref
        $this->assertStringContainsString('<ref name="cite">Full content</ref>', $result);
    }

    public function testDelEmptyRefsWithWhitespaceInShortRef()
    {
        $text = '<ref name="test">Full</ref> <ref name="test"  />';
        $result = del_empty_refs($text);

        // Should handle whitespace variations
        $this->assertStringContainsString('<ref name="test">Full</ref>', $result);
    }

    public function testDelEmptyRefsWithNestedContent()
    {
        $text = '<ref name="complex">Citation with <span>nested</span> content</ref> <ref name="complex" />';
        $result = del_empty_refs($text);

        $this->assertStringContainsString('<span>nested</span>', $result);
    }
}
