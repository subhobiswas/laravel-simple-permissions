# Laravel Simple Permissions

A simple Laravel package for handling roles and permissions. This package allows you to assign roles to users, manage permissions, and easily check for roles and permissions in your application.

## Installation

To install the package, run the following command:

```bash
composer require lazycode/laravel-simple-permissions
```
### Publish Configuration
After installing the package, publish the configuration file using:


```bash
php artisan vendor:publish --provider="Lazycode\Permissions\PermissionsServiceProvider" --tag=config
```

### Run Migrations
Next, run the migrations to create the necessary tables in your database:
```bash
php artisan migrate
```

## Usage
### Setting Up the User Model
In your User model (usually located at app/Models/User.php), ensure you include the HasRolesAndPermissions trait:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Lazycode\Permissions\Traits\HasRolesAndPermissions; // Ensure this is correct

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRolesAndPermissions;

    // Your existing model code...
}

```

### Assigning Roles and Permissions
You can now use the package's functionality in your application. Here are some examples:

#### Assign a Role
To assign a role to a user:

```php
$user = User::find(1); // Replace with the appropriate user ID
$user->assignRole('Admin');
```

#### Get User's Role
To get the user's assigned role:
```php
$role = $user->getRole();
```

#### Check User's Permissions
To check if a user has a specific permission:
```php
if ($user->hasPermission('edit-user')) {
    // The user has permission to edit a user
}

IN blade.php

@hasPermission('edit-user')
    {{ show the content if user has this permission }}
@endhasPermission
```

### Functions List
- `role` : Return the Role collection of modal.
- `hasRole` `@hasRole` : Check Loggedin User has the role.
- `hasAnyRole` `@hasAnyRole` : Checks if the user has any of the specified roles.
- `permission` : Get the permission collection from permission modal.
- `@hasPermission` `@haspermission` : Check Loggedin user has the permission.
- `hasAnyPermission` `@hasAnyPermission` : Checks if the user has any of the specified permissions.
- `hasAllPermission` `@hasAllPermission` : Verifies that the user has all the specified permissions.
- `hasExactPermissions` `@hasExactPermissions` : Checks if the user has exactly the specified permissions.

## Seeding Roles and Permissions
You can create a seeder to start with some default roles and permissions. For example:

```php
use Illuminate\Database\Seeder;
use Lazycode\Permissions\Models\Role;
use Lazycode\Permissions\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $editUserPermission = Permission::create(['name' => 'edit-user']);
        $addUserPermission = Permission::create(['name' => 'add-user']);
        $deleteUserPermission = Permission::create(['name' => 'delete-user']);

        // Create a role
        $adminRole = Role::create(['name' => 'Admin']);

        // Attach permissions to the role
        $adminRole->permissions()->attach([$editUserPermission->id, $addUserPermission->id, $deleteUserPermission->id]);

        // Create a user and assign the role
        $user = User::create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);
        $user->assignRole('Admin');
    }
}
```

## Database Schema
Ensure your database schema is set up correctly. The following tables should exist:

- `roles` : To store roles.
- `permissions` : To store permissions.
- `role_permission` : Pivot table to manage many-to-many relationships between roles and permissions.
- `users` : Ensure there is a `role_id` column in the `users` table to associate users with their roles.

## Conclusion
This package provides a simple way to manage user roles and permissions in your Laravel application. For further customization or additional features, feel free to contribute or reach out for support.
