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

    public function testRemoveLangLinksWithPipeDisplayText()
    {
        // Language links can have pipe characters for display text
        $text = '[[en:Article|Display Text]] content';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:Article|Display Text]]', $result);
        $this->assertStringContainsString('content', $result);
    }

    public function testRemoveLangLinksConsecutiveWithoutSpace()
    {
        // Multiple language links right next to each other
        $text = 'content[[en:Article]][[de:Artikel]][[fr:Article]]text';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:Article]]', $result);
        $this->assertStringNotContainsString('[[de:Artikel]]', $result);
        $this->assertStringNotContainsString('[[fr:Article]]', $result);
        $this->assertStringContainsString('content', $result);
        $this->assertStringContainsString('text', $result);
    }

    public function testRemoveLangLinksWithColonInArticleName()
    {
        // Article names can contain colons (e.g., namespaces)
        $text = '[[en:User:Example]] [[de:Wikipedia:Featured article]]';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:User:Example]]', $result);
        $this->assertStringNotContainsString('[[de:Wikipedia:Featured article]]', $result);
    }

    public function testIsValidLangCodeWithTwoCharacterCode()
    {
        // Boundary case: exactly 2 characters (minimum)
        $this->assertTrue(is_valid_lang_code('en'));
        $this->assertTrue(is_valid_lang_code('de'));
        $this->assertTrue(is_valid_lang_code('fr'));
        $this->assertTrue(is_valid_lang_code('ja'));
    }

    public function testIsValidLangCodeWithVeryLongHyphenatedCode()
    {
        // Very long hyphenated codes should still work
        $this->assertTrue(is_valid_lang_code('zh-min-nan'));
        $this->assertTrue(is_valid_lang_code('be-tarask'));
        $this->assertTrue(is_valid_lang_code('roa-rup'));
    }

    public function testRemoveLangLinksPreservesWhitespace()
    {
        // Whitespace around removed links should be preserved
        $text = "Line 1\n[[en:Article]]\nLine 2";
        $result = remove_lang_links($text);

        $this->assertStringContainsString("Line 1\n", $result);
        $this->assertStringContainsString("\nLine 2", $result);
        $this->assertStringNotContainsString('[[en:Article]]', $result);
    }

    public function testRemoveLangLinksAtVeryStartOfText()
    {
        // Language link as the first thing in the text
        $text = '[[en:Article]] followed by content';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:Article]]', $result);
        $this->assertStringContainsString('followed by content', $result);
    }

    public function testRemoveLangLinksAtVeryEndOfText()
    {
        // Language link as the last thing in the text
        $text = 'content before [[en:Article]]';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:Article]]', $result);
        $this->assertStringContainsString('content before', $result);
    }

    public function testRemoveLangLinksOnlyLanguageLinks()
    {
        // Text contains only language links, nothing else
        $text = '[[en:Article]][[de:Artikel]][[fr:Article]]';
        $result = remove_lang_links($text);

        $this->assertEquals('', $result);
    }

    public function testIsValidLangCodeWithSingleCharacter()
    {
        // Single character should be invalid (minimum is 2)
        $this->assertFalse(is_valid_lang_code('e'));
        $this->assertFalse(is_valid_lang_code('x'));
        $this->assertFalse(is_valid_lang_code('a'));
    }

    public function testIsValidLangCodeWithNumbersInCode()
    {
        // Language codes cannot contain numbers
        $this->assertFalse(is_valid_lang_code('en1'));
        $this->assertFalse(is_valid_lang_code('2de'));
        $this->assertFalse(is_valid_lang_code('e3n'));
        $this->assertFalse(is_valid_lang_code('en-123'));
    }

    public function testIsValidLangCodeWithMultipleHyphens()
    {
        // Multiple hyphens should work if properly formatted
        $this->assertTrue(is_valid_lang_code('zh-min-nan'));
        $this->assertFalse(is_valid_lang_code('en--de')); // Double hyphen
        $this->assertFalse(is_valid_lang_code('en-')); // Trailing hyphen
        $this->assertFalse(is_valid_lang_code('-en')); // Leading hyphen
    }

    public function testRemoveLangLinksDoesNotMatchUppercase()
    {
        // Uppercase language codes should not match (they're invalid)
        $text = '[[EN:Article]] [[De:Artikel]] [[FR:Article]]';
        $result = remove_lang_links($text);

        // All should remain because they don't match the lowercase pattern
        $this->assertStringContainsString('[[EN:Article]]', $result);
        $this->assertStringContainsString('[[De:Artikel]]', $result);
        $this->assertStringContainsString('[[FR:Article]]', $result);
    }

    public function testRemoveLangLinksWithUnicodeInArticleName()
    {
        // Article names with various Unicode characters
        $text = '[[ja:日本語の記事]] [[ar:مقالة عربية]] [[ru:Русская статья]] [[zh:中文文章]]';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[ja:日本語の記事]]', $result);
        $this->assertStringNotContainsString('[[ar:مقالة عربية]]', $result);
        $this->assertStringNotContainsString('[[ru:Русская статья]]', $result);
        $this->assertStringNotContainsString('[[zh:中文文章]]', $result);
    }

    public function testRemoveLangLinksWithComplexMixedContent()
    {
        // Complex real-world scenario with all types of content
        $text = <<<TEXT
== Section ==
This is content with [[internal link]] and [[en:English article]].

{{template|param=value}}
More text [[Category:Test Category]] and [[de:Deutscher Artikel]].

* List item with [[fr:Article français]]
* Another item

[[File:Example.jpg|thumb|Caption]]

[[zh-min-nan:Bûn-chiuⁿ]]
TEXT;
        $result = remove_lang_links($text);

        // Should preserve all non-language-link content
        $this->assertStringContainsString('[[internal link]]', $result);
        $this->assertStringContainsString('{{template|param=value}}', $result);
        $this->assertStringContainsString('[[Category:Test Category]]', $result);
        $this->assertStringContainsString('[[File:Example.jpg|thumb|Caption]]', $result);

        // Should remove all language links
        $this->assertStringNotContainsString('[[en:English article]]', $result);
        $this->assertStringNotContainsString('[[de:Deutscher Artikel]]', $result);
        $this->assertStringNotContainsString('[[fr:Article français]]', $result);
        $this->assertStringNotContainsString('[[zh-min-nan:Bûn-chiuⁿ]]', $result);
    }

    public function testRemoveLangLinksWithTrailingSpaces()
    {
        // Language links with various whitespace
        $text = "Before  [[en:Article]]  After";
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:Article]]', $result);
        $this->assertStringContainsString('Before', $result);
        $this->assertStringContainsString('After', $result);
    }

    public function testIsValidLangCodeWithSpecialCharacters()
    {
        // Language codes can only contain lowercase letters and hyphens
        $this->assertFalse(is_valid_lang_code('en_us'));
        $this->assertFalse(is_valid_lang_code('en.us'));
        $this->assertFalse(is_valid_lang_code('en us'));
        $this->assertFalse(is_valid_lang_code('en@us'));
        $this->assertFalse(is_valid_lang_code('en:us'));
    }

    public function testRemoveLangLinksRegressionAllKnownCodes()
    {
        // Regression test: ensure all known Wikipedia language codes are properly removed
        $problematicCodes = [
            'be-tarask', 'bat-smg', 'cbk-zam', 'fiu-vro', 'map-bms',
            'nds-nl', 'roa-rup', 'roa-tara', 'zh-classical', 'zh-min-nan', 'zh-yue'
        ];

        foreach ($problematicCodes as $code) {
            $text = "Content [[{$code}:Article]] more text";
            $result = remove_lang_links($text);

            $this->assertStringNotContainsString("[[{$code}:", $result,
                "Failed to remove language code: {$code}");
            $this->assertStringContainsString('Content', $result);
            $this->assertStringContainsString('more text', $result);
        }
    }

    public function testRemoveLangLinksDoesNotRemoveImageLinks()
    {
        // Ensure we don't accidentally remove Image: or File: links
        $text = '[[Image:Test.jpg]] [[File:Another.png]] [[en:Article]]';
        $result = remove_lang_links($text);

        $this->assertStringContainsString('[[Image:Test.jpg]]', $result);
        $this->assertStringContainsString('[[File:Another.png]]', $result);
        $this->assertStringNotContainsString('[[en:Article]]', $result);
    }

    public function testRemoveLangLinksWithParenthesesAndBrackets()
    {
        // Article names can have complex punctuation
        $text = '[[en:Article (disambiguation)]] [[de:Begriff [Erklärung]]]';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:Article (disambiguation)]]', $result);
        // Note: [Erklärung] inside might cause issues with regex, but should still work
        // because the regex looks for the closing ]] of the outer link
    }

    public function testRemoveLangLinksWithQueryParameters()
    {
        // Article names with query-like parameters
        $text = '[[en:Article?action=edit]] [[de:Artikel&param=value]]';
        $result = remove_lang_links($text);

        $this->assertStringNotContainsString('[[en:Article?action=edit]]', $result);
        $this->assertStringNotContainsString('[[de:Artikel&param=value]]', $result);
    }
}