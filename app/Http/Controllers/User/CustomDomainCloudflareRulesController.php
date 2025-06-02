<?php

namespace App\Http\Controllers\User;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CustomDomainCloudflareRulesController extends Controller
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

    // Custom domain cloudflare rules
    public function index(Request $request)
    {
        // Queries
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();

        return view('user.pages.custom-domain-cloudflare-rules.index', compact('config', 'settings'));
    }
}
