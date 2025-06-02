<?php

namespace App\Http\Controllers\Admin;

use ZipArchive;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request as serverReq;

class UpdateController extends Controller
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

    // Check
    public function check()
    {
        // Queries
        $settings = Setting::first();
        $purchase_code = config('app.code');
        $config = DB::table('config')->get();

        // Default message
        $resp_data = [];
        $errorMessage = trans('Something went wrong! Please contact author support team.');
        $server_name = serverReq::server("SERVER_NAME");
        $server_name = $server_name ? $server_name : "LOCAL.TEST";

        try {
            // Check update validator
            $client = new \GuzzleHttp\Client();
            $res = $client->post('https://verify.nativecode.in/check-update', [
                'form_params' => [
                    'purchase_code' => $purchase_code,
                    'server_name' => $server_name,
                    'version' => $config[32]->config_value
                ]
            ]);

            $resp_data = json_decode($res->getBody(), true);
        } catch (\Throwable $th) {
            //throw $th;
        }

        // Check support expiry
        $supportStatusMessage = "";

        if ($resp_data) {

            // Check support expiry
            if (isset($resp_data['support_remaining_days']) && $resp_data['support_remaining_days'] <= 0) {
                $supportStatusMessage = '<div class="alert alert-important alert-danger alert-dismissible mt-3" role="alert">
                    <h2 class="mb-1">'. trans('Your support plan has ended!').'</h2>
                    <p>'. trans('Renew now to continue enjoying priority support, updates, and uninterrupted access to exclusive features.') .'</p>
                    <div class="btn-list">
                        <a href="https://store.nativecode.in?ref=' . urlencode(config("app.url")) . '&size=source" target="_blank" class="btn btn-xs btn-light">'. trans('Renew Now').'</a>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>';
            }

            if ($resp_data['status'] == true) {
                // Queries
                $settings = Setting::first();
                $purchase_code = config('app.code');

                // Response
                $response = ['message' => $resp_data['message'], 'version' => $resp_data['version'], 'update' => $resp_data['update'], 'notes' => $resp_data['notes'], 'license' => $resp_data['license'], 'support_status_message' => $supportStatusMessage];
            
                return view('admin.pages.update.index', compact('response', 'settings', 'purchase_code', 'config'));
            } else {
                $errorMessage = $resp_data['message'];

                return view('admin.pages.update.index', compact('settings', 'purchase_code', 'config'))->with('failed', trans($errorMessage));
            }
        } else {
            return view('admin.pages.update.index', compact('settings', 'purchase_code', 'config'))->with('failed', trans($errorMessage));
        }
    }

    // Check Update
    public function checkUpdate(Request $request)
    {
        // Queries
        $config = DB::table('config')->get();

        // Default message
        $resp_data = [];
        $errorMessage = trans('Something went wrong! Please contact author support team.');
        $server_name = serverReq::server("SERVER_NAME");
        $server_name = $server_name ? $server_name : "LOCAL.TEST";

        try {
            // Check update validator
            $client = new \GuzzleHttp\Client();
            $res = $client->post('https://verify.nativecode.in/check-update', [
                'form_params' => [
                    'purchase_code' => $request->purchase_code,
                    'server_name' => $server_name,
                    'version' => $config[32]->config_value
                ]
            ]);

            $resp_data = json_decode($res->getBody(), true);
        } catch (\Throwable $th) {
            //throw $th;
        }

        // Default message
        $supportStatusMessage = "";

        if ($resp_data) {
            if ($resp_data['status'] == true) {

                // Check support expiry
                if (isset($resp_data['support_remaining_days']) && $resp_data['support_remaining_days'] <= 0) {
                    $supportStatusMessage = '<div class="alert alert-important alert-danger alert-dismissible mt-3" role="alert">
                        <h2 class="mb-1">'. trans('Your support plan has ended!').'</h2>
                        <p>'. trans('Renew now to continue enjoying priority support, updates, and uninterrupted access to exclusive features.') .'</p>
                        <div class="btn-list">
                            <a href="https://store.nativecode.in?ref=' . urlencode(config("app.url")) . '&size=source" target="_blank" class="btn btn-xs btn-light">'. trans('Renew Now').'</a>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>';
                }

                // Queries
                $settings = Setting::first();
                $purchase_code = config('app.code');
                // Response
                $response = ['message' => $resp_data['message'], 'version' => $resp_data['version'], 'update' => $resp_data['update'], 'notes' => $resp_data['notes'], 'license' => $resp_data['license'], 'support_status_message' => $supportStatusMessage];
                
                return view('admin.pages.update.index', compact('response', 'settings', 'purchase_code', 'config'));
            } else {
                $errorMessage = $resp_data['message'];
                return redirect()->route('admin.check')->with('failed', trans($errorMessage));
            }
        } else {
            return redirect()->route('admin.check')->with('failed', trans('The system is already in the latest version.'));
        }
    }

    // Update code
    public function updateCode(Request $request)
    {
        // Queries
        $config = DB::table('config')->get();
        // Default message
        $resp_data = [];
        $errorMessage = trans('Something went wrong! Please contact author support team. URL: https://support.nativecode.in');
        $server_name = serverReq::server("SERVER_NAME");
        $server_name = $server_name ? $server_name : "LOCAL.TEST";

        try {
            // Check update validator
            $client = new \GuzzleHttp\Client();
            $res = $client->post('https://verify.nativecode.in/update-code', [
                'form_params' => [
                    'purchase_code' => config('app.code'),
                    'server_name' => $server_name,
                    'version' => $config[32]->config_value
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('admin.check')->with('failed', trans('The system is already in the latest version.'));
        }

        // Get status code is "200
        if ($res->getStatusCode() == 200) {
            // Get file
            $download = uniqid();
            file_put_contents(public_path($download . '.zip'), $res->getBody());

            // ZipArchive
            $unzip = new ZipArchive;
            $out = $unzip->open($download . '.zip');

            if ($out === TRUE) {
                // Exact zip
                $unzip->extractTo('../');
                $unzip->close();
                // Delete zip
                unlink($download . '.zip');

                // Update version
                DB::table('config')->where('config_key', 'app_version')->update([
                    'config_value' => $request->app_version,
                ]);

                $filecode = str_replace(".", "", $request->app_version);
                if (file_exists(app_path("./Classes/GoBizUpdater$filecode.php"))) {
                    $baseClassName = "\App\Classes\GoBizUpdater";
                    $dynamicClassName = $baseClassName . $filecode;
                    if (class_exists($dynamicClassName)) {
                        $dynamicClass = new $dynamicClassName();
                        $dynamicClass->runUpdate();
                    }

                    // After update, remove the GoBizUpdater$filecode.php
                    unlink(app_path("./Classes/GoBizUpdater$filecode.php"));
                }

                try {
                    // Clear application cache
                    Cache::flush();

                    // Clear caches using Artisan
                    Artisan::call('cache:clear');   // Clear application cache
                    Artisan::call('route:clear');   // Clear route cache
                    Artisan::call('config:clear');  // Clear configuration cache
                    Artisan::call('view:clear');    // Clear compiled view files

                    // Delete all files in bootstrap/cache except .gitignore
                    $cachePath = base_path('bootstrap/cache');
                    $cacheFiles = File::files($cachePath);
                    foreach ($cacheFiles as $file) {
                        if ($file->getFilename() !== '.gitignore') {
                            File::delete($file);
                        }
                    }
                } catch (\Exception $e) {
                }

                // Success message and redirect
                return redirect()->route('admin.check')->with('success', trans('Hurray! The latest version was updated successfully.'));
            } else {
                // Failed message and redirect
                return redirect()->route('admin.check')->with('failed', trans('Installation failed.'));
            }
        } else {
            // Success message and redirect
            $resp_data = json_decode($res->getBody(), true);
            return redirect()->route('admin.check')->with('failed', $resp_data['message']);
        }

        // Failed message and redirect
        return redirect()->route('admin.check')->with('success', trans('Purchase code verification failed!'));
    }
}
