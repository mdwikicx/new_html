<?php

namespace FixRefs\Tests\WikiTextFixes;

use FixRefs\Tests\bootstrap;

use function MDWiki\NewHtml\Domain\Fixes\Structure\remove_lang_links;
use function MDWiki\NewHtml\Domain\Fixes\Structure\is_valid_lang_code;

class FixLangsLinksTest extends bootstrap
{
    /**
     * Complete list of Wikipedia language codes (from the original array)
     * Used to verify that the regex pattern matches all valid codes
     */
    private const ALL_WIKI_LANG_CODES = [
        'aa', 'ab', 'ace', 'ady', 'af', 'ak', 'als', 'alt', 'am', 'an', 'ang', 'ar', 'as', 'ast', 'atj', 'av', 'avk',
        'awa', 'ay', 'az', 'azb', 'ba', 'ban', 'bar', 'bat-smg', 'bcl', 'be', 'be-tarask', 'bg', 'bh', 'bi', 'bjn',
        'bm', 'bn', 'bo', 'bpy', 'br', 'bs', 'bug', 'bxr', 'ca', 'cbk-zam', 'cdo', 'ce', 'ceb', 'ch', 'cho', 'chr',
        'chy', 'ckb', 'co', 'cr', 'crh', 'cs', 'csb', 'cu', 'cv', 'cy', 'da', 'de', 'din', 'diq', 'dsb', 'dty', 'dv',
        'dz', 'ee', 'el', 'eml', 'en', 'eo', 'es', 'et', 'eu', 'ext', 'fa', 'ff', 'fi', 'fiu-vro', 'fj', 'fo', 'fr',
        'frp', 'frr', 'fur', 'fy', 'ga', 'gag', 'gan', 'gcr', 'gd', 'gl', 'glk', 'gn', 'gom', 'gor', 'got', 'gu',
        'gv', 'ha', 'hak', 'haw', 'he', 'hi', 'hif', 'ho', 'hr', 'hsb', 'ht', 'hu', 'hy', 'hyw', 'hz', 'ia', 'id',
        'ie', 'ig', 'ii', 'ik', 'ilo', 'inh', 'io', 'is', 'it', 'iu', 'ja', 'jam', 'jbo', 'jv', 'ka', 'kaa', 'kab',
        'kbd', 'kbp', 'kg', 'ki', 'kj', 'kk', 'kl', 'km', 'kn', 'ko', 'koi', 'kr', 'krc', 'ks', 'ksh', 'ku', 'kv',
        'kw', 'ky', 'la', 'lad', 'lb', 'lbe', 'lez', 'lfn', 'lg', 'li', 'lij', 'lld', 'lmo', 'ln', 'lo', 'lrc', 'lt',
        'ltg', 'lv', 'mad', 'mai', 'map-bms', 'mdf', 'mg', 'mh', 'mhr', 'mi', 'min', 'mk', 'ml', 'mn', 'mni', 'mnw',
        'mr', 'mrj', 'ms', 'mt', 'mus', 'mwl', 'my', 'myv', 'mzn', 'na', 'nah', 'nap', 'nds', 'nds-nl', 'ne', 'new',
        'ng', 'nia', 'nl', 'nn', 'no', 'nov', 'nqo', 'nrm', 'nso', 'nv', 'ny', 'oc', 'olo', 'om', 'or', 'os', 'pa',
        'pag', 'pam', 'pap', 'pcd', 'pdc', 'pfl', 'pi', 'pih', 'pl', 'pms', 'pnb', 'pnt', 'ps', 'pt', 'qu', 'rm',
        'rmy', 'rn', 'ro', 'roa-rup', 'roa-tara', 'ru', 'rue', 'rw', 'sa', 'sah', 'sat', 'sc', 'scn', 'sco', 'sd',
        'se', 'sg', 'sh', 'shn', 'si', 'simple', 'sk', 'skr', 'sl', 'sm', 'smn', 'sn', 'so', 'sq', 'sr', 'srn', 'ss',
        'st', 'stq', 'su', 'sv', 'sw', 'szl', 'szy', 'ta', 'tay', 'tcy', 'te', 'tet', 'tg', 'th', 'ti', 'tk', 'tl',
        'tn', 'to', 'tpi', 'tr', 'trv', 'ts', 'tt', 'tum', 'tw', 'ty', 'tyv', 'udm', 'ug', 'uk', 'ur', 'uz', 've',
        'vec', 'vep', 'vi', 'vls', 'vo', 'wa', 'war', 'wo', 'wuu', 'xal', 'xh', 'xmf', 'yi', 'yo', 'za', 'zea',
        'zh', 'zh-classical', 'zh-min-nan', 'zh-yue', 'zu',
    ];

    public function testAllWikiLangCodesAreValid()
    {
        // Test that all Wikipedia language codes match the regex pattern
        foreach (self::ALL_WIKI_LANG_CODES as $code) {
            $this->assertTrue(
                is_valid_lang_code($code),
                "Language code '{$code}' should be valid"
            );
        }
    }

    public function testInvalidLangCodes()
    {
        // Test codes that should NOT match
        $invalidCodes = [
            'X',         // Too short (single uppercase)
            'E',         // Single letter
            '1',         // Number
            'en1',       // Contains number
            'EN',        // Uppercase
            'En',        // Mixed case
            '-en',       // Starts with hyphen
            'en-',       // Ends with hyphen
            '',          // Empty string
            'test_',     // Contains underscore
            'en.test',   // Contains dot
            'en space',  // Contains space
        ];

        foreach ($invalidCodes as $code) {
            $this->assertFalse(
                is_valid_lang_code($code),
                "Code '{$code}' should be invalid"
            );
        }
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

    public function testRemoveLangLinksWithHyphenatedCodes()
    {
        // Test that hyphenated language codes work correctly
        $text = '[[be-tarask:Артыкул]] [[zh-min-nan:Bûn-chiuⁿ]] [[roa-rup:Articlu]]';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[be-tarask:', $result);
        $this->assertStringNotContainsString('[[zh-min-nan:', $result);
        $this->assertStringNotContainsString('[[roa-rup:', $result);
    }

    public function testRemoveLangLinksPreservesShortInvalidCodes()
    {
        // Short codes (1-2 chars) that look like language codes but shouldn't match
        // Actually, 'xy' and 'zz' match the pattern (2+ lowercase letters)
        // so they WILL be removed as they're valid lang codes
        $text = '[[X:Article]] [[12:Number]] [[EN:Uppercase]] [[e:Single]]';
        $result = remove_lang_links($text);

        // These should remain (don't match the pattern)
        $this->assertStringContainsString('[[X:Article]]', $result);      // Uppercase
        $this->assertStringContainsString('[[12:Number]]', $result);      // Starts with number
        $this->assertStringContainsString('[[EN:Uppercase]]', $result);   // Uppercase
        $this->assertStringContainsString('[[e:Single]]', $result);       // Single char
    }

    public function testRemoveLangLinksWithSimpleCode()
    {
        // Test the 'simple' language code specifically
        $text = '[[simple:Basic English article]] content';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[simple:', $result);
        $this->assertStringContainsString('content', $result);
    }
}
