```
tests/
├── NetworkRealTests/
│   ├── CommonsApiRealTest.php
│   ├── SegApiRealTest.php
│   └── TransformApiRealTest.php
├── new_html/
│   ├── src/
│   │   ├── Application/
│   │   │   ├── Controllers/
│   │   │   │   └── JsonDataControllerTest.php
│   │   │   └── Handlers/
│   │   │       └── WikitextHandlerTest.php
│   │   ├── Domain/
│   │   │   ├── Fixes/
│   │   │   │   ├── Media/
│   │   │   │   │   ├── FixImagesFixtureTest.php
│   │   │   │   │   └── RemoveMissingImagesServiceTest.php
│   │   │   │   ├── References/
│   │   │   │   │   ├── DeleteEmptyRefsFixtureTest.php
│   │   │   │   │   ├── ExpandRefsFixtureTest.php
│   │   │   │   │   └── RefWorkerFixtureTest.php
│   │   │   │   ├── Structure/
│   │   │   │   │   ├── FixCategoriesFixtureTest.php
│   │   │   │   │   └── FixLanguageLinksFixtureTest.php
│   │   │   │   └── Templates/
│   │   │   │       ├── DeleteTemplatesFixtureTest.php
│   │   │   │       └── FixTemplatesFixtureTest.php
│   │   │   └── Parser/
│   │   │       ├── CategoryParserTest.php
│   │   │       ├── CitationsParserTest.php
│   │   │       ├── LeadSectionParserTest.php
│   │   │       ├── ParserTemplatesTest.php
│   │   │       ├── ParserTemplateTest.php
│   │   │       └── TemplateTest.php
│   │   ├── Infrastructure/
│   │   │   ├── Debug/
│   │   │   │   └── PrintHelperTest.php
│   │   │   └── Utils/
│   │   │       ├── FileUtilsTest.php
│   │   │       └── HtmlUtilsTest.php
│   │   └── Services/
│   │       ├── Api/
│   │       │   ├── CommonsImageServiceTest.php
│   │       │   ├── HttpClientServiceTest.php
│   │       │   ├── MdwikiApiServiceTest.php
│   │       │   ├── SegmentApiServiceTest.php
│   │       │   ├── TransformApiServiceTest.php
│   │       │   └── TransformApiTest.php
│   │       ├── Html/
│   │       │   ├── HtmlToSegmentsServiceTest.php
│   │       │   └── WikitextToHtmlServiceTest.php
│   │       ├── Interfaces/
│   │       │   ├── CommonsImageServiceInterfaceTest.php
│   │       │   └── HttpClientInterfaceTest.php
│   │       └── Wikitext/
│   │           ├── data/
│   │           │   ├── result-1.wiki
│   │           │   ├── result-2.wiki
│   │           │   ├── source-1.wiki
│   │           │   └── source-2.wiki
│   │           └── WikitextFixerServiceTest.php
│   └── mainTest.php
├── bootstrap.php
├── README.md
└── tree.md

```