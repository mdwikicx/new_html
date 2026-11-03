<?php
namespace MDWiki\NewHtml\Tests\Services\Api;

use MDWiki\NewHtml\Services\Api\LocalRestJsonRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(LocalRestJsonRepository::class)]
class LocalRestJsonRepositoryTest extends TestCase
{
    private string $rootDir;
    private string $dataDir;
    private string $mapPath;

    protected function setUp(): void
    {
        // Each test gets its own isolated temp directory.
        // Layout mirrors production:
        //   {root}/titles_map.json   -> title => file name
        //   {root}/data/*.json       -> cached pages (the "members" dir)
        $this->rootDir = sys_get_temp_dir() . '/local_rest_repo_' . uniqid('', true);
        $this->dataDir = $this->rootDir . '/data';
        $this->mapPath = $this->rootDir . '/titles_map.json';
        mkdir($this->dataDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->rootDir);
    }

    // ------------------------------------------------------------------
    // find: success cases
    // ------------------------------------------------------------------

    public function testFindReturnsDecodedJsonForExistingFile(): void
    {
        $payload = ['source' => 'Some wikitext', 'latest' => ['id' => 123]];
        $this->addPage('Heart failure', 'Heart_failure.json', json_encode($payload));

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertSame($payload, $repo->find('Heart failure'));
    }

    public function testFindTreatsSpacesAndUnderscoresAsTheSameTitle(): void
    {
        $payload = ['source' => 'Text', 'latest' => ['id' => 1]];
        // The map is keyed by the title with spaces (as returned by the category API)
        $this->addPage('Heart failure', 'Heart_failure.json', json_encode($payload));

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertSame($payload, $repo->find('Heart failure'));
        $this->assertSame($payload, $repo->find('Heart_failure'));
    }

    public function testFindSupportsTitlesContainingSlash(): void
    {
        $payload = ['source' => 'Slash title', 'latest' => ['id' => 2]];
        $this->addPage('A/B', 'A%2FB.json', json_encode($payload));

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertSame($payload, $repo->find('A/B'));
    }

    public function testFindSupportsTitlesContainingParentheses(): void
    {
        // Characters that are hard to derive by hand are exactly why the map exists
        $payload = ['source' => 'Zinc', 'latest' => ['id' => 4]];
        $this->addPage(
            'Zinc sulfate (medical use)',
            'Zinc_sulfate_%28medical_use%29.json',
            json_encode($payload)
        );

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertSame($payload, $repo->find('Zinc sulfate (medical use)'));
    }

    public function testFindSupportsNonAsciiTitles(): void
    {
        $payload = ['source' => 'Arabic title', 'latest' => ['id' => 3]];
        $this->addPage('قلب', rawurlencode('قلب') . '.json', json_encode($payload));

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertSame($payload, $repo->find('قلب'));
    }

    public function testFindUsesExplicitMapPath(): void
    {
        $payload = ['source' => 'Custom map', 'latest' => ['id' => 5]];
        file_put_contents($this->dataDir . '/Custom_file.json', json_encode($payload));

        $customMap = $this->rootDir . '/elsewhere_map.json';
        file_put_contents($customMap, json_encode(['Custom title' => 'Custom_file.json']));

        $repo = new LocalRestJsonRepository($this->dataDir, $customMap);

        $this->assertSame($payload, $repo->find('Custom title'));
    }

    // ------------------------------------------------------------------
    // titleToFileName
    // ------------------------------------------------------------------

    public function testTitleToFileNameReturnsNameFromMap(): void
    {
        $this->writeMap(['Heart failure' => 'Heart_failure.json']);

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertSame('Heart_failure.json', $repo->titleToFileName('Heart failure'));
        $this->assertSame('Heart_failure.json', $repo->titleToFileName('Heart_failure'));
    }

