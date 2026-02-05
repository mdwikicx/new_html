# MDWiki NewHtml - Refactored Architecture

## Overview

This project has been refactored to follow modern PHP architecture patterns with clear separation of concerns and PSR-4 autoloading.

## New Directory Structure

```
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
│   │   ├── HttpClient.php
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
│       │   └── RemoveMissingImagesFixture.php
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
└── new_html_src/           # Legacy code (backward compatibility)
```

## Namespace Structure

All new code follows the `MDWiki\NewHtml\{Layer}\{Component}` namespace pattern:

- `MDWiki\NewHtml\Application\Controllers\*` - Application controllers
- `MDWiki\NewHtml\Application\Handlers\*` - Request handlers
- `MDWiki\NewHtml\Services\Api\*` - API services
- `MDWiki\NewHtml\Services\Html\*` - HTML services
- `MDWiki\NewHtml\Services\Wikitext\*` - Wikitext services
- `MDWiki\NewHtml\Domain\Parser\*` - Parsers
- `MDWiki\NewHtml\Domain\Fixes\{Category}\*` - Fix operations
- `MDWiki\NewHtml\Infrastructure\Utils\*` - Utilities
- `MDWiki\NewHtml\Infrastructure\Debug\*` - Debug tools

## Backward Compatibility

All legacy namespaces are still supported through compatibility wrappers:

- `Printn\*` → `MDWiki\NewHtml\Infrastructure\Debug\*`
- `NewHtml\FileHelps\*` → `MDWiki\NewHtml\Infrastructure\Utils\*`
- `HtmlFixes\*` → `MDWiki\NewHtml\Infrastructure\Utils\*`
- `WikiParse\*` → `MDWiki\NewHtml\Domain\Parser\*`
- `Lead\*` → `MDWiki\NewHtml\Domain\Parser\*`
- `Fixes\*` → `MDWiki\NewHtml\Domain\Fixes\*`
- `APIServices\*` → `MDWiki\NewHtml\Services\Api\*`
- `Segments\*` → `MDWiki\NewHtml\Services\Html\*`
- `Html\*` → `MDWiki\NewHtml\Services\Html\*`
- `FixText\*` → `MDWiki\NewHtml\Services\Wikitext\*`
- `NewHtml\JsonData\*` → `MDWiki\NewHtml\Application\Controllers\*`
- `Wikitext\*` → `MDWiki\NewHtml\Application\Handlers\*`
- `PostMdwiki\*` → `MDWiki\NewHtml\Application\Handlers\*`

## Migration Guide

### For New Code

Use the new namespaces:

```php
<?php

use function MDWiki\NewHtml\Infrastructure\Debug\test_print;
use function MDWiki\NewHtml\Domain\Parser\get_categories;
use function MDWiki\NewHtml\Services\Wikitext\fix_wikitext;

// Your code here
```

### For Existing Code

No changes required - legacy namespaces still work:

```php
<?php

use function Printn\test_print;
use function WikiParse\Category\get_categories;
use function FixText\fix_wikitext;

// Your existing code continues to work
```

### Gradual Migration

Update imports gradually as you work on files:

1. Replace old namespace imports with new ones
2. Test thoroughly
3. Commit changes

Example:

```php
// Old
use function Printn\test_print;

// New
use function MDWiki\NewHtml\Infrastructure\Debug\test_print;
```

## Installation

```bash
# Install dependencies
composer install

# Generate optimized autoloader
composer dump-autoload -o
```

## Testing

```bash
# Run all tests
composer test

# Run static analysis
composer phpstan
```

## Benefits of New Structure

### Code Quality
- ✅ Better organized and maintainable
- ✅ Easier to understand for new developers
- ✅ Clear dependency boundaries
- ✅ Improved testability

### Development Experience
- ✅ Faster feature development
- ✅ Easier to locate and modify code
- ✅ Better IDE support (autocomplete, navigation)
- ✅ Reduced cognitive load

### Scalability
- ✅ Easy to add new features
- ✅ Simple to refactor individual components
- ✅ Better support for dependency injection
- ✅ Preparation for future enhancements

### Maintainability
- ✅ Clear responsibility for each component
- ✅ Easier to identify and fix bugs
- ✅ Simplified onboarding for new team members
- ✅ Better documentation structure

## Architecture Layers

### Application Layer
Entry points and request handlers. This is where HTTP requests are processed and responses are generated.

### Service Layer
Business operations and external API integrations. Services orchestrate domain logic and infrastructure.

### Domain Layer
Core business logic including parsers and fix operations. This is where the essential wikitext processing happens.

### Infrastructure Layer
Utilities and support code. File I/O, HTML utilities, debugging tools, etc.

## Contributing

When adding new features:

1. Place code in the appropriate layer (Application/Services/Domain/Infrastructure)
2. Use proper namespacing: `MDWiki\NewHtml\{Layer}\{Component}`
3. Add backward compatibility wrappers if replacing legacy code
4. Update tests
5. Run `composer dump-autoload -o`

## Questions?

For questions about the new architecture, please refer to:
- `REFACTORING_PLAN.md` - Detailed refactoring plan
- This README - Architecture overview
- Code comments - Inline documentation
