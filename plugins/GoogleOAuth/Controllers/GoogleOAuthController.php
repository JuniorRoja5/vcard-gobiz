<?php
namespace Plugins\GoogleOAuth\Controllers;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;

class GoogleOAuthController extends Controller
{
    public function googleOAuthSettings(Request $request)
    {
        $settings = Setting::where('id', 1)->first();

        $google_configuration = [
            'GOOGLE_ENABLE'        => env('GOOGLE_ENABLE', 'off'),
            'GOOGLE_CLIENT_ID'     => env('GOOGLE_CLIENT_ID', ''),
            'GOOGLE_CLIENT_SECRET' => env('GOOGLE_CLIENT_SECRET', ''),
            'GOOGLE_REDIRECT'      => env('GOOGLE_REDIRECT', ''),
        ];

        return view()->file(base_path('plugins/GoogleOAuth/Views/index.blade.php'), compact('settings', 'google_configuration'));
    }

    public function googleOAuthSettingsUpdate(Request $request)
    {

        $this->updateEnv('GOOGLE_ENABLE', $request->google_auth_enable);
        $this->updateEnv('GOOGLE_CLIENT_ID', '"' . str_replace('"', "'", $request->google_client_id) . '"');
        $this->updateEnv('GOOGLE_CLIENT_SECRET', '"' . str_replace('"', "'", $request->google_client_secret) . '"');
        $this->updateEnv('GOOGLE_REDIRECT', '"' . str_replace('"', "'", $request->google_redirect) . '"');

        return redirect()->route('admin.plugin.google_oauth.settings')->with('success', __('Google OAuth Settings updated successfully.'));
    }

    public function updateEnv($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            // Read the file contents
            $envContent = file_get_contents($path);

            // Create a new key-value pair
            $pattern     = "/^" . preg_quote($key) . "=.*/m";
            $replacement = $key . '=' . $value;

            // Check if the key exists in .env
            if (preg_match($pattern, $envContent)) {
                // Replace existing key
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                // Append new key-value pair
                $envContent .= "\n" . $replacement;
            }

            // Write back to .env file
            file_put_contents($path, $envContent);
        }
    }

}
