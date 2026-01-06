# Laravel Boilerplate Documentation

This is a comprehensive guide for AI assistants working on this Laravel boilerplate project. It consolidates all patterns, conventions, and step-by-step instructions needed to solve development tasks.

**Purpose**: This guide serves as a complete reference for implementing any feature in this boilerplate. If you're given a task, follow this guide step-by-step to ensure consistency and correctness.

---

## Table of Contents

1. [Project Overview & Architecture](#project-overview--architecture)
2. [Quick Reference Checklist](#quick-reference-checklist)
3. [Creating a New API Resource (Complete Guide)](#creating-a-new-api-resource-complete-guide)
4. [Project Structure](#project-structure)
5. [Routes](#routes)
6. [Models](#models)
7. [Controllers](#controllers)
8. [Requests](#requests)
9. [Resources](#resources)
10. [API Responses](#api-responses)
11. [Authentication](#authentication)
12. [CRUD Operations](#crud-operations)
13. [Filtering and Pagination](#filtering-and-pagination)
14. [Common Patterns & Conventions](#common-patterns--conventions)
15. [Helpers (ApiCodes)](#helpers-apicodes)
16. [Traits](#traits)
17. [Database Migrations](#database-migrations)
18. [Custom Commands](#custom-commands)
19. [Exception Handling](#exception-handling)
20. [Middleware](#middleware)
21. [Key Files Reference](#key-files-reference)
22. [Important Decision Points & Clarifications](#important-decision-points--clarifications)
23. [Example References in the Codebase](#example-references-in-the-codebase)
24. [Common Mistakes to Avoid](#common-mistakes-to-avoid)
25. [Best Practices](#best-practices)

---

## Project Overview & Architecture

### Architecture Pattern

This Laravel application uses a **modular API architecture** where functionality is organized by app type (indicated by `*App` suffixes in folder names). This separation allows for clear organization between API endpoints and authentication endpoints.

### Directory Structure

```
app/
├── Console/Commands/          # Custom artisan commands (MakeController, MakeModel, etc.)
├── Exceptions/                # Exception handlers
├── Helpers/                   # Helper classes (ApiCodes)
├── Http/
│   ├── Controllers/
│   │   ├── ApiApp/           # API controllers (use ApiBaseController)
│   │   └── AuthApp/          # Authentication controllers (use AuthBaseController)
│   ├── Middleware/           # Custom middleware
│   ├── Requests/
│   │   ├── ApiApp/           # Form requests for API endpoints
│   │   └── AuthApp/          # Form requests for auth endpoints
│   └── Resources/
│       └── ApiApp/           # API resources (Individual and List)
├── Models/                    # ⚠️ Models go DIRECTLY here (NO subfolders like ApiApp)
├── Providers/                 # Service providers (RouteServiceProvider, etc.)
└── Traits/                    # Shared traits (ApiTrait, CommandTrait)

routes/
├── api.php                    # API endpoints (protected by auth:api)
├── auth.php                   # Authentication endpoints
└── web.php                    # Web routes (rarely used in API projects)

database/
└── migrations/               # Database migrations
```

### Key Principles

- **Modularity**: Controllers, Requests, and Resources are organized by app type (`ApiApp/`, `AuthApp/`)
- **Consistency**: All API controllers extend `ApiBaseController` for standardized responses
- **Separation**: Models are placed directly in `app/Models/` (no subfolders) to be shared across the app
- **Validation**: All endpoints use Form Requests for validation
- **Resources**: All responses use Laravel Resources for consistent data formatting
- **Authentication**: Uses Laravel Passport (OAuth2) for API authentication

---

## Quick Reference Checklist

When creating a new API resource, follow this checklist:

- [ ] **Migration**: Create `database/migrations/YYYY_MM_DD_create_<resource>_table.php` with `up()` and `down()` methods
- [ ] **Model**: Create `app/Models/<Resource>.php` directly (no subfolders), add `$fillable` array
- [ ] **Requests**: Create three request classes in `app/Http/Requests/ApiApp/`:
  - `<Resource>Request.php` (for `index`, `show`, `destroy`)
  - `Store<Resource>Request.php` (for `store`)
  - `Update<Resource>Request.php` (for `update`)
- [ ] **Resources**: Create two resource classes in `app/Http/Resources/ApiApp/`:
  - `<Resource>IndividualResource.php` (for single items)
  - `<Resource>ListResource.php` (for collections)
- [ ] **Controller**: Create `app/Http/Controllers/ApiApp/<Resources>Controller.php` extending `ApiBaseController`, implement all 5 CRUD methods
- [ ] **Route**: Add `Route::resource('<resources>', <Resources>Controller::class)` to `routes/api.php` inside `auth:api` middleware group
- [ ] **Test**: Test all CRUD endpoints, filtering, pagination, sorting, and field selection

---

## Creating a New API Resource (Complete Guide)

Follow the **Quick Reference Checklist** above for the step-by-step process. For complete code examples, refer to existing resources in the codebase:

- **`app/Http/Controllers/ApiApp/ExamplesController.php`** - Complete CRUD controller example
- **`app/Models/Example.php`** - Model example
- **`app/Http/Requests/ApiApp/ExampleRequest.php`** - Base request example
- **`app/Http/Resources/ApiApp/ExampleIndividualResource.php`** - Individual resource example

These examples demonstrate all the patterns and conventions used in this project.

---

## Project Structure

### Directory Organization

```
app/
├── Console/Commands/          # Custom artisan commands
├── Exceptions/                # Exception handlers
├── Helpers/                   # Helper classes (ApiCodes, etc.)
├── Http/
│   ├── Controllers/
│   │   ├── ApiApp/           # API controllers (use ApiBaseController)
│   │   └── AuthApp/          # Authentication controllers (use AuthBaseController)
│   ├── Middleware/           # Custom middleware
│   ├── Requests/
│   │   ├── ApiApp/           # Form requests for API endpoints
│   │   └── AuthApp/          # Form requests for auth endpoints
│   └── Resources/
│       └── ApiApp/           # API resources (Individual and List)
├── Models/                    # ⚠️ Models go DIRECTLY here (NO subfolders like ApiApp)
├── Providers/                 # Service providers
└── Traits/                    # Shared traits (ApiTrait, CommandTrait)
```

### Key Rules

- **Models**: Always placed directly in `app/Models/` (e.g., `app/Models/Example.php`, `app/Models/User.php`)
- **Controllers**: Organized by app type in subfolders (`ApiApp/`, `AuthApp/`)
- **Requests**: Organized by app type in subfolders (`ApiApp/`, `AuthApp/`)
- **Resources**: Organized by app type in subfolders (`ApiApp/`, `AuthApp/`)

### Important Folders Explained

#### `app/Console/Commands/`

**Purpose**: Custom artisan commands that extend Laravel's default make commands.

**What Goes Here**:
- `MakeController.php` - Custom controller generator with `--app` option
- `MakeModel.php` - Custom model generator (places models directly in `app/Models/`)
- `MakeRequest.php` - Custom request generator with `--app` option
- `MakeResource.php` - Custom resource generator with `--app` option

**Key Features**:
- All commands use `CommandTrait` to add `--app` option
- Commands automatically organize files into appropriate subfolders (`ApiApp/`, `AuthApp/`)
- Models are exception - they always go directly in `app/Models/` regardless of `--app` option

**When to Add Files Here**:
- When you need to customize Laravel's default make commands
- When you need to add new options or change default behavior

**Example Usage**:
```bash
php artisan make:controller ApiApp/ProductsController --app=api
# Creates: app/Http/Controllers/ApiApp/ProductsController.php
```

---

#### `app/Exceptions/`

**Purpose**: Exception handling and custom error responses.

**What Goes Here**:
- `Handler.php` - Main exception handler that processes all exceptions

**Key Features**:
- Currently uses Laravel's default exception handling
- Can be customized to return JSON responses for API routes
- Handles validation exceptions, authentication exceptions, etc.

**When to Modify**:
- When you need custom error response formats for API endpoints
- When you need to log exceptions differently
- When you need to handle specific exception types

**Customization Example**:
```php
// In Handler.php
public function render($request, Throwable $e)
{
    if ($request->is('api/*') || $request->is('auth/*')) {
        return response()->json([
            'message' => $e->getMessage(),
            'status' => ApiCodes::INTERNAL_SERVER_ERROR,
        ], ApiCodes::INTERNAL_SERVER_ERROR);
    }
    return parent::render($request, $e);
}
```

---

#### `app/Helpers/`

**Purpose**: Shared helper classes that provide constants and utility methods.

**What Goes Here**:
- `ApiCodes.php` - HTTP status codes and standardized messages

**Key Features**:
- Provides constants for HTTP status codes (SUCCESS, NOT_FOUND, etc.)
- Provides methods for standardized error messages
- Used throughout the application for consistent responses

**When to Add Files Here**:
- When you need shared utility classes used across multiple modules
- When you need constants or helper methods that don't belong to a specific model or controller
- **Don't add here**: Code specific to one module (keep it in that module)

**Usage Pattern**:
```php
// Always use ApiCodes instead of hardcoding
return $this->responseToJson(
    ApiCodes::getSuccessMessage(),
    ApiCodes::SUCCESS,
    $data
);
```

---

#### `app/Http/Controllers/`

**Purpose**: All HTTP controllers that handle incoming requests.

**Organization**:
- `ApiApp/` - API controllers (extend `ApiBaseController`)
- `AuthApp/` - Authentication controllers (extend `AuthBaseController`)

**What Goes in Each Subfolder**:

**`ApiApp/`**:
- All CRUD controllers for API resources
- Must extend `ApiBaseController`
- Examples: `ExamplesController.php`, `ProductsController.php`

**`AuthApp/`**:
- Authentication-related controllers
- Must extend `AuthBaseController`
- Examples: `AuthController.php` (login, logout, refresh token)

**Key Principles**:
- Controllers should be thin - delegate business logic to services if needed
- All controllers must use Form Requests for validation
- All controllers must use Resources for responses
- All API controllers require `auth:api` middleware (applied at route level)

**When to Add Files Here**:
- When creating a new API resource (CRUD operations)
- When adding new authentication endpoints
- **Don't add here**: Business logic (create service classes instead)

---

#### `app/Http/Middleware/`

**Purpose**: HTTP middleware that processes requests before they reach controllers.

**What Goes Here**:
- `Authenticate.php` - Handles authentication
- `EncryptCookies.php` - Cookie encryption
- `TrustProxies.php` - Proxy configuration
- `VerifyCsrfToken.php` - CSRF protection (typically disabled for APIs)

**Key Features**:
- Middleware runs before requests reach controllers
- Can modify requests, responses, or terminate early
- Most API projects use `auth:api` middleware from Laravel Passport

**When to Add Files Here**:
- When you need to process requests before controllers
- When you need to add custom authentication/authorization logic
- When you need to modify requests or responses globally

**Usage**:
- Register in `app/Http/Kernel.php`
- Apply to routes or route groups

---

#### `app/Http/Requests/`

**Purpose**: Form Request classes for validation and authorization.

**Organization**:
- `ApiApp/` - Form requests for API endpoints
- `AuthApp/` - Form requests for auth endpoints

**What Goes in Each Subfolder**:

**`ApiApp/`**:
- Base requests: `<Resource>Request.php` (for `index`, `show`, `destroy`)
- Store requests: `Store<Resource>Request.php` (for `store`)
- Update requests: `Update<Resource>Request.php` (for `update`)

**`AuthApp/`**:
- Authentication requests: `LoginRequest.php`, etc.

**Key Principles**:
- Three request types per resource (Base, Store, Update)
- Base Request: Validates filters and query parameters
- Store Request: Validates required fields for creation
- Update Request: Uses `sometimes` for optional fields

**When to Add Files Here**:
- When creating a new API resource (create 3 request classes)
- When adding new validation rules
- **Always use**: Form Requests instead of validating in controllers

---

#### `app/Http/Resources/`

**Purpose**: API Resources that transform models into JSON responses.

**Organization**:
- `ApiApp/` - API resources

**What Goes Here**:
- Individual Resources: `<Resource>IndividualResource.php` (for single items)
- List Resources: `<Resource>ListResource.php` (for collections)

**Key Principles**:
- Two resource types per model (Individual, List)
- Both use `ApiTrait` and `resourceFields($request)` method
- Support field selection via `?fields=...` query parameter
- Never include sensitive data (passwords, tokens)

**When to Add Files Here**:
- When creating a new API resource (create 2 resource classes)
- When you need to customize response format
- **Always use**: Resources instead of returning models directly

---

#### `app/Models/`

**Purpose**: Eloquent models representing database tables.

**⚠️ CRITICAL RULE**: Models **MUST** be placed directly in `app/Models/`. **DO NOT** create subfolders like `ApiApp` inside Models.

**What Goes Here**:
- All Eloquent models: `User.php`, `Example.php`, `Product.php`, etc.

**Key Principles**:
- Models are shared across the entire application
- No subfolders - all models at the same level
- Always include `$fillable` array for mass assignment protection
- Use `HasFactory` trait for model factories
- Define relationships using standard Eloquent methods

**When to Add Files Here**:
- When creating a new database table (create model after migration)
- **Never create subfolders** - always place directly in `app/Models/`

**Why This Structure**:
- Models represent database entities that can be used by multiple app types
- Prevents duplication and allows models to be shared between `ApiApp` and `AuthApp`
- Keeps the model layer simple and accessible

---

#### `app/Providers/`

**Purpose**: Service providers that bootstrap and configure application services.

**What Goes Here**:
- `RouteServiceProvider.php` - Registers and configures routes
- `AppServiceProvider.php` - General application services
- `AuthServiceProvider.php` - Authentication services
- `EventServiceProvider.php` - Event listeners
- `BroadcastServiceProvider.php` - Broadcasting services

**Key Features**:
- `RouteServiceProvider` is particularly important - it registers all routes with prefixes
- Service providers run during application bootstrapping
- Can register services, bind interfaces, configure routes, etc.

**When to Modify**:
- When adding new route files or changing route prefixes
- When registering new services or bindings
- When configuring application-wide settings

**RouteServiceProvider Pattern**:
```php
// Routes are registered with prefixes
Route::middleware('api')
    ->prefix('api')
    ->group(base_path('routes/api.php'));

Route::middleware('api')
    ->prefix('auth')
    ->group(base_path('routes/auth.php'));
```

---

#### `app/Traits/`

**Purpose**: Reusable traits that provide shared functionality.

**What Goes Here**:
- `ApiTrait.php` - Response methods for controllers
- `CommandTrait.php` - Adds `--app` option to artisan commands

**Key Features**:

**`ApiTrait`**:
- Used by `ApiBaseController` and `AuthBaseController`
- Provides standardized response methods (`responseToJson()`, `getJsonResponse()`, etc.)
- Handles pagination, sorting, field selection
- Provides error response methods

**`CommandTrait`**:
- Used by custom make commands
- Adds `--app` option to commands
- Converts app name to namespace (e.g., `--app=api` → `ApiApp`)

**When to Add Files Here**:
- When you need functionality shared across multiple classes
- When you need to add methods to multiple controllers
- **Don't add here**: Code specific to one class (keep it in that class)

---

#### `database/migrations/`

**Purpose**: Database schema migrations that version control database structure.

**What Goes Here**:
- Migration files: `YYYY_MM_DD_HHMMSS_descriptive_name.php`

**Key Principles**:
- Always create migrations before creating models
- Migrations are versioned by timestamp
- Must implement both `up()` and `down()` methods
- `up()` applies changes, `down()` reverses them

**Naming Convention**:
- `YYYY_MM_DD_HHMMSS_create_<table>_table.php` - Creating tables
- `YYYY_MM_DD_HHMMSS_add_<column>_to_<table>_table.php` - Adding columns
- `YYYY_MM_DD_HHMMSS_modify_<table>_table.php` - Modifying tables

**When to Add Files Here**:
- When creating new database tables
- When modifying existing table structure
- When adding/removing columns, indexes, foreign keys
- **Always**: Create migration before creating/updating model

**Best Practices**:
- One change per migration (keep focused and atomic)
- Always implement `down()` method (make reversible)
- Use descriptive names
- Never modify migrations that have run in production

---

#### `routes/`

**Purpose**: Route definitions that map URLs to controller methods.

**What Goes Here**:
- `api.php` - API endpoints (protected by `auth:api` middleware)
- `auth.php` - Authentication endpoints (login, logout, refresh token)
- `web.php` - Web routes (rarely used in API projects)

**Organization**:

**`routes/api.php`**:
- All API resource routes
- Must be inside `auth:api` middleware group
- Uses `Route::resource()` for standard CRUD operations
- Example: `Route::resource('examples', ExamplesController::class)`

**`routes/auth.php`**:
- Public routes: `login`, `refresh-token` (no auth required)
- Protected routes: `logout` (requires `auth:api` middleware)
- All routes automatically prefixed with `/auth`

**`routes/web.php`**:
- Web routes (typically not used in API-only projects)
- Can be used for admin panels or web interfaces

**Key Principles**:
- API routes use RESTful conventions
- All API routes require authentication
- Routes are automatically prefixed in `RouteServiceProvider`
- Use `Route::resource()` for standard CRUD operations

**When to Add Routes**:
- When creating a new API resource (add to `routes/api.php`)
- When adding new authentication endpoints (add to `routes/auth.php`)
- **Always**: Place API routes inside `auth:api` middleware group

---

## Routes

### Route Files

Routes are organized into separate files based on their purpose:

- **`routes/api.php`** - API endpoints (protected by `auth:api` middleware)
- **`routes/auth.php`** - Authentication endpoints (login, logout, refresh token)
- **`routes/web.php`** - Web routes (typically not used in API-only projects)

### Route Prefixes

Routes are automatically prefixed in `RouteServiceProvider`:

- **Auth routes**: `/auth/*` (e.g., `/auth/login`, `/auth/logout`)
- **API routes**: `/api/*` (e.g., `/api/examples`, `/api/products`)

### Route Structure

#### API Routes (`routes/api.php`)

All API routes **MUST** be protected with `auth:api` middleware:

```php
<?php

use App\Http\Controllers\ApiApp\ExamplesController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function () {
    Route::resource('examples', ExamplesController::class);
    // Add more resource routes here
});
```

**Key Points:**
- Use `Route::resource()` for standard CRUD operations
- All routes inside the group require authentication
- Routes follow RESTful conventions

#### Auth Routes (`routes/auth.php`)

Authentication routes are split between public and protected:

```php
<?php

use App\Http\Controllers\AuthApp\AuthController;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::post('login', [AuthController::class, 'login']);
Route::post('refresh-token', [AuthController::class, 'refreshToken']);

// Protected routes (authentication required)
Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
});
```

**Key Points:**
- `login` and `refresh-token` are public (no auth required)
- `logout` requires authentication
- All routes are prefixed with `/auth` automatically

### Adding New Routes

When adding a new resource:

1. Create the controller, requests, resources, and model following the patterns
2. Add the route to `routes/api.php`:
   ```php
   Route::group(['middleware' => ['auth:api']], function () {
       Route::resource('examples', ExamplesController::class);
       Route::resource('products', ProductsController::class);  // New route
   });
   ```

3. The route will be available at `/api/products` with all RESTful endpoints

---

## Models

### Location
Models **MUST** be placed directly in `app/Models/` directory. **DO NOT** create subfolders like `ApiApp` inside Models.

### Structure
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        // ... other fields
    ];
}
```

### Creating Models
Use the custom command:
```bash
php artisan make:model Example
```

**Note**: The `MakeModel` command has been customized to place models directly in `app/Models/` without app subfolders.

### Model Relationships

Models can define relationships following Laravel's standard Eloquent patterns:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
    ];

    // Define relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
```

**Key Points**:
- Relationships follow standard Laravel conventions
- Use relationships in queries: `Product::with('category')->get()`
- Access relationships in resources if needed

### Important Model Guidelines

- Always place models directly in `app/Models/` (no subfolders)
- Always include `$fillable` array for mass assignment protection
- Use `HasFactory` trait for model factories
- Define relationships using standard Eloquent methods
- Use eager loading (`with()`) to prevent N+1 query problems

---

## Controllers

### Base Controllers

- **API Controllers**: Extend `App\Http\Controllers\ApiApp\ApiBaseController`
- **Auth Controllers**: Extend `App\Http\Controllers\AuthApp\AuthBaseController`

Both base controllers use the `ApiTrait` which provides standardized response methods.

### Controller Structure

All API controllers should follow this structure:

```php
<?php

namespace App\Http\Controllers\ApiApp;

use App\Helpers\ApiCodes;
use App\Http\Requests\ApiApp\ExampleRequest;
use App\Http\Requests\ApiApp\StoreExampleRequest;
use App\Http\Requests\ApiApp\UpdateExampleRequest;
use App\Http\Resources\ApiApp\ExampleIndividualResource;
use App\Http\Resources\ApiApp\ExampleListResource;
use App\Models\Example;  // ⚠️ Import from App\Models, not App\Models\ApiApp

class ExamplesController extends ApiBaseController
{
    // All CRUD methods here
}
```

### Important Controller Guidelines

- Always extend `ApiBaseController` or `AuthBaseController`
- Always use Form Requests for validation (never use `Request` directly)
- Always use Resources for responses (never return models directly)
- Always use `ApiCodes` constants (never hardcode status codes)
- Keep controllers thin - move complex logic to service classes if needed

---

## Requests

### Request Types

For each resource, create three request classes:

1. **Base Request** (e.g., `ExampleRequest.php`) - Used for `index`, `show`, `destroy`
2. **Store Request** (e.g., `StoreExampleRequest.php`) - Used for `store`
3. **Update Request** (e.g., `UpdateExampleRequest.php`) - Used for `update`

### Location
- API requests: `app/Http/Requests/ApiApp/`
- Auth requests: `app/Http/Requests/AuthApp/`

### Structure
```php
<?php

namespace App\Http\Requests\ApiApp;

use Illuminate\Foundation\Http\FormRequest;

class ExampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Validation rules
        ];
    }
}
```

### Creating Requests
Use the custom command with `--app` option:
```bash
php artisan make:request ApiApp/ExampleRequest --app=api
php artisan make:request ApiApp/StoreExampleRequest --app=api
php artisan make:request ApiApp/UpdateExampleRequest --app=api
```

### Request Validation Patterns

**Base Request** (for filters, query parameters):
```php
public function rules(): array
{
    return [
        'search' => 'sometimes|string',
        'status' => 'sometimes|in:active,inactive',
        'page' => 'sometimes|integer|min:1',
        'page-size' => 'sometimes|integer|min:1|max:100',
    ];
}
```

**Store Request** (for creating resources):
```php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
    ];
}
```

**Update Request** (for updating resources):
```php
public function rules(): array
{
    return [
        'name' => 'sometimes|string|max:255',
        'description' => 'nullable|string',
        'price' => 'sometimes|numeric|min:0',
    ];
}
```

**Key Points:**
- Use `required` for Store requests
- Use `sometimes` for Update requests (all fields optional)
- Use `nullable` for optional fields
- Always validate input data types and constraints

---

## Resources

### Resource Types

Create two resource classes for each model:

1. **Individual Resource** (e.g., `ExampleIndividualResource.php`) - Used for single model instances (`show`, `store`, `update`)
2. **List Resource** (e.g., `ExampleListResource.php`) - Used for collections (`index`)

### Location
- API resources: `app/Http/Resources/ApiApp/`

### Structure
```php
<?php

namespace App\Http\Resources\ApiApp;

use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExampleIndividualResource extends JsonResource
{
    use ApiTrait;

    public function toArray(Request $request): array
    {
        return $this->resourceFields($request);
    }
}
```

### Field Selection

Resources use the `resourceFields()` method from `ApiTrait` which allows clients to request specific fields via query parameters:

```
GET /api/examples/1?fields=id,title,created_at
```

### Creating Resources
Use the custom command:
```bash
php artisan make:resource ApiApp/ExampleIndividualResource --app=api
php artisan make:resource ApiApp/ExampleListResource --app=api
```

### Important Resource Guidelines

- Always use `ApiTrait` in resources
- Always use `resourceFields($request)` for field selection
- Use Individual Resource for single items (`show`, `store`, `update`)
- Use List Resource for collections (`index`)
- Never return sensitive data (passwords, tokens) in resources

---

## API Responses

### Response Format

All API responses follow this structure:

```json
{
    "message": "The action was completed successfully!",
    "data": [...],
    "meta": {...}
}
```

### Using Resources with Additional Parameters

The `responseToJson()` method supports an optional 4th parameter for additional data (e.g., tokens, metadata):

```php
// Basic usage
return $this->responseToJson(
    ApiCodes::getSuccessMessage(),
    ApiCodes::SUCCESS,
    $data
);

// With additional parameters (e.g., token)
$data = [new UserResource($user)];
return $this->responseToJson(
    ApiCodes::getSuccessMessage(),
    ApiCodes::SUCCESS,
    $data,
    ['token' => $token]  // ← Additional parameter here
);
```

The additional parameters are merged into the response, allowing you to add extra fields without modifying the resource itself.

### Response Methods from ApiTrait

- `responseToJson($message, $status, $data = [], $additional = [])` - Main response method
- `successResponse($message = null, $status = null)` - Empty success response
- `successResponseWithData($data)` - Success with data (returns array, not response)
- `resourceNotFound($message = null, $status = null)` - 404 response
- `resourceCantBeModified($message = null, $status = null)` - 403 response
- `validationFailed($message = null, $status = null)` - Validation error
- `generalError($message = null, $status = null)` - General error
- `getJsonResponse($resourceItems, $resourceClass, $asModel = false, $message = null)` - Helper for resources

### Example: Login with Token

```php
// In AuthController or similar
$user = User::where('email', $request->email)->first();
$token = $user->createToken('auth-token')->accessToken;

$data = [new UserResource($user)];
return $this->responseToJson(
    ApiCodes::getSuccessMessage(),
    ApiCodes::SUCCESS,
    $data,
    ['token' => $token]  // Additional parameter
);
```

Response:
```json
{
    "message": "The action was completed successfully!",
    "data": [{
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    }],
    "meta": {},
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

---

## Authentication

### Authentication System

This boilerplate uses **Laravel Passport** for API authentication (OAuth2).

### Authentication Flow

1. **Login**: User provides email/password → receives access token and refresh token
2. **API Requests**: Include access token in `Authorization` header
3. **Token Refresh**: Use refresh token to get new access token
4. **Logout**: Revoke current access token

### AuthController Pattern

The `AuthController` extends `AuthBaseController` and handles:

- `login()` - Authenticate user and return tokens
- `refreshToken()` - Refresh access token using refresh token
- `logout()` - Revoke current user's token

### Login Example

```php
// In AuthController
public function login(LoginRequest $request)
{
    $data = [
        'email' => $request->email,
        'password' => $request->password
    ];

    if (auth()->attempt($data)) {
        $response = $this->getTokenAndRefreshToken(request('email'), request('password'));
        return $this->successResponseWithData($response);
    }

    return $this->generalError(ApiCodes::getWrongCredentialsMessage(), ApiCodes::UNAUTHENTICATED);
}
```

### Token Response Format

Login returns:
```json
{
    "message": "The action was completed successfully!",
    "status": 200,
    "data": {
        "token_type": "Bearer",
        "expires_in": 31536000,
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "refresh_token": "def50200..."
    }
}
```

### Using Authentication in Controllers

All API controllers automatically require authentication via `auth:api` middleware. Access the authenticated user:

```php
$user = auth()->user();
// or
$user = \auth()->user();
```

### Token Revocation

The `User` model includes a `revokeToken()` method for logout:

```php
public function logout()
{
    $user = auth()->user();
    $user->revokeToken();
    return $this->responseToJson(ApiCodes::getSuccessMessage(), ApiCodes::SUCCESS);
}
```

---

## CRUD Operations

### Standard CRUD Structure

All CRUD controllers should implement these methods:

1. **`index()`** - List all resources (with filtering)
2. **`show()`** - Show single resource
3. **`store()`** - Create new resource
4. **`update()`** - Update existing resource
5. **`destroy()`** - Delete resource

### Index Method Pattern

The `index()` method **MUST** handle all filtering logic. Use this exact pattern:

```php
public function index(ExampleRequest $request)
{
    $query = Example::query();

    // Add filters here
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }
    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Fetch results (handles pagination and sorting automatically)
    $examples = $this->fetchResults($query);

    if ($examples->isEmpty()) {
        return $this->resourceNotFound(null, ApiCodes::SUCCESS);
    }

    return $this->getJsonResponse($examples, ExampleListResource::class);
}
```

**Key Points:**
- All filtering logic goes in `index()`
- Use `$this->fetchResults($query)` for pagination and sorting
- Return `ExampleListResource::class` for collections

### Show Method Pattern

```php
public function show(ExampleRequest $request, $exampleId)
{
    $example = Example::query()->find($exampleId);

    return $example ?
        $this->getJsonResponse($example, ExampleIndividualResource::class) :
        $this->resourceNotFound();
}
```

### Store Method Pattern

```php
public function store(StoreExampleRequest $request)
{
    $example = Example::query()->create($request->all());

    if (!$example) {
        return $this->generalError();
    }

    return $this->getJsonResponse($example, ExampleIndividualResource::class);
}
```

### Update Method Pattern

```php
public function update(UpdateExampleRequest $request, $exampleId)
{
    $example = Example::query()->find($exampleId);

    if (!$example) {
        return $this->resourceNotFound();
    }

    if (!$example->update($request->all())) {
        return $this->generalError();
    }

    return $this->getJsonResponse($example, ExampleIndividualResource::class);
}
```

### Destroy Method Pattern

```php
public function destroy(ExampleRequest $request, $exampleId)
{
    $example = Example::find($exampleId);

    if (!$example) {
        return $this->resourceNotFound();
    }

    if (!$example->delete()) {
        return $this->generalError();
    }

    return $this->successResponse();
}
```

---

## Filtering and Pagination

### Query Parameters

The `fetchResults()` method from `ApiTrait` automatically handles:

- **Pagination**: `?page=1&page-size=25`
- **No Pagination**: `?no-pagination=true`
- **Sorting**: `?sort-by=created_at&descending=true`

### Sorting

The `prepareSortingQuery()` method (called automatically by `fetchResults()`) handles:
- `sort-by`: Column name to sort by
- `descending`: `true` for descending, `false` or omitted for ascending

Example:
```
GET /api/examples?sort-by=created_at&descending=true&page-size=10
```

### Custom Filtering

Add custom filters in the `index()` method:

```php
public function index(ExampleRequest $request)
{
    $query = Example::query();

    // Custom filters
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $examples = $this->fetchResults($query);

    return $this->getJsonResponse($examples, ExampleListResource::class);
}
```

---

## Common Patterns & Conventions

### 1. Standard Response Wrapper via ApiTrait

Controllers extend `ApiBaseController` which uses `ApiTrait` and provides:
- `responseToJson()` → returns `{ message, data, meta }` with HTTP status
- `getJsonResponse()` → helper for resources
- `successResponse()` → empty success response
- `resourceNotFound()` → 404 response
- `generalError()` → 500 response

**Why:** This guarantees a consistent response envelope across all endpoints.

### 2. Pagination Service

`fetchResults()` method from `ApiTrait` centralizes pagination logic.

**Request query params:**
- `page` (default 1), `page-size` (default 25), `sort-by`, `descending` (default `false`), `no-pagination` (default `false`)

**Usage in controllers:**
```php
$results = $this->fetchResults($query);
// Automatically handles pagination, sorting, and no-pagination option
```

**Why:** This keeps pagination consistent and keeps controllers focused on business logic.

### 3. Form Requests for Validation

For each resource, create three request classes:
- Base Request: For `index`, `show`, `destroy` (filters, query params)
- Store Request: For `store` (required fields)
- Update Request: For `update` (use `sometimes` for optional fields)

**Why:** Centralizes validation logic and keeps controllers clean.

### 4. Resources for Response Formatting

Create two resource classes per model:
- Individual Resource: For single items (`show`, `store`, `update`)
- List Resource: For collections (`index`)

Both use `resourceFields($request)` for field selection via `?fields=...` query parameter.

**Why:** Ensures consistent response format and allows clients to request specific fields.

### 5. Models in Shared Location

Models are placed directly in `app/Models/` (no subfolders) to be shared across the app.

**Why:** Prevents duplication and allows models to be used by multiple app types.

### 6. Authentication Middleware

All API routes use `auth:api` middleware (Laravel Passport).

**Why:** Standard OAuth2 flow with JWT tokens; middleware is applied at route group level.

### 7. ApiCodes for Status Codes

Always use `ApiCodes` constants and methods instead of hardcoding values.

**Why:** Ensures consistency and makes it easy to update messages/codes globally.

---

## Helpers (ApiCodes)

### Purpose

The `ApiCodes` helper class provides standardized HTTP status codes and messages for consistent API responses.

### Location

`app/Helpers/ApiCodes.php`

### Available Constants

```php
ApiCodes::SUCCESS = 200
ApiCodes::ACCEPTED = 202
ApiCodes::BAD_REQUEST = 400
ApiCodes::UNAUTHENTICATED = 401
ApiCodes::FORBIDDEN = 403
ApiCodes::RESOURCE_NOT_FOUND = 404
ApiCodes::METHOD_NOT_ALLOWED = 405
ApiCodes::INTERNAL_SERVER_ERROR = 500
ApiCodes::SERVICE_UNAVAILABLE = 503
```

### Available Methods

```php
ApiCodes::getSuccessMessage()                    // "The action was completed successfully!"
ApiCodes::getResourceNotFoundMessage()           // "No data found!"
ApiCodes::getGeneralErrorMessage()              // "Internal error! Please try again!"
ApiCodes::getModelSoftDeletedMessage()          // "There is a deleted entity with this name!"
ApiCodes::getForbiddenErrorMessage()            // "You are not allowed to perform this action!"
ApiCodes::getWrongCredentialsMessage()          // "The credentials entered are incorrect!"
ApiCodes::getRecordExistsErrorMessage()         // "This record exists in the database!"
```

### Usage in Controllers

Always use `ApiCodes` constants and methods instead of hardcoding values:

```php
// ✅ Correct
return $this->responseToJson(
    ApiCodes::getSuccessMessage(),
    ApiCodes::SUCCESS,
    $data
);

// ❌ Wrong
return $this->responseToJson(
    'Success',
    200,
    $data
);
```

### Adding New Codes/Messages

When you need a new status code or message:

1. Add the constant to `ApiCodes` class:
   ```php
   const CUSTOM_ERROR = 422;
   ```

2. Add the message method:
   ```php
   public static function getCustomErrorMessage(): string
   {
       return 'Custom error message!';
   }
   ```

3. Use it in controllers:
   ```php
   return $this->responseToJson(
       ApiCodes::getCustomErrorMessage(),
       ApiCodes::CUSTOM_ERROR
   );
   ```

---

## Traits

### ApiTrait

**Location**: `app/Traits/ApiTrait.php`

**Purpose**: Provides standardized response methods for all controllers.

**Used By**: `ApiBaseController`, `AuthBaseController`

**Key Methods**:
- `responseToJson()` - Main response formatter
- `getJsonResponse()` - Helper for resources
- `fetchResults()` - Pagination and sorting
- `prepareSortingQuery()` - Query sorting
- `resourceFields()` - Field selection for resources
- Error response methods (see [API Responses](#api-responses))

**Usage**: Automatically available in all controllers extending base controllers.

### CommandTrait

**Location**: `app/Traits/CommandTrait.php`

**Purpose**: Enables the `--app` option for custom artisan commands.

**Used By**: `MakeController`, `MakeRequest`, `MakeResource`, `MakeModel`

**How It Works**:

1. Adds `--app` option to commands
2. Converts app name to namespace (e.g., `--app=api` → `ApiApp`)
3. Places files in appropriate subfolders

**Example**:
```bash
php artisan make:controller ApiApp/ProductsController --app=api
# Creates: app/Http/Controllers/ApiApp/ProductsController.php
```

**Important**: Models don't use app subfolders, but the trait is still used for consistency.

---

## Database Migrations

### Location

`database/migrations/`

### Naming Convention

Migrations follow Laravel's standard naming:
- `YYYY_MM_DD_HHMMSS_create_table_name_table.php`
- Example: `2023_03_19_223133_create_examples_table.php`

### Migration Structure

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examples', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examples');
    }
};
```

### Key Points

- Always include `$table->id()` for primary key
- Always include `$table->timestamps()` for created_at/updated_at
- Use descriptive column types (`string`, `text`, `integer`, `boolean`, etc.)
- Implement `down()` method to reverse the migration

### Creating Migrations

```bash
php artisan make:migration create_examples_table
php artisan make:migration add_status_to_examples_table
```

### Running Migrations

```bash
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh  # Drops all tables and re-runs migrations
```

### Migration Best Practices

- Always implement `down()` - make migrations reversible
- Test migrations: test `up()`, test `down()`, test on copy of production data
- Use descriptive names: `CreateProductsTable`, `AddStatusToProducts`, etc.
- One change per migration - keep migrations focused and atomic
- Handle existing data when adding/modifying columns
- Never modify migrations that have run in production - create a new migration instead
- Provide defaults when adding NOT NULL columns to existing tables

---

## Custom Commands

### Available Commands

All custom commands support the `--app` option to organize files by app type:

- `php artisan make:model ModelName` (Models don't use --app, always in `app/Models/`)
- `php artisan make:controller ApiApp/ControllerName --app=api`
- `php artisan make:request ApiApp/RequestName --app=api`
- `php artisan make:resource ApiApp/ResourceName --app=api`

**Important**: The `--app` option is used for Controllers, Requests, and Resources, but **NOT for Models**. Models are always placed directly in `app/Models/`.

---

## Exception Handling

### Exception Handler

**Location**: `app/Exceptions/Handler.php`

The exception handler manages how exceptions are reported and rendered. Currently uses Laravel's default behavior, but can be customized for API-specific error responses.

### Custom Exception Responses

To customize exception responses for API endpoints, modify the `render()` method in `Handler.php`:

```php
public function render($request, Throwable $e)
{
    if ($request->is('api/*') || $request->is('auth/*')) {
        // Return JSON response for API routes
        return response()->json([
            'message' => $e->getMessage(),
            'status' => ApiCodes::INTERNAL_SERVER_ERROR,
        ], ApiCodes::INTERNAL_SERVER_ERROR);
    }

    return parent::render($request, $e);
}
```

---

## Middleware

### Authentication Middleware

All API routes use `auth:api` middleware (Laravel Passport):

```php
Route::group(['middleware' => ['auth:api']], function () {
    // Protected routes
});
```

### Available Middleware

Located in `app/Http/Middleware/`:
- `Authenticate.php` - Handles authentication
- `EncryptCookies.php` - Cookie encryption
- `TrustProxies.php` - Proxy configuration
- `VerifyCsrfToken.php` - CSRF protection (typically disabled for APIs)

### Custom Middleware

To create custom middleware:

```bash
php artisan make:middleware CustomMiddleware
```

Register in `app/Http/Kernel.php` and use in routes.

---

## Key Files Reference

### Core Infrastructure Files

- **`app/Http/Controllers/ApiApp/ApiBaseController.php`**: Base controller for all API controllers. Uses `ApiTrait` for standardized responses.
- **`app/Http/Controllers/AuthApp/AuthBaseController.php`**: Base controller for authentication controllers. Uses `ApiTrait`.
- **`app/Traits/ApiTrait.php`**: Provides standardized response methods (`responseToJson()`, `getJsonResponse()`, `fetchResults()`, etc.).
- **`app/Helpers/ApiCodes.php`**: HTTP status codes and message constants.
- **`app/Providers/RouteServiceProvider.php`**: Registers routes with prefixes (`/api`, `/auth`).

### Resource Module Layout

- **`app/Http/Controllers/ApiApp/<Resources>Controller.php`**: CRUD controller extending `ApiBaseController`
- **`app/Models/<Resource>.php`**: Eloquent model (directly in `app/Models/`)
- **`app/Http/Requests/ApiApp/<Resource>Request.php`**: Base request for filters
- **`app/Http/Requests/ApiApp/Store<Resource>Request.php`**: Store request
- **`app/Http/Requests/ApiApp/Update<Resource>Request.php`**: Update request
- **`app/Http/Resources/ApiApp/<Resource>IndividualResource.php`**: Individual resource
- **`app/Http/Resources/ApiApp/<Resource>ListResource.php`**: List resource

### Authentication Module

- **`app/Http/Controllers/AuthApp/AuthController.php`**: Authentication endpoints (login, logout, refresh token)
- **`app/Models/User.php`**: User model with `revokeToken()` method
- **`routes/auth.php`**: Authentication routes

### Custom Commands

- **`app/Console/Commands/MakeController.php`**: Custom controller command with `--app` option
- **`app/Console/Commands/MakeModel.php`**: Custom model command (places models directly in `app/Models/`)
- **`app/Console/Commands/MakeRequest.php`**: Custom request command with `--app` option
- **`app/Console/Commands/MakeResource.php`**: Custom resource command with `--app` option

---

## Important Decision Points & Clarifications

### When to Use Individual Resource vs List Resource

- **Individual Resource**: Use for single items (`show`, `store`, `update` endpoints)
- **List Resource**: Use for collections (`index` endpoint)
- Both use the same `resourceFields($request)` method, but separating them allows for future customization

### Service Return Format

**Resources format (Recommended for all endpoints):**
- Use `getJsonResponse($items, ResourceClass::class)` which automatically handles:
  - Single items: Uses `ResourceClass::make()`
  - Collections: Uses `ResourceClass::collection()`
- This keeps the response format consistent

### Validation: Where to Apply

- **Form Requests**: Always use Form Requests for controller methods
- **Base Request**: For `index`, `show`, `destroy` (filters, query params)
- **Store Request**: For `store` (required fields)
- **Update Request**: For `update` (use `sometimes` for optional fields)

### Error Handling Strategy

- **Controllers**: Use methods from `ApiTrait` (`resourceNotFound()`, `generalError()`, etc.)
- **Always use ApiCodes**: Never hardcode status codes or messages
- **Consistent responses**: All errors follow the same `{ message, data, meta }` format

### Filtering Strategy

- **All filtering in `index()`**: Never create separate search/filter endpoints
- **Use query parameters**: All filters come from request query parameters
- **Use `fetchResults()`**: Automatically handles pagination and sorting

### Model Location Strategy

- **Models directly in `app/Models/`**: No subfolders, shared across the app
- **Why**: Prevents duplication and allows models to be used by multiple app types

### Query Optimization

- **Eager loading**: Use `Model::with('relation')->get()` to prevent N+1 queries
- **Select specific fields**: Use `select()` when you don't need all columns
- **Indexes**: Add database indexes for frequently queried columns

---

## Example References in the Codebase

- **Examples**: `app/Http/Controllers/ApiApp/ExamplesController.php` (Complete CRUD example)
- **User Model**: `app/Models/User.php` (Model with relationships and custom methods)
- **Auth Controller**: `app/Http/Controllers/AuthApp/AuthController.php` (Authentication patterns)
- **ApiTrait**: `app/Traits/ApiTrait.php` (All response methods)

Use these as live templates when creating new features.

---

## Common Mistakes to Avoid

### ❌ Wrong: Models in Subfolders
```php
// DON'T DO THIS
namespace App\Models\ApiApp;
class Example extends Model { }
```

### ✅ Correct: Models Directly in Models
```php
// DO THIS
namespace App\Models;
class Example extends Model { }
```

### ❌ Wrong: Hardcoded Status Codes
```php
// DON'T DO THIS
return response()->json($data, 200);
```

### ✅ Correct: Use ApiCodes
```php
// DO THIS
return $this->responseToJson(
    ApiCodes::getSuccessMessage(),
    ApiCodes::SUCCESS,
    $data
);
```

### ❌ Wrong: Filtering Outside index()
```php
// DON'T DO THIS
public function search(ExampleRequest $request) {
    // Filtering logic here
}
```

### ✅ Correct: All Filtering in index()
```php
// DO THIS
public function index(ExampleRequest $request) {
    $query = Example::query();
    // All filtering logic here
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }
    return $this->getJsonResponse($this->fetchResults($query), ExampleListResource::class);
}
```

### ❌ Wrong: Wrong Resource Type
```php
// DON'T DO THIS
return $this->getJsonResponse($examples, ExampleIndividualResource::class); // For collection
```

### ✅ Correct: Use Appropriate Resource
```php
// DO THIS
// For collections (index)
return $this->getJsonResponse($examples, ExampleListResource::class);

// For single items (show, store, update)
return $this->getJsonResponse($example, ExampleIndividualResource::class);
```

### ❌ Wrong: Missing Request Validation
```php
// DON'T DO THIS
public function store(Request $request) {
    $example = Example::create($request->all());
}
```

### ✅ Correct: Use Form Requests
```php
// DO THIS
public function store(StoreExampleRequest $request) {
    $example = Example::create($request->all());
}
```

### ❌ Wrong: Returning Models Directly
```php
// DON'T DO THIS
return $example;
```

### ✅ Correct: Use Resources
```php
// DO THIS
return $this->getJsonResponse($example, ExampleIndividualResource::class);
```

---

## Best Practices

1. **Always validate**: Use Form Requests for all controller methods
2. **Always use Resources**: Never return models directly, always wrap in Resources
3. **Consistent error handling**: Use methods from `ApiTrait` for all error responses
4. **Follow naming conventions**: 
   - Controllers: Plural, PascalCase (e.g., `ProductsController`)
   - Models: Singular, PascalCase (e.g., `Product`)
   - Requests: Descriptive (e.g., `StoreProductRequest`)
   - Resources: Descriptive (e.g., `ProductIndividualResource`)
5. **Query optimization**: Use eager loading for relationships: `Model::with('relation')->get()`
6. **Code organization**: Keep controllers thin, move complex logic to service classes if needed
7. **Documentation**: Add comments for complex business logic
8. **Always use ApiCodes**: Never hardcode status codes or messages
9. **All filtering in index()**: Never create separate search/filter endpoints
10. **Models in shared location**: Always place models directly in `app/Models/` (no subfolders)

---

## Important Guidelines Summary

### General
- Check existing similar resources before creating new ones
- For API endpoints, always extend `ApiBaseController`
- All models should be placed directly in `app/Models/` (no subfolders)
- Use Form Requests for all validation
- Always use Resources for responses (never return models directly)
- Use `ApiCodes` constants and methods (never hardcode values)
- Follow existing patterns and conventions

### API Development
- Always extend `ApiBaseController` or `AuthBaseController`
- Always use Form Requests (Base, Store, Update)
- Always use Resources (Individual for single items, List for collections)
- All filtering logic goes in `index()` method
- Use `fetchResults()` for pagination and sorting
- Use `getJsonResponse()` for resource responses
- Use `responseToJson()` with optional 4th parameter for additional data
- All API routes must use `auth:api` middleware
- Always use `ApiCodes` constants and methods

### Models
- Always place models directly in `app/Models/` (no subfolders)
- Always include `$fillable` array for mass assignment protection
- Use `HasFactory` trait for model factories
- Define relationships using standard Eloquent methods
- Use eager loading (`with()`) to prevent N+1 query problems

### Migrations
- Always implement `down()` - make migrations reversible
- Test migrations: test `up()`, test `down()`, test on copy of production data
- Use descriptive names: `CreateProductsTable`, `AddStatusToProducts`, etc.
- One change per migration - keep migrations focused and atomic
- Handle existing data when adding/modifying columns
- Never modify migrations that have run in production - create a new migration instead

---

**Remember**: Always follow these patterns exactly. Consistency is key to maintaining this boilerplate. When in doubt, refer to existing implementations in the codebase as examples.
