<?php
namespace Plugins\TawkChat\Controllers;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;

class TawkChatController extends Controller
{
    public function tawkChatSettings(Request $request)
    {
        $settings = Setting::where('id', 1)->first();

        return view()->file(base_path('plugins/TawkChat/Views/index.blade.php'), compact('settings'));
    }

    public function tawkChatSettingsUpdate(Request $request)
    {

        Setting::where('id', 1)->update([
            'tawk_chat_bot_key' => $request->tawk_chat_bot_key,
        ]);

        return redirect()->route('admin.plugin.tawkchat.settings')->with('success', __('Tawk.to Settings updated successfully.'));
    }

}
