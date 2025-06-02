<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Setting;
use App\Currency;
use Carbon\Carbon;
use App\BusinessCard;
use App\CustomDomainRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ConnectDomainController extends Controller
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

    // Request to connect with custom domain
    public function connectDomain(Request $request, $id)
    {
        // Queries
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();

        // Get plan details
        $plan = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
        $plan_details = json_decode($plan->plan_details);

        // Check validity
        $validity = User::where('user_id', Auth::user()->user_id)->whereDate('plan_validity', '>=', Carbon::now())->count();

        // Check number of stores
        if ($validity == 1) {
            // Check business card
            $business_card = BusinessCard::where('card_id', $id)->count();

            // Check valid business card
            if ($business_card != 1) {
                return redirect()->back()->with('failed', trans('Business card / Store not found!'));
            } else {
                if ($plan_details->custom_domain == 1) {
                    // Previous connected domain list
                    $previous_domains = CustomDomainRequest::where('card_id', $id)->orderBy('id', 'desc')->get();

                    return view('user.pages.connect-domain.index', compact('previous_domains', 'config', 'settings'));
                } else {
                    return redirect()->route('user.plans')->with('failed', trans('Your current plan does not include a plan to link your domain. Upgrade to a domain linking plan.'));
                }
            }
        } else {
            // Redirect
            return redirect()->route('user.plans')->with('failed', trans('Your plan is over. Choose your plan renewal or new package and use it.'));
        }
    }

    // Submit new domain request
    public function newDomainRequest(Request $request)
    {
        // Get and validate inputs
        $domain = $request->domain;
        $card_id = $request->card_id;

        // Check if the business card exists
        $card = BusinessCard::where('card_id', $card_id)->first();

        // Check domain is exists in the system
        $domain_exists = CustomDomainRequest::where('current_domain', $domain)->count();
        if ($domain_exists) {
            return redirect()->back()->with('failed', trans('This domain already connected to another business card / store.'));
        }

        // Check domain is already requested
        $domain_request = CustomDomainRequest::where('card_id', $card_id)->where('current_domain', $domain)->first();
        if ($domain_request) {
            return redirect()->back()->with('failed', trans('Domain already requested.'));
        }

        // Check card
        if ($card) {
            // Get previous domain in CustomDomainRequest
            $previousDomainDetails = CustomDomainRequest::where('card_id', $card_id)->first();
            // Check $previous_domain is not null
            $previous_domain = "-";
            if ($previousDomainDetails) {
                $previous_domain = $previousDomainDetails->current_domain;
            }

            // Save domain request as CNAME is verified
            $domain_request = new CustomDomainRequest();
            $domain_request->custom_domain_request_id = uniqid();
            $domain_request->user_id = Auth::user()->user_id;
            $domain_request->card_id = $card_id;
            $domain_request->previous_domain = $previous_domain;
            $domain_request->current_domain = $domain;
            $domain_request->transfer_status = 0;
            $domain_request->save();

            return redirect()->back()->with('success', trans('CNAME record is correctly pointing to ') . str_replace(['http://', 'https://', 'www.'], '', config('app.url')) . (', and your domain request is successfully submitted.'));
        } else {
            return redirect()->back()->with('failed', trans('Business card not found!'));
        }
    }

    // Unlink domain
    public function unlinkDomain(Request $request)
    {
        // Get and validate inputs
        $domain_request_id = $request->query('id');
        $card_id = $request->query('card_id');

        // Check if the domain request exists
        $domain_request = CustomDomainRequest::where('card_id', $card_id)->where('custom_domain_request_id', $domain_request_id)->first();

        // Check domain is exists in the system
        $domain_exists = CustomDomainRequest::where('card_id', $card_id)->where('custom_domain_request_id', $domain_request_id)->count();
        if ($domain_exists) {
            // Check custom domain exists in business cards table
            $business_card = BusinessCard::where('custom_domain', $domain_request->current_domain)->count();

            // Check if the business card exists
            if ($business_card != 0) {
                try {
                    // Delete business card
                    $business_card = BusinessCard::where('card_id', $card_id)->where('custom_domain', $domain_request->current_domain)->first();

                    // Nullify custom domain
                    $business_card->custom_domain = null;
                    $business_card->save();
                } catch (\Exception $e) {
                }
            }

            // Delete domain request
            $domain_request->delete();

            return redirect()->back()->with('success', trans('Your domain request is successfully unlinked.'));
        } else {
            return redirect()->back()->with('failed', trans('Domain request not found!'));
        }
    }
}
