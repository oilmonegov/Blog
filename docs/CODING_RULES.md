# Coding Rules & Standards
## Simple Blog System

### 1. General Principles

- **SOLID Principles**: All code must adhere to SOLID principles
- **DRY (Don't Repeat Yourself)**: Avoid code duplication
- **KISS (Keep It Simple, Stupid)**: Prefer simple, readable solutions
- **YAGNI (You Aren't Gonna Need It)**: Don't build features that aren't needed
- **Clean Code**: Write self-documenting, maintainable code

### 2. Architecture Patterns

#### 2.1 Actions Pattern
- **Single Responsibility**: One action per class, one method per action
- **Naming Convention**: `{Verb}{Noun}Action.php` (e.g., `CreatePostAction.php`)
- **Location**: `app/Actions/{Domain}/`
- **Structure**:
  ```php
  class CreatePostAction
  {
      public function __construct(
          private PostRepository $repository,
          private PostService $service
      ) {}
      
      public function execute(CreatePostDTO $dto): Post
      {
          // Action logic
      }
  }
  ```

#### 2.2 Services Pattern
- **Domain Services**: Business logic lives in services
- **Naming Convention**: `{Domain}Service.php` (e.g., `PostService.php`)
- **Location**: `app/Services/`
- **Responsibilities**:
  - Business logic
  - Data transformation
  - Orchestration of multiple repositories/actions
- **No Direct DB Access**: Services use repositories, not Eloquent directly

#### 2.3 Repository Pattern
- **Data Access Abstraction**: All database queries go through repositories
- **Naming Convention**: `{Model}Repository.php` (e.g., `PostRepository.php`)
- **Location**: `app/Repositories/`
- **Interface Required**: Create interfaces for repositories
- **Methods**: Standard CRUD + domain-specific query methods

#### 2.4 Data Transfer Objects (DTOs)
- **Request DTOs**: For incoming data validation and transformation
- **Response DTOs**: For outgoing data formatting
- **Naming Convention**: `{Action}{Noun}DTO.php` (e.g., `CreatePostDTO.php`)
- **Location**: `app/DTOs/Requests/` and `app/DTOs/Responses/`
- **Immutable**: DTOs should be immutable where possible
- **Use Spatie Data**: Consider using `spatie/laravel-data` package

#### 2.5 Data Objects
- **Value Objects**: For domain value objects
- **Naming Convention**: `{Domain}Data.php` (e.g., `PostData.php`)
- **Location**: `app/Data/`
- **Purpose**: Represent domain concepts as objects

### 3. Enums

- **Use PHP 8.1+ Enums**: Always use backed enums for database values
- **Naming Convention**: `{Domain}{Type}Enum.php` (e.g., `PostStatus.php`, `UserRole.php`)
- **Location**: `app/Enums/`
- **Backed Enums**: Use string-backed enums for database storage
- **Example**:
  ```php
  enum PostStatus: string
  {
      case DRAFT = 'draft';
      case PUBLISHED = 'published';
      
      public function label(): string
      {
          return match($this) {
              self::DRAFT => 'Draft',
              self::PUBLISHED => 'Published',
          };
      }
  }
  ```

### 4. Controllers

#### 4.1 Resource Controllers
- **Allowed Methods ONLY**: Resource controllers can ONLY have these methods:
  - `index` - List all resources
  - `create` - Show form for creating a new resource (typically for web routes)
  - `store` - Store a newly created resource
  - `show` - Display a specific resource
  - `edit` - Show form for editing a resource (typically for web routes)
  - `update` - Update a specific resource
  - `destroy` - Delete a specific resource
- **Multiple Methods Required**: Resource controllers should have multiple methods; if only one resource method is applicable, convert it to an invokable controller
- **No Custom Methods**: Any functionality that doesn't fit into the above resource methods MUST have its own Laravel invokable controller
- **Naming Convention**: `{Resource}Controller.php` (e.g., `PostController.php`)
- **Location**: 
  - Public: `app/Http/Controllers/Public/`
  - Admin: `app/Http/Controllers/Admin/`
- **One Controller Per Resource**: Don't mix multiple resources in one controller

#### 4.2 Invokable Controllers
- **Single Action Controllers**: Use for controllers with only one method or for functionality that doesn't fit resource methods
- **Only __invoke Method**: Invokable controllers can ONLY have the `__invoke()` method - no other public methods are allowed
- **Naming Convention**: `{Action}Controller.php` (e.g., `PublishPostController.php`)
- **When to Use**:
  - When only one resource method is applicable to a controller
  - For any functionality that doesn't fit into standard resource methods
  - For single-purpose actions

#### 4.3 Action Classes
- **Reusable Business Logic**: Use action classes where necessary - for reusable business logic that may be called from multiple places
- **Location**: `app/Actions/{Domain}/`
- **Purpose**: Encapsulate business logic that may be reused across controllers or other parts of the application

#### 4.4 Routes
- **Only Controllers in Routes**: Only controllers are called in routes - never call services, actions, or closures directly
- **Resource Routes**: Use `Route::resource()` for CRUD operations
- **Route Groups**: Group by middleware (auth, admin, etc.)
- **Naming**: Use named routes

#### 4.5 Form Requests
- **Always Use Form Requests**: Use Form Requests for validation - always use Form Request classes for request validation
- **No Inline Validation**: Never validate directly in controllers
- **Authorization**: Use `authorize()` method in Form Requests for permission checks

#### 4.6 Controller Structure
- **Thin Controllers**: Keep controllers thin - delegate all business logic to services or action classes
- **Proper HTTP Status Codes**: Return proper HTTP status codes
- **Resource Classes for API**: Use Resource classes for API responses
- **Example**:
```php
class PostController extends Controller
{
    public function __construct(
        private PostService $service
    ) {}
    
    public function index(): View
    {
        // Delegate to service
    }
    
    public function store(StorePostRequest $request): RedirectResponse
    {
        // Validate (via Form Request), delegate to action, redirect
    }
}
```

#### 4.7 Domain-Based Organization
- **Group by Domain**: When there are more than two controllers related to the same domain, group them in a directory named after that domain
- **Example Structure**:
  - `app/Http/Controllers/Admin/PostController.php`
  - `app/Http/Controllers/Admin/CategoryController.php`
  - `app/Http/Controllers/Admin/TagController.php`
  - If all three exist, consider: `app/Http/Controllers/Admin/Content/PostController.php`

### 5. Models

- **Eloquent Models**: Use Eloquent ORM
- **Naming Convention**: Singular, PascalCase (e.g., `Post.php`)
- **Location**: `app/Models/`
- **Relationships**: Define all relationships in models
- **Accessors/Mutators**: Use for data transformation
- **Scopes**: Use query scopes for reusable queries
- **Soft Deletes**: Use where applicable (posts, comments)
- **Fillable/Guarded**: Always define `$fillable` array

### 6. Requests (Form Requests)

- **Validation Logic**: All validation in Form Request classes
- **Naming Convention**: `{Action}{Resource}Request.php` (e.g., `StorePostRequest.php`)
- **Location**: `app/Http/Requests/`
- **Authorization**: Use `authorize()` method for permission checks
- **Rules**: Define in `rules()` method

### 7. Policies

- **Authorization**: Use Policies for model-based authorization
- **Naming Convention**: `{Model}Policy.php` (e.g., `PostPolicy.php`)
- **Location**: `app/Policies/`
- **Methods**: Standard methods (`viewAny`, `view`, `create`, `update`, `delete`)
- **Register**: Register in `AuthServiceProvider`

### 8. Routes

- **Resource Routes**: Use `Route::resource()` for CRUD operations
- **Route Groups**: Group by middleware (auth, admin, etc.)
- **Naming**: Use named routes
- **Location**: `routes/web.php` and `routes/admin.php` (if separate)

### 9. Views (Blade Templates)

- **Component-Based**: Use Blade components for reusable UI elements
- **Layouts**: Separate layouts for public and admin
- **Naming Convention**: kebab-case for component names
- **Location**: 
  - Public: `resources/views/public/`
  - Admin: `resources/views/admin/`
  - Components: `resources/views/components/`
  - Layouts: `resources/views/layouts/`

### 10. Testing

#### 10.1 Testing Framework
- **PestPHP**: Use PestPHP for all tests
- **Test Types**:
  - **Feature Tests**: Test HTTP endpoints, full request/response cycle
  - **Unit Tests**: Test individual classes, methods, functions
  - **Arch Tests**: Test code structure, dependencies, architecture
  - **Behavioral Tests**: Test business logic, user scenarios
  - **Property Tests**: Test data validation, edge cases

#### 10.2 Test Structure
- **Location**: `tests/`
- **Naming**: `{Domain}{Type}Test.php` (e.g., `PostFeatureTest.php`)
- **Coverage**: Aim for 100% code coverage
- **Arrange-Act-Assert**: Follow AAA pattern
- **Factories**: Use factories for test data

#### 10.3 Test Examples
```php
// Feature Test
it('can create a post', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->post(route('admin.posts.store'), [
            'title' => 'Test Post',
            'content' => 'Test content',
        ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('posts', ['title' => 'Test Post']);
});

// Unit Test
it('generates excerpt from content', function () {
    $service = new PostService();
    $excerpt = $service->generateExcerpt('Long content...', 100);
    
    expect($excerpt)->toHaveLength(100);
});
```

### 11. Code Quality Tools

#### 11.1 Larastan (PHPStan)
- **Level**: 5 (strictest)
- **Configuration**: `.phpstan.neon`
- **Run**: `./vendor/bin/phpstan analyse`
- **CI**: Must pass before merge

#### 11.2 Laravel Pint
- **PSR-12**: Code style must follow PSR-12
- **Configuration**: `pint.json`
- **Run**: `./vendor/bin/pint`
- **Auto-fix**: `./vendor/bin/pint --test` (dry run), `./vendor/bin/pint` (fix)

#### 11.3 PestPHP
- **Coverage**: Generate coverage reports
- **Run**: `./vendor/bin/pest`
- **With Coverage**: `./vendor/bin/pest --coverage`

### 12. Database

#### 12.1 Migrations
- **Naming**: `{timestamp}_create_{table}_table.php`
- **Location**: `database/migrations/`
- **Foreign Keys**: Always define foreign key constraints
- **Indexes**: Add indexes for frequently queried columns
- **Soft Deletes**: Use `$table->softDeletes()` where needed

#### 12.2 Seeders
- **Naming**: `{Model}Seeder.php`
- **Location**: `database/seeders/`
- **Factories**: Use factories in seeders

### 13. Caching

#### 13.1 Redis Usage
- **Sessions**: Store in Redis
- **Cache**: Cache frequently accessed data
- **Queues**: Use Redis for queue driver
- **Cache Keys**: Use descriptive, namespaced keys
- **TTL**: Set appropriate TTL for cached data
- **Invalidation**: Clear cache on data mutations

### 14. Error Handling

- **Exceptions**: Create custom exceptions for domain errors
- **Location**: `app/Exceptions/`
- **HTTP Exceptions**: Use Laravel's HTTP exceptions
- **Logging**: Log errors appropriately
- **User-Friendly Messages**: Don't expose technical errors to users

### 15. Documentation

#### 15.1 Code Comments
- **PHPDoc**: Document all public methods and classes
- **Inline Comments**: Explain "why", not "what"
- **Complex Logic**: Comment complex business logic

#### 15.2 README
- **Setup Instructions**: Clear setup steps
- **Environment Variables**: Document all required env vars
- **Testing**: How to run tests
- **Deployment**: Deployment instructions

### 16. Git & Version Control

#### 16.1 Commit Messages
- **Format**: Follow conventional commits
- **Examples**:
  - `feat: add post creation functionality`
  - `fix: resolve comment validation issue`
  - `test: add post service unit tests`
  - `refactor: extract post action to service`

#### 16.2 Branching
- **Main Branch**: `main` or `master`
- **Feature Branches**: `feature/{feature-name}`
- **Bug Fixes**: `fix/{bug-description}`
- **Hotfixes**: `hotfix/{issue-description}`

### 17. Dependencies

#### 17.1 Package Selection
- **Official First**: Prefer Laravel official packages
- **Well-Maintained**: Check package maintenance status
- **Documentation**: Ensure good documentation
- **License**: Check license compatibility

#### 17.2 Required Packages
- `laravel/framework` (latest stable)
- `pestphp/pest` (testing)
- `larastan/larastan` (static analysis)
- `laravel/pint` (code style)
- `spatie/laravel-data` (optional, for DTOs)

### 18. Environment Configuration

- **.env.example**: Document all required environment variables
- **Config Files**: Use config files for all settings
- **Environment-Specific**: Don't hardcode environment-specific values

### 19. Security

- **CSRF Protection**: All forms must have CSRF tokens
- **XSS Prevention**: Sanitize user input
- **SQL Injection**: Use Eloquent, never raw queries with user input
- **Authorization**: Check permissions on every action
- **Rate Limiting**: Apply to sensitive endpoints
- **Password Hashing**: Use Laravel's built-in hashing

### 20. Performance

- **N+1 Queries**: Use eager loading (`with()`, `load()`)
- **Database Indexes**: Add indexes for foreign keys and frequently queried columns
- **Caching**: Cache expensive operations
- **Query Optimization**: Use `select()` to limit columns
- **Pagination**: Always paginate large datasets

### 21. Code Review Checklist

Before submitting code for review, ensure:
- [ ] All tests pass
- [ ] Larastan level 5 passes
- [ ] Pint formatting applied
- [ ] No code duplication
- [ ] PHPDoc comments added
- [ ] Authorization checks in place
- [ ] Error handling implemented
- [ ] Cache invalidation considered
- [ ] Database indexes added if needed
- [ ] No N+1 query issues
- [ ] Follows all coding rules

---

**Document Version**: 1.0  
**Last Updated**: 2024  
**Status**: Active

