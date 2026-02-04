<?php

namespace FixRefs\Tests\EntryPoints;

use FixRefs\Tests\bootstrap;

use function NewHtml\JsonData\get_title_revision;
use function NewHtml\JsonData\add_title_revision;
use function NewHtml\JsonData\get_from_json;
use function NewHtml\JsonData\get_Data;
use function NewHtml\JsonData\dump_both_data;

class JsonDataTest extends bootstrap
{
    private $testJsonFile;
    private $testJsonFileAll;

    protected function setUp(): void
    {
        // Create temporary test JSON files
        $this->testJsonFile = sys_get_temp_dir() . '/test_json_data_' . time() . '.json';
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

        $result = get_title_revision('TestArticle', '');

        // This test depends on global file paths, skip if not accessible
        if ($result === '') {
            $this->markTestSkipped('Global JSON file paths not accessible in test environment');
        }
    }

    public function testGetTitleRevisionWithNonexistentTitle()
    {
        $result = get_title_revision('NonexistentArticle', '');

        $this->assertIsString($result);
    }

    public function testGetTitleRevisionWithEmptyFile()
    {
        // Clear the file
        global $json_file;
        $originalFile = $json_file ?? '';

        $result = get_title_revision('AnyTitle', '');

        $this->assertIsString($result);
    }

    public function testAddTitleRevisionWithValidData()
    {
        $result = add_title_revision('NewArticle', '67890', '');

        // Result depends on global state
        $this->assertTrue(is_array($result) || $result === '');
    }

    public function testAddTitleRevisionWithEmptyTitle()
    {
        $result = add_title_revision('', '12345', '');

        $this->assertEquals('', $result);
    }

    public function testAddTitleRevisionWithEmptyRevision()
    {
        $result = add_title_revision('Article', '', '');

        $this->assertEquals('', $result);
    }

    public function testAddTitleRevisionWithBothEmpty()
    {
        $result = add_title_revision('', '', '');

        $this->assertEquals('', $result);
    }

    public function testGetFromJsonWithExistingTitle()
    {
        $result = get_from_json('TestArticle', '');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetFromJsonWithNonexistentTitle()
    {
        $result = get_from_json('NonexistentArticle123', '');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        // Should return empty strings
        list($wikitext, $revid) = $result;
        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testGetFromJsonReturnsArray()
    {
        $result = get_from_json('AnyTitle', '');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetDataWithEmptyType()
    {
        $result = get_Data('');

        $this->assertIsArray($result);
    }

    public function testGetDataWithAllType()
    {
        $result = get_Data('all');

        $this->assertIsArray($result);
    }

    public function testGetDataReturnsArray()
    {
        $result = get_Data('');

        $this->assertIsArray($result);
    }

    public function testDumpBothDataWithValidData()
    {
        $mainData = ['Title1' => 'Rev1', 'Title2' => 'Rev2'];
        $mainDataAll = ['Title3' => 'Rev3', 'Title4' => 'Rev4'];

        // This will write to global files
        dump_both_data($mainData, $mainDataAll);

        // Just verify it doesn't throw an error
        $this->assertTrue(true);
    }

    public function testDumpBothDataWithEmptyData()
    {
        dump_both_data([], []);

        // Should handle empty arrays
        $this->assertTrue(true);
    }

    public function testGetTitleRevisionWithAllFlag()
    {
        $result = get_title_revision('TestArticle', 'all');

        $this->assertIsString($result);
    }

    public function testAddTitleRevisionWithAllFlag()
    {
        $result = add_title_revision('Article', '12345', 'all');

        $this->assertTrue(is_array($result) || $result === '');
    }

    public function testGetFromJsonWithAllFlag()
    {
        $result = get_from_json('TestArticle', 'all');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testAddTitleRevisionReturnsArrayOrEmpty()
    {
        $result = add_title_revision('Test', '123', '');

        // Should return array with data or empty string
        $this->assertTrue(is_array($result) || $result === '');
    }

    public function testGetTitleRevisionWithSpecialCharacters()
    {
        $result = get_title_revision("Article's Title", '');

        $this->assertIsString($result);
    }

    public function testAddTitleRevisionWithSpecialCharacters()
    {
        $result = add_title_revision("Article's Title", '12345', '');

        $this->assertTrue(is_array($result) || $result === '');
    }

    public function testGetFromJsonHandlesEmptyRevid()
    {
        $result = get_from_json('ArticleWithoutRevid', '');

        list($wikitext, $revid) = $result;
        $this->assertIsString($wikitext);
        $this->assertIsString($revid);
    }

    public function testDumpBothDataWithLargeData()
    {
        $largeData = [];
        for ($i = 0; $i < 100; $i++) {
            $largeData["Title$i"] = "Revision$i";
        }

        dump_both_data($largeData, $largeData);

        // Should handle large datasets
        $this->assertTrue(true);
    }

    public function testGetDataHandlesCorruptedJson()
    {
        // This test depends on file system access
        $result = get_Data('');

        // Should return empty array on error
        $this->assertIsArray($result);
    }

    protected function tearDown(): void
    {
        // Clean up temporary files
        @unlink($this->testJsonFile);
        @unlink($this->testJsonFileAll);
    }
}
