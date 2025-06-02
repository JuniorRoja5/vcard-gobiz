<?php
namespace Plugins\ReferralSystem\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReferralSystemController extends Controller
{
    // Enable/Disable Referral System
    public function index()
    {
        // Queries
        $settings = Setting::active()->first();
        $config   = DB::table('config')->get();

        // Update Enable/Disable NFC Card Orders
        return view()->file(base_path('plugins/ReferralSystem/Views/index.blade.php'), compact('settings', 'config'));
    }

    // Update Enable/Disable Referral System
    public function update(Request $request)
    {
        // Check if the form is valid
        $referralSystem = $request->enable_referral_system == '1' ? 1 : 0;

        // Update the database
        DB::table('config')->where('config_key', 'referral_system')->update(['config_value' => $referralSystem]);

        return redirect()->route('admin.plugin.referral.system')->with('success', trans('Referral System updated successfully.'));
    }
}
