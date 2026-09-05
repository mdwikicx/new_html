#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Generates empty PHPUnit placeholder test files for classes in src/
that do not yet have a corresponding test file under tests/src/.

Usage:
    python3 generate_placeholder_tests.py

Adjust ROOT_DIR if this script is not run from the project root.
"""

import os
from pathlib import Path

# Project root (adjust if needed)
ROOT_DIR = Path(__file__).resolve().parent
SRC_DIR = ROOT_DIR / "src"
TESTS_SRC_DIR = ROOT_DIR / "tests" / "src"

# ---------------------------------------------------------------------------
# Root namespace prefix for classes under src/, per composer.json PSR-4 map.
# Leave empty if there is no prefix.
# ---------------------------------------------------------------------------
SRC_NAMESPACE_ROOT = ""  # e.g. "App\\" if defined in composer.json

# Relative paths (from src/) of classes that should have a test file
SOURCE_FILES = [
    "Application/Controllers/JsonDataController.php",
    "Application/Handlers/WikitextHandler.php",
    "Domain/Fixes/Media/FixImagesFixture.php",
    "Domain/Fixes/Media/RemoveMissingImagesService.php",
    "Domain/Fixes/References/DeleteEmptyRefsFixture.php",
    "Domain/Fixes/References/ExpandRefsFixture.php",
    "Domain/Fixes/References/RefWorkerFixture.php",
    "Domain/Fixes/Structure/FixCategoriesFixture.php",
    "Domain/Fixes/Structure/FixLanguageLinksFixture.php",
    "Domain/Fixes/Templates/DeleteTemplatesFixture.php",
    "Domain/Fixes/Templates/FixTemplatesFixture.php",
    "Domain/Parser/CategoryParser.php",
    "Domain/Parser/CitationsParser.php",
    "Domain/Parser/LeadSectionParser.php",
    "Domain/Parser/ParserTemplate.php",
    "Domain/Parser/ParserTemplates.php",
    "Domain/Parser/Template.php",
    "Infrastructure/Debug/PrintHelper.php",
    "Infrastructure/Utils/FileUtils.php",
    "Infrastructure/Utils/HtmlUtils.php",
    "Services/Api/CommonsImageService.php",
    "Services/Api/HttpClientService.php",
    "Services/Api/MdwikiApiService.php",
    "Services/Api/SegmentApiService.php",
    "Services/Api/TransformApiService.php",
    "Services/Html/HtmlToSegmentsService.php",
    "Services/Html/WikitextToHtmlService.php",
    "Services/Interfaces/CommonsImageServiceInterface.php",
    "Services/Interfaces/HttpClientInterface.php",
    "Services/Wikitext/WikitextFixerService.php",
]

# Test files that already exist (after the git mv reorganization) — never overwritten
EXISTING_TEST_FILES = {
    "Services/Api/CommonsImageServiceTest.php",
    "Services/Api/HttpClientServiceTest.php",
    "Services/Api/MdwikiApiServiceTest.php",
    "Services/Api/SegmentApiServiceTest.php",
    "Application/Controllers/JsonDataControllerTest.php",
    "Application/Handlers/WikitextHandlerTest.php",
    "Domain/Fixes/Media/RemoveMissingImagesServiceTest.php",
    "Infrastructure/Utils/FileUtilsTest.php",
    "Infrastructure/Utils/HtmlUtilsTest.php",
    "Domain/Parser/CategoryParserTest.php",
    "Domain/Parser/CitationsParserTest.php",
    "Domain/Parser/LeadSectionParserTest.php",
    "Domain/Parser/ParserTemplatesTest.php",
    "Domain/Parser/ParserTemplateTest.php",
    "Domain/Parser/TemplateTest.php",
    "Domain/Fixes/References/DeleteEmptyRefsFixtureTest.php",
    "Domain/Fixes/References/ExpandRefsFixtureTest.php",
    "Domain/Fixes/References/RefWorkerFixtureTest.php",
    "Domain/Fixes/Structure/FixCategoriesFixtureTest.php",
    "Domain/Fixes/Structure/FixLanguageLinksFixtureTest.php",
    "Domain/Fixes/Templates/DeleteTemplatesFixtureTest.php",
    "Domain/Fixes/Templates/FixTemplatesFixtureTest.php",
    "Domain/Fixes/Media/FixImagesFixtureTest.php",
}


def to_relative_test_path(src_relative_path: str) -> str:
    """Converts 'Domain/Parser/Template.php' to 'Domain/Parser/TemplateTest.php'."""
    p = Path(src_relative_path)
    class_name = p.stem  # file name without .php
    test_name = f"{class_name}Test.php"
    return str(p.parent / test_name)


def to_namespace(relative_dir: str) -> str:
    """Converts a relative directory path into a PHP namespace (Tests\\...)."""
    parts = [p for p in relative_dir.split(os.sep) if p not in ("", ".")]
    ns = "Tests\\" + "\\".join(parts) if parts else "Tests"
    return ns


def to_covered_fqcn(relative_dir: str, class_name: str) -> str:
    """Fully-qualified class name of the class under test (for @covers only)."""
    parts = [p for p in relative_dir.split(os.sep) if p not in ("", ".")]
    prefix = SRC_NAMESPACE_ROOT.strip("\\")
    ns_parts = ([prefix] if prefix else []) + parts
    return "\\".join(ns_parts + [class_name]) if ns_parts else class_name


TEMPLATE = '''<?php

namespace {namespace};

use PHPUnit\\Framework\\TestCase;

/**
 * @covers \\{covered_fqcn}
 *
 * TODO: write tests for {class_name} (src/{src_relative_path}).
 */
class {test_class_name} extends TestCase
{{
}}
'''


def main() -> None:
    created = []
    skipped = []

    for src_relative_path in SOURCE_FILES:
        test_relative_path = to_relative_test_path(src_relative_path)

        if test_relative_path in EXISTING_TEST_FILES:
            skipped.append(test_relative_path)
            continue

        test_full_path = TESTS_SRC_DIR / test_relative_path

        if test_full_path.exists():
            skipped.append(test_relative_path)
            continue

        class_name = Path(src_relative_path).stem
        relative_dir = str(Path(test_relative_path).parent)
        namespace = to_namespace(relative_dir)
        covered_fqcn = to_covered_fqcn(str(Path(src_relative_path).parent), class_name)
        test_class_name = Path(test_relative_path).stem

        content = TEMPLATE.format(
            namespace=namespace,
            covered_fqcn=covered_fqcn,
            class_name=class_name,
            src_relative_path=src_relative_path,
            test_class_name=test_class_name,
        )

        test_full_path.parent.mkdir(parents=True, exist_ok=True)
        test_full_path.write_text(content, encoding="utf-8")
        created.append(str(test_full_path.relative_to(ROOT_DIR)))

    print("=" * 60)
    print(f"Created {len(created)} new test file(s):")
    for f in created:
        print(f"  [+] {f}")

    print("-" * 60)
    print(f"Skipped {len(skipped)} file(s) (already exist):")
    for f in skipped:
        print(f"  [=] {f}")
    print("=" * 60)


if __name__ == "__main__":
    main()
