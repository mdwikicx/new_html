# Project Refactoring Plan - new_html_src Restructuring

**Project:** MDWiki NewHtml
**Date:** February 4, 2026
**Objective:** Reorganize the `new_html_src` directory to follow modern PHP architecture patterns with better separation of concerns, improved maintainability, and PSR-4 compliance.

---

## 📋 Table of Contents

1. [Current State Analysis](#current-state-analysis)
2. [Proposed Structure](#proposed-structure)
3. [Migration Strategy](#migration-strategy)
4. [Implementation Checklist](#implementation-checklist)
5. [Testing Strategy](#testing-strategy)
6. [Rollback Plan](#rollback-plan)

---

## 🔍 Current State Analysis

### Current Directory Structure
```
src/new_html_src/
├── fix_wikitext.php              # Wikitext fixing orchestrator
├── get_text.php                  # Wikitext retrieval handler
├── json_data.php                 # JSON data management
├── post_mdwiki.php               # Post to MDWiki handler
├── print.php                     # Debug/print utilities
├── bootstrap.php                 # Manual dependency loader
├── api_services/                 # External API integrations
│   ├── commons_api.php
│   ├── mdwiki_api_wikitext.php
│   ├── post.php
│   ├── seg_api.php
│   └── transform_api.php
├── html_services/                # HTML processing services
│   ├── html_to_segments.php
│   └── wikitext_to_html.php
├── utils/                        # Utility functions
│   ├── files_utils.php
│   └── html_utils.php
├── WikiParse/                    # Wikitext parsing components
│   ├── Category.php
│   ├── Citations_reg.php
│   ├── lead_section.php
│   └── ParserTemplates.php
└── WikiTextFixes/                # Wikitext fixing operations
    ├── del_mt_refs.php
    ├── del_temps.php
    ├── expend_refs.php
    ├── fix_cats.php
    ├── fix_images.php
    ├── fix_langs_links.php
    ├── fix_temps.php
    ├── ref_work.php
    └── remove_missing_images.php
```

### Issues with Current Structure
- ❌ **Mixed Responsibilities**: Entry points mixed with business logic
- ❌ **Flat Organization**: Related files not grouped logically
- ❌ **Inconsistent Naming**: Mixed snake_case and naming conventions
- ❌ **Poor Scalability**: Difficult to add new features
- ❌ **Manual Autoloading**: `require.php` manually loads all files
- ❌ **No Clear Boundaries**: Services, domain logic, and utilities mixed

---

## 🎯 Proposed Structure

### New Directory Structure
```
src/
├── Application/                  # Application layer (entry points & controllers)
│   ├── Controllers/
│   │   ├── JsonDataController.php        [json_data.php]
│   │   └── TextProcessorController.php   [new - orchestrates processing]
│   └── Handlers/
│       └── WikitextHandler.php           [get_text.php]
│
├── Services/                     # Service layer (business operations)
│   ├── Api/                      [api_services/]
│   │   ├── CommonsApiService.php         [commons_api.php]
│   │   ├── HttpClientService.php                [post.php]
│   │   ├── MdwikiApiService.php          [mdwiki_api_wikitext.php]
│   │   ├── SegmentApiService.php         [seg_api.php]
│   │   └── TransformApiService.php       [transform_api.php]
│   │
│   ├── Html/                     [html_services/]
│   │   ├── HtmlToSegmentsService.php     [html_to_segments.php]
│   │   └── WikitextToHtmlService.php     [wikitext_to_html.php]
│   │
│   └── Wikitext/
│       ├── WikitextFixerService.php      [fix_wikitext.php]
│       └── WikitextRetrieverService.php  [logic from get_text.php]
│
├── Domain/                       # Domain layer (core business logic)
│   ├── Parser/                   [WikiParse/]
│   │   ├── CategoryParser.php            [Category.php]
│   │   ├── CitationsParser.php           [Citations_reg.php]
│   │   ├── LeadSectionParser.php         [lead_section.php]
│   │   └── TemplateParser.php            [ParserTemplates.php]
│   │
│   └── Fixes/                    [WikiTextFixes/]
│       ├── References/
│       │   ├── DeleteEmptyRefsFixture.php    [del_mt_refs.php]
│       │   ├── ExpandRefsFixture.php         [expend_refs.php]
│       │   └── RefWorkerFixture.php          [ref_work.php]
│       │
│       ├── Templates/
│       │   ├── DeleteTemplatesFixture.php    [del_temps.php]
│       │   └── FixTemplatesFixture.php       [fix_temps.php]
│       │
│       ├── Media/
│       │   ├── FixImagesFixture.php          [fix_images.php]
│       │   └── RemoveMissingImagesService.php [remove_missing_images.php]
│       │
│       └── Structure/
│           ├── FixCategoriesFixture.php      [fix_cats.php]
│           └── FixLanguageLinksFixture.php   [fix_langs_links.php]
│
├── Infrastructure/               # Infrastructure layer (utilities & support)
│   ├── Utils/                    [utils/]
│   │   ├── FileUtils.php                 [files_utils.php]
│   │   ├── HtmlUtils.php                 [html_utils.php]
│   │   └── StringUtils.php               [new - string operations]
│   │
│   ├── Storage/
│   │   ├── JsonStorage.php               [logic from json_data.php]
│   │   └── FileStorage.php               [new - file operations]
│   │
│   └── Debug/
│       └── PrintHelper.php               [print.php]
│
└── bootstrap.php                 [require.php - modernized]
```

### Namespace Mapping
```php
Application\Controllers\*         -> MDWiki\NewHtml\Application\Controllers
Application\Handlers\*            -> MDWiki\NewHtml\Application\Handlers
Services\Api\*                    -> MDWiki\NewHtml\Services\Api
Services\Html\*                   -> MDWiki\NewHtml\Services\Html
Services\Wikitext\*               -> MDWiki\NewHtml\Services\Wikitext
Domain\Parser\*                   -> MDWiki\NewHtml\Domain\Parser
Domain\Fixes\References\*         -> MDWiki\NewHtml\Domain\Fixes\References
Domain\Fixes\Templates\*          -> MDWiki\NewHtml\Domain\Fixes\Templates
Domain\Fixes\Media\*              -> MDWiki\NewHtml\Domain\Fixes\Media
Domain\Fixes\Structure\*          -> MDWiki\NewHtml\Domain\Fixes\Structure
Infrastructure\Utils\*            -> MDWiki\NewHtml\Infrastructure\Utils
Infrastructure\Storage\*          -> MDWiki\NewHtml\Infrastructure\Storage
Infrastructure\Debug\*            -> MDWiki\NewHtml\Infrastructure\Debug
```

---

## 🚀 Migration Strategy

### Phase 1: Preparation (Pre-Migration)
**Goal:** Set up infrastructure for new structure without breaking existing code

- [ ] Create branch `feature/restructure-new-html-src`
- [ ] Back up current codebase
- [ ] Document all current dependencies
- [ ] Review and update all tests to ensure they pass
- [ ] Create new directory structure (empty)
- [ ] Update `composer.json` with new PSR-4 autoload paths

### Phase 2: Infrastructure Layer Migration
**Goal:** Move utilities and support code first (least dependencies)

- [ ] Migrate `print.php` → `Infrastructure/Debug/PrintHelper.php`
- [ ] Migrate `utils/files_utils.php` → `Infrastructure/Utils/FileUtils.php`
- [ ] Migrate `utils/html_utils.php` → `Infrastructure/Utils/HtmlUtils.php`
- [ ] Create `Infrastructure/Utils/StringUtils.php` (consolidate string ops)
- [ ] Extract JSON logic from `json_data.php` → `Infrastructure/Storage/JsonStorage.php`
- [ ] Create `Infrastructure/Storage/FileStorage.php`
- [ ] Update namespaces in migrated files
- [ ] Run tests for Infrastructure layer

### Phase 3: Domain Layer Migration
**Goal:** Move core business logic (parsers and fixes)

#### 3.1: Parser Migration
- [ ] Migrate `WikiParse/Category.php` → `Domain/Parser/CategoryParser.php`
- [ ] Migrate `WikiParse/Citations_reg.php` → `Domain/Parser/CitationsParser.php`
- [ ] Migrate `WikiParse/lead_section.php` → `Domain/Parser/LeadSectionParser.php`
- [ ] Migrate `WikiParse/ParserTemplates.php` → `Domain/Parser/TemplateParser.php`
- [ ] Update namespaces and imports
- [ ] Run parser tests

#### 3.2: Fixes Migration (References)
- [ ] Migrate `WikiTextFixes/del_mt_refs.php` → `Domain/Fixes/References/DeleteEmptyRefsFixture.php`
- [ ] Migrate `WikiTextFixes/expend_refs.php` → `Domain/Fixes/References/ExpandRefsFixture.php`
- [ ] Migrate `WikiTextFixes/ref_work.php` → `Domain/Fixes/References/RefWorkerFixture.php`
- [ ] Update namespaces and imports
- [ ] Run reference tests

#### 3.3: Fixes Migration (Templates)
- [ ] Migrate `WikiTextFixes/del_temps.php` → `Domain/Fixes/Templates/DeleteTemplatesFixture.php`
- [ ] Migrate `WikiTextFixes/fix_temps.php` → `Domain/Fixes/Templates/FixTemplatesFixture.php`
- [ ] Update namespaces and imports
- [ ] Run template tests

#### 3.4: Fixes Migration (Media)
- [ ] Migrate `WikiTextFixes/fix_images.php` → `Domain/Fixes/Media/FixImagesFixture.php`
- [ ] Migrate `WikiTextFixes/remove_missing_images.php` → `Domain/Fixes/Media/RemoveMissingImagesService.php`
- [ ] Update namespaces and imports
- [ ] Run media tests

#### 3.5: Fixes Migration (Structure)
- [ ] Migrate `WikiTextFixes/fix_cats.php` → `Domain/Fixes/Structure/FixCategoriesFixture.php`
- [ ] Migrate `WikiTextFixes/fix_langs_links.php` → `Domain/Fixes/Structure/FixLanguageLinksFixture.php`
- [ ] Update namespaces and imports
- [ ] Run structure tests

### Phase 4: Service Layer Migration
**Goal:** Move business operations and API integrations

#### 4.1: API Services
- [ ] Migrate `api_services/post.php` → `Services/Api/HttpClientService.php`
- [ ] Migrate `api_services/commons_api.php` → `Services/Api/CommonsApiService.php`
- [ ] Migrate `api_services/mdwiki_api_wikitext.php` → `Services/Api/MdwikiApiService.php`
- [ ] Migrate `post_mdwiki.php` → `Services/Api/MdwikiApiService.php`
- [ ] Migrate `api_services/seg_api.php` → `Services/Api/SegmentApiService.php`
- [ ] Migrate `api_services/transform_api.php` → `Services/Api/TransformApiService.php`
- [ ] Update namespaces and imports
- [ ] Run API tests

#### 4.2: HTML Services
- [ ] Migrate `html_services/html_to_segments.php` → `Services/Html/HtmlToSegmentsService.php`
- [ ] Migrate `html_services/wikitext_to_html.php` → `Services/Html/WikitextToHtmlService.php`
- [ ] Update namespaces and imports
- [ ] Run HTML service tests

#### 4.3: Wikitext Services
- [ ] Migrate `fix_wikitext.php` → `Services/Wikitext/WikitextFixerService.php`
- [ ] Extract logic from `get_text.php` → `Services/Wikitext/WikitextRetrieverService.php`
- [ ] Update namespaces and imports
- [ ] Run wikitext service tests

### Phase 5: Application Layer Migration
**Goal:** Move entry points and controllers

- [ ] Migrate `json_data.php` → `Application/Controllers/JsonDataController.php`
- [ ] Migrate `get_text.php` → `Application/Handlers/WikitextHandler.php`
- [ ] Create `Application/Controllers/TextProcessorController.php` (orchestration)
- [ ] Update namespaces and imports
- [ ] Update entry points in `src/` to use new handlers
- [ ] Run integration tests

### Phase 6: Bootstrap & Autoloading
**Goal:** Modernize dependency loading

- [ ] Create `src/bootstrap.php` (replace `require.php`)
- [ ] Implement PSR-4 autoloading for all new namespaces
- [ ] Remove manual `require_once` statements
- [ ] Update `composer.json` autoload configuration
- [ ] Run `composer dump-autoload`
- [ ] Test autoloading with all files

### Phase 7: Entry Point Updates
**Goal:** Update main entry points to use new structure

- [ ] Update `src/main.php` to use new Application layer
- [ ] Update `src/index.php` if needed
- [ ] Update `src/fix.php` references
- [ ] Update `src/check.php` references
- [ ] Update `src/open.php` references
- [ ] Test all entry points

---

## ✅ Implementation Checklist

### Pre-Migration Tasks
- [ ] **Git Setup**
  - [ ] Create feature branch: `git checkout -b feature/restructure-new-html-src`
  - [ ] Tag current state: `git tag -a v1.0.0-pre-refactor -m "State before refactoring"`
  - [ ] Push tag: `git push origin v1.0.0-pre-refactor`

- [ ] **Documentation**
  - [ ] Document all current function signatures
  - [ ] Create dependency map (what calls what)
  - [ ] List all test files and their coverage
  - [ ] Document external dependencies

- [ ] **Validation**
  - [ ] Run all tests: `composer test`
  - [ ] Run static analysis: `composer phpstan`
  - [ ] Ensure all tests pass (baseline)
  - [ ] Document test results

### Phase 1: Directory Structure Creation
- [ ] **Create New Directories**
  ```powershell
  # Application Layer
  New-Item -ItemType Directory -Path "src/Application/Controllers" -Force
  New-Item -ItemType Directory -Path "src/Application/Handlers" -Force

  # Service Layer
  New-Item -ItemType Directory -Path "src/Services/Api" -Force
  New-Item -ItemType Directory -Path "src/Services/Html" -Force
  New-Item -ItemType Directory -Path "src/Services/Wikitext" -Force

  # Domain Layer
  New-Item -ItemType Directory -Path "src/Domain/Parser" -Force
  New-Item -ItemType Directory -Path "src/Domain/Fixes/References" -Force
  New-Item -ItemType Directory -Path "src/Domain/Fixes/Templates" -Force
  New-Item -ItemType Directory -Path "src/Domain/Fixes/Media" -Force
  New-Item -ItemType Directory -Path "src/Domain/Fixes/Structure" -Force

  # Infrastructure Layer
  New-Item -ItemType Directory -Path "src/Infrastructure/Utils" -Force
  New-Item -ItemType Directory -Path "src/Infrastructure/Storage" -Force
  New-Item -ItemType Directory -Path "src/Infrastructure/Debug" -Force
  ```

- [ ] **Update composer.json**
  - [ ] Add new PSR-4 autoload mappings
  - [ ] Keep old mappings temporarily (backward compatibility)
  - [ ] Run `composer dump-autoload`

### Phase 2: Infrastructure Layer (Day 1-2)
- [ ] **Debug Tools**
  - [ ] Migrate `print.php` → `Infrastructure/Debug/PrintHelper.php`
  - [ ] Update namespace: `MDWiki\NewHtml\Infrastructure\Debug`
  - [ ] Update function imports across codebase
  - [ ] Test debug functionality

- [ ] **File Utilities**
  - [ ] Migrate `utils/files_utils.php` → `Infrastructure/Utils/FileUtils.php`
  - [ ] Update namespace: `MDWiki\NewHtml\Infrastructure\Utils`
  - [ ] Convert to class methods or keep as functions
  - [ ] Update all imports
  - [ ] Run file utility tests

- [ ] **HTML Utilities**
  - [ ] Migrate `utils/html_utils.php` → `Infrastructure/Utils/HtmlUtils.php`
  - [ ] Update namespace: `MDWiki\NewHtml\Infrastructure\Utils`
  - [ ] Update all imports
  - [ ] Run HTML utility tests

- [ ] **Storage**
  - [ ] Create `Infrastructure/Storage/JsonStorage.php`
  - [ ] Extract JSON logic from `json_data.php`
  - [ ] Create `Infrastructure/Storage/FileStorage.php`
  - [ ] Implement storage interfaces
  - [ ] Test storage operations

- [ ] **Validation**
  - [ ] Run infrastructure tests
  - [ ] Verify no regressions
  - [ ] Commit changes: `git commit -m "feat: migrate infrastructure layer"`

### Phase 3: Domain Layer - Parsers (Day 3-4)
- [ ] **Category Parser**
  - [ ] Migrate `WikiParse/Category.php` → `Domain/Parser/CategoryParser.php`
  - [ ] Update namespace: `MDWiki\NewHtml\Domain\Parser`
  - [ ] Update class name to `CategoryParser`
  - [ ] Update all imports and usages
  - [ ] Run `tests/WikiParse/CategoryTest.php`

- [ ] **Citations Parser**
  - [ ] Migrate `WikiParse/Citations_reg.php` → `Domain/Parser/CitationsParser.php`
  - [ ] Update namespace and class name
  - [ ] Update all imports
  - [ ] Run `tests/WikiParse/CitationsRegTest.php`

- [ ] **Lead Section Parser**
  - [ ] Migrate `WikiParse/lead_section.php` → `Domain/Parser/LeadSectionParser.php`
  - [ ] Update namespace and naming
  - [ ] Update all imports
  - [ ] Run `tests/WikiParse/LeadSectionTest.php`

- [ ] **Template Parser**
  - [ ] Migrate `WikiParse/ParserTemplates.php` → `Domain/Parser/TemplateParser.php`
  - [ ] Update namespace
  - [ ] Update all imports
  - [ ] Run `tests/WikiParse/ParserTemplatesTest.php`

- [ ] **Validation**
  - [ ] Run all parser tests
  - [ ] Verify test coverage maintained
  - [ ] Commit: `git commit -m "feat: migrate domain parsers"`

### Phase 4: Domain Layer - Fixes (Day 5-7)
- [ ] **Reference Fixes**
  - [ ] Migrate `del_mt_refs.php` → `Domain/Fixes/References/DeleteEmptyRefsFixture.php`
  - [ ] Migrate `expend_refs.php` → `Domain/Fixes/References/ExpandRefsFixture.php`
  - [ ] Migrate `ref_work.php` → `Domain/Fixes/References/RefWorkerFixture.php`
  - [ ] Update namespaces: `MDWiki\NewHtml\Domain\Fixes\References`
  - [ ] Update all imports
  - [ ] Run reference tests

- [ ] **Template Fixes**
  - [ ] Migrate `del_temps.php` → `Domain/Fixes/Templates/DeleteTemplatesFixture.php`
  - [ ] Migrate `fix_temps.php` → `Domain/Fixes/Templates/FixTemplatesFixture.php`
  - [ ] Update namespaces
  - [ ] Update all imports
  - [ ] Run template tests

- [ ] **Media Fixes**
  - [ ] Migrate `fix_images.php` → `Domain/Fixes/Media/FixImagesFixture.php`
  - [ ] Migrate `remove_missing_images.php` → `Domain/Fixes/Media/RemoveMissingImagesService.php`
  - [ ] Update namespaces
  - [ ] Update all imports
  - [ ] Run `tests/RemoveMissingImagesTest.php`

- [ ] **Structure Fixes**
  - [ ] Migrate `fix_cats.php` → `Domain/Fixes/Structure/FixCategoriesFixture.php`
  - [ ] Migrate `fix_langs_links.php` → `Domain/Fixes/Structure/FixLanguageLinksFixture.php`
  - [ ] Update namespaces
  - [ ] Update all imports
  - [ ] Run structure tests

- [ ] **Validation**
  - [ ] Run all WikiTextFixes tests
  - [ ] Verify all tests pass
  - [ ] Commit: `git commit -m "feat: migrate domain fixes"`

### Phase 5: Service Layer - API (Day 8-9)
- [ ] **HTTP Client**
  - [ ] Migrate `api_services/post.php` → `Services/Api/HttpClientService.php`
  - [ ] Update namespace: `MDWiki\NewHtml\Services\Api`
  - [ ] Refactor to class-based approach
  - [ ] Update all imports
  - [ ] Run `tests/APIServices/HttpClientServiceTest.php`

- [ ] **Commons API**
  - [ ] Migrate `commons_api.php` → `Services/Api/CommonsApiService.php`
  - [ ] Update namespace and dependencies
  - [ ] Update all imports
  - [ ] Run `tests/APIServices/CommonsApiTest.php`

- [ ] **MDWiki API**
  - [ ] Migrate `mdwiki_api_wikitext.php` → `Services/Api/MdwikiApiService.php`
  - [ ] Migrate `post_mdwiki.php` → `Services/Api/MdwikiApiService.php`
  - [ ] Update namespace and dependencies
  - [ ] Update all imports
  - [ ] Run `tests/APIServices/MdwikiApiTest.php`

- [ ] **Segment API**
  - [ ] Migrate `seg_api.php` → `Services/Api/SegmentApiService.php`
  - [ ] Update namespace and dependencies
  - [ ] Update all imports
  - [ ] Run `tests/APIServices/SegApiTest.php`

- [ ] **Transform API**
  - [ ] Migrate `transform_api.php` → `Services/Api/TransformApiService.php`
  - [ ] Update namespace and dependencies
  - [ ] Update all imports
  - [ ] Run `tests/APIServices/TransformApiTest.php`

- [ ] **Validation**
  - [ ] Run all API service tests
  - [ ] Verify external API calls work
  - [ ] Commit: `git commit -m "feat: migrate API services"`

### Phase 6: Service Layer - HTML & Wikitext (Day 10)
- [ ] **HTML Services**
  - [ ] Migrate `html_to_segments.php` → `Services/Html/HtmlToSegmentsService.php`
  - [ ] Migrate `wikitext_to_html.php` → `Services/Html/WikitextToHtmlService.php`
  - [ ] Update namespaces: `MDWiki\NewHtml\Services\Html`
  - [ ] Update all imports
  - [ ] Run HTML service tests

- [ ] **Wikitext Services**
  - [ ] Migrate `fix_wikitext.php` → `Services/Wikitext/WikitextFixerService.php`
  - [ ] Create `Services/Wikitext/WikitextRetrieverService.php` (extract from get_text.php)
  - [ ] Update namespaces: `MDWiki\NewHtml\Services\Wikitext`
  - [ ] Update all imports
  - [ ] Run wikitext service tests

- [ ] **Validation**
  - [ ] Run all service tests
  - [ ] Commit: `git commit -m "feat: migrate HTML and wikitext services"`

### Phase 7: Application Layer (Day 11-12)
- [ ] **Controllers**
  - [ ] Migrate `json_data.php` → `Application/Controllers/JsonDataController.php`
  - [ ] Create `Application/Controllers/TextProcessorController.php`
  - [ ] Update namespaces: `MDWiki\NewHtml\Application\Controllers`
  - [ ] Update all imports
  - [ ] Run `tests/EntryPoints/JsonDataTest.php`

- [ ] **Handlers**
  - [ ] Migrate `get_text.php` → `Application/Handlers/WikitextHandler.php`
  - [ ] Update namespaces: `MDWiki\NewHtml\Application\Handlers`
  - [ ] Update all imports
  - [ ] Run handler tests

- [ ] **Validation**
  - [ ] Run application layer tests
  - [ ] Test integration with entry points
  - [ ] Commit: `git commit -m "feat: migrate application layer"`

### Phase 8: Bootstrap & Autoloading (Day 13)
- [ ] **Create Bootstrap**
  - [ ] Create `src/bootstrap.php`
  - [ ] Implement environment setup
  - [ ] Load Composer autoloader
  - [ ] Initialize application constants
  - [ ] Remove manual requires

- [ ] **Update Composer**
  - [ ] Finalize PSR-4 autoload mappings in `composer.json`
  - [ ] Remove old namespace mappings
  - [ ] Update `files` array if needed
  - [ ] Run `composer dump-autoload -o`

- [ ] **Update Entry Points**
  - [ ] Update `src/main.php` to use `bootstrap.php`
  - [ ] Update `src/index.php` to use new structure
  - [ ] Update `src/fix.php`
  - [ ] Update `src/check.php`
  - [ ] Update `src/open.php`

- [ ] **Remove Old Files**
  - [ ] Mark old `new_html_src/require.php` for deletion
  - [ ] Mark old directories for deletion (after validation)

- [ ] **Validation**
  - [ ] Test all entry points manually
  - [ ] Run full test suite
  - [ ] Verify application works end-to-end
  - [ ] Commit: `git commit -m "feat: implement bootstrap and finalize autoloading"`

### Phase 9: Testing & Validation (Day 14-15)
- [ ] **Unit Tests**
  - [ ] Run all unit tests: `composer test`
  - [ ] Verify 100% test pass rate
  - [ ] Check test coverage hasn't decreased
  - [ ] Fix any failing tests

- [ ] **Integration Tests**
  - [ ] Test `index.php` routing
  - [ ] Test `main.php` API endpoint
  - [ ] Test all entry points with various parameters

- [ ] **Static Analysis**
  - [ ] Run PHPStan: `composer phpstan`
  - [ ] Fix any type errors
  - [ ] Fix any undefined references
  - [ ] Ensure level 5+ compliance

- [ ] **Manual Testing**
  - [ ] Test wikitext retrieval
  - [ ] Test wikitext fixing
  - [ ] Test HTML conversion
  - [ ] Test API integrations
  - [ ] Test JSON data persistence

### Phase 10: Cleanup & Documentation (Day 16)
- [ ] **Code Cleanup**
  - [ ] Remove old `new_html_src/` files (after final validation)
  - [ ] Remove deprecated functions
  - [ ] Clean up unused imports
  - [ ] Format code with PSR-12 standards

- [ ] **Documentation Updates**
  - [ ] Update `README.md` with new structure
  - [ ] Document new namespace conventions
  - [ ] Update API documentation
  - [ ] Create architecture diagram
  - [ ] Document migration process

- [ ] **Composer Finalization**
  - [ ] Optimize autoloader: `composer dump-autoload -o --classmap-authoritative`
  - [ ] Update package description
  - [ ] Verify all dependencies

### Phase 11: Deployment Preparation (Day 17)
- [ ] **Final Validation**
  - [ ] Run complete test suite
  - [ ] Run static analysis
  - [ ] Test on clean environment
  - [ ] Performance testing

- [ ] **Version Control**
  - [ ] Review all commits
  - [ ] Squash if necessary
  - [ ] Create pull request
  - [ ] Tag release: `git tag -a v2.0.0 -m "Major refactoring: new architecture"`

- [ ] **Merge & Deploy**
  - [ ] Get code review approval
  - [ ] Merge to main branch
  - [ ] Deploy to staging environment
  - [ ] Monitor for issues
  - [ ] Deploy to production

---

## 🧪 Testing Strategy

### Test Categories
1. **Unit Tests**: Test individual classes and functions
2. **Integration Tests**: Test component interactions
3. **End-to-End Tests**: Test complete workflows
4. **Regression Tests**: Ensure no existing functionality broken

### Test Execution Plan
```bash
# Run all tests
composer test

# Run specific test suite
vendor/bin/phpunit tests/APIServices/ --testdox

# Run with coverage
vendor/bin/phpunit tests --coverage-html coverage/

# Run static analysis
composer phpstan
```

### Test Validation Checklist
- [ ] All existing tests still pass
- [ ] New tests added for new classes
- [ ] Code coverage maintained (target: >80%)
- [ ] No PHPStan errors
- [ ] No deprecated function warnings

---

## 🔄 Rollback Plan

### If Issues Found During Migration
1. **Stop immediately** - Don't proceed to next phase
2. **Document the issue** - Note what failed and why
3. **Revert to last working commit**: `git reset --hard <commit-hash>`
4. **Analyze the problem** - Determine root cause
5. **Fix or adjust plan** - Update strategy if needed
6. **Resume from last good state**

### If Issues Found After Completion
1. **Assess severity**:
   - Critical (data loss, application down) → Immediate rollback
   - High (major features broken) → Rollback within 1 hour
   - Medium (minor features broken) → Fix forward or rollback
   - Low (cosmetic issues) → Fix forward

2. **Rollback procedure**:
   ```bash
   # Revert to pre-refactor tag
   git checkout v1.0.0-pre-refactor

   # Or create hotfix branch
   git checkout -b hotfix/revert-refactoring
   git revert <refactoring-merge-commit>
   ```

3. **Communication**:
   - Notify team of rollback
   - Document issues encountered
   - Create tickets for issues
   - Schedule post-mortem

---

## 📊 Success Criteria

### Must Have (Required)
- ✅ All existing tests pass
- ✅ No functionality regression
- ✅ PHPStan analysis passes
- ✅ All entry points functional
- ✅ Proper PSR-4 autoloading

### Should Have (Strongly Desired)
- ✅ Improved code organization
- ✅ Clear separation of concerns
- ✅ Updated documentation
- ✅ Performance maintained or improved
- ✅ Clean git history

### Nice to Have (Optional)
- ✅ Increased test coverage
- ✅ Reduced code duplication
- ✅ Improved naming conventions
- ✅ Architecture documentation
- ✅ Performance improvements

---

## 📅 Timeline Estimate

**Total Duration**: 17 days (3.5 weeks)

| Phase | Duration | Days |
|-------|----------|------|
| Pre-Migration | 1 day | Day 1 |
| Infrastructure Layer | 2 days | Days 2-3 |
| Domain Layer - Parsers | 2 days | Days 4-5 |
| Domain Layer - Fixes | 3 days | Days 6-8 |
| Service Layer - API | 2 days | Days 9-10 |
| Service Layer - HTML/Wikitext | 1 day | Day 11 |
| Application Layer | 2 days | Days 12-13 |
| Bootstrap & Autoloading | 1 day | Day 14 |
| Testing & Validation | 2 days | Days 15-16 |
| Cleanup & Documentation | 1 day | Day 17 |

**Note**: Timeline assumes one developer working full-time. Adjust as needed for your team size and availability.

---

## 🎯 Post-Migration Benefits

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

---

## 📞 Support & Questions

If you encounter issues during migration:
1. Check this plan for guidance
2. Review git history for similar changes
3. Consult team members
4. Document any deviations from plan
5. Update this plan with lessons learned

---

**Document Version**: 1.0
**Last Updated**: February 4, 2026
**Maintained By**: Development Team
**Status**: Ready for Implementation
