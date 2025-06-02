<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Setting;
use App\Referral;
use App\EmailTemplate;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if (env('RECAPTCHA_ENABLE') == 'on') {
            return Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'terms' => ['required'],
                'g-recaptcha-response' => ['recaptcha', 'required']
            ]);
        } else {
            return Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'terms' => ['required']
            ]);
        }
    }

    public function showRegistrationForm()
    {
        // Queries
        $config = DB::table('config')->get();
        $settings = Setting::first();

        // Check if registration page is enabled
        $registration_page = $config->where('config_key', 'registration_page')->first();
        $registration_page_enabled = $registration_page->config_value == '0' ? true : false;

        if ($registration_page_enabled) {
            // Google configuration
            $google_configuration = [
                'GOOGLE_ENABLE' => env('GOOGLE_ENABLE', ''),
                'GOOGLE_CLIENT_ID' => env('GOOGLE_CLIENT_ID', ''),
                'GOOGLE_CLIENT_SECRET' => env('GOOGLE_CLIENT_SECRET', ''),
                'GOOGLE_REDIRECT' => env('GOOGLE_REDIRECT', '')
            ];

            $recaptcha_configuration = [
                'RECAPTCHA_ENABLE' => env('RECAPTCHA_ENABLE', ''),
                'RECAPTCHA_SITE_KEY' => env('RECAPTCHA_SITE_KEY', ''),
                'RECAPTCHA_SECRET_KEY' => env('RECAPTCHA_SECRET_KEY', '')
            ];

            $settings['google_configuration'] = $google_configuration;
            $settings['recaptcha_configuration'] = $recaptcha_configuration;

            return view('auth.register', compact('config', 'settings'));
        } else {
            return redirect()->route('home-locale')->with('failed', __('Customer registration is currently closed. Please try again later.'));
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        // Queries
        $config = DB::table('config')->get();

        // Find the user by email
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            return $user;
        }

        // Generate user id
        $userId = uniqid();

        // Check enable referral system
        if ($config[80]->config_value == '1') {
            // Check referral code
            $referralCode = User::where('user_id', $data['referral_code'])->first();

            if ($referralCode) {
                // Referral amount details
                $referralCalculation = [];
                $referralCalculation['referral_type'] = $config[81]->config_value;
                $referralCalculation['referral_value'] = $config[82]->config_value;

                // Check referral_type is percentage or amount
                if ($config[81]->config_value == '0') {
                    $referralCalculation['referral_amount'] = 0;
                } else {
                    $referralCalculation['referral_amount'] = $referralCalculation['referral_value'];
                }

                // Save referral
                $referral = new Referral();
                $referral->user_id = $userId;
                $referral->referred_user_id = $referralCode->user_id;
                $referral->is_registered = 1;
                $referral->referral_scheme = json_encode($referralCalculation);
                $referral->status = 1;
                $referral->save();
            }
        }

        $user = User::create([
            'user_id' => $userId,
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile_number' => $data['mobile_number'],
            'email_verified_at' => $config[43]->config_value == '1' ? null : now(),
            'auth_type' => 'Email',
            'password' => Hash::make($data['password']),
        ]);

        // Get appointment pending email template content
        $emailTemplateDetails = EmailTemplate::where('email_template_id', '584922675208')->first();

        $message = [
            'status' => "",
            'emailSubject' => $emailTemplateDetails->email_template_subject,
            'emailContent' => $emailTemplateDetails->email_template_content,
            'registeredName' => $data['name'],
            'registeredEmail' => $data['email'],
        ];

        $mail = false;

        // Booking mail sent to customer
        if ($emailTemplateDetails->is_enabled == 1) {

            try {
                // Welcome email
                Mail::to($data['email'])->bcc(env('MAIL_FROM_ADDRESS'))->send(new \App\Mail\AppointmentMail($message));

                $mail = true;

                // Check email verification system is enabled
                if ($config[43]->config_value == "1") {
                    // Send email verification
                    $user->newEmail($data['email']);
                }
            } catch (\Exception $e) {
                return $user;
            }
        }


        if ($mail == true) {
            return $user;
        }

        return $user;
    }

    protected function redirectTo()
    {
        return '/user/dashboard';
    }
}
