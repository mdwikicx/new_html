
# MediaWiki Content Transformation & API Services

A comprehensive PHP library for processing MediaWiki articles and generating segmented content for the [ContentTranslation tool](https://github.com/mdwikicx/cx-1).

## Overview

This library processes articles from `mdwiki.org` and transforms them into various formats, including segmented content in JSON format. It provides a suite of tools for fetching, parsing, transforming, and fixing MediaWiki content.

## Features

- 🔄 **Content Transformation**: Convert MediaWiki wikitext to HTML and segmented content
- 🖼️ **Image Processing**: Fix image references and validate Wikimedia Commons images
- 🏷️ **Category Management**: Parse and manage article categories
- 📝 **Wikitext Fixes**: Clean up and normalize wikitext content
- 🌐 **Language Links**: Handle interwiki language links
- 📚 **Template Processing**: Parse and manipulate MediaWiki templates
- 🔍 **Citation Handling**: Process and expand citation references
- 🧪 **Comprehensive Testing**: PHPUnit test suite with API integration tests

## Requirements

- PHP >= 7.4
- Composer

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

- `wikitext` - Output raw wikitext
- `html` - Output HTML
- `seg` - Output segmented content
- Default - Output JSON with segmented content

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

- **Commons API** (`commons_api.php`): Check if images exist on Wikimedia Commons
- **MDWiki API** (`mdwiki_api_wikitext.php`): Fetch wikitext from mdwiki.org
- **Transform API** (`transform_api.php`): Transform wikitext to HTML
- **Segmentation API** (`seg_api.php`): Generate segmented content

### Wikitext Fixes

Available wikitext transformation modules:

- `del_mt_refs.php` - Delete machine translation references
- `del_temps.php` - Delete specific templates
- `expend_refs.php` - Expand reference tags
- `fix_cats.php` - Fix category formatting
- `fix_images.php` - Fix image references
- `fix_langs_links.php` - Fix language links
- `fix_temps.php` - Fix template formatting
- `ref_work.php` - Process reference sections

## Development

### Running Tests

Run the complete test suite:
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

### Static Analysis

Run PHPStan for static code analysis:
```bash
composer phpstan
```

### Project Structure

```
new_html/
├── new_html_src/          # Source code
│   ├── api_services/      # API integration modules
│   ├── html_services/     # HTML processing services
│   ├── utils/             # Utility functions
│   ├── WikiParse/         # MediaWiki parsing tools
│   └── WikiTextFixes/     # Wikitext transformation modules
├── tests/                 # Test suite
│   ├── unit/              # Unit tests
│   └── bootstrap.php      # Test bootstrap
├── composer.json          # Dependency management
├── phpunit.xml            # PHPUnit configuration
└── README.md              # This file
```

## Testing

The project includes comprehensive tests:

- **Integration Tests**: Test API connectivity and real-world scenarios
- **Unit Tests**: Test individual functions and modules
- **Commons API Tests**: Validate Wikimedia Commons image existence checks

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

- [ContentTranslation tool (cx-1)](https://github.com/mdwikicx/cx-1)
- [HtmltoSegments tool](https://ncc2c.toolforge.org/HtmltoSegments)

## Support

For issues, questions, or contributions, please visit the project repository.
