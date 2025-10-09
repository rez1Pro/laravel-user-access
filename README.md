# 📦 Laravel UserAccess

A Laravel package to manage **Roles & Permissions** using Models, Enums, and Facade support.  
Easily integrate role-based access control into your application.

---

## 🚀 Installation

### 1. Require the package

```bash
composer require rez1pro/user-access
```

### 2. Service Provider

The service provider is auto-discovered.  
If needed, register manually in `config/app.php`:

```php
'providers' => [
    Rez1pro\UserAccess\UserAccessServiceProvider::class,
]
```

## ⚡ Installer Command

For a one-shot setup:

```bash
php artisan user-access:install --force
php artisan migrate
```

This will:
- Publish **config**
- Publish **migrations**
- Publish **models**
- Publish **enums**
- Run database migrations

---

## 📸 Screenshots

### Installation
![Installation](screenshots/install.png)

### Permission Management
![Permissions](screenshots/all.png)

### When run rollback
![Permission Rollback](screenshots/insert_and_rollback_run.png)

### Permission Management 
![Permission](screenshots/after_rolling_back.png)

---

## 🗄️ Database Schema

The migration will create the following tables:
- `roles`  
- `permissions`  
- `role_has_permissions` (pivot)  

---

## 👤 User Model Integration

Add the `HasPermission` trait to your User model:

```php
use Rez1pro\UserAccess\Traits\HasPermission;

class User extends Authenticatable
{
    use HasPermission;
    
    // ...existing code...
}
```

### User Permission Methods

```php
$user = User::find(1);

// Assign role to user
$user->assignRole('admin');
$user->assignRole(['admin', 'editor']);

// Check if user has role
$user->hasRole('admin'); // true/false
$user->hasAnyRole(['admin', 'editor']); // true/false

// Check if user has permission
$user->hasPermissionTo('create:user'); // true/false
$user->hasPermissionTo(ExamplePermissionEnum::CREATE_USER); // true/false

// Get user roles and permissions
$user->roles; // Collection of roles
$user->permissions; // Collection of permissions through roles

// Remove role from user
$user->removeRole('admin');
```

---

## 🧩 Models

The package ships with:
- `App\Models\Role`
- `App\Models\Permission`

### Role Model Usage

```php
use App\Models\Role;

// Create role
$role = Role::create(['name' => 'Admin']);

// Assign permissions to role
$role->givePermissionTo(ExamplePermissionEnum::CREATE_USER);
$role->givePermissionTo(['create:user', 'edit:user']);

// Check role permissions
$role->hasPermissionTo(ExamplePermissionEnum::CREATE_USER); // true/false

// Get role permissions
$role->permissions; // Collection of permissions

// Remove permission from role
$role->removePermission(ExamplePermissionEnum::CREATE_USER);
```

### Permission Model Usage

```php
use App\Models\Permission;

$permission = Permission::where('name', 'create:user')->first();

// Get all roles that have this permission
$permission->roles; // Collection of roles
```

---

## 🏷️ Enums

Enums are placed in `App\Enums\Permissions`.

Example:

```php
use Rez1pro\UserAccess\Traits\HasAccess;

enum ExamplePermissionEnum: string
{
    use HasAccess;

    case VIEW_EXAMPLE = 'view:example';
    case CREATE_EXAMPLE = 'create:example';
    case EDIT_EXAMPLE = 'edit:example';
    case DELETE_EXAMPLE = 'delete:example';
}
```

Usage:

```php
ExamplePermissionEnum::VIEW_EXAMPLE->value; // "view:example"
```

### Permission Commands

```bash
# Create new permission enums
php artisan permission:create

# Insert all permissions to database
php artisan permission:insert

# Remove permissions from database
php artisan permission:rollback
```

After running rollback command:
```php
namespace App\Enums\Permissions;

use Rez1pro\UserAccess\Traits\HasAccess;

enum ExamplePermissionEnum: string
{
    use HasAccess;

    // case VIEW_EXAMPLE = 'view:example'; // commented by UserAccess package
    case CREATE_EXAMPLE = 'create:example';
}
```

---

## 🎭 Facade

The `UserAccess` Facade provides quick helpers:

```php
use Rez1pro\UserAccess\Facades\UserAccess;

// Get all permissions as array
$permissions = UserAccess::all(); 
// Returns: ['view:example', 'create:example']

// Get permissions grouped by enum
$permissionWithGroups = UserAccess::withGroup();
```

Returns JSON structure:
```json
[
    {
        "name": "Example Permission Enum",
        "permissions": [
            {
                "id": "view:example",
                "name": "VIEW EXAMPLE"
            },
            {
                "id": "create:example",
                "name": "CREATE EXAMPLE"
            }
        ]
    }
]
```

---

## 🔐 Practical Usage Examples

### Complete User Role & Permission Setup

```php
use App\Models\User;
use App\Models\Role;
use App\Enums\Permissions\ExamplePermissionEnum;

// Create a role
$adminRole = Role::create(['name' => 'admin']);

// Assign permissions to role
$adminRole->givePermissionTo([
    ExamplePermissionEnum::VIEW_EXAMPLE,
    ExamplePermissionEnum::CREATE_EXAMPLE,
    ExamplePermissionEnum::EDIT_EXAMPLE
]);

// Assign role to user
$user = User::find(1);
$user->assignRole('admin');

// Check user permissions
if ($user->hasPermissionTo(ExamplePermissionEnum::CREATE_EXAMPLE)) {
    // User can create examples
}

// In your controllers/middleware
if (auth()->user()->hasPermissionTo('edit:example')) {
    // Allow edit action
}
```

### Middleware Usage

```php
// In your routes
Route::middleware(['auth', 'permission:create:user'])->group(function () {
    Route::post('/users', [UserController::class, 'store']);
});

// Or check in controller
public function store(Request $request)
{
    if (!auth()->user()->hasPermissionTo('create:user')) {
        abort(403, 'Unauthorized');
    }
    
    // Create user logic
}
```

---

## 📌 Summary

- `php artisan user-access:install` → Quick setup  
- `UserAccess` Facade → Easy access to roles & permissions  
- `HasPermission` Trait → Add to User model for permission checking
- `Enums` → Strongly typed permissions  
- `Models` → Extendable Role and Permission models
- `Migrations` → Published and customizable

---

## 📝 License

This package is open-sourced software licensed under the [MIT license](LICENSE).