    public function testTitleToFileNameReturnsNullForUnknownTitle(): void
    {
        $this->writeMap(['Heart failure' => 'Heart_failure.json']);

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->titleToFileName('Unknown title'));
    }

    public function testTitleToFileNameReturnsNullWhenMapIsMissing(): void
    {
        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->titleToFileName('Anything'));
    }

    public function testMapIsLoadedOnlyOnceAfterSuccess(): void
    {
        $this->writeMap(['First' => 'First.json']);

        $repo = new LocalRestJsonRepository($this->dataDir);
        $this->assertSame('First.json', $repo->titleToFileName('First'));

        // Changes to the map after the first successful load are not picked up
        $this->writeMap(['Second' => 'Second.json']);

        $this->assertSame('First.json', $repo->titleToFileName('First'));
        $this->assertNull($repo->titleToFileName('Second'));
    }

    public function testMapLoadIsRetriedAfterFailure(): void
    {
        $repo = new LocalRestJsonRepository($this->dataDir);

        // First call: the map does not exist yet
        $this->assertNull($repo->titleToFileName('Late'));

        // The map appears later; a failed load must not be cached
        $this->writeMap(['Late' => 'Late.json']);

        $this->assertSame('Late.json', $repo->titleToFileName('Late'));
    }

    // ------------------------------------------------------------------
    // find: failure cases
    // ------------------------------------------------------------------

    public function testFindReturnsNullWhenDirectoryDoesNotExist(): void
    {
        $repo = new LocalRestJsonRepository($this->rootDir . '/missing_dir');

        $this->assertNull($repo->find('Anything'));
    }

    public function testFindReturnsNullWhenMapFileIsMissing(): void
    {
        // The page file exists, but without a map there is no way to resolve it
        file_put_contents($this->dataDir . '/Heart_failure.json', json_encode(['source' => 'x']));

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->find('Heart failure'));
    }

    public function testFindReturnsNullWhenMapIsInvalidJson(): void
    {
        file_put_contents($this->mapPath, '{ not json');
        file_put_contents($this->dataDir . '/Heart_failure.json', json_encode(['source' => 'x']));

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->find('Heart failure'));
    }

    public function testFindReturnsNullWhenTitleIsNotInMap(): void
    {
        $this->addPage('Heart failure', 'Heart_failure.json', json_encode(['source' => 'x']));

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->find('Not_cached'));
    }

    public function testFindReturnsNullWhenMappedFileDoesNotExist(): void
    {
        $this->writeMap(['Ghost' => 'Ghost.json']);

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->find('Ghost'));
    }

    public function testFindReturnsNullForInvalidJson(): void
    {
        $this->addPage('Broken', 'Broken.json', '{ this is not json');

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->find('Broken'));
    }

    public function testFindReturnsNullWhenJsonIsNotAnArray(): void
    {
        // A valid JSON scalar must be rejected because callers expect an array
        $this->addPage('Scalar', 'Scalar.json', '"just a string"');

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->find('Scalar'));
    }

    public function testFindReturnsNullWhenPathIsADirectory(): void
    {
        mkdir($this->dataDir . '/Folder.json');
        $this->writeMap(['Folder' => 'Folder.json']);

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->find('Folder'));
    }

    // ------------------------------------------------------------------
    // find: path safety
    // ------------------------------------------------------------------

    public function testFindDoesNotEscapeBaseDirectoryWithDotDotTitle(): void
    {
        // A file that exists OUTSIDE the data directory
        file_put_contents($this->rootDir . '/secret.json', json_encode(['source' => 'secret']));

        $repo = new LocalRestJsonRepository($this->dataDir);

        // The title is not in the map, so nothing is resolved
        $this->assertNull($repo->find('../secret'));
    }

    public function testFindRejectsMapEntryPointingOutsideBaseDirectory(): void
    {
        // Even a (tampered) map entry must not be able to leave the base directory
        file_put_contents($this->rootDir . '/secret.json', json_encode(['source' => 'secret']));
        $this->writeMap(['Evil' => '../secret.json']);

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->find('Evil'));
    }

    public function testFindRejectsSymlinkPointingOutsideBaseDirectory(): void
    {
        if (!function_exists('symlink') || DIRECTORY_SEPARATOR === '\\') {
            $this->markTestSkipped('Symlinks are not reliably supported on this platform.');
        }

        $outside = $this->rootDir . '/outside.json';
        file_put_contents($outside, json_encode(['source' => 'outside']));

        if (!@symlink($outside, $this->dataDir . '/Linked.json')) {
            $this->markTestSkipped('Could not create a symlink in this environment.');
        }

        $this->writeMap(['Linked' => 'Linked.json']);

        $repo = new LocalRestJsonRepository($this->dataDir);

        // The realpath check must detect that the target is outside the base directory
        $this->assertNull($repo->find('Linked'));
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Write a page file into the data dir and register it in titles_map.json.
     */
    private function addPage(string $title, string $fileName, string $content): void
    {
        file_put_contents($this->dataDir . '/' . $fileName, $content);

        $map = [];
        if (is_file($this->mapPath)) {
            $map = json_decode((string) file_get_contents($this->mapPath), true) ?: [];
        }
        $map[$title] = $fileName;

        $this->writeMap($map);
    }

    /**
     * @param array<string, string> $map
     */
    private function writeMap(array $map): void
    {
        file_put_contents($this->mapPath, json_encode($map, JSON_UNESCAPED_UNICODE));
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir) || is_link($dir)) {
            return;
        }

        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;

            if (is_dir($path) && !is_link($path)) {
                $this->removeDir($path);
            } else {
                // unlink() also removes symlinks without following them
                @unlink($path);
            }
        }

        @rmdir($dir);
    }
}
