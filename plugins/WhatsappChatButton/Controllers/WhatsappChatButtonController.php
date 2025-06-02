<?php
namespace Plugins\WhatsappChatButton\Controllers;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhatsappChatButtonController extends Controller
{
    public function whatsappChatButtonSettings(Request $request)
    {
        $whatsapp_settings = DB::table('config')->get();

        $settings = Setting::where('id', 1)->first();

        return view()->file(base_path('plugins/WhatsappChatButton/Views/index.blade.php'), compact('whatsapp_settings', 'settings'));
    }

    public function whatsappChatButtonSettingsUpdate(Request $request)
    {
        DB::table('config')->where('config_key', 'show_whatsapp_chatbot')->update([
            'config_value' => $request->show_whatsapp_chatbot,
        ]);

        DB::table('config')->where('config_key', 'whatsapp_chatbot_mobile_number')->update([
            'config_value' => $request->whatsapp_chatbot_mobile_number,
        ]);

        DB::table('config')->where('config_key', 'whatsapp_chatbot_message')->update([
            'config_value' => $request->whatsapp_chatbot_message,
        ]);


        return redirect()->route('admin.plugin.whatsapp_chat_button.settings')->with('success', __('Whatsapp Chat Button Settings updated successfully.'));
    }

}
