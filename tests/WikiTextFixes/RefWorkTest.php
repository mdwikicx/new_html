<?php

namespace FixRefs\Tests\WikiTextFixes;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Domain\Fixes\References\check_one_cite;
use function MDWiki\NewHtml\Domain\Fixes\References\remove_bad_refs;

class RefWorkTest extends bootstrap
{
    public function testCheckOneCiteWithBadDOI()
    {
        $cite = '<ref>{{cite journal|doi=10.5539/test}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testCheckOneCiteWithGoodDOI()
    {
        $cite = '<ref>{{cite journal|doi=10.1001/test}}</ref>';
        $result = check_one_cite($cite);

        $this->assertFalse($result);
    }

    public function testCheckOneCiteWithBadJournal()
    {
        $cite = '<ref>{{cite journal|url=http://scirp.org/article}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testCheckOneCiteWithSelfPublisher()
    {
        $cite = '<ref>{{cite book|publisher=Author House}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testCheckOneCiteWithCreateSpace()
    {
        $cite = '<ref>{{cite book|publisher=CreateSpace}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testCheckOneCiteWithSelfPubUrl()
    {
        $cite = '<ref>{{cite web|url=http://lulu.com/book}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testCheckOneCiteWithCleanCitation()
    {
        $cite = '<ref>{{cite journal|title=Test|author=Smith|journal=Nature|year=2020}}</ref>';
        $result = check_one_cite($cite);

        $this->assertFalse($result);
    }

    public function testCheckOneCiteWithMultipleBadPatterns()
    {
        $cite = '<ref>{{cite journal|doi=10.5539/test|url=http://scirp.org/article}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testRemoveBadRefsWithSingleBadRef()
    {
        $text = 'Good text <ref>{{cite journal|doi=10.5539/bad}}</ref> more text';
        $result = remove_bad_refs($text);

        $this->assertStringNotContainsString('doi=10.5539/bad', $result);
        $this->assertStringContainsString('Good text', $result);
        $this->assertStringContainsString('more text', $result);
    }

    public function testRemoveBadRefsWithMultipleBadRefs()
    {
        $text = '<ref>{{cite|doi=10.5539/bad1}}</ref> text <ref>{{cite|url=http://scirp.org/x}}</ref>';
        $result = remove_bad_refs($text);

        $this->assertStringNotContainsString('doi=10.5539/bad1', $result);
        $this->assertStringNotContainsString('scirp.org', $result);
        $this->assertStringContainsString('text', $result);
    }

    public function testRemoveBadRefsPreservesGoodRefs()
    {
        $text = '<ref>{{cite journal|doi=10.1001/good}}</ref> <ref>{{cite|doi=10.5539/bad}}</ref>';
        $result = remove_bad_refs($text);

        $this->assertStringContainsString('doi=10.1001/good', $result);
        $this->assertStringNotContainsString('doi=10.5539/bad', $result);
    }

    public function testRemoveBadRefsWithNoRefs()
    {
        $text = 'Plain text without references';
        $result = remove_bad_refs($text);

        $this->assertEquals($text, $result);
    }

    public function testRemoveBadRefsWithEmptyText()
    {
        $result = remove_bad_refs('');

        $this->assertEquals('', $result);
    }

    public function testRemoveBadRefsWithNamedRef()
    {
        $text = '<ref name="bad">{{cite|doi=10.5539/test}}</ref>';
        $result = remove_bad_refs($text);

        $this->assertStringNotContainsString('doi=10.5539/test', $result);
    }

    public function testCheckOneCiteWithOmicsGroup()
    {
        $cite = '<ref>{{cite journal|url=http://omicsonline.org/article}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testCheckOneCiteWithTraffordPublishing()
    {
        $cite = '<ref>{{cite book|publisher=Trafford Publishing}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testCheckOneCiteWithIUniverse()
    {
        $cite = '<ref>{{cite book|publisher=iUniverse}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testCheckOneCiteWithXLibris()
    {
        $cite = '<ref>{{cite book|publisher=XLibris}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testCheckOneCiteWithEdwinMellenPress()
    {
        $cite = '<ref>{{cite book|publisher=Edwin Mellen Press}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testCheckOneCiteCaseInsensitive()
    {
        $cite = '<ref>{{cite book|publisher=AUTHOR HOUSE}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testRemoveBadRefsWithMixedRefs()
    {
        $text = '<ref>Good ref</ref> <ref>{{cite|doi=10.5539/bad}}</ref> <ref>Another good</ref>';
        $result = remove_bad_refs($text);

        $this->assertStringContainsString('<ref>Good ref</ref>', $result);
        $this->assertStringContainsString('<ref>Another good</ref>', $result);
        $this->assertStringNotContainsString('doi=10.5539/bad', $result);
    }

    public function testCheckOneCiteWithHindawi()
    {
        $cite = '<ref>{{cite journal|doi=10.1155/test}}</ref>';
        // 10.1155 is not in the bad list, so should be false
        $result = check_one_cite($cite);

        $this->assertFalse($result);
    }

    public function testCheckOneCiteWithWorkParameter()
    {
        $cite = '<ref>{{cite|work=CreateSpace}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testCheckOneCiteWithUrlInText()
    {
        $cite = '<ref>Text with createspace.com in URL</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testRemoveBadRefsWithComplexCitations()
    {
        $text = '<ref name="complex">{{cite journal|author=Smith|title=Test|doi=10.5539/bad|year=2020}}</ref>';
        $result = remove_bad_refs($text);

        $this->assertStringNotContainsString('doi=10.5539/bad', $result);
    }

    public function testCheckOneCiteWithMultipleDOIBadPrefixes()
    {
        $cite1 = '<ref>{{cite|doi=10.11648/test}}</ref>';
        $cite2 = '<ref>{{cite|doi=10.1166/test}}</ref>';
        $cite3 = '<ref>{{cite|doi=10.1234/test}}</ref>';

        $this->assertTrue(check_one_cite($cite1));
        $this->assertTrue(check_one_cite($cite2));
        $this->assertTrue(check_one_cite($cite3));
    }

    public function testCheckOneCiteWithSpacesInPattern()
    {
        $cite = '<ref>{{cite|doi = 10.5539/test}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testRemoveBadRefsPreservesTextStructure()
    {
        $text = "Paragraph 1.\n\n<ref>{{cite|doi=10.5539/bad}}</ref>\n\nParagraph 2.";
        $result = remove_bad_refs($text);

        $this->assertStringContainsString("Paragraph 1.\n\n", $result);
        $this->assertStringContainsString("\n\nParagraph 2.", $result);
        $this->assertStringNotContainsString('doi=10.5539/bad', $result);
    }

    public function testCheckOneCiteWithNestedTemplates()
    {
        $cite = '<ref>{{cite journal|title={{lang|en|Title}}|doi=10.5539/bad}}</ref>';
        $result = check_one_cite($cite);

        $this->assertTrue($result);
    }

    public function testRemoveBadRefsWithAllGoodRefs()
    {
        $text = '<ref>{{cite journal|doi=10.1001/test}}</ref> <ref>Good citation</ref>';
        $result = remove_bad_refs($text);

        // Should remain unchanged
        $this->assertEquals($text, $result);
    }
}
