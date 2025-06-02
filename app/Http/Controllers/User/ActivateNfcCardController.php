<?php

namespace App\Http\Controllers\User;

use App\Plan;
use App\User;
use App\Setting;
use Carbon\Carbon;
use App\NfcCardKey;
use App\BusinessCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ActivateNfcCardController extends Controller
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

    // Activate NFC Card
    public function index()
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }

        // Check if the user has a plan
        $active_plan = Plan::where('plan_id', Auth::user()->plan_id)->first();
        $plan = User::where('user_id', Auth::user()->user_id)->first();
        $active_plan = json_decode($plan->plan_details);

        if ($active_plan != null) {
            if ($active_plan->nfc_card == 1) {
                // List of business cards
                $businessCards = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_status', 'activated')->get();

                return view('user.pages.activate-nfc-card.index', compact('businessCards', 'settings', 'config'))->with('failed', __('This feature is not available on your current plan.'));
            } else {
                return redirect()->route('user.plans')->with('failed', __('This feature is not available on your current plan. Upgrade to a plan that includes this feature.'));
            }
        } else {
            return redirect()->route('user.plans')->with('failed', __('This feature is not available on your current plan. Upgrade to a plan that includes this feature.'));
        }
    }

    // Store NFC Card
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'activationCode' => 'required',
            'businessCard' => 'required',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get the business card details
        $businessCard = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_status', 'activated')->where('card_id', $request->businessCard)->first();

        // Check if the business card exists
        if (!$businessCard) {
            return redirect()->route('user.activate.nfc.card')->with('failed', __('Business card not found!'));
        }

        // Check if the activation code matches
        $existsNfcKey = NfcCardKey::where('unqiue_key', $request->activationCode)->first();

        if (!$existsNfcKey) {
            return redirect()->route('user.activate.nfc.card')->with('failed', __('Activation code not found!'));
        }

        if ($existsNfcKey->link_status == 'linked') {
            return redirect()->route('user.activate.nfc.card')->with('failed', __('Activation code already linked to a business card/store.'));
        }

        // Update the business card
        $updateNFCKey = NfcCardKey::where('unqiue_key', $request->activationCode)->update([
            'card_id' => $businessCard->card_id,
            'link_status' => 'linked',
            'status' => 1,
            'updated_at' => Carbon::now()
        ]);

        // Check if the update operation was successful
        if (!$updateNFCKey) {
            return redirect()->route('user.activate.nfc.card')->with('failed', __('Failed to update the business card!'));
        }

        return redirect()->route('user.activate.nfc.card')->with('success', __('Business card activated successfully!'));
    }
}
