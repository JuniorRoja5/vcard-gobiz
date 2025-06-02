<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Setting;
use App\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    public function showLinkRequestForm()
    {
        $config = DB::table('config')->get();
        $settings = Setting::first();

        $google_configuration = [
            'GOOGLE_ENABLE' => env('GOOGLE_ENABLE', ''),
            'GOOGLE_CLIENT_ID' => env('GOOGLE_CLIENT_ID', ''),
            'GOOGLE_CLIENT_SECRET' => env('GOOGLE_CLIENT_SECRET', ''),
            'GOOGLE_REDIRECT' => env('GOOGLE_REDIRECT', '')
        ];

        $settings['google_configuration'] = $google_configuration;

        return view('auth.passwords.email', compact('config', 'settings'));
    }

    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email input
        $request->validate(['email' => 'required|email']);

        // Try to send the password reset link
        $response = Password::sendResetLink(
            $request->only('email')
        );

        // If the password reset link was sent successfully
        if ($response == Password::RESET_LINK_SENT) {
            // Find the email template details
            $emailTemplateDetails = EmailTemplate::where('email_template_id', '584922675212')->first();

            // Get the user by email to create the reset token
            $user = User::where('email', $request->email)->first();

            if ($user) {
                // Generate the password reset token for the user
                $token = app('auth.password.broker')->createToken($user);

                // Generate the password reset link with the token
                $actionLink = url(route('password.reset', ['token' => $token, 'email' => $request->email], false));

                // Prepare the email details
                $details = [
                    'emailSubject' => $emailTemplateDetails->email_template_subject,
                    'emailContent' => $emailTemplateDetails->email_template_content,
                    'appname' => config('app.name'),
                    'actionlink' => $actionLink,  // Action link passed to the email template
                ];

                // Send the email
                try {
                    Mail::to($request->email)->send(new \App\Mail\AppointmentMail($details));
                } catch (\Exception $e) {
                    // Handle any exceptions during mail sending
                    return back()->with('email', $e->getMessage());
                }

                return back()->with('status', trans('We have emailed your password reset link!'));
            }

            // If user not found, return with error
            return back()->withErrors(['email' => trans('We can\'t find a user with that email address.')]);
        }

        // If the reset link could not be sent, return with error
        return back()->withErrors(['email' => trans($response)]);
    }
}
