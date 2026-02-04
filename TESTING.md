# Testing Guide

This document provides comprehensive information about testing the MediaWiki Content Transformation & API Services library.

## Quick Start

```bash
# Install dependencies
composer install

# Run all tests
composer test

# Run with coverage
vendor/bin/phpunit --coverage-text

# Run specific test suite
vendor/bin/phpunit tests/WikiParse/
```

## Test Suite Overview

The project includes **375+ tests** across **18 test files**, organized into 4 main categories:

### 1. WikiParse Tests (~100 tests)
Tests for MediaWiki content parsing:
- Category extraction
- Citation/reference parsing
- Template parsing and manipulation
- Lead section extraction

### 2. WikiTextFixes Tests (~150 tests)
Tests for wikitext transformation and cleanup:
- Reference handling (deletion, expansion)
- Template removal and fixing
- Category removal
- Image and video handling
- Language link removal
- Bad reference detection

### 3. APIServices Tests (~80 tests)
Tests for external API integrations:
- HTTP request handling
- MDWiki API integration
- HTML segmentation API
- Wikitext to HTML transformation

### 4. EntryPoints Tests (~35 tests)
Tests for application entry points:
- Revision checking
- JSON data management

## Running Tests

### By Module

```bash
# WikiParse module
vendor/bin/phpunit tests/WikiParse/CategoryTest.php
vendor/bin/phpunit tests/WikiParse/CitationsRegTest.php
vendor/bin/phpunit tests/WikiParse/ParserTemplatesTest.php
vendor/bin/phpunit tests/WikiParse/LeadSectionTest.php

# WikiTextFixes module
vendor/bin/phpunit tests/WikiTextFixes/

# APIServices module (requires network)
vendor/bin/phpunit tests/APIServices/

# EntryPoints module
vendor/bin/phpunit tests/EntryPoints/
```

### By Functionality

```bash
# Category handling
vendor/bin/phpunit tests/WikiParse/CategoryTest.php tests/WikiTextFixes/FixCatsTest.php

# Reference handling
vendor/bin/phpunit tests/WikiParse/CitationsRegTest.php tests/WikiTextFixes/DelMtRefsTest.php tests/WikiTextFixes/ExpendRefsTest.php tests/WikiTextFixes/RefWorkTest.php

# Template handling
vendor/bin/phpunit tests/WikiParse/ParserTemplatesTest.php tests/WikiTextFixes/DelTempsTest.php tests/WikiTextFixes/FixTempsTest.php
```

### Filtering Tests

```bash
# Run only tests matching a pattern
vendor/bin/phpunit --filter Category

# Run only tests in a specific method
vendor/bin/phpunit --filter testGetCategoriesWithMultipleCategories

# Run tests for a specific class
vendor/bin/phpunit --filter CategoryTest
```

### Verbose Output

```bash
# Show detailed test information
vendor/bin/phpunit --verbose

# Show even more details
vendor/bin/phpunit --debug
```

## Test Categories

### Unit Tests

Pure unit tests with no external dependencies:
- All WikiParse tests
- Most WikiTextFixes tests
- Some EntryPoints tests

These tests are fast and always run successfully.

### Integration Tests

Tests that interact with external services:
- APIServices tests (require network)
- Some EntryPoints tests (require file system)

These tests automatically skip when dependencies are unavailable.

## Code Coverage

### Generate HTML Coverage Report

```bash
vendor/bin/phpunit --coverage-html coverage/
open coverage/index.html  # macOS
xdg-open coverage/index.html  # Linux
```

### Generate Text Coverage Report

```bash
vendor/bin/phpunit --coverage-text
```

### Coverage Thresholds

Current test coverage targets:
- **Overall**: 80%+
- **WikiParse**: 90%+
- **WikiTextFixes**: 85%+
- **APIServices**: 70%+ (lower due to external dependencies)
- **EntryPoints**: 75%+

## Test Development

### Writing New Tests

1. **Create test file** in appropriate directory:
   ```php
   <?php
   namespace FixRefs\Tests\WikiParse;

   use PHPUnit\Framework\TestCase;
   use function WikiParse\YourFunction;

   class YourFunctionTest extends TestCase
   {
       public function testBasicFunctionality()
       {
           $result = YourFunction('input');
           $this->assertEquals('expected', $result);
       }
   }
   ```

2. **Follow naming conventions**:
   - File: `{FunctionName}Test.php`
   - Class: `{FunctionName}Test`
   - Method: `test{Scenario}` (e.g., `testWithEmptyInput`)

3. **Cover edge cases**:
   - Empty inputs
   - Null values
   - Invalid data
   - Boundary conditions
   - Special characters

4. **Use descriptive assertions**:
   ```php
   // Good
   $this->assertStringContainsString('expected', $result);
   $this->assertArrayHasKey('key', $array);

   // Avoid
   $this->assertTrue(strpos($result, 'expected') !== false);
   $this->assertTrue(isset($array['key']));
   ```

### Test Structure

Use the Arrange-Act-Assert pattern:

