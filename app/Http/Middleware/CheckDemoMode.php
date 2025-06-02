<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDemoMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Exclude the demo route from this middleware
        if ($request->routeIs('admin.site.demo') || $request->routeIs('admin.demo.toggle') || $request->routeIs('admin.dashboard')) {
            return $next($request);
        }

        // Fetch the configuration value directly
        $demoMode = DB::table('config')->where('config_key', 'demo_mode')->value('config_value');

        if ($demoMode === 'on') {
            // Check authentication
            if (!Auth::check()) {
                return redirect()->route('admin.login')->with('failed', trans('You are not authorized to access this page.'));
            }
            // Check login user is admin
            if (Auth::user()->role_id != 2) {
                return redirect()->route('admin.dashboard')->with('failed', trans('Demo mode is enabled. So, you can not perform this action.'));
            } else {
                return redirect()->route('user.dashboard')->with('failed', trans('Demo mode is enabled. So, you can not perform this action.'));
            }
        }

        return $next($request);
    }
}
