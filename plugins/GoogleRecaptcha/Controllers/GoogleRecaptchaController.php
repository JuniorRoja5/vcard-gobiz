<?php
namespace Plugins\GoogleRecaptcha\Controllers;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;

class GoogleRecaptchaController extends Controller
{
    public function googleRecaptchaSettings(Request $request)
    {
        $settings = Setting::where('id', 1)->first();

        $recaptcha_configuration = [
            'RECAPTCHA_ENABLE'     => env('RECAPTCHA_ENABLE', 'off'),
            'RECAPTCHA_SITE_KEY'   => env('RECAPTCHA_SITE_KEY', ''),
            'RECAPTCHA_SECRET_KEY' => env('RECAPTCHA_SECRET_KEY', ''),
        ];

        return view()->file(base_path('plugins/GoogleRecaptcha/Views/index.blade.php'), compact('settings', 'recaptcha_configuration'));
    }

    public function googleRecaptchaSettingsUpdate(Request $request)
    {

        $this->updateEnv('RECAPTCHA_ENABLE', $request->recaptcha_enable);
        $this->updateEnv('RECAPTCHA_SITE_KEY', $request->recaptcha_site_key);
        $this->updateEnv('RECAPTCHA_SECRET_KEY', $request->recaptcha_secret_key);

        return redirect()->route('admin.plugin.google_recaptcha.settings')->with('success', __('Google reCAPTCHA Settings updated successfully.'));
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
