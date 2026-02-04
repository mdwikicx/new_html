<?php

namespace FixRefs\Tests\EntryPoints;

use PHPUnit\Framework\TestCase;

class CheckTest extends TestCase
{
    private $checkPhpPath;

    protected function setUp(): void
    {
        $this->checkPhpPath = __DIR__ . '/../../check.php';
    }

    public function testCheckPhpWithoutRevid()
    {
        // Simulate GET request without revid
        $_GET = [];

        ob_start();
        include $this->checkPhpPath;
        $output = ob_get_clean();

        $this->assertEquals('false', $output);
    }

    public function testCheckPhpWithEmptyRevid()
    {
        $_GET = ['revid' => ''];

        ob_start();
        include $this->checkPhpPath;
        $output = ob_get_clean();

        $this->assertEquals('false', $output);
    }

    public function testCheckPhpWithNonexistentRevid()
    {
        $_GET = ['revid' => '99999999999'];

        ob_start();
        include $this->checkPhpPath;
        $output = ob_get_clean();

        $this->assertEquals('false', $output);
    }

    public function testCheckPhpWithTestParameter()
    {
        // Test with test parameter to enable error display
        $_GET = ['revid' => '12345', 'test' => '1'];

        ob_start();
        include $this->checkPhpPath;
        $output = ob_get_clean();

        // Should return false for nonexistent revid
        $this->assertEquals('false', $output);
    }

    public function testCheckPhpWithTestCookie()
    {
        // Test with test cookie
        $_GET = ['revid' => '12345'];
        $_COOKIE = ['test' => '1'];

        ob_start();
        include $this->checkPhpPath;
        $output = ob_get_clean();

        $this->assertEquals('false', $output);
    }

    public function testCheckPhpOutputIsBooleanString()
    {
        $_GET = ['revid' => '12345'];

        ob_start();
        include $this->checkPhpPath;
        $output = ob_get_clean();

        // Output should be either 'true' or 'false'
        $this->assertTrue(in_array($output, ['true', 'false']));
    }

    public function testCheckPhpWithValidRevisionDirectory()
    {
        // Create a test revision directory
        $testRevid = 'test_' . time();
        $testDir = __DIR__ . '/../../revisions_new/' . $testRevid;

        // Skip if we can't create directories
        if (!is_writable(dirname($testDir))) {
            $this->markTestSkipped('Cannot create test directory');
        }

        @mkdir($testDir, 0755, true);
        @touch($testDir . '/seg.html');
        @touch($testDir . '/html.html');

        $_GET = ['revid' => $testRevid];

        ob_start();
        include $this->checkPhpPath;
        $output = ob_get_clean();

        // Clean up
        @unlink($testDir . '/seg.html');
        @unlink($testDir . '/html.html');
        @rmdir($testDir);

        $this->assertEquals('true', $output);
    }

    public function testCheckPhpWithMissingSegFile()
    {
        $testRevid = 'test_missing_seg_' . time();
        $testDir = __DIR__ . '/../../revisions_new/' . $testRevid;

        if (!is_writable(dirname($testDir))) {
            $this->markTestSkipped('Cannot create test directory');
        }

        @mkdir($testDir, 0755, true);
        @touch($testDir . '/html.html'); // Only html.html exists

        $_GET = ['revid' => $testRevid];

        ob_start();
        include $this->checkPhpPath;
        $output = ob_get_clean();

        // Clean up
        @unlink($testDir . '/html.html');
        @rmdir($testDir);

        $this->assertEquals('false', $output);
    }

    public function testCheckPhpWithMissingHtmlFile()
    {
        $testRevid = 'test_missing_html_' . time();
        $testDir = __DIR__ . '/../../revisions_new/' . $testRevid;

        if (!is_writable(dirname($testDir))) {
            $this->markTestSkipped('Cannot create test directory');
        }

        @mkdir($testDir, 0755, true);
        @touch($testDir . '/seg.html'); // Only seg.html exists

        $_GET = ['revid' => $testRevid];

        ob_start();
        include $this->checkPhpPath;
        $output = ob_get_clean();

        // Clean up
        @unlink($testDir . '/seg.html');
        @rmdir($testDir);

        $this->assertEquals('false', $output);
    }

    public function testCheckPhpWithNumericRevid()
    {
        $_GET = ['revid' => '123456'];

        ob_start();
        include $this->checkPhpPath;
        $output = ob_get_clean();

        // Should handle numeric revid
        $this->assertIsString($output);
        $this->assertTrue(in_array($output, ['true', 'false']));
    }

    public function testCheckPhpWithSpecialCharactersInRevid()
    {
        $_GET = ['revid' => '../../../etc/passwd'];

        ob_start();
        include $this->checkPhpPath;
        $output = ob_get_clean();

        // Should safely handle path traversal attempts
        $this->assertEquals('false', $output);
    }

    protected function tearDown(): void
    {
        // Clean up global variables
        $_GET = [];
        $_COOKIE = [];
    }
}