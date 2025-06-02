<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    // Site Maintenance
    public function siteMaintenance(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.settings.index', compact('settings', 'config'));
    }

    // Maintenance Mode
    public function maintenanceToggle(Request $request)
    {
        $envFile = base_path('.env');

        if ($request->maintenance_mode == "0") {
            // The app is in maintenance mode
            try {
                // Clear the MAINTENANCE_SECRET_CODE in the .env file
                $env = file_get_contents($envFile);

                // Remove existing MAINTENANCE_SECRET_CODE
                $env = preg_replace('/MAINTENANCE_SECRET_CODE=.*/', 'MAINTENANCE_SECRET_CODE=', $env);
                file_put_contents($envFile, $env);

                // Bring the application up
                Artisan::call('up');

                // Clear the cache
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('view:clear');
                Artisan::call('route:clear');

                // Success message
                $status = 'success';
                $message = trans('Maintenance Mode Disabled');
            } catch (\Exception $e) {
                // Error message
                $status = 'failed';
                $message = trans('Failed to Disable Maintenance Mode');
            }

            return redirect()->route('admin.settings')->with($status, trans($message));
        } else {
            // Generate a new secret key
            $secret = Str::uuid();

            try {
                $env = file_get_contents($envFile);

                // Check if MAINTENANCE_SECRET_CODE already exists
                if (strpos($env, 'MAINTENANCE_SECRET_CODE=') !== false) {
                    // Update existing MAINTENANCE_SECRET_CODE
                    $env = preg_replace('/MAINTENANCE_SECRET_CODE=.*/', 'MAINTENANCE_SECRET_CODE=' . $secret, $env);
                } elseif (strpos($env, 'PURCHASE_CODE=') !== false) {
                    // Insert MAINTENANCE_SECRET_CODE after PURCHASE_CODE
                    $env = preg_replace('/(PURCHASE_CODE=.*\n)/', "$1MAINTENANCE_SECRET_CODE=" . $secret . "\n", $env);
                } else {
                    // Add PURCHASE_CODE and MAINTENANCE_SECRET_CODE at the end if PURCHASE_CODE is missing
                    $env .= "\nPURCHASE_CODE=\nMAINTENANCE_SECRET_CODE=" . $secret;
                }
                file_put_contents($envFile, $env);

                // Bring the application down with a maintenance page and the secret
                Artisan::call('down', [
                    '--render' => 'maintenance',
                    '--secret' => $secret
                ]);

                // Clear the cache
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('view:clear');
                Artisan::call('route:clear');
            } catch (\Exception $e) {
                return redirect()->route('admin.settings')->with('failed', trans('Failed to Enable Maintenance Mode'));
            }

            // Redirect to the home page with the secret key
            return redirect()->to('/' . $secret);
        }
    }
}
