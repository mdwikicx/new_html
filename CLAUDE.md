# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

MediaWiki Content Transformation & API Services - a PHP library for processing MediaWiki articles from mdwiki.org and transforming them into segmented content for the ContentTranslation tool.

## Commands

### Testing
```bash
# Run all tests (excludes network tests by default)
composer test

# Run PHPUnit directly (excludes network tests)
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/WikiParse/CategoryTest.php

# Run specific test method
vendor/bin/phpunit --filter testGetCategoriesWithMultipleCategories tests/WikiParse/CategoryTest.php

# Run tests excluding API tests (faster, no network)
vendor/bin/phpunit --exclude-group api

# Run network tests only (requires internet connection)
RUN_NETWORK_TESTS=true vendor/bin/phpunit tests/NetworkRealTests --testsuite network
```

**Network Tests (`tests/NetworkRealTests/`)**
- Located in `tests/NetworkRealTests/` directory
- Test real API connections to mdwiki.org, Wikimedia Commons, Wikipedia, and segmentation services
- Excluded from default test suite (use `--testsuite network` to run)
- Skipped by default (require `RUN_NETWORK_TESTS=true` environment variable)
- Useful for validating actual API integrations in production-like scenarios
- Tests automatically skip if external APIs are unreachable

### Static Analysis
```bash
composer phpstan
# or
vendor/bin/phpstan analyse --memory-limit=256M
```

### Dependencies
```bash
composer install
composer dump-autoload -o
```

## Architecture

The codebase follows a layered architecture with namespaces under `MDWiki\NewHtml\`:

### Directory Structure
- `new_html/` - Entry points (index.php, check.php, fix.php, etc.)
- `src/Application/` - Controllers and request handlers
- `src/Services/` - External API integrations and business operations
- `src/Domain/` - Core parsers and wikitext fixes
- `src/Infrastructure/` - Utilities (file I/O, HTML utils, debug)
- `tests/` - PHPUnit test suites organized by module

### Namespace Convention
All new code uses `MDWiki\NewHtml\{Layer}\{Component}` namespace pattern:
- `MDWiki\NewHtml\Application\Controllers\*`
- `MDWiki\NewHtml\Services\Api\*` - External API services
- `MDWiki\NewHtml\Services\Html\*` - HTML processing
- `MDWiki\NewHtml\Services\Wikitext\*` - Wikitext fixes
- `MDWiki\NewHtml\Domain\Parser\*` - Wikitext parsers
- `MDWiki\NewHtml\Domain\Fixes\*` - Fix operations
- `MDWiki\NewHtml\Infrastructure\Utils\*` - Utilities

Legacy namespaces are supported via backward compatibility wrappers.

### Processing Pipeline
1. Fetch wikitext from mdwiki.org REST API
2. Apply wikitext fixes via `fix_wikitext()`
3. Transform to HTML using Wikipedia's REST API
4. Generate segmented content via HtmltoSegments tool
5. Return JSON with segments and categories

## Configuration

- `.env` - Environment configuration (copy from `.env.example`)
- Key settings: `APP_DEBUG`, `REVISIONS_DIR`
- Output format controlled by `printetxt` parameter: `wikitext`, `html`, `seg`, or JSON (default)

## Requirements

- PHP >= 8.2
- Composer
