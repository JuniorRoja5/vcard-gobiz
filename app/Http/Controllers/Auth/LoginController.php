<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Setting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticated()
    {
        if (Auth::check() && Auth::user()->role_id != 2) {
            return redirect('/admin/dashboard');
        }

        return redirect('/user/dashboard');
    }

    public function showLoginForm()
    {
        $config = DB::table('config')->get();
        $settings = Setting::first();

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

        return view('auth.login', compact('config', 'settings'));
    }

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            // Using stateless() to avoid session issues if needed
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', trans('Google login failed.'));
        }

        // Check if user exists in the database
        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if ($existingUser) {
            if ($existingUser->status == 1) {
                Auth::login($existingUser, true);
                return redirect()->to('/user/dashboard');
            } else {
                return redirect('/login')->with('error', trans('Your account is inactive.'));
            }
        } else {
            // Create a new user
            $newUser = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'email_verified_at' => now(),
                'user_id' => $googleUser->getId(),
                'profile_image' => $googleUser->getAvatar(),
                'password' => bcrypt($googleUser->getId()), // Secure placeholder password
                'auth_type' => 'Google',
                'role_id' => 2,
                'status' => 1, // Make sure the default is active
            ]);

            Auth::login($newUser, true);
            return redirect()->to('/user/dashboard');
        }
    }
}
