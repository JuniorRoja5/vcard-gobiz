<?php
namespace Plugins\GoogleAdSense\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoogleAdSenseController extends Controller
{
    public function googleAdSenseSettings(Request $request)
    {
        $settings = Setting::where('id', 1)->first();

        return view()->file(base_path('plugins/GoogleAdSense/Views/index.blade.php'), compact('settings'));
    }

    public function googleAdSenseSettingsUpdate(Request $request)
    {
        Setting::where('id', '1')->update([
            'google_adsense_code' => $request->google_adsense_code
        ]);

        return redirect()->route('admin.plugin.google_adsense.settings')->with('success', __('Google AdSense Settings updated successfully.'));
    }

}
