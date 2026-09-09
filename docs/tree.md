src/
├── Application/
│   ├── Controllers/
│   │   └── JsonDataController.php
│   └── Handlers/
│       └── WikitextHandler.php
│
├── Domain/
│   ├── Fixes/
│   │   ├── Media/
│   │   │   ├── FixImagesFixture.php
│   │   │   └── RemoveMissingImagesService.php
│   │   ├── References/
│   │   │   ├── DeleteEmptyRefsFixture.php
│   │   │   ├── ExpandRefsFixture.php
│   │   │   └── RefWorkerFixture.php
│   │   ├── Structure/
│   │   │   ├── FixCategoriesFixture.php
│   │   │   └── FixLanguageLinksFixture.php
│   │   └── Templates/
│   │       ├── DeleteTemplatesFixture.php
│   │       └── FixTemplatesFixture.php
│   └── Parser/
│       ├── CategoryParser.php
│       ├── CitationsParser.php
│       ├── LeadSectionParser.php
│       ├── ParserTemplate.php
│       ├── ParserTemplates.php
│       └── Template.php
│
├── Infrastructure/
│   ├── Debug/
│   │   └── PrintHelper.php
│   └── Utils/
│       ├── FileUtils.php
│       └── HtmlUtils.php
│
├── Services/
│   ├── Api/
│   │   ├── CommonsImageService.php
│   │   ├── HttpClientService.php
│   │   ├── MdwikiApiService.php
│   │   ├── SegmentApiService.php
│   │   └── TransformApiService.php
│   ├── Html/
│   │   ├── HtmlToSegmentsService.php
│   │   └── WikitextToHtmlService.php
│   ├── Interfaces/
│   │   ├── CommonsImageServiceInterface.php
│   │   └── HttpClientInterface.php
│   └── Wikitext/
│       └── WikitextFixerService.php
│
└── bootstrap.php
    ├── bootstrap.php
    ├── fix.php
    ├── index.php
    ├── open.php
    ├── revisions_api.php
    └── utils.php

```
