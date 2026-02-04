<?php

namespace FixRefs\Tests\WikiTextFixes;

use PHPUnit\Framework\TestCase;

use function Fixes\fix_langs_links\remove_lang_links;

class FixLangsLinksTest extends TestCase
{
    public function setUp(): void
    {
        // Define the global $code_to_lang array for testing
        global $code_to_lang;
        $code_to_lang = [
            'en' => 'English',
            'ar' => 'Arabic',
            'de' => 'German',
            'fr' => 'French',
            'es' => 'Spanish',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'zh' => 'Chinese',
            'ru' => 'Russian',
            'pt' => 'Portuguese'
        ];
    }

    public function testRemoveLangLinksWithSingleLink()
    {
        $text = 'Article content [[en:Article]] more text';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:Article]]', $result);
        $this->assertStringContainsString('Article content', $result);
        $this->assertStringContainsString('more text', $result);
    }

    public function testRemoveLangLinksWithMultipleLinks()
    {
        $text = '[[en:English Article]] content [[de:German Article]] [[fr:French Article]]';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:English Article]]', $result);
        $this->assertStringNotContainsString('[[de:German Article]]', $result);
        $this->assertStringNotContainsString('[[fr:French Article]]', $result);
        $this->assertStringContainsString('content', $result);
    }

    public function testRemoveLangLinksPreservesNormalLinks()
    {
        $text = '[[Normal link]] [[en:Language link]] [[Another link]]';
        $result = remove_lang_links($text);

        $this->assertStringContainsString('[[Normal link]]', $result);
        $this->assertStringContainsString('[[Another link]]', $result);
        $this->assertStringNotContainsString('[[en:Language link]]', $result);
    }

    public function testRemoveLangLinksWithNoLanguageLinks()
    {
        $text = 'Text without language links [[Article]] more text';
        $result = remove_lang_links($text);

        $this->assertEquals($text, $result);
    }

    public function testRemoveLangLinksWithEmptyText()
    {
        $result = remove_lang_links('');

        $this->assertEquals('', $result);
    }

    public function testRemoveLangLinksWithVariousLanguages()
    {
        $text = '[[ar:مقالة]] [[ja:記事]] [[zh:文章]] [[ru:Статья]]';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[ar:', $result);
        $this->assertStringNotContainsString('[[ja:', $result);
        $this->assertStringNotContainsString('[[zh:', $result);
        $this->assertStringNotContainsString('[[ru:', $result);
    }

    public function testRemoveLangLinksAtEndOfArticle()
    {
        $text = "Article content.\n\n[[en:English]]\n[[de:Deutsch]]\n[[fr:Français]]";
        $result = remove_lang_links($text);

        $this->assertStringContainsString('Article content.', $result);
        $this->assertStringNotContainsString('[[en:', $result);
        $this->assertStringNotContainsString('[[de:', $result);
        $this->assertStringNotContainsString('[[fr:', $result);
    }

    public function testRemoveLangLinksWithComplexArticleNames()
    {
        $text = '[[en:Article with spaces and (parentheses)]] content';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:Article with spaces and (parentheses)]]', $result);
        $this->assertStringContainsString('content', $result);
    }

    public function testRemoveLangLinksPreservesCategories()
    {
        $text = '[[Category:Test]] [[en:Article]] [[Category:Another]]';
        $result = remove_lang_links($text);

        $this->assertStringContainsString('[[Category:Test]]', $result);
        $this->assertStringContainsString('[[Category:Another]]', $result);
        $this->assertStringNotContainsString('[[en:Article]]', $result);
    }

    public function testRemoveLangLinksWithSpecialCharacters()
    {
        $text = '[[es:Artículo con acentos]] [[de:Artikel_mit_Unterstrichen]]';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[es:', $result);
        $this->assertStringNotContainsString('[[de:', $result);
    }

    public function testRemoveLangLinksInlineWithText()
    {
        $text = 'Start [[en:Article]] middle [[fr:Article]] end';
        $result = remove_lang_links($text);

        $this->assertStringContainsString('Start', $result);
        $this->assertStringContainsString('middle', $result);
        $this->assertStringContainsString('end', $result);
        $this->assertStringNotContainsString('[[en:', $result);
        $this->assertStringNotContainsString('[[fr:', $result);
    }

    public function testRemoveLangLinksWithDuplicates()
    {
        $text = '[[en:Article]] content [[en:Article]]';
        $result = remove_lang_links($text);

        // Both occurrences should be removed
        $this->assertStringNotContainsString('[[en:Article]]', $result);
        $this->assertStringContainsString('content', $result);
    }

    public function testRemoveLangLinksPreservesTemplates()
    {
        $text = '{{Template}} [[en:Article]] {{Another}}';
        $result = remove_lang_links($text);

        $this->assertStringContainsString('{{Template}}', $result);
        $this->assertStringContainsString('{{Another}}', $result);
        $this->assertStringNotContainsString('[[en:Article]]', $result);
    }

    public function testRemoveLangLinksWithNewlines()
    {
        $text = "Content\n[[en:Article]]\nMore content";
        $result = remove_lang_links($text);

        $this->assertStringContainsString("Content\n", $result);
        $this->assertStringContainsString("More content", $result);
        $this->assertStringNotContainsString('[[en:Article]]', $result);
    }

    public function testRemoveLangLinksDoesNotMatchPartialCodes()
    {
        // Test that it doesn't match non-existent language codes
        global $code_to_lang;
        $text = '[[xy:Article]] [[en:Real]] [[zz:Fake]]';
        $result = remove_lang_links($text);

        // Only en: should be removed
        $this->assertStringNotContainsString('[[en:Real]]', $result);
        // xy: and zz: should remain if not in code_to_lang
        if (!isset($code_to_lang['xy'])) {
            $this->assertStringContainsString('[[xy:Article]]', $result);
        }
        if (!isset($code_to_lang['zz'])) {
            $this->assertStringContainsString('[[zz:Fake]]', $result);
        }
    }

    public function testRemoveLangLinksWithMixedContent()
    {
        $text = '[[Article]] text [[en:Lang]] {{Template}} [[Category:Cat]] [[de:Sprache]]';
        $result = remove_lang_links($text);

        $this->assertStringContainsString('[[Article]]', $result);
        $this->assertStringContainsString('{{Template}}', $result);
        $this->assertStringContainsString('[[Category:Cat]]', $result);
        $this->assertStringNotContainsString('[[en:', $result);
        $this->assertStringNotContainsString('[[de:', $result);
    }

    public function testRemoveLangLinksWithUnderscoresAndSpaces()
    {
        $text = '[[en:Article_with_underscores]] [[de:Article with spaces]]';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:Article_with_underscores]]', $result);
        $this->assertStringNotContainsString('[[de:Article with spaces]]', $result);
    }

    public function testRemoveLangLinksWithSectionLinks()
    {
        $text = '[[en:Article#Section]] content';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:Article#Section]]', $result);
        $this->assertStringContainsString('content', $result);
    }

    public function testRemoveLangLinksPreservesFileLinks()
    {
        $text = '[[File:Image.jpg]] [[en:Article]] [[Category:Test]]';
        $result = remove_lang_links($text);

        $this->assertStringContainsString('[[File:Image.jpg]]', $result);
        $this->assertStringContainsString('[[Category:Test]]', $result);
        $this->assertStringNotContainsString('[[en:Article]]', $result);
    }
}