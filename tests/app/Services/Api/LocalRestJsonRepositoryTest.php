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

    protected function setUp(): void
    {
        // Each test gets its own isolated temp directory
        $this->rootDir = sys_get_temp_dir() . '/local_rest_repo_' . uniqid('', true);
        $this->dataDir = $this->rootDir . '/data';
        mkdir($this->dataDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->rootDir);
    }

    // ------------------------------------------------------------------
    // titleToFileName
    // ------------------------------------------------------------------

    public function testTitleToFileNameReplacesSpacesWithUnderscores(): void
    {
        $this->assertSame('Heart_failure.json', LocalRestJsonRepository::titleToFileName('Heart failure'));
    }

    public function testTitleToFileNameEncodesSlash(): void
    {
        $this->assertSame('A%2FB.json', LocalRestJsonRepository::titleToFileName('A/B'));
    }

    public function testTitleToFileNameEncodesNonAsciiCharacters(): void
    {
        $this->assertSame('%D9%82%D9%84%D8%A8.json', LocalRestJsonRepository::titleToFileName('قلب'));
    }

    // ------------------------------------------------------------------
    // find: success cases
    // ------------------------------------------------------------------

    public function testFindReturnsDecodedJsonForExistingFile(): void
    {
        $payload = ['source' => 'Some wikitext', 'latest' => ['id' => 123]];
        $this->writeFile('Heart_failure.json', json_encode($payload));

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertSame($payload, $repo->find('Heart_failure'));
    }

    public function testFindTreatsSpacesAndUnderscoresAsTheSameTitle(): void
    {
        $payload = ['source' => 'Text', 'latest' => ['id' => 1]];
        $this->writeFile('Heart_failure.json', json_encode($payload));

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertSame($payload, $repo->find('Heart failure'));
    }

    public function testFindSupportsTitlesContainingSlash(): void
    {
        $payload = ['source' => 'Slash title', 'latest' => ['id' => 2]];
        $this->writeFile('A%2FB.json', json_encode($payload));

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertSame($payload, $repo->find('A/B'));
    }

    public function testFindSupportsNonAsciiTitles(): void
    {
        $payload = ['source' => 'Arabic title', 'latest' => ['id' => 3]];
        $this->writeFile(LocalRestJsonRepository::titleToFileName('قلب'), json_encode($payload));

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertSame($payload, $repo->find('قلب'));
    }

    // ------------------------------------------------------------------
    // find: failure cases
    // ------------------------------------------------------------------

    public function testFindReturnsNullWhenDirectoryDoesNotExist(): void
    {
        $repo = new LocalRestJsonRepository($this->rootDir . '/missing_dir');

        $this->assertNull($repo->find('Anything'));
    }

    public function testFindReturnsNullWhenFileDoesNotExist(): void
    {
        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->find('Not_cached'));
    }

    public function testFindReturnsNullForInvalidJson(): void
    {
        $this->writeFile('Broken.json', '{ this is not json');

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->find('Broken'));
    }

    public function testFindReturnsNullWhenJsonIsNotAnArray(): void
    {
        // A valid JSON scalar must be rejected because callers expect an array
        $this->writeFile('Scalar.json', '"just a string"');

        $repo = new LocalRestJsonRepository($this->dataDir);

        $this->assertNull($repo->find('Scalar'));
    }

    public function testFindReturnsNullWhenPathIsADirectory(): void
    {
        mkdir($this->dataDir . '/Folder.json');

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

        $this->assertNull($repo->find('../secret'));
    }

    public function testFindRejectsSymlinkPointingOutsideBaseDirectory(): void
    {
        if (! function_exists('symlink') || DIRECTORY_SEPARATOR === '\\') {
            $this->markTestSkipped('Symlinks are not reliably supported on this platform.');
        }

        $outside = $this->rootDir . '/outside.json';
        file_put_contents($outside, json_encode(['source' => 'outside']));

        if (! @symlink($outside, $this->dataDir . '/Linked.json')) {
            $this->markTestSkipped('Could not create a symlink in this environment.');
        }

        $repo = new LocalRestJsonRepository($this->dataDir);

        // The realpath check must detect that the target is outside the base directory
        $this->assertNull($repo->find('Linked'));
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function writeFile(string $name, string $content): void
    {
        file_put_contents($this->dataDir . '/' . $name, $content);
    }

    private function removeDir(string $dir): void
    {
        if (! is_dir($dir) || is_link($dir)) {
            return;
        }

        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;

            if (is_dir($path) && ! is_link($path)) {
                $this->removeDir($path);
            } else {
                // unlink() also removes symlinks without following them
                @unlink($path);
            }
        }

        @rmdir($dir);
    }
}
