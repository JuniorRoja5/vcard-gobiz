<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DemoController extends Controller
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

    // Site Demo
    public function siteDemo(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.demo.index', compact('settings', 'config'));
    }

    // Demo Mode
    public function demoToggle()
    {
        // Default message
        $message = trans('Failed to update!');

        // Update
        try {
            // Get the config data
            $config = DB::table('config')->get();

            // Check if demo mode is enabled from $config[62]->config_value
            if ($config[62]->config_value == 'on') {
                // Disable demo mode
                DB::table('config')->where('config_key', 'demo_mode')->update(['config_value' => 'off']);

                $message = trans('Demo mode disabled!');
            } else {
                // Enable demo mode
                DB::table('config')->where('config_key', 'demo_mode')->update(['config_value' => 'on']);

                $message = trans('Demo mode enabled!');
            }
        } catch (\Exception $e) {
            return back()->with('failed', $e->getMessage())->withInput();
        }

        return redirect()->route('admin.site.demo')->with('success', $message);
    }
}
