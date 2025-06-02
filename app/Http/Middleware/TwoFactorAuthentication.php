<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the table exists
        if (Schema::hasTable('two_factor_auth_settings')) {
            // Fetch the two-factor authentication settings
            $twoFactorSettings = DB::table('two_factor_auth_settings')->first();

            // Check if two-factor authentication is enabled and the user has not completed it
            if ($twoFactorSettings && isset($twoFactorSettings->status) && $twoFactorSettings->status == 1 && ! session()->get('two_factor_authentication') && File::exists(base_path('plugins/TwoFactorAuthentication/plugin.json')) && ! empty(env('MAIL_USERNAME')) && ! empty(env('MAIL_PASSWORD'))) {
                // Redirect to the two-factor authentication page
                return redirect()->route('send.otp');
            } else {
                return $next($request);
            }
        } else {
            return $next($request);
        }
    }
}
