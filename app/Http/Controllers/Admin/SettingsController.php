<?php
namespace App\Http\Controllers\Admin;

use App\Currency;
use App\Http\Controllers\Controller;
use App\Setting;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
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

    // Setting
    public function settings()
    {
        // Queries
        $timezonelist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $currencies   = Currency::get();
        $settings     = Setting::first();
        $config       = DB::table('config')->get();    

        $image_limit = [
            'SIZE_LIMIT' => env('SIZE_LIMIT', ''),
        ];
        $settings['image_limit'] = $image_limit;

        // Check if application is in maintenance mode
        $isDown = app()->isDownForMaintenance();

        // Get all languages from the config
        $languages = config('app.languages');

        // Define all languages as selected (or you can replace this with any subset of languages)
        $selectedLanguages = array_keys($languages); // This will make all languages selected

        // Get the default language
        $defaultLanguage = config('app.locale');

        return view('admin.pages.settings.index', compact('settings', 'timezonelist', 'currencies', 'config', 'isDown', 'languages', 'selectedLanguages', 'defaultLanguage'));
    }

    // Update General Setting
    public function changeGeneralSettings(Request $request)
    {
        // Check show website
        DB::table('config')->where('config_key', 'show_website')->update([
            'config_value' => $request->show_website,
        ]);

        // Enable/disable registration page
        DB::table('config')->where('config_key', 'registration_page')->update([
            'config_value' => $request->registration_page,
        ]);

        // This will update the languages array in config/app.php file
        $this->updateLanguages($request->languages, $request->default_language);

        // Check timezone
        DB::table('config')->where('config_key', 'timezone')->update([
            'config_value' => $request->timezone,
        ]);

        // Set new values using putenv
        $this->updateEnvFile('TIMEZONE', $request->timezone);

        // Update the date format
        DB::table('config')->where('config_key', 'date_time_format')->update([
            'config_value' => $request->date_time_format,
        ]);

        // Check currency
        DB::table('config')->where('config_key', 'currency')->update([
            'config_value' => $request->currency,
        ]);

        DB::table('config')->where('config_key', 'currency_format_type')->update([
            'config_value' => $request->currency_format,
        ]);

        DB::table('config')->where('config_key', 'currency_decimals_place')->update([
            'config_value' => $request->currency_decimals_place,
        ]);

        // Check plan term
        DB::table('config')->where('config_key', 'term')->update([
            'config_value' => $request->term,
        ]);

        DB::table('config')->where('config_key', 'share_content')->update([
            'config_value' => $request->share_content,
        ]);

        DB::table('config')->where('config_key', 'tiny_api_key')->update([
            'config_value' => $request->tiny_api_key,
        ]);

        // Check cookie consent
        $this->updateEnvFile('COOKIE_CONSENT_ENABLED', $request->cookie);
        $this->updateEnvFile('SIZE_LIMIT', $request->image_limit ?? '5120');

        // Page redirect
        return redirect()->route('admin.settings')->with('success', trans('Updated!'));
    }

    // Update Custom CSS & Scripts
    public function updateCustomScript(Request $request)
    {
        // Queries
        Setting::where('id', '1')->update([
            'custom_css'     => $request->header,
            'custom_scripts' => $request->footer,
        ]);

        // Page redirect
        return redirect()->route('admin.settings')->with('success', trans('Updated!'));
    }

    // Clear cache
    public function clearCache()
    {
        try {
            // Clear application cache
            Cache::flush();

            // Clear caches using Artisan
            Artisan::call('cache:clear');  // Clear application cache
            Artisan::call('route:clear');  // Clear route cache
            Artisan::call('config:clear'); // Clear configuration cache
            Artisan::call('view:clear');   // Clear compiled view files

            // Delete all files in bootstrap/cache except .gitignore
            $cachePath  = base_path('bootstrap/cache');
            $cacheFiles = File::files($cachePath);
            foreach ($cacheFiles as $file) {
                if ($file->getFilename() !== '.gitignore') {
                    File::delete($file);
                }
            }

            // Delete all files in storage/framework/cache except .gitignore
            $cachePath  = base_path('storage/framework/cache');
            $cacheFiles = File::files($cachePath);
            foreach ($cacheFiles as $file) {
                if ($file->getFilename() !== '.gitignore') {
                    File::delete($file);
                }
            }

            // Delete all files in storage/framework/views except .gitignore
            $cachePath  = base_path('storage/framework/views');
            $cacheFiles = File::files($cachePath);
            foreach ($cacheFiles as $file) {
                if ($file->getFilename() !== '.gitignore') {
                    File::delete($file);
                }
            }

            return redirect()->back()->with('success', trans('Application Cache Cleared Successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', trans('Failed to Clear Cache. Due to the following error: ') . ' ' . $e->getMessage());
        }
    }

    public function updateEnvFile($key, $value)
    {
        $envPath = base_path('.env');

        // Check if the .env file exists
        if (file_exists($envPath)) {

            // Read the .env file
            $contentArray = file($envPath);

            // Loop through each line to find the key and update its value
            foreach ($contentArray as &$line) {

                // Split the line by '=' to get key and value
                $parts = explode('=', $line, 2);

                // Check if the key matches and update its value
                if (isset($parts[0]) && $parts[0] === $key) {
                    $line = $key . '=' . $value . PHP_EOL;
                }
            }

            // Implode the array back to a string and write it to the .env file
            $newContent = implode('', $contentArray);
            file_put_contents($envPath, $newContent);

            // Reload the environment variables
            putenv($key . '=' . $value);
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }

    /**
     * This will update the languages array in config/app.php file
     *
     * @param array $languages
     * @return void
     */
    private function updateLanguages(array $languageCodes, string $defaultLanguage)
    {
        // Define a mapping of language codes to full names
        $languageMap = [
            'ar' => 'Arabic',
            'bn' => 'Bangla',
            'bg' => 'Bulgarian',
            'zh' => 'Chinese',
            'nl' => 'Dutch',
            'en' => 'English',
            'fr' => 'French',
            'de' => 'German',
            'ht' => 'Haitian Creole',
            'hi' => 'Hindi',
            'he' => 'Hebrew',
            'hu' => 'Hungarian',
            'id' => 'Indonesian',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'lt' => 'Lithuanian',
            'ms' => 'Malay',
            'pt' => 'Portuguese',
            'pl' => 'Polish',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'es' => 'Spanish',
            'si' => 'Sinhala',
            'sv' => 'Swedish',
            'ta' => 'Tamil',
            'th' => 'Thai',
            'tr' => 'Turkish',
            'ur' => 'Urdu',
            'vi' => 'Vietnamese',
        ];

        // Convert indexed array to associative array using the map
        $languagesArray = [];
        foreach ($languageCodes as $code) {
            if (isset($languageMap[$code])) {
                $languagesArray[$code] = $languageMap[$code];
            }
        }

        // Set the first language as the default locale
        $defaultLocale = $defaultLanguage ?? 'en';

        // Update the languages array in config/app.php
        $this->updateConfigFile($languagesArray, $defaultLocale);
    }

    /**
     * Function to update config/app.php file
     */
    private function updateConfigFile(array $languagesArray, string $defaultLocale)
    {
        $configPath = config_path('app.php');

        // Read the config file
        $configContent = file_get_contents($configPath);

        // Convert the array to a PHP string format with short array syntax
        $newLanguagesArray = var_export($languagesArray, true);
        $newLanguagesArray = str_replace("array (", "[", $newLanguagesArray);
        $newLanguagesArray = str_replace(")", "]", $newLanguagesArray);

        // Replace the existing 'languages' array
        $configContent = preg_replace(
            "/'languages'\s*=>\s*\[[^\]]*\]/",
            "'languages' => " . $newLanguagesArray,
            $configContent
        );

        // Update 'locale' and 'fallback_locale' values
        $configContent = preg_replace(
            "/'locale'\s*=>\s*'[^']*'/",
            "'locale' => '$defaultLocale'",
            $configContent
        );

        $configContent = preg_replace(
            "/'fallback_locale'\s*=>\s*'[^']*'/",
            "'fallback_locale' => '$defaultLocale'",
            $configContent
        );

        // Save the updated content back to config/app.php
        file_put_contents($configPath, $configContent);

        try {
            // Clear application cache
            Cache::flush();

                                           // Clear caches using Artisan
            Artisan::call('cache:clear');  // Clear application cache
            Artisan::call('route:clear');  // Clear route cache
            Artisan::call('config:clear'); // Clear configuration cache
            Artisan::call('view:clear');   // Clear compiled view files

            // Delete all files in bootstrap/cache except .gitignore
            $cachePath  = base_path('bootstrap/cache');
            $cacheFiles = File::files($cachePath);
            foreach ($cacheFiles as $file) {
                if ($file->getFilename() !== '.gitignore') {
                    File::delete($file);
                }
            }

            // Delete all files in storage/framework/cache except .gitignore
            $cachePath  = base_path('storage/framework/cache');
            $cacheFiles = File::files($cachePath);
            foreach ($cacheFiles as $file) {
                if ($file->getFilename() !== '.gitignore') {
                    File::delete($file);
                }
            }

            // Delete all files in storage/framework/views except .gitignore
            $cachePath  = base_path('storage/framework/views');
            $cacheFiles = File::files($cachePath);
            foreach ($cacheFiles as $file) {
                if ($file->getFilename() !== '.gitignore') {
                    File::delete($file);
                }
            }
        } catch (\Exception $e) {
        }
    }
}
