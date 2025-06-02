<?php

namespace App\Http\Controllers\User;

use App\Plan;
use App\User;
use App\Gateway;
use App\Setting;
use App\Currency;
use App\NfcCardDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderNfcCardController extends Controller
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

    // Show all nfc cards
    public function index()
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();
        $currency = Currency::where('iso_code', $config[1]->config_value)->first();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }

        // Check if the user has a plan
        $active_plan = Plan::where('plan_id', Auth::user()->plan_id)->first();
        $plan = User::where('user_id', Auth::user()->user_id)->first();
        $active_plan = json_decode($plan->plan_details);

        if ($active_plan != null) {
            // Check "nfc_card" is available in the plan
            if ($active_plan->nfc_card == 1) {
                // Available NFC Cards
                $availableNfcCards = NfcCardDesign::where('status', 1)->get();

                return view('user.pages.order.nfc-card.index', compact('availableNfcCards', 'settings', 'config', 'currency'));
            } else {
                return redirect()->route('user.plans')->with('failed', __('This feature is not available on your current plan. Upgrade to a plan that includes this feature.'));
            }
        } else {
            return redirect()->route('user.plans');
        }
    }

    // Select a nfc card
    public function nfcCardCheckout(Request $request, $designId)
    {
        // Default values
        $coupon_code = '';

        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();
        $currency = Currency::where('iso_code', $config[1]->config_value)->first();
        $gateways = Gateway::where('is_status', 'enabled')->where('status', 1)->get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }

        // Check if the user has a plan
        $active_plan = Plan::where('plan_id', Auth::user()->plan_id)->first();
        $plan = User::where('user_id', Auth::user()->user_id)->first();
        $active_plan = json_decode($plan->plan_details);

        // Check "nfc_card" is available in the plan
        if ($active_plan->nfc_card == 1) {

            // Get NFC Card details
            $nfcCardDetails = NfcCardDesign::where('nfc_card_id', $designId)->first();

            // NFC Card details existing in the database
            if (!$nfcCardDetails) {
                return redirect()->route('user.order.nfc.cards')->with('failed', __('NFC Card not found.'));
            }

            // Check available stock
            if ($nfcCardDetails->available_stocks <= 0) {
                return redirect()->route('user.order.nfc.cards')->with('failed', __('This NFC card is out of stock.'));
            }

            // Set total
            $total = ((float) ($nfcCardDetails->nfc_card_price) * (float) ($config[25]->config_value) / 100) + (float) ($nfcCardDetails->nfc_card_price);
            $total = number_format($total, 2, '.', '');

            return view('user.pages.order.nfc-card.checkout', compact('nfcCardDetails', 'settings', 'config', 'currency', 'total', 'coupon_code', 'gateways'));
        } else {
            return redirect()->route('user.plans')->with('failed', __('This feature is not available on your current plan. Upgrade to a plan that includes this feature.'));
        }
    }
}
