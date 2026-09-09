<?php

namespace Tests\Services\Wikitext;

use PHPUnit\Framework\TestCase;

use function MDWiki\NewHtml\Domain\Parser\expend_all_templates;
use MDWiki\NewHtml\Services\Wikitext\WikitextFixerService;

/**
 * Unit tests for WikitextFixerService.
 *
 * @covers \MDWiki\NewHtml\Services\Wikitext\WikitextFixerService
 */
class WikitextFixerServiceTest extends TestCase
{
    private string $fixturePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixturePath = __DIR__ . '/fixtures';
    }

    /**
     * Loads the contents of a fixture file from the specified folder.
     */
    private function loadFixture(string $name, string $folder = 'source'): string
    {
        $path = $this->fixturePath . '/' . $folder . '/' . $name;
        $this->assertFileExists($path, "Fixture file missing: {$path}");

        $content = file_get_contents($path);
        $this->assertNotFalse($content, "Unable to read fixture file: {$path}");

        return $content;
    }

    /**
     * Strips trailing whitespace from each line and trims the overall string.
     */
    private function stripResult(string $result): string
    {
        $text = trim($result);
        $lines = explode("\n", $text);

        $lines = array_map(function (string $line): string {
            return rtrim($line);
        }, $lines);

        return trim(implode("\n", $lines));
    }

    /**
     * Tests fixing wikitext against expected result fixtures.
     *
     * @dataProvider fixtureFilesProvider
     */
    public function testFixWikitextMatchesResultFixture(string $file, bool $allFlag): void
    {
        $source = $this->loadFixture($file, 'source');
        $expected = $this->loadFixture($file, 'result');
        $expected = $this->stripResult($expected);

        $service = new WikitextFixerService();
        $result = $service->run($source, 'PLACEHOLDER_TEST', !$allFlag);
        $result = $this->stripResult($result);

        // Write processed result to the output directory
        $outputFolder = $this->fixturePath . '/output';
        if (!is_dir($outputFolder)) {
            mkdir($outputFolder, 0777, true);
        }

        $outputPath = $outputFolder . '/' . $file;
        file_put_contents($outputPath, $result . "\n");

        $this->assertSame(
            expend_all_templates($result),
            expend_all_templates($expected)
        );
    }

    /**
     * Data provider for wikitext fixture files.
     *
     * @return array<string, array{0: string, 1: bool}>
     */
    public function fixtureFilesProvider(): array
    {
        $fixturePath = __DIR__ . '/fixtures';
        $sourceDir = $fixturePath . '/source';

        if (!is_dir($sourceDir)) {
            return [];
        }

        $leadOnlyFiles = [
            'abdominal_pain.wiki' => true,
            'Uterine_atony.wiki' => true,
            'obstructive_sleep_apnea.wiki' => true,
            'Wernicke–Korsakoff_syndrome.wiki' => true,
        ];

        $files = glob($sourceDir . '/*.wiki');
        $dataset = [];

        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            $allFlag = !isset($leadOnlyFiles[$fileName]);

            // Use file name as dataset key for better test output readability
            $dataset[$fileName] = [$fileName, $allFlag];
        }

        return $dataset;
    }

    public function testFixWikitextWithEmptyInputReturnsEmpty(): void
    {
        $service = new WikitextFixerService();
        $result = $service->fix('', 'PLACEHOLDER_TEST');

        $this->assertSame('', $result);
    }
}
