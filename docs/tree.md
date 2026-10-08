```
src/
├── app/
│   ├── Controllers/
│   │   ├── AppRouterController.php
│   │   ├── JsonDataController.php
│   │   ├── OpenController.php
│   │   └── RevisionsApiController.php
│   ├── Domain/
│   │   ├── Fixes/
│   │   │   ├── Media/
│   │   │   │   ├── FixImagesFixture.php
│   │   │   │   └── RemoveMissingImagesService.php
│   │   │   ├── References/
│   │   │   │   ├── DeleteEmptyRefsFixture.php
│   │   │   │   ├── ExpandRefsFixture.php
│   │   │   │   └── RefWorkerFixture.php
│   │   │   ├── Structure/
│   │   │   │   ├── FixCategoriesFixture.php
│   │   │   │   └── FixLanguageLinksFixture.php
│   │   │   └── Templates/
│   │   │       ├── DeleteTemplatesFixture.php
│   │   │       └── FixTemplatesFixture.php
│   │   └── Parser/
│   │       ├── CategoryParser.php
│   │       ├── CitationsParser.php
│   │       ├── LeadSectionParser.php
│   │       ├── ParserTemplate.php
│   │       ├── ParserTemplates.php
│   │       └── Template.php
│   ├── DTO/
│   │   ├── PageRequest.php
│   │   └── PageResult.php
│   ├── Handlers/
│   │   └── WikitextHandler.php
│   ├── Infrastructure/
│   │   └── Utils/
│   │       ├── FileUtils.php
│   │       └── HtmlUtils.php
│   ├── Services/
│   │   ├── Api/
│   │   │   ├── CommonsImageService.php
│   │   │   ├── HttpClientService.php
│   │   │   ├── MdwikiApiService.php
│   │   │   ├── SegmentApiService.php
│   │   │   └── TransformApiService.php
│   │   ├── Html/
│   │   │   ├── HtmlToSegmentsService.php
│   │   │   └── WikitextToHtmlService.php
│   │   ├── Interfaces/
│   │   │   ├── CommonsImageServiceInterface.php
│   │   │   └── HttpClientInterface.php
│   │   ├── Pipeline/
│   │   │   └── PagePipelineService.php
│   │   └── Wikitext/
│   │       └── WikitextFixerService.php
│   ├── bootstrap.php
│   ├── Cors.php
│   ├── Logger.php
│   └── Settings.php
├── bootstrap.php
├── fix.php
├── index.php
├── open.php
└── revisions_api.php

```