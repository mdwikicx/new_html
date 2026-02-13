You are a senior software architect. Refactor the codebase to follow Clean Architecture principles.

**Task:**
Remove direct dependency from Domain layer on Services\Api layer by implementing Dependency Inversion Principle.

**Current Issue:**
- File: `src/Domain/Fixes/Media/RemoveMissingImagesService.php`
- Uses: `use function MDWiki\NewHtml\Services\Api\check_commons_image_exists;`
- Violates: Domain should NOT depend on Services\Api

**Required Changes:**

1. Create interface in Domain layer:
   - Path: `src/Domain/Contracts/ImageValidatorInterface.php`
   - Method: `imageExists(string $imageName): bool`

2. Create implementation in Services layer:
   - Path: `src/Services/Api/CommonsImageValidator.php`
   - Implements: `ImageValidatorInterface`
   - Wraps existing `check_commons_image_exists()` function

3. Refactor `RemoveMissingImagesService`:
   - Inject `ImageValidatorInterface` via constructor
   - Replace function calls with interface method calls

4. Update bootstrap/dependency injection container to bind interface to implementation

**Success Criteria:**
- Domain layer has zero dependencies on Services or Infrastructure
- All tests pass
- Backward compatibility maintained
