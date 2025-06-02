<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MailgunController extends Controller
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

    // All Mailgun configurations
    public function index()
    {
        // Get Mailgun configuration
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();

        return view('admin.pages.marketing.mailgun.index', compact('config', 'settings'));
    }

    // Update Mailgun configuration
    public function update(Request $request)
    {
        // Update Mailgun configurations
        DB::table('config')->where('config_key', 'mailgun_smtp_username')->update([
            'config_value' => $request->mailgun_username,
        ]);

        DB::table('config')->where('config_key', 'mailgun_smtp_password')->update([
            'config_value' => $request->mailgun_password,
        ]);

        DB::table('config')->where('config_key', 'mailgun_from_address')->update([
            'config_value' => $request->mailgun_from_email,
        ]);

        return redirect()->route('admin.marketing.mailgun')->with('success', trans('Updated!'));
    }
}
