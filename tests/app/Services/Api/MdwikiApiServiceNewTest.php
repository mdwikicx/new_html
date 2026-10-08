<?php

declare (strict_types = 1);

namespace MDWiki\NewHtml\Tests\Services\Api;

use MDWiki\NewHtml\Services\Api\MdwikiApiService;
use PHPUnit\Framework\TestCase;

class MdwikiApiServiceNewTest extends TestCase
{
    /**
     * العنوان => اسم الملف المتوقع
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function fileNameProvider(): array
    {
        return [
            'parentheses'       => ['Zinc sulfate (medical use)', 'Zinc_sulfate_%28medical_use%29.json'],
            'non-latin en dash' => ['Wernicke–Korsakoff syndrome', 'Wernicke%E2%80%93Korsakoff_syndrome.json'],
            'single slash'      => ['Sodium phenylacetate/sodium benzoate', 'Sodium_phenylacetate%2Fsodium_benzoate.json'],
            'multiple slashes'  => ['Sofosbuvir/velpatasvir/voxilaprevir', 'Sofosbuvir%2Fvelpatasvir%2Fvoxilaprevir.json'],
            'plain title'       => ['Sodium nitroprusside', 'Sodium_nitroprusside.json'],
        ];
    }

    /**
     * الأمثلة المطلوبة: العنوان ← اسم الملف
     *
     * @dataProvider fileNameProvider
     */
    public function testTitleToFileName(string $title, string $expected): void
    {
        $this->assertSame($expected, MdwikiApiService::titleToFileName($title));
    }

    /**
     * الترميز المستخدم في رابط الـ API يطابق اسم الملف بدون ".json"
     *
     * @dataProvider fileNameProvider
     */
    public function testEncodeTitleMatchesFileNameWithoutExtension(string $title, string $expected): void
    {
        $this->assertSame(substr($expected, 0, -5), MdwikiApiService::encodeTitle($title));
    }

    /** الأحرف المشفّرة HEX بحروف كبيرة، وحالة الأحرف محفوظة */
    public function testUppercaseHexAndCasePreserved(): void
    {
        $name = MdwikiApiService::titleToFileName('Sodium phenylacetate/sodium benzoate');

        $this->assertStringContainsString('%2F', $name);
        $this->assertStringNotContainsString('%2f', $name);
        $this->assertStringStartsWith('Sodium', $name);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function dangerousTitleProvider(): array
    {
        return [
            'path traversal' => ['../../etc/passwd'],
            'nested slashes' => ['a/b/c'],
            'backslash'      => ['a\\b'],
            'null byte'      => ["a\0b"],
            'mixed'          => ['Some/Title (x)'],
        ];
    }

    /**
     * اسم الملف الناتج لا يحتوي على "/" أو "\" أو NUL أبدًا
     *
     * @dataProvider dangerousTitleProvider
     */
    public function testNoPathSeparatorsInFileName(string $title): void
    {
        $name = MdwikiApiService::titleToFileName($title);

        $this->assertStringNotContainsString('/', $name);
        $this->assertStringNotContainsString('\\', $name);
        $this->assertStringNotContainsString("\0", $name);
    }
}
