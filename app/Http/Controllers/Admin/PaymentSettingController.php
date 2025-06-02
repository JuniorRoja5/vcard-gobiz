<?php

namespace App\Http\Controllers\Admin;

use App\Gateway;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PaymentSettingController extends Controller
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

    // Payment Configuration
    public function configurePaymentMethod(Request $request, $id)
    {
        // Queries
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();
        $gateway_details = Gateway::where('payment_gateway_id', $id)->first();

        // Check gateway details
        if (empty($gateway_details)) {
            return redirect()->route('admin.payment.methods')->with('failed', trans('Not Found!'));
        }

        return view('admin.pages.payment-methods.configuration', compact('config', 'settings', 'gateway_details'));
    }

    // Update Payment Configuration
    public function updatePaymentConfiguration(Request $request, $id)
    {
        // Paypal mode
        if ($id == '60964401751ab') {
            DB::table('config')->where('config_key', 'paypal_mode')->update([
                'config_value' => $request->paypal_mode,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'paypal_client_id')->update([
                'config_value' => $request->paypal_client_key,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'paypal_secret')->update([
                'config_value' => $request->paypal_secret,
                'updated_at' => now(),
            ]);
        }

        // Razorpay
        if ($id == '60964410731d9') {
            DB::table('config')->where('config_key', 'razorpay_key')->update([
                'config_value' => $request->razorpay_client_key,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'razorpay_secret')->update([
                'config_value' => $request->razorpay_secret,
                'updated_at' => now(),
            ]);
        }

        // Phonepe
        if ($id == '19065566166715') {
            DB::table('config')->where('config_key', 'phonepe_client_id')->update([
                'config_value' => $request->clientId,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'phonepe_client_version')->update([
                'config_value' => $request->clientVersion,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'phonepe_client_secret')->update([
                'config_value' => $request->clientSecret,
                'updated_at' => now(),
            ]);
        }

        // Stripe
        if ($id == '60964410732t9') {
            DB::table('config')->where('config_key', 'stripe_publishable_key')->update([
                'config_value' => $request->stripe_publishable_key,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'stripe_secret')->update([
                'config_value' => $request->stripe_secret,
                'updated_at' => now(),
            ]);
        }

        // Paystack
        if ($id == '60964410736592') {
            DB::table('config')->where('config_key', 'paystack_public_key')->update([
                'config_value' => $request->paystack_public_key,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'paystack_secret_key')->update([
                'config_value' => $request->paystack_secret,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'merchant_email')->update([
                'config_value' => $request->merchant_email,
                'updated_at' => now(),
            ]);
        }

        // Mollie
        if ($id == '6096441071589632') {
            DB::table('config')->where('config_key', 'mollie_key')->update([
                'config_value' => $request->mollie_key,
                'updated_at' => now(),
            ]);
        }

        // Mercadopago
        if ($id == '776111730465') {
            DB::table('config')->where('config_key', 'mercado_pago_public_key')->update([
                'config_value' => $request->mercado_pago_public_key,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'mercado_pago_access_token')->update([
                'config_value' => $request->mercado_pago_access_token,
                'updated_at' => now(),
            ]);
        }

        // Toyyibpay
        if ($id == '767510608137') {
            DB::table('config')->where('config_key', 'toyyibpay_mode')->update([
                'config_value' => $request->toyyibpay_mode,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'toyyibpay_api_key')->update([
                'config_value' => $request->toyyibpay_api_key,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'toyyibpay_category_code')->update([
                'config_value' => $request->toyyibpay_category_code,
                'updated_at' => now(),
            ]);
        }

        // Flutterwave
        if ($id == '754201940107') {
            DB::table('config')->where('config_key', 'flw_public_key')->update([
                'config_value' => $request->flw_public_key,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'flw_secret_key')->update([
                'config_value' => $request->flw_secret_key,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'flw_encryption_key')->update([
                'config_value' => $request->flw_encryption_key,
                'updated_at' => now(),
            ]);
        }

        // Paddle Settings
        if ($id == '5992737427969') {
            DB::table('config')->where('config_key', 'paddle_environment')->update([
                'config_value' => $request->paddle_environment,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'paddle_seller_id')->update([
                'config_value' => $request->paddle_seller_id,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'paddle_api_key')->update([
                'config_value' => $request->paddle_api_key,
                'updated_at' => now(),
            ]);

            DB::table('config')->where('config_key', 'paddle_client_side_token')->update([
                'config_value' => $request->paddle_client_side_token,
                'updated_at' => now(),
            ]);
        }

        // PayTR Mode
        if ($id == '5992737427970') {
            DB::table('config')->where('config_key', 'paytr_mode')->update([
                'config_value' => $request->paytr_mode,
                'updated_at' => now(),
            ]);

            // PayTR Merchant ID
            DB::table('config')->where('config_key', 'paytr_merchant_id')->update([
                'config_value' => $request->paytr_merchant_id,
                'updated_at' => now(),
            ]);

            // PayTR Merchant Key
            DB::table('config')->where('config_key', 'paytr_merchant_key')->update([
                'config_value' => $request->paytr_merchant_key,
                'updated_at' => now(),
            ]);

            // PayTR Merchant Salt Key
            DB::table('config')->where('config_key', 'paytr_merchant_salt')->update([
                'config_value' => $request->paytr_merchant_salt_key,
                'updated_at' => now(),
            ]);
        }

        // Xendit Access Token
        if ($id == '278523098674') {
            DB::table('config')->where('config_key', 'xendit_secret_key')->update([
                'config_value' => $request->xendit_secret_key,
                'updated_at' => now(),
            ]);
        }

        // Cashfree Mode
        if ($id == '278523098675') {
            // Cashfree Mode
            DB::table('config')->where('config_key', 'cashfree_mode')->update([
                'config_value' => $request->cashfree_mode,
                'updated_at' => now(),
            ]);

            // Cashfree App ID
            DB::table('config')->where('config_key', 'cashfree_app_id')->update([
                'config_value' => $request->cashfree_app_id,
                'updated_at' => now(),
            ]);

            // Cashfree Secret Key
            DB::table('config')->where('config_key', 'cashfree_secret_key')->update([
                'config_value' => $request->cashfree_secret_key,
                'updated_at' => now(),
            ]);
        }

        // Bank transfer
        if ($id == '659644107y2g5') {
            DB::table('config')->where('config_key', 'bank_transfer')->update([
                'config_value' => $request->bank_transfer,
                'updated_at' => now(),
            ]);
        }

        // Page redirect
        return redirect()->route('admin.configure.payment', $id)->with('success', trans('Updated!'));
    }
}
