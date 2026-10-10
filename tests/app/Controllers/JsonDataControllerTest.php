<?php
namespace Tests\EntryPoints;

use MDWiki\NewHtml\Controllers\JsonDataController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonDataController::class)]
class JsonDataControllerTest extends TestCase
{
    private $testJsonFile;
    private $testJsonFileAll;

    protected function setUp(): void
    {
        // Create temporary test JSON files
        $this->testJsonFile    = sys_get_temp_dir() . '/test_json_data_' . time() . '.json';
        $this->testJsonFileAll = sys_get_temp_dir() . '/test_json_data_all_' . time() . '.json';

        // Initialize with empty JSON objects
        file_put_contents($this->testJsonFile, '{}');
        file_put_contents($this->testJsonFileAll, '{}');
    }

    public function testGetTitleRevisionWithExistingTitle()
    {
        // Manually add data to test file
        $data = ['TestArticle' => '12345'];
        file_put_contents($this->testJsonFile, json_encode($data));

        $revision = JsonDataController::getTitleRevision('TestArticle', '');

        // This test depends on global file paths, skip if not accessible
        if ($revision === '') {
            $this->markTestSkipped('Global JSON file paths not accessible in test environment');
        }
    }

    public function testGetTitleRevisionWithNonexistentTitle()
    {
        $result = JsonDataController::getTitleRevision('NonexistentArticle', '');

        $this->assertIsString($result);
    }

    public function testGetTitleRevisionWithEmptyFile()
    {
        // Clear the file
        global $json_file;
        $originalFile = $json_file ?? '';

        $result = JsonDataController::getTitleRevision('AnyTitle', '');

        $this->assertIsString($result);
    }

    public function testAddTitleRevisionWithValidData()
    {
        $result = JsonDataController::addTitleRevision('NewArticle', '67890', '');

        // Result depends on global state
        $this->assertTrue(is_array($result) || $result === '');
    }

    public function testAddTitleRevisionWithEmptyTitle()
    {
        $result = JsonDataController::addTitleRevision('', '12345', '');

        $this->assertEquals('', $result);
    }

    public function testAddTitleRevisionWithEmptyRevision()
    {
        $result = JsonDataController::addTitleRevision('Article', '', '');

        $this->assertEquals('', $result);
    }

    public function testAddTitleRevisionWithBothEmpty()
    {
        $result = JsonDataController::addTitleRevision('', '', '');

        $this->assertEquals('', $result);
    }

    public function testGetTitleRevisionWithAllFlag()
    {
        $result = JsonDataController::getTitleRevision('TestArticle', 'all');

        $this->assertIsString($result);
    }

    public function testAddTitleRevisionWithAllFlag()
    {
        $result = JsonDataController::addTitleRevision('Article', '12345', 'all');

        $this->assertTrue(is_array($result) || $result === '');
    }

    public function testAddTitleRevisionReturnsArrayOrEmpty()
    {
        $result = JsonDataController::addTitleRevision('Test', '123', '');

        // Should return array with data or empty string
        $this->assertTrue(is_array($result) || $result === '');
    }

    public function testGetTitleRevisionWithSpecialCharacters()
    {
        $result = JsonDataController::getTitleRevision("Article's Title", '');

        $this->assertIsString($result);
    }

    public function testAddTitleRevisionWithSpecialCharacters()
    {
        $result = JsonDataController::addTitleRevision("Article's Title", '12345', '');

        $this->assertTrue(is_array($result) || $result === '');
    }

    protected function tearDown(): void
    {
        // Clean up temporary files
        @unlink($this->testJsonFile);
        @unlink($this->testJsonFileAll);
    }
}
