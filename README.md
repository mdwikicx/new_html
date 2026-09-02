# MediaWiki Content Transformation & API Services

> live at [https://mdwikicx.toolforge.org/new_html_1](https://mdwikicx.toolforge.org/new_html_1)

A comprehensive PHP library for processing MediaWiki articles and generating segmented content for the [ContentTranslation tool](https://github.com/mdwikicx/cx-1).

## Overview

This library processes articles from `mdwiki.org` and transforms them into various formats, including segmented content in JSON format. It provides a suite of tools for fetching, parsing, transforming, and fixing MediaWiki content.

## Features

-   🔄 **Content Transformation**: Convert MediaWiki wikitext to HTML and segmented content
-   🖼️ **Image Processing**: Fix image references and validate Wikimedia Commons images
-   🏷️ **Category Management**: Parse and manage article categories
-   📝 **Wikitext Fixes**: Clean up and normalize wikitext content
-   🌐 **Language Links**: Handle interwiki language links
-   📚 **Template Processing**: Parse and manipulate MediaWiki templates
-   🔍 **Citation Handling**: Process and expand citation references
-   🧪 **Comprehensive Testing**: PHPUnit test suite with API integration tests

## Requirements

-   PHP >= 8.2
-   Composer

## Installation

1. Clone the repository:

```bash
git clone https://github.com/mdwikicx/new_html.git
cd new_html
```

2. Install dependencies:

```bash
composer install
```

## Usage

### Basic Usage

The library can be used to process MediaWiki articles with different output formats controlled by the `printetxt` parameter:

-   `wikitext` - Output raw wikitext
-   `html` - Output HTML
-   `seg` - Output segmented content
-   Default - Output JSON with segmented content

### Processing Pipeline

1. **Wikitext Generation**: Fetch wikitext and revision ID from [mdwiki.org REST API](https://mdwiki.org/w/rest.php/v1/page/title)

2. **HTML Generation**: Transform wikitext to HTML using [enwiki rest.php](https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/title)

3. **Segmented Content Generation**: Generate segmented content using [HtmltoSegments tool](https://ncc2c.toolforge.org/HtmltoSegments)

4. **JSON Data Preparation**: Prepare JSON object with:

    - Source language
    - Article title
    - Revision ID
    - Segmented content
    - Categories

5. **Error Handling**: Returns 404 status code with error message if content is not found

### API Services

The library provides several API service modules:

-   **Commons API** (`commons_api.php`): Check if images exist on Wikimedia Commons
-   **MDWiki API** (`mdwiki_api_wikitext.php`): Fetch wikitext from mdwiki.org
-   **Transform API** (`transform_api.php`): Transform wikitext to HTML
-   **Segmentation API** (`seg_api.php`): Generate segmented content

### Wikitext Fixes

Available wikitext transformation modules:

-   `del_mt_refs.php` - Delete machine translation references
-   `del_temps.php` - Delete specific templates
-   `expend_refs.php` - Expand reference tags
-   `fix_cats.php` - Fix category formatting
-   `fix_images.php` - Fix image references
-   `fix_langs_links.php` - Fix language links
-   `fix_temps.php` - Fix template formatting
-   `ref_work.php` - Process reference sections

## End Points

| Endpoint             | Method | Description                                                             |
| -------------------- | ------ | ----------------------------------------------------------------------- |
| `/`                  | GET    | Main entry - router (redirects to dashboard or processes `title` param) |
| `/check.php`         | GET    | Check if cached content exists for a revision ID                        |
| `/open.php`          | GET    | View generated files (wikitext, HTML, segments) by revision ID          |
| `/fix.php`           | GET    | Wikitext fix testing form                                               |
| `/fix.php`           | POST   | Apply wikitext fixes and display result                                 |
| `/revisions.php`     | GET    | Revisions dashboard (HTML table)                                        |
| `/revisions_api.php` | GET    | Revisions API (JSON payload)                                            |
| `/revisions.html`    | GET    | Static dashboard page                                                   |

## Development

### Running Tests

Run the complete test suite (excludes network tests):

```bash
composer test
```

Or run PHPUnit directly:

```bash
vendor/bin/phpunit
```

Run specific test files:

```bash
vendor/bin/phpunit tests/commons_api_test.php
```

#### Network Tests

Network tests are located in `tests/NetworkRealTests/` and test real API connections. They are **excluded from the default test suite** and require both the test suite option and `RUN_NETWORK_TESTS=true`:

```bash
# Run only network tests
RUN_NETWORK_TESTS=true vendor/bin/phpunit tests/NetworkRealTests --testsuite network
```

**Windows (Command Prompt):**

```cmd
set RUN_NETWORK_TESTS=true
vendor/bin/phpunit tests/NetworkRealTests --testsuite network
```

**Windows (PowerShell):**

```powershell
$env:RUN_NETWORK_TESTS="true"
vendor/bin/phpunit tests/NetworkRealTests --testsuite network
```

The `phpunit.xml` configuration excludes `tests/NetworkRealTests/` from the default test suite, so regular tests never run network tests accidentally.

**Available Network Tests:**

-   `CommonsApiRealTest` - Tests Wikimedia Commons API connectivity
-   `MdwikiApiRealTest` - Tests mdwiki.org REST API
-   `SegApiRealTest` - Tests HTML segmentation service
-   `TransformApiRealTest` - Tests Wikipedia wikitext transformation API

Network tests automatically skip if external APIs are unreachable, making them safe to run even with intermittent connectivity.

### Static Analysis

Run PHPStan for static code analysis:

```bash
vendor/bin/phpstan analyse
```

### Project Structure

```text
src/
├── Application/              # Application layer (entry points & controllers)
│   ├── Controllers/         # Business logic controllers
│   │   └── JsonDataController.php
│   └── Handlers/            # Request handlers
│       └── WikitextHandler.php
│
├── Services/                # Service layer (business operations)
│   ├── Api/                # External API integrations
│   │   ├── CommonsApiService.php
│   │   ├── HttpClientService.php
│   │   ├── MdwikiApiService.php
│   │   ├── SegmentApiService.php
│   │   └── TransformApiService.php
│   │
│   ├── Html/               # HTML processing services
│   │   ├── HtmlToSegmentsService.php
│   │   └── WikitextToHtmlService.php
│   │
│   └── Wikitext/           # Wikitext processing services
│       └── WikitextFixerService.php
│
├── Domain/                  # Domain layer (core business logic)
│   ├── Parser/             # Wikitext parsing
│   │   ├── CategoryParser.php
│   │   ├── CitationsParser.php
│   │   ├── LeadSectionParser.php
│   │   └── TemplateParser.php
│   │
│   └── Fixes/              # Wikitext fixing operations
│       ├── References/     # Reference-related fixes
│       │   ├── DeleteEmptyRefsFixture.php
│       │   ├── ExpandRefsFixture.php
│       │   └── RefWorkerFixture.php
│       │
│       ├── Templates/      # Template-related fixes
│       │   ├── DeleteTemplatesFixture.php
│       │   └── FixTemplatesFixture.php
│       │
│       ├── Media/          # Media-related fixes
│       │   ├── FixImagesFixture.php
│       │   └── RemoveMissingImagesService.php
│       │
│       └── Structure/      # Structural fixes
│           ├── FixCategoriesFixture.php
│           └── FixLanguageLinksFixture.php
│
├── Infrastructure/          # Infrastructure layer (utilities & support)
│   ├── Utils/              # Utility functions
│   │   ├── FileUtils.php
│   │   └── HtmlUtils.php
│   │
│   └── Debug/              # Debug utilities
│       └── PrintHelper.php
│
├── bootstrap.php           # Application bootstrap
```

## Testing

The project includes comprehensive tests:

-   **Integration Tests**: Test API connectivity and real-world scenarios
-   **Unit Tests**: Test individual functions and modules
-   **Commons API Tests**: Validate Wikimedia Commons image existence checks

Tests automatically skip when external APIs are unreachable, making them safe to run offline.

## Output Format

### JSON Output (Default)

```json
{
  "source_lang": "ary",
  "title": "Article Title",
  "revision": 12345,
  "segmentedContent": [...],
  "categories": [...]
}
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Run tests and static analysis
6. Submit a pull request

## License

This project is part of the mdwikicx organization.

## Related Projects

-   [ContentTranslation tool (cx-1)](https://github.com/mdwikicx/cx-1)
-   [HtmltoSegments tool](https://ncc2c.toolforge.org/HtmltoSegments)

## Support

For issues, questions, or contributions, please visit the project repository.