```php
public function testExample()
{
    // Arrange: Set up test data
    $input = 'test data';
    $expected = 'expected result';

    // Act: Execute function
    $result = functionToTest($input);

    // Assert: Verify results
    $this->assertEquals($expected, $result);
}
```

### Testing External APIs

For tests that depend on external APIs:

```php
protected function setUp(): void
{
    if (!$this->isApiAvailable()) {
        $this->markTestSkipped('API unavailable - skipping test');
    }
}

private function isApiAvailable(): bool
{
    $socket = @fsockopen('api.example.com', 443, $errno, $errstr, 5);
    if ($socket) {
        fclose($socket);
        return true;
    }
    return false;
}
```

## Common Test Scenarios

### Testing String Manipulation

```php
public function testStringManipulation()
{
    $text = "[[Category:Test]] content";
    $result = removeCategories($text);

    $this->assertStringNotContainsString('[[Category:Test]]', $result);
    $this->assertStringContainsString('content', $result);
}
```

### Testing Array Returns

```php
public function testArrayReturn()
{
    $result = getCategories($text);

    $this->assertIsArray($result);
    $this->assertCount(2, $result);
    $this->assertArrayHasKey('Medicine', $result);
}
```

### Testing Error Handling

```php
public function testErrorHandling()
{
    $result = functionWithError('invalid');

    $this->assertIsArray($result);
    $this->assertArrayHasKey('error', $result);
    $this->assertStringContainsString('Error:', $result['error']);
}
```

### Testing Empty Inputs

```php
public function testWithEmptyInput()
{
    $result = function('');

    $this->assertIsString($result);
    $this->assertEquals('', $result);
    // or
    $this->assertEmpty($result);
}
```

## Debugging Tests

### Run Single Test with Debug

```bash
vendor/bin/phpunit --debug --filter testSpecificMethod tests/YourTest.php
```

### Print Debug Information

```php
public function testDebug()
{
    $result = complexFunction();

    // Print for debugging (don't commit)
    var_dump($result);
    print_r($result);

    $this->assertTrue(true);
}
```

### Use Data Providers

For testing multiple scenarios:

```php
/**
 * @dataProvider categoryProvider
 */
public function testCategories($input, $expected)
{
    $result = getCategories($input);
    $this->assertEquals($expected, $result);
}

public function categoryProvider()
{
    return [
        'single category' => ['[[Category:Test]]', ['Test' => '[[Category:Test]]']],
        'multiple categories' => ['[[Category:A]] [[Category:B]]', ['A' => '...', 'B' => '...']],
        'no categories' => ['plain text', []],
    ];
}
```

## Continuous Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
    - uses: actions/checkout@v2

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '7.4'

    - name: Install dependencies
      run: composer install

    - name: Run tests
      run: composer test

    - name: Run PHPStan
      run: composer phpstan
```

## Performance Testing

### Benchmark Tests

```php
public function testPerformance()
{
    $start = microtime(true);

    for ($i = 0; $i < 1000; $i++) {
        getCategories($largeText);
    }

    $duration = microtime(true) - $start;

    $this->assertLessThan(1.0, $duration, 'Should complete in under 1 second');
}
```

## Best Practices

1. **Keep tests independent**: Each test should run in isolation
2. **Use setUp and tearDown**: Initialize and clean up properly
3. **Test one thing**: Each test should verify one behavior
4. **Use descriptive names**: Test names should explain what they test
5. **Avoid test interdependencies**: Tests should not rely on execution order
6. **Mock external dependencies**: Use mocks for APIs and file system when possible
7. **Test edge cases**: Empty, null, invalid, boundary conditions
8. **Keep tests fast**: Unit tests should execute quickly
9. **Maintain tests**: Update tests when code changes
10. **Document complex tests**: Add comments for non-obvious test logic

## Troubleshooting

### Common Issues

**Problem**: Tests fail with "Class not found"
```bash
# Solution: Regenerate autoloader
composer dump-autoload
```

**Problem**: API tests timing out
```bash
# Solution: Increase timeout or skip API tests
vendor/bin/phpunit --exclude-group api
```

**Problem**: Permission denied errors
```bash
# Solution: Check file permissions
chmod -R 755 tests/
```

**Problem**: Memory limit errors
```bash
# Solution: Increase PHP memory limit
php -d memory_limit=512M vendor/bin/phpunit
```

### Getting Help

- Check [PHPUnit documentation](https://phpunit.de/documentation.html)
- Review test examples in the test suite
- Search for error messages in project issues
- Ask in project discussions

## Test Maintenance

### Regular Tasks

1. **Update tests** when adding new features
2. **Remove obsolete tests** when removing features
3. **Refactor tests** to reduce duplication
4. **Update fixtures** to reflect current data
5. **Review coverage** and add tests for uncovered code
6. **Update documentation** when test structure changes

### Quarterly Review

- Analyze test execution times
- Identify flaky tests
- Review and update mocks
- Update external API tests
- Check for deprecated PHPUnit features

## Additional Resources

- [PHPUnit Manual](https://phpunit.readthedocs.io/)
- [Test-Driven Development](https://en.wikipedia.org/wiki/Test-driven_development)
- [Project README](README.md)
- [Test Suite Documentation](tests/README.md)