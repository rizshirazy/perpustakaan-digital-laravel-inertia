<?php

namespace App\Http\Middleware;

use App\Models\RouteAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class RouteAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = Route::currentRouteName();

        if (! $routeName) {
            return $next($request);
        }

        $routeAccesses = RouteAccess::where('route_name', $routeName)
            ->with(['role:id,name', 'permission:id,name'])
            ->get();

        if ($routeAccesses->isEmpty()) {
            return $next($request);
        }

        $user = $request->user();

        $hasAccess = $routeAccesses->contains(function (RouteAccess $access) use ($user) {
            $roleName = $access->role?->name;
            $permissionName = $access->permission?->name;

            $requiresRole = filled($roleName);
            $requiresPermission = filled($permissionName);

            if (! $requiresRole && ! $requiresPermission) {
                return false;
            }

            $roleOk = $requiresRole && $user?->hasRole($roleName);
            $permissionOk = $requiresPermission && $user?->hasPermissionTo($permissionName);

            return $roleOk || $permissionOk;
        });

        if (! $hasAccess) {
            $requiredRoles = $routeAccesses->pluck('role.name')->filter()->unique()->values()->all();
            $requiredPermissions = $routeAccesses->pluck('permission.name')->filter()->unique()->values()->all();

            throw UnauthorizedException::forRolesOrPermissions([
                ...$requiredRoles,
                ...$requiredPermissions,
            ]);
        }

        return $next($request);
    }
}
