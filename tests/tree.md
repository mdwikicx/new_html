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
│   │           ├── fixtures/
│   │           │   ├── output/
│   │           │   │   ├── abdominal_pain.wiki
│   │           │   │   ├── obstructive_sleep_apnea.wiki
│   │           │   │   ├── test-1.wiki
│   │           │   │   ├── test-2.wiki
│   │           │   │   ├── Uterine_atony.wiki
│   │           │   │   └── Wernicke–Korsakoff_syndrome.wiki
│   │           │   ├── result/
│   │           │   │   ├── abdominal_pain.wiki
│   │           │   │   ├── obstructive_sleep_apnea.wiki
│   │           │   │   ├── test-1.wiki
│   │           │   │   ├── test-2.wiki
│   │           │   │   ├── Uterine_atony.wiki
│   │           │   │   └── Wernicke–Korsakoff_syndrome.wiki
│   │           │   └── source/
│   │           │       ├── abdominal_pain.wiki
│   │           │       ├── obstructive_sleep_apnea.wiki
│   │           │       ├── test-1.wiki
│   │           │       ├── test-2.wiki
│   │           │       ├── Uterine_atony.wiki
│   │           │       └── Wernicke–Korsakoff_syndrome.wiki
│   │           └── WikitextFixerServiceTest.php
│   └── utilsTest.php
├── bootstrap.php
├── README.md
└── tree.md

```