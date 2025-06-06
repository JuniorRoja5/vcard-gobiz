<?php

namespace App\Http\Controllers\User;

use App\Setting;
use App\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NewsletterController extends Controller
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


    // Show vcard wise newsletter
    public function index(Request $request, $id)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();

        // Get plan details
        $plan = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
        $plan_details = json_decode($plan->plan_details);

        // Newsletter subscription email
        $subscripedEmails = Newsletter::where('card_id', $id)->where('status', 1)->get();

        return view('user.pages.cards.newsletter', compact('subscripedEmails', 'settings', 'plan_details'));
    }
}
