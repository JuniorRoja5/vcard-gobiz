<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PusherController extends Controller
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

    // Pusher Configuration
    public function index()
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.marketing.pusher-notification.configuration.index', compact('settings', 'config'));
    }

    // Update Pusher Configuration
    public function update(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'enable_beams' => 'required|string|max:255',
            'beams_instance_id' => 'required|string|max:255',
            'beams_secret_key' => 'required|string|max:255'
        ]);

        // Check if the required keys are missing
        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Check if "PUSHER_BEAMS_ENABLED", "PUSHER_BEAMS_INSTANCE_ID" and "PUSHER_BEAMS_SECRET_KEY" are set in the .env file
        if (!env('PUSHER_BEAMS_ENABLED') || !env('PUSHER_BEAMS_INSTANCE_ID') || !env('PUSHER_BEAMS_SECRET_KEY')) {
            // Insert Pusher Configuration into the .env file
            $envFile = base_path('.env');

            // Read the current .env file
            $env = file_get_contents($envFile);

            // Check if each key exists and set default values if missing
            if (!env('PUSHER_BEAMS_ENABLED')) {
                $env .= "\n\nPUSHER_BEAMS_ENABLED=" . $request->enable_beams;
            }

            if (!env('PUSHER_BEAMS_INSTANCE_ID')) {
                $env .= "\nPUSHER_BEAMS_INSTANCE_ID=" . $request->beams_instance_id;
            }

            if (!env('PUSHER_BEAMS_SECRET_KEY')) {
                $env .= "\nPUSHER_BEAMS_SECRET_KEY=" . $request->beams_secret_key;
            }

            // Save the updated .env file
            file_put_contents($envFile, $env);
        }

        // Update Pusher Configuration from .env file
        $this->updateEnvFile('PUSHER_BEAMS_ENABLED', '"' . str_replace('"', "'", $request->enable_beams) . '"');
        $this->updateEnvFile('PUSHER_BEAMS_INSTANCE_ID', '"' . str_replace('"', "'", $request->beams_instance_id) . '"');
        $this->updateEnvFile('PUSHER_BEAMS_SECRET_KEY', '"' . str_replace('"', "'", $request->beams_secret_key) . '"');

        return redirect()->route('admin.marketing.pusher')->with('success', trans('Updated!'));
    }

    // Update .env file
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
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
