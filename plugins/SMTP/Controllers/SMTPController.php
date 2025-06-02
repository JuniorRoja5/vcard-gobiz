<?php
namespace Plugins\SMTP\Controllers;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SMTPController extends Controller
{
    public function smtpSettings(Request $request)
    {
        $settings = Setting::where('id', 1)->first();

        $email_configuration = [
            'driver'     => env('MAIL_MAILER', 'smtp'),
            'host'       => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port'       => env('MAIL_PORT', 587),
            'username'   => env('MAIL_USERNAME'),
            'password'   => env('MAIL_PASSWORD'),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'address'    => env('MAIL_FROM_ADDRESS'),
            'name'       => env('MAIL_FROM_NAME', $settings->site_name),
        ];

        $config = DB::table('config')->get();

        return view()->file(base_path('plugins/SMTP/Views/index.blade.php'), compact('settings', 'email_configuration', 'config'));
    }

    public function smtpSettingsUpdate(Request $request)
    {
        // Mail username
        $mailDriver = str_replace('"', "", $request->mail_driver);
        $mailDriver = str_replace("'", "", $mailDriver);

        // Mail host
        $mailHost = str_replace('"', "", $request->mail_host);
        $mailHost = str_replace("'", "", $mailHost);

        // Mail port
        $mailPort = str_replace('"', "", $request->mail_port);
        $mailPort = str_replace("'", "", $mailPort);

        // Mail username
        $userName = str_replace('"', "", $request->mail_username);
        $userName = str_replace("'", "", $userName);

        // Mail password
        $password = str_replace('"', "", $request->mail_password);
        $password = str_replace("'", "", $password);

        // Mail encryption
        $mailEncryption = str_replace('"', "", $request->mail_encryption);
        $mailEncryption = str_replace("'", "", $mailEncryption);

        // Mail email
        $senderEmail = str_replace('"', "", $request->mail_address);
        $senderEmail = str_replace("'", "", $senderEmail);

        // Mail sender name
        $mailSenderName = str_replace('"', "", $request->mail_sender);
        $mailSenderName = str_replace("'", "", $mailSenderName);

        // Set new values using putenv (google login)
        $this->updateEnvFile('MAIL_MAILER', $mailDriver);
        $this->updateEnvFile('MAIL_HOST', $mailHost);
        $this->updateEnvFile('MAIL_PORT', $mailPort);
        $this->updateEnvFile('MAIL_USERNAME', $userName);
        $this->updateEnvFile('MAIL_PASSWORD', '"' . $password . '"');
        // Check mail encryption
        $this->updateEnvFile('MAIL_ENCRYPTION', $mailEncryption);
        $this->updateEnvFile('MAIL_FROM_ADDRESS', $senderEmail);
        $this->updateEnvFile('MAIL_FROM_NAME', '"' . $mailSenderName . '"');

        // User Email Verification Syetem
        DB::table('config')->where('config_key', 'disable_user_email_verification')->update([
            'config_value' => $request->disable_user_email_verification,
        ]);

        return redirect()->route('admin.plugin.smtp.settings')->with('success', __('SMTP Settings updated successfully.'));
    }

    // Test email
    public function testEmail()
    {
        $message = [
            'msg' => 'Test mail',
        ];
        $mail = false;
        try {
            Mail::to(ENV('MAIL_FROM_ADDRESS'))->send(new \App\Mail\TestMail($message));
            $mail = true;
        } catch (\Exception $e) {
            // Page redirect
            return redirect()->route('admin.plugin.smtp.settings')->with('failed', trans('Email configuration wrong.'));
        }
        // Check email
        if ($mail == true) {
            // Page redirect
            return redirect()->route('admin.plugin.smtp.settings')->with('success', trans('Test mail send successfully.'));
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

}
