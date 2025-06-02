<?php

namespace App\Http\Controllers\Payment;

use App\Plan;
use App\User;
use App\Coupon;
use App\Setting;
use App\Transaction;
use App\AppliedCoupon;
use Illuminate\Support\Str;
use App\Classes\UpgradePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PhonePeController extends Controller
{
    public function preparePhonpe($planId, $couponId)
    {
        if (Auth::user()) {
            $settings = Setting::where('status', 1)->first();
            $config = DB::table('config')->get();
            $userData = User::where('id', Auth::user()->id)->first();
            $plan_details = Plan::where('plan_id', $planId)->where('status', 1)->first();

            if (!$plan_details) {
                return view('errors.404');
            }

            $authToken = $this->getPhonePeAuthToken();
            if (!$authToken) {
                return redirect()->route('user.plans')->with('failed', trans('Failed to fetch PhonePe authentication token.'));
            }

            // Check applied coupon
            $couponDetails = Coupon::where('used_for', 'plan')->where('coupon_id', $couponId)->first();

            // Applied tax in total
            $appliedTaxInTotal = 0;

            // Discount price
            $discountPrice = 0;

            // Applied coupon
            $appliedCoupon = null;

            // Check coupon type
            if ($couponDetails != null) {
                if ($couponDetails->coupon_type == 'fixed') {
                    // Applied tax in total
                    $appliedTaxInTotal = ((float)($plan_details->plan_price) * (float)($config[25]->config_value) / 100);

                    // Get discount in plan price
                    $discountPrice = $couponDetails->coupon_amount;

                    // Total
                    $amountToBePaid = ($plan_details->plan_price + $appliedTaxInTotal) - $discountPrice;
                    $amountToBePaid = (float)number_format($amountToBePaid, 2, '.', '');

                    // Coupon is applied
                    $appliedCoupon = $couponDetails->coupon_code;
                } else {
                    // Applied tax in total
                    $appliedTaxInTotal = ((float)($plan_details->plan_price) * (float)($config[25]->config_value) / 100);

                    // Get discount in plan price
                    $discountPrice = $plan_details->plan_price * $couponDetails->coupon_amount / 100;

                    // Total
                    $amountToBePaid = ($plan_details->plan_price + $appliedTaxInTotal) - $discountPrice;
                    $amountToBePaid = (float)number_format($amountToBePaid, 2, '.', '');

                    // Coupon is applied
                    $appliedCoupon = $couponDetails->coupon_code;
                }
            } else {
                // Applied tax in total
                $appliedTaxInTotal = ((float)($plan_details->plan_price) * (float)($config[25]->config_value) / 100);

                // Total
                $amountToBePaid = ($plan_details->plan_price + $appliedTaxInTotal);
            }

            $amountToBePaidPaise = $amountToBePaid * 100;

            // Generate a unique transaction ID
            $transactionId = "TX" . preg_replace('/\D/', '', Str::uuid());

            try {
                $data = [
                    'merchantOrderId' => $transactionId,  // Unique transaction ID
                    'amount' => $amountToBePaidPaise,  // Amount in paise (1000 = ₹10)
                    'paymentFlow' => [
                        'type' => 'PG_CHECKOUT',  // Correct type for PhonePe checkout
                        'merchantUrls' => [
                            'redirectUrl' => route('phonepe.payment.status') // Redirect after payment
                        ]
                    ]
                ];

                $response = Http::withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => "O-Bearer " . $authToken
                ])->post('https://api.phonepe.com/apis/pg/checkout/v2/pay', $data);

                // Get JSON response
                $rData = $response->json();

                // Redirect on success
                if (!empty($rData['state']) && $rData['state'] == "PENDING") {
                    $invoice_details = [];

                    $invoice_details['from_billing_name'] = $config[16]->config_value;
                    $invoice_details['from_billing_address'] = $config[19]->config_value;
                    $invoice_details['from_billing_city'] = $config[20]->config_value;
                    $invoice_details['from_billing_state'] = $config[21]->config_value;
                    $invoice_details['from_billing_zipcode'] = $config[22]->config_value;
                    $invoice_details['from_billing_country'] = $config[23]->config_value;
                    $invoice_details['from_vat_number'] = $config[26]->config_value;
                    $invoice_details['from_billing_phone'] = $config[18]->config_value;
                    $invoice_details['from_billing_email'] = $config[17]->config_value;
                    $invoice_details['to_billing_name'] = $userData->billing_name;
                    $invoice_details['to_billing_address'] = $userData->billing_address;
                    $invoice_details['to_billing_city'] = $userData->billing_city;
                    $invoice_details['to_billing_state'] = $userData->billing_state;
                    $invoice_details['to_billing_zipcode'] = $userData->billing_zipcode;
                    $invoice_details['to_billing_country'] = $userData->billing_country;
                    $invoice_details['to_billing_phone'] = $userData->billing_phone;
                    $invoice_details['to_billing_email'] = $userData->billing_email;
                    $invoice_details['to_vat_number'] = $userData->vat_number;
                    $invoice_details['subtotal'] = $plan_details->plan_price;
                    $invoice_details['tax_name'] = $config[24]->config_value;
                    $invoice_details['tax_type'] = $config[14]->config_value;
                    $invoice_details['tax_value'] = $config[25]->config_value;
                    $invoice_details['tax_amount'] = $appliedTaxInTotal;
                    $invoice_details['applied_coupon'] = $appliedCoupon;
                    $invoice_details['discounted_price'] = $discountPrice;
                    $invoice_details['invoice_amount'] = $amountToBePaid;

                    // Save transactions
                    $transaction = new Transaction();
                    $transaction->gobiz_transaction_id = $transactionId;
                    $transaction->transaction_date = now();
                    $transaction->transaction_id = $rData['orderId'];
                    $transaction->user_id = Auth::user()->id;
                    $transaction->plan_id = $plan_details->plan_id;
                    $transaction->desciption = $plan_details->plan_name . " Plan";
                    $transaction->payment_gateway_name = "PhonePe";
                    $transaction->transaction_amount = $amountToBePaid;
                    $transaction->transaction_currency = $config[1]->config_value;
                    $transaction->invoice_details = json_encode($invoice_details);
                    $transaction->payment_status = "PENDING";
                    $transaction->save();

                    // Coupon is not applied
                    if ($couponId != " ") {
                        // Save applied coupon
                        $appliedCoupon = new AppliedCoupon;
                        $appliedCoupon->applied_coupon_id = uniqid();
                        $appliedCoupon->transaction_id = $transactionId;
                        $appliedCoupon->user_id = Auth::user()->id;
                        $appliedCoupon->coupon_id = $couponId;
                        $appliedCoupon->status = 0;
                        $appliedCoupon->save();
                    }

                    return redirect()->to($rData['redirectUrl']);
                } else {
                    return redirect()->route('user.plans')->with('failed', trans('Payment failed.'));
                }
            } catch (\Exception $e) {
                return redirect()->route('user.plans')->with('failed', trans('Payment failed.'));
            }
        } else {
            return redirect()->route('login');
        }
    }

    private function getPhonePeAuthToken()
    {
        // Check if the token is cached
        if (Cache::has('phonepe_auth_token')) {
            return Cache::get('phonepe_auth_token');
        }

        // Query the config table
        $config = DB::table('config')->get();

        // Check if the config values are empty
        if ($config[77]->config_value == 'YOUR_PHONEPE_CLIENT_ID' || $config[78]->config_value == 'YOUR_PHONEPE_CLIENT_VERSION' || $config[79]->config_value == 'YOUR_PHONEPE_CLIENT_SECRET') {
            return trans("Something went wrong!");
        }

        // Set the auth URL
        $authUrl = "https://api.phonepe.com/apis/identity-manager/v1/oauth/token";

        // Set the payload
        $payload = [
            "client_id" => $config[77]->config_value,
            "client_version" => $config[78]->config_value,
            "client_secret" => $config[79]->config_value,
            "grant_type" => "client_credentials"
        ];

        // Send the request
        $response = Http::asForm()->post($authUrl, $payload); // Ensuring correct encoding

        // Decode the response
        $responseData = $response->json();

        // Check if the response contains an access token
        if (isset($responseData['access_token'])) {
            Cache::put('phonepe_auth_token', $responseData['access_token'], now()->addMinutes(55));

            return $responseData['access_token'];
        } else {
            return trans("Failed to retrieve token");
        }

        return null;
    }

    public function phonepePaymentStatus(Request $request)
    {
        // Get last transaction id for phonepe and user id
        $transactionDetails = Transaction::where('payment_gateway_name', 'PhonePe')->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();

        if (isset($transactionDetails)) {
            $authToken = $this->getPhonePeAuthToken();
            if (!$authToken) {
                return redirect()->route('user.plans')->with('failed', trans('Failed to fetch PhonePe authentication token.'));
            }

            $statusUrl = "https://api.phonepe.com/apis/pg/checkout/v2/order/" . $transactionDetails->gobiz_transaction_id . "/status?details=false&errorContext=true";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "O-Bearer " . $authToken
            ])->get($statusUrl);

            $res = json_decode($response->body());

            try {
                // Check status is failed
                if ($res->success == false) {
                    return redirect()->route('user.plans')->with('failed', trans($res->message));
                }
            } catch (\Exception $e) {
            }

            if ($res->state == "COMPLETED") {

                // Get transactionId
                $orderId = $res->orderId;
                $transactionId = $res->paymentDetails[0]->transactionId;

                $upgradePlan = new UpgradePlan;
                $upgradePlan->upgrade($orderId, $res);
                
                // Update transaction id
                Transaction::where('gobiz_transaction_id', $transactionDetails->gobiz_transaction_id)->update(['transaction_id' => $transactionId]);
                
                return redirect()->route('user.plans')->with('success', trans('Plan activated successfully!'));
            } else {
                Transaction::where('gobiz_transaction_id', $transactionDetails->gobiz_transaction_id)->update(['payment_status' => 'FAILED']);
                
                return redirect()->route('user.plans')->with('failed', trans('Payment failed.'));
            }
        }

        return redirect()->route('user.plans')->with('failed', trans('Payment failed.'));
    }
}
