<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReferralSystemConfigurationController extends Controller
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
    public function referralSystemConfiguration()
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable referral system
        if ($config[80]->config_value == '0') {
            return abort(404);
        } 
        
        return view('admin.pages.referral-system-configuration.index', compact('settings', 'config'));
    }

    // Update referral system configuration
    public function updateReferralSystemConfiguration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referral_commission_type' => 'required',
            'referral_commission_amount' => 'required',
            'minimum_withdrawal_amount' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update config
        DB::table('config')->where('config_key', 'referral_commission_type')->update([
            'config_value' => $request->referral_commission_type,
        ]);
        DB::table('config')->where('config_key', 'referral_commission_amount')->update([
            'config_value' => $request->referral_commission_amount,
        ]);
        DB::table('config')->where('config_key', 'referral_minimum_withdraw_amount')->update([
            'config_value' => $request->minimum_withdrawal_amount,
        ]);

        // Redirect 
        return redirect()->route('admin.referral.system.configuration')->with('success', __('Updated!'));
    }
}
