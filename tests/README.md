# Test Suite Documentation

This test suite provides comprehensive coverage for the MediaWiki Content Transformation & API Services library.

## Test Structure

```
tests/
├── bootstrap.php                    # Test bootstrap and initialization
├── WikiParse/                       # Tests for WikiParse modules
│   ├── CategoryTest.php            # Category parsing tests
│   ├── CitationsRegTest.php        # Citation/reference parsing tests
│   ├── ParserTemplatesTest.php     # Template parsing tests
│   └── LeadSectionTest.php         # Lead section extraction tests
├── WikiTextFixes/                   # Tests for wikitext transformation
│   ├── DelMtRefsTest.php           # Empty reference deletion tests
│   ├── DelTempsTest.php            # Template removal tests
│   ├── ExpendRefsTest.php          # Reference expansion tests
│   ├── FixCatsTest.php             # Category removal tests
│   ├── FixImagesTest.php           # Image/video handling tests
│   ├── FixLangsLinksTest.php       # Language link removal tests
│   ├── FixTempsTest.php            # Template title fixing tests
│   └── RefWorkTest.php             # Bad reference removal tests
├── APIServices/                     # Tests for API integrations
│   ├── PostTest.php                # HTTP request handling tests
│   ├── MdwikiApiTest.php           # MDWiki API integration tests
│   ├── SegApiTest.php              # Segmentation API tests
│   └── TransformApiTest.php        # WikiText to HTML transform tests
└── EntryPoints/                     # Tests for entry point files
    ├── CheckTest.php               # Revision check endpoint tests
    └── JsonDataTest.php            # JSON data management tests

```

## Running Tests

### Run All Tests

```bash
composer test
# or
vendor/bin/phpunit
```

### Run Specific Test Suites

```bash
# WikiParse tests
vendor/bin/phpunit tests/WikiParse/

# WikiTextFixes tests
vendor/bin/phpunit tests/WikiTextFixes/

# API Services tests (requires network)
vendor/bin/phpunit tests/APIServices/

# Entry point tests
vendor/bin/phpunit tests/EntryPoints/
```

### Run Individual Test Files

```bash
vendor/bin/phpunit tests/WikiParse/CategoryTest.php
vendor/bin/phpunit tests/WikiTextFixes/RefWorkTest.php
vendor/bin/phpunit tests/APIServices/PostTest.php
```

### Run Specific Test Methods

```bash
vendor/bin/phpunit --filter testGetCategoriesWithMultipleCategories tests/WikiParse/CategoryTest.php
```

## Test Coverage

### WikiParse Module Tests (4 files, ~100 tests)

**CategoryTest.php** - Tests category extraction functionality:
- Single and multiple categories
- Categories with sort keys
- Case insensitive matching
- Whitespace handling
- Special characters
- Edge cases (empty text, no categories)

**CitationsRegTest.php** - Tests citation/reference parsing:
- Full references with and without names
- Short self-closing references
- Multiple citation extraction
- Name attribute parsing (quoted and unquoted)
- Complex attributes and nested content
- Edge cases (anonymous refs, empty names)

**ParserTemplatesTest.php** - Tests template parsing and manipulation:
- Template construction and parameter access
- Parameter setting, deletion, and renaming
- Template to string conversion (inline and multiline)
- Nested template parsing
- Multiple template extraction
- Edge cases (empty text, malformed templates)

**LeadSectionTest.php** - Tests lead section extraction:
- Section splitting at headings
- References section addition
- Multi-level heading handling
- Content preservation (templates, links, formatting)
- Edge cases (no sections, empty lead)

### WikiTextFixes Module Tests (8 files, ~150 tests)

**DelMtRefsTest.php** - Tests empty reference deletion:
- Orphan short reference removal
- Full reference replacement
- Multiple reference handling
- Anonymous reference preservation
- Complex name matching

**DelTempsTest.php** - Tests template removal:
- Metadata template deletion (short description, featured article, etc.)
- Stub template removal
- Protection template removal
- Pattern-based removal (pp-*, articles *, *-stub)
- Lead template extraction (infobox, drugbox, speciesbox)
- Case insensitive matching

**ExpendRefsTest.php** - Tests reference expansion:
- Short reference expansion with full content
- Handling existing full references
- Multiple reference expansion
- Empty alltext fallback
- Special characters in reference names

**FixCatsTest.php** - Tests category removal:
- Single and multiple category removal
- Categories with sort keys
- Case variation handling
- Preserving other wiki elements
- Edge cases (inline categories, duplicates)

**FixImagesTest.php** - Tests image and video handling:
- Image wrapping with #ifexist
- Video file removal (webm, ogv, ogg, mp4)
- Complex parameter preservation
- Nested content handling
- Extension case sensitivity

