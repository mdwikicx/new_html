# MDWiki NewHtml - Architecture

## Overview

This project follows modern PHP 8.2+ architecture patterns with strict typing, PSR-4 autoloading, and clear separation of concerns.

## Technical Standards

- **PHP 8.2+** with `declare(strict_types=1)` in all files
- **PSR-4 autoloading** via Composer for classes and interfaces
- **Composer `files` autoloading** for namespaced standalone functions
- **PHPUnit 9.6** for testing
- **PHPStan** for static analysis

## Directory Structure

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
│   │   ├── CommonsImageService.php
│   │   ├── HttpClientService.php
│   │   ├── MdwikiApiService.php
│   │   ├── SegmentApiService.php
│   │   └── TransformApiService.php
│   │
│   ├── Html/               # HTML processing services
│   │   ├── HtmlToSegmentsService.php
│   │   └── WikitextToHtmlService.php
│   │
│   ├── Interfaces/         # Service contracts
│   │   ├── CommonsImageServiceInterface.php
│   │   └── HttpClientInterface.php
│   │
│   └── Wikitext/           # Wikitext processing services
│       └── WikitextFixerService.php
│
├── Domain/                  # Domain layer (core business logic)
│   ├── Parser/             # Wikitext parsing
│   │   ├── CategoryParser.php
│   │   ├── CitationsParser.php
│   │   ├── LeadSectionParser.php
│   │   ├── ParserTemplate.php
│   │   ├── ParserTemplates.php
│   │   └── Template.php
│   │
│   └── Fixes/              # Wikitext fixing operations
│       ├── References/
│       │   ├── DeleteEmptyRefsFixture.php
│       │   ├── ExpandRefsFixture.php
│       │   └── RefWorkerFixture.php
│       │
│       ├── Templates/
│       │   ├── DeleteTemplatesFixture.php
│       │   └── FixTemplatesFixture.php
│       │
│       ├── Media/
│       │   ├── FixImagesFixture.php
│       │   └── RemoveMissingImagesService.php
│       │
│       └── Structure/
│           ├── FixCategoriesFixture.php
│           └── FixLanguageLinksFixture.php
│
├── Infrastructure/          # Infrastructure layer (utilities & support)
│   ├── Utils/
│   │   ├── FileUtils.php
│   │   └── HtmlUtils.php
│   │
│   └── Debug/
│       └── PrintHelper.php
│
└── bootstrap.php           # Application constants

new_html/                   # HTTP entry points
├── index.php              # Router
├── main.php               # Main API endpoint
├── bootstrap.php          # Application initialization
├── utils.php              # CORS, content-type, error helpers
├── fix.php                # Wikitext fix test interface
└── require.php            # Backward compatibility (now uses autoloader)
```

## Autoloading

Configured in `composer.json`:

- **PSR-4**: `MDWiki\NewHtml\` → `src/` (classes and interfaces)
- **Files**: Standalone namespaced functions loaded explicitly
- **Test PSR-4**: `FixRefs\Tests\` → `tests/`

## Namespace Convention

All code uses `MDWiki\NewHtml\{Layer}\{Component}` namespace pattern:

- `MDWiki\NewHtml\Application\Controllers\*`
- `MDWiki\NewHtml\Application\Handlers\*`
- `MDWiki\NewHtml\Services\Api\*`
- `MDWiki\NewHtml\Services\Html\*`
- `MDWiki\NewHtml\Services\Interfaces\*`
- `MDWiki\NewHtml\Services\Wikitext\*`
- `MDWiki\NewHtml\Domain\Parser\*`
- `MDWiki\NewHtml\Domain\Fixes\{Category}\*`
- `MDWiki\NewHtml\Infrastructure\Utils\*`
- `MDWiki\NewHtml\Infrastructure\Debug\*`

Entry point utilities use `MDWiki\NewHtmlMain\Utils` namespace.

## Processing Pipeline

1. Fetch wikitext from mdwiki.org REST API
2. Apply wikitext fixes via `fix_wikitext()`
3. Transform to HTML using Wikipedia's REST API
4. Generate segmented content via HtmltoSegments tool
5. Return JSON with segments and categories

## Architecture Layers

### Application Layer
Entry points and request handlers. Processes HTTP requests and generates responses.

### Service Layer
Business operations and external API integrations. Services orchestrate domain logic and infrastructure. Interfaces define contracts for dependency injection and testability.

### Domain Layer
Core business logic including parsers and fix operations. Pure wikitext processing with no external dependencies.

### Infrastructure Layer
Utilities and support code: file I/O, HTML utilities, debugging tools.

## Testing

```bash
# Run all tests (excludes network tests)
composer test

# Run specific test file
vendor/bin/phpunit tests/WikiParse/CategoryTest.php

# Run network tests (requires internet)
RUN_NETWORK_TESTS=true vendor/bin/phpunit tests/NetworkRealTests --testsuite network

# Run static analysis
composer phpstan
```

## Installation

```bash
composer install
composer dump-autoload -o
```
