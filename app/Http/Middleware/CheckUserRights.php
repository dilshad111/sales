<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRights
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin has access to everything
        if ($user->role === 'Admin') {
            return $next($request);
        }

        $routeName = $request->route()->getName();
        $menuConfig = config('menu_permissions.menus');

        // Find which menu key this route belongs to
        $menuKey = null;
        foreach ($menuConfig as $key => $config) {
            $baseRoute = explode('.', $config['route'])[0];
            if ($routeName === $config['route'] || 
                $routeName === $baseRoute || 
                str_starts_with($routeName, $baseRoute . '.') ||
                (isset($config['active_on']) && $request->is($config['active_on']))) {
                $menuKey = $key;
                break;
            }
        }

        // If route is part of a protected menu, check permissions
        if ($menuKey && !$user->hasMenuPermission($menuKey)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access ' . ($menuConfig[$menuKey]['label'] ?? 'this section') . '.');
        }

        return $next($request);
    }
}
