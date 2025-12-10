<?php

namespace Database\Seeders;

use App\Models\RouteAccess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RouteAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define route accesses with their permissions and roles
        $routeAccesses = [
            // Loan Statistics
            [
                'route_name' => 'admin.loan-statistics.index',
                'permission' => 'view_loan_statistics',
                'roles' => ['admin', 'operator'],
            ],

            // Fine Reports
            [
                'route_name' => 'admin.fine-reports.index',
                'permission' => 'view_fine_reports',
                'roles' => ['admin', 'operator'],
            ],

            // Book Stock Reports
            [
                'route_name' => 'admin.book-stock-reports.index',
                'permission' => 'view_book_stock_reports',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.book-stock-reports.edit',
                'permission' => 'edit_book_stock_reports',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.book-stock-reports.update',
                'permission' => 'edit_book_stock_reports',
                'roles' => ['admin'],
            ],

            // Categories (Resource: index, create, store, show, edit, update, destroy)
            [
                'route_name' => 'admin.categories.index',
                'permission' => 'view_categories',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.categories.create',
                'permission' => 'create_categories',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.categories.store',
                'permission' => 'create_categories',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.categories.show',
                'permission' => 'view_categories',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.categories.edit',
                'permission' => 'edit_categories',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.categories.update',
                'permission' => 'edit_categories',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.categories.destroy',
                'permission' => 'delete_categories',
                'roles' => ['admin'],
            ],

            // Publishers (Resource: index, create, store, show, edit, update, destroy)
            [
                'route_name' => 'admin.publishers.index',
                'permission' => 'view_publishers',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.publishers.create',
                'permission' => 'create_publishers',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.publishers.store',
                'permission' => 'create_publishers',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.publishers.show',
                'permission' => 'view_publishers',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.publishers.edit',
                'permission' => 'edit_publishers',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.publishers.update',
                'permission' => 'edit_publishers',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.publishers.destroy',
                'permission' => 'delete_publishers',
                'roles' => ['admin'],
            ],

            // Books (Resource: index, create, store, show, edit, update, destroy)
            [
                'route_name' => 'admin.books.index',
                'permission' => 'view_books',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.books.create',
                'permission' => 'create_books',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.books.store',
                'permission' => 'create_books',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.books.show',
                'permission' => 'view_books',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.books.edit',
                'permission' => 'edit_books',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.books.update',
                'permission' => 'edit_books',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.books.destroy',
                'permission' => 'delete_books',
                'roles' => ['admin'],
            ],

            // Users (Resource: index, create, store, show, edit, update, destroy)
            [
                'route_name' => 'admin.users.index',
                'permission' => 'view_users',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.users.create',
                'permission' => 'create_users',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.users.store',
                'permission' => 'create_users',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.users.show',
                'permission' => 'view_users',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.users.edit',
                'permission' => 'edit_users',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.users.update',
                'permission' => 'edit_users',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.users.destroy',
                'permission' => 'delete_users',
                'roles' => ['admin'],
            ],

            // Fine Settings
            [
                'route_name' => 'admin.fine-settings.edit',
                'permission' => 'manage_fine_settings',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.fine-settings.update',
                'permission' => 'manage_fine_settings',
                'roles' => ['admin'],
            ],

            // Loans (Resource: index, create, store, show, edit, update, destroy)
            [
                'route_name' => 'admin.loans.index',
                'permission' => 'view_loans',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.loans.create',
                'permission' => 'create_loans',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.loans.store',
                'permission' => 'create_loans',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.loans.show',
                'permission' => 'view_loans',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.loans.edit',
                'permission' => 'edit_loans',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.loans.update',
                'permission' => 'edit_loans',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.loans.destroy',
                'permission' => 'delete_loans',
                'roles' => ['admin'],
            ],

            // Return Books
            [
                'route_name' => 'admin.return-books.index',
                'permission' => 'view_return_books',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.return-books.create',
                'permission' => 'create_return_books',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.return-books.store',
                'permission' => 'create_return_books',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.return-books.approve',
                'permission' => 'approve_return_books',
                'roles' => ['admin'],
            ],

            // Fines
            [
                'route_name' => 'admin.fines.show',
                'permission' => 'view_fines',
                'roles' => ['admin', 'operator'],
            ],

            // Announcements (Resource: index, create, store, show, edit, update, destroy)
            [
                'route_name' => 'admin.announcements.index',
                'permission' => 'view_announcements',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.announcements.create',
                'permission' => 'create_announcements',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.announcements.store',
                'permission' => 'create_announcements',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.announcements.show',
                'permission' => 'view_announcements',
                'roles' => ['admin', 'operator'],
            ],
            [
                'route_name' => 'admin.announcements.edit',
                'permission' => 'edit_announcements',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.announcements.update',
                'permission' => 'edit_announcements',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.announcements.destroy',
                'permission' => 'delete_announcements',
                'roles' => ['admin'],
            ],

            // Roles (Resource: index, create, store, show, edit, update, destroy)
            [
                'route_name' => 'admin.roles.index',
                'permission' => 'manage_roles',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.roles.create',
                'permission' => 'manage_roles',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.roles.store',
                'permission' => 'manage_roles',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.roles.show',
                'permission' => 'manage_roles',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.roles.edit',
                'permission' => 'manage_roles',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.roles.update',
                'permission' => 'manage_roles',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.roles.destroy',
                'permission' => 'manage_roles',
                'roles' => ['admin'],
            ],

            // Permissions (Resource: index, create, store, show, edit, update, destroy)
            [
                'route_name' => 'admin.permissions.index',
                'permission' => 'manage_permissions',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.permissions.create',
                'permission' => 'manage_permissions',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.permissions.store',
                'permission' => 'manage_permissions',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.permissions.show',
                'permission' => 'manage_permissions',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.permissions.edit',
                'permission' => 'manage_permissions',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.permissions.update',
                'permission' => 'manage_permissions',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.permissions.destroy',
                'permission' => 'manage_permissions',
                'roles' => ['admin'],
            ],

            // Assign Permissions
            [
                'route_name' => 'admin.assign-permissions.index',
                'permission' => 'manage_permissions',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.assign-permissions.edit',
                'permission' => 'manage_permissions',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.assign-permissions.update',
                'permission' => 'manage_permissions',
                'roles' => ['admin'],
            ],

            // Assign Users
            [
                'route_name' => 'admin.assign-users.index',
                'permission' => 'manage_users',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.assign-users.edit',
                'permission' => 'manage_users',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.assign-users.update',
                'permission' => 'manage_users',
                'roles' => ['admin'],
            ],

            // Route Accesses (Resource: index, create, store, show, edit, update, destroy)
            [
                'route_name' => 'admin.route-accesses.index',
                'permission' => 'manage_route_access',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.route-accesses.create',
                'permission' => 'manage_route_access',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.route-accesses.store',
                'permission' => 'manage_route_access',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.route-accesses.show',
                'permission' => 'manage_route_access',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.route-accesses.edit',
                'permission' => 'manage_route_access',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.route-accesses.update',
                'permission' => 'manage_route_access',
                'roles' => ['admin'],
            ],
            [
                'route_name' => 'admin.route-accesses.destroy',
                'permission' => 'manage_route_access',
                'roles' => ['admin'],
            ],

            // Member Permissions (for public/member area)
            [
                'route_name' => 'dashboard',
                'permission' => 'view_dashboard',
                'roles' => ['member', 'admin', 'operator'],
            ],
            [
                'route_name' => 'front.books.index',
                'permission' => 'view_books',
                'roles' => ['member'],
            ],
            [
                'route_name' => 'front.books.show',
                'permission' => 'view_books',
                'roles' => ['member'],
            ],
            [
                'route_name' => 'front.categories.index',
                'permission' => 'view_categories',
                'roles' => ['member'],
            ],
            [
                'route_name' => 'front.categories.show',
                'permission' => 'view_categories',
                'roles' => ['member'],
            ],
            [
                'route_name' => 'front.loans.index',
                'permission' => 'view_loans',
                'roles' => ['member'],
            ],
            [
                'route_name' => 'front.loans.show',
                'permission' => 'view_loans',
                'roles' => ['member'],
            ],
            [
                'route_name' => 'front.loans.store',
                'permission' => 'create_loans',
                'roles' => ['member'],
            ],
            [
                'route_name' => 'front.return-books.index',
                'permission' => 'view_return_books',
                'roles' => ['member'],
            ],
            [
                'route_name' => 'front.return-books.show',
                'permission' => 'view_return_books',
                'roles' => ['member'],
            ],
            [
                'route_name' => 'front.return-books.store',
                'permission' => 'create_return_books',
                'roles' => ['member'],
            ],
            [
                'route_name' => 'front.fines.index',
                'permission' => 'view_fines',
                'roles' => ['member'],
            ],
        ];

        // Get all roles
        $roles = Role::all()->keyBy('name');

        // Create permissions and route accesses
        foreach ($routeAccesses as $access) {
            // Create or get permission
            $permission = Permission::firstOrCreate(['name' => $access['permission']]);

            // Create route access entries for each role
            foreach ($access['roles'] as $roleName) {
                if (isset($roles[$roleName])) {
                    RouteAccess::firstOrCreate([
                        'route_name'    => $access['route_name'],
                        'permission_id' => $permission->id,
                    ]);

                    // Attach permission to role if not already attached
                    if (!$roles[$roleName]->hasPermissionTo($permission)) {
                        $roles[$roleName]->givePermissionTo($permission);
                    }
                }
            }
        }
    }
}