**FixLangsLinksTest.php** - Tests language link removal:
- Interwiki language link removal
- Multiple language handling
- Preserving normal links and categories
- Special characters and complex names
- Pattern matching across known language codes

**FixTempsTest.php** - Tests template title fixing:
- Missing title addition for infoboxes and drugboxes
- Empty/whitespace title replacement
- Multiple template handling
- Parameter preservation
- Case insensitive template matching

**RefWorkTest.php** - Tests bad reference removal:
- Predatory journal detection (DOI patterns)
- Open access journal filtering
- Self-published source detection
- URL pattern matching
- Multiple bad reference removal
- Good reference preservation

### APIServices Module Tests (4 files, ~80 tests)

**PostTest.php** - Tests HTTP request functionality:
- GET and POST requests
- Parameter handling
- Error handling (invalid URLs, timeouts)
- User agent setting
- HTTP status code handling
- Network availability checking

**MdwikiApiTest.php** - Tests MDWiki API integration:
- Wikitext fetching via API and REST endpoints
- Valid and invalid article handling
- Special character handling
- Revision ID extraction
- API consistency checking
- Network availability detection

**SegApiTest.php** - Tests HTML segmentation API:
- Simple and complex HTML segmentation
- Various HTML elements (headings, lists, tables)
- Unicode and special character handling
- Error handling
- Large document handling
- API availability checking

**TransformApiTest.php** - Tests wikitext to HTML transformation:
- Basic wikitext conversion
- Wiki markup (bold, italic, links, headings)
- Templates and references
- Lists, tables, and images
- Unicode character handling
- Title with special characters
- API availability checking

### EntryPoints Module Tests (2 files, ~35 tests)

**CheckTest.php** - Tests revision check endpoint:
- Missing and empty revid handling
- Directory and file existence checking
- Boolean output validation
- Security (path traversal prevention)
- Test mode activation

**JsonDataTest.php** - Tests JSON data management:
- Title-revision mapping
- Data retrieval and storage
- File handling (empty, corrupted)
- Special character handling
- Large dataset handling

## Test Features

### Automatic Test Skipping

Tests that depend on external APIs automatically skip when the API is unavailable:

```php
protected function setUp(): void
{
    if (!$this->isNetworkAvailable()) {
        $this->markTestSkipped('Network unavailable - skipping API tests');
    }
}
```

This makes tests safe to run offline or in CI environments without network access.

### Edge Case Coverage

Each test suite includes comprehensive edge case testing:
- Empty inputs
- Special characters and Unicode
- Malformed data
- Boundary conditions
- Security concerns (injection, path traversal)

### Integration Testing

API tests verify real-world integration with:
- MDWiki.org REST API
- Wikipedia Transform API
- HtmltoSegments service
- HTTP request handling

## Test Conventions

### Naming

- Test files: `{ClassName}Test.php`
- Test methods: `test{FunctionName}With{Scenario}`
- Example: `testGetCategoriesWithMultipleCategories`

### Assertions

- Use specific assertions: `assertStringContainsString`, `assertArrayHasKey`
- Test both positive and negative cases
- Verify data types: `assertIsArray`, `assertIsString`
- Check counts: `assertCount`, `assertEmpty`

### Structure

1. Arrange: Set up test data
2. Act: Execute the function
3. Assert: Verify expected results

## Known Limitations

1. **API Tests**: Require network connectivity and may be slow
2. **File System Tests**: Depend on write permissions
3. **Global State**: Some tests interact with global variables
4. **External Dependencies**: API availability affects test results

## Running Static Analysis

```bash
composer phpstan
```

## Continuous Integration

Tests are designed to run in CI environments:
- Automatic skipping of unavailable services
- No external dependencies required for core tests
- Fast execution for unit tests
- Comprehensive coverage reporting

## Contributing

When adding new functionality:
1. Write tests first (TDD approach)
2. Cover main functionality and edge cases
3. Include negative test cases
4. Test with empty/null/invalid inputs
5. Add integration tests for API interactions
6. Update this README with new test files

## Code Coverage

To generate code coverage reports:

```bash
vendor/bin/phpunit --coverage-html coverage/
```

View the report by opening `coverage/index.html` in a browser.

## Troubleshooting

### Tests Failing

1. Check PHPUnit version: `vendor/bin/phpunit --version`
2. Verify PHP version: `php --version` (requires PHP >= 7.4)
3. Ensure dependencies are installed: `composer install`
4. Check API availability for API tests

### Slow Tests

API tests can be slow due to network latency. Run only unit tests:

```bash
vendor/bin/phpunit --exclude-group api
```

### Permission Issues

Some tests require write permissions:

```bash
chmod -R 755 tests/
```

## Additional Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [MediaWiki API Documentation](https://www.mediawiki.org/wiki/API)
- [Project README](../README.md)