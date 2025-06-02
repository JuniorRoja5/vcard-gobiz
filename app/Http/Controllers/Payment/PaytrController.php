<?php

namespace App\Http\Controllers\Payment;

use App\Plan;
use App\User;
use App\Coupon;
use App\Setting;
use App\Referral;
use Carbon\Carbon;
use App\Transaction;
use App\BusinessCard;
use App\AppliedCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PaytrController extends Controller
{
    private $merchant_id;
    private $merchant_key;
    private $merchant_salt;
    private $mode;

    public function __construct()
    {
        /** paytr api context **/
        $paytr_configuration = DB::table('config')->get();

        $this->merchant_id = $paytr_configuration[68]->config_value;
        $this->merchant_key = $paytr_configuration[69]->config_value;
        $this->merchant_salt = $paytr_configuration[70]->config_value;
        $this->mode = $paytr_configuration[71]->config_value;
    }

    // Process payment
    public function generatePaymentLink(Request $request, $planId, $couponId)
    {
        if (Auth::user()) {
            // Queries
            $config = DB::table('config')->get();
            $userData = User::where('id', Auth::user()->id)->first();
            $plan_details = Plan::where('plan_id', $planId)->where('status', 1)->first();
            $settings = Setting::where('status', 1)->first();

            // Check plan details
            if ($plan_details == null) {
                // Page redirect
                return redirect()->route('paytr.payment.failure')->with('failed', trans('Payment failed'));
            } else {
                $gobiz_transaction_id = uniqid();

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

                // Transaction ID
                $transactionId = uniqid();

                // Generate JSON
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
                $transaction->gobiz_transaction_id = $gobiz_transaction_id;
                $transaction->transaction_date = now();
                $transaction->transaction_id = $transactionId;
                $transaction->user_id = Auth::user()->id;
                $transaction->plan_id = $plan_details->plan_id;
                $transaction->desciption = $plan_details->plan_name . " Plan";
                $transaction->payment_gateway_name = "Paytr";
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

                $paymentData = [
                    'merchant_id' => $this->merchant_id,
                    'user_ip' => $request->ip(),
                    'merchant_oid' => $transactionId, // Unique order ID
                    'email' => $userData->billing_email,
                    'payment_amount' => $amountToBePaidPaise, // Amount in cents
                    'user_basket' => base64_encode(json_encode([
                        ['Plan Purchase for' . $plan_details->plan_name, $plan_details->plan_price, 1],
                    ])),
                    'debug_on' => 1,
                    'no_installment' => 0,
                    'max_installment' => 0,
                    'currency' => 'TL',
                    'test_mode' => $this->mode,
                    'user_name' => Auth::user()->billing_name,
                    'user_address' => Auth::user()->billing_address,
                    'user_phone' => Auth::user()->billing_phone,
                    'merchant_ok_url' => route('paytr.payment.status'),
                    'merchant_fail_url' => route('paytr.payment.failure'),
                ];

                // Generate paytr_token
                $hash_str = $this->merchant_id .
                    $paymentData['user_ip'] .
                    $paymentData['merchant_oid'] .
                    $paymentData['email'] .
                    $paymentData['payment_amount'] .
                    $paymentData['user_basket'] .
                    $paymentData['no_installment'] .
                    $paymentData['max_installment'] .
                    $paymentData['currency'] .
                    $paymentData['test_mode'];

                $paymentData['paytr_token'] = base64_encode(hash_hmac('sha256', $hash_str . $this->merchant_salt, $this->merchant_key, true));

                try {
                    // Send request to PayTR
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($paymentData));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $response = curl_exec($ch);

                    if (curl_errno($ch)) {
                        return back()->withErrors(['failed' => curl_error($ch)]);
                    }

                    curl_close($ch);

                    $result = json_decode($response, true);

                    if ($result['status'] === 'success') {
                        return view('user.pages.checkout.pay-with-paytr', [
                            'settings' => $settings,
                            'iframe_token' => $result['token'],
                        ]);
                    }
                } catch (\Exception $e) {
                    return back()->with(['failed' => trans('Something went wrong. Please try again later.')]);
                }

                return back()->with(['failed' => $result['reason']]);
            }
        } else {
            return redirect()->route('login');
        }
    }

    // Process Payment
    public function paytrPaymentStatus(Request $request)
    {
        $paymentDetails = $request->all();

        // Check payment id
        if (!$paymentDetails) {
            // Page redirect
            return redirect()->route('user.plans')->with('failed', trans('Payment failed'));
        } else {

            // Security: Verify PayTR's notification
            $hash = base64_encode(hash_hmac(
                'sha256',
                $paymentDetails['merchant_oid'] . $this->merchant_salt . $paymentDetails['status'] . $paymentDetails['total_amount'],
                $this->merchant_key,
                true
            ));

            if ($hash != $paymentDetails['hash']) {
                return redirect()->route('user.plans')->with(['failed' => trans('Payment failed')]);
            }

            // Queries
            $transactionId = $paymentDetails['merchant_oid'];
            $paymentId = $paymentDetails['merchant_oid'];
            $config = DB::table('config')->get();

            // Check payment status
            if ($paymentDetails['status'] == "success") {

                // Get transaction details
                $transaction_details = Transaction::where('transaction_id', $transactionId)->where('status', 1)->first();

                // Get user details
                $user_details = User::find($transaction_details->user_id);

                // Get plan details
                $plan_data = Plan::where('plan_id', $transaction_details->plan_id)->first();
                $term_days = (int) $plan_data->validity;

                // Check plan validity
                if ($user_details->plan_validity == "") {

                    // Add days
                    if ($term_days == "9999") {
                        $plan_validity = "2050-12-30 23:23:59";
                    } else {
                        $plan_validity = Carbon::now();
                        $plan_validity->addDays($term_days);
                    }

                    // Transactions count
                    $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
                    $invoice_number = $invoice_count + 1;

                    // Update transaction details
                    Transaction::where('transaction_id', $transactionId)->update([
                        'invoice_prefix' => $config[15]->config_value,
                        'invoice_number' => $invoice_number,
                        'payment_status' => 'SUCCESS',
                    ]);

                    // Update customer details
                    if ($user_details) {
                        $user_details->plan_id              = $transaction_details->plan_id;
                        $user_details->term                 = $term_days;
                        $user_details->plan_validity        = $plan_validity;
                        $user_details->plan_activation_date = now();
                        $user_details->plan_details         = $plan_data;
        
                        $user_details->save();
                    }

                    if ($config[80]->config_value == '1') {
                        // Referral amount details
                        $referralCalculation = [];
                        $referralCalculation['referral_type'] = $config[81]->config_value;
                        $referralCalculation['referral_value'] = $config[82]->config_value;

                        // Check referral_type is percentage or amount
                        if ($config[81]->config_value == '0') {
                            // Plan amount
                            $base_amount = (float) $plan_data->plan_price;
                            
                            $referralCalculation['referral_amount'] = ($base_amount * $referralCalculation['referral_value']) / 100;
                        } else {
                            $referralCalculation['referral_amount'] = $referralCalculation['referral_value'];
                        }

                        // Update referral details
                        Referral::where('user_id', Auth::user()->user_id)->update([
                            'is_subscribed' => 1,
                            'referral_scheme' => json_encode($referralCalculation),
                        ]);
                    }

                    // Save applied coupon
                    AppliedCoupon::where('transaction_id', $transactionId)->update([
                        'status' => 1
                    ]);

                    // Generate JSON
                    $encode = json_decode($transaction_details['invoice_details'], true);
                    $details = [
                        'from_billing_name' => $encode['from_billing_name'],
                        'from_billing_email' => $encode['from_billing_email'],
                        'from_billing_address' => $encode['from_billing_address'],
                        'from_billing_city' => $encode['from_billing_city'],
                        'from_billing_state' => $encode['from_billing_state'],
                        'from_billing_country' => $encode['from_billing_country'],
                        'from_billing_zipcode' => $encode['from_billing_zipcode'],
                        'transaction_id' => $paymentId,
                        'to_billing_name' => $encode['to_billing_name'],
                        'invoice_currency' => $transaction_details->transaction_currency,
                        'subtotal' => $encode['subtotal'],
                        'tax_amount' => (float)($plan_data->plan_price) * (float)($config[25]->config_value) / 100,
                        'applied_coupon' => $encode['applied_coupon'],
                        'discounted_price' => $encode['discounted_price'],
                        'invoice_amount' => $encode['invoice_amount'],
                        'invoice_id' => $config[15]->config_value . $invoice_number,
                        'invoice_date' => $transaction_details->created_at,
                        'description' => $transaction_details->desciption,
                        'email_heading' => $config[27]->config_value,
                        'email_footer' => $config[28]->config_value,
                    ];

                    // Send email to user email
                    try {
                        Mail::to($encode['to_billing_email'])->send(new \App\Mail\SendEmailInvoice($details));
                    } catch (\Exception $e) {
                    }

                    // Page redirect
                    return redirect()->route('user.plans')->with('success', trans('Plan activation success!'));
                } else {

                    $message = "";

                    // Check plan id
                    if ($user_details->plan_id == $transaction_details->plan_id) {

                        // Check if plan validity is expired or not.
                        $plan_validity = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $user_details->plan_validity);
                        $current_date = Carbon::now();
                        $remaining_days = $current_date->diffInDays($plan_validity, false);

                        // Check plan remaining days
                        if ($remaining_days > 0) {
                            // Add days
                            if ($term_days == "9999") {
                                $plan_validity = "2050-12-30 23:23:59";
                                $message = trans("Plan activated successfully!");
                            } else {
                                $plan_validity = Carbon::parse($user_details->plan_validity);
                                $plan_validity->addDays($term_days);
                                $message = trans("Plan activated successfully!");
                            }
                        } else {
                            // Add days
                            if ($term_days == "9999") {
                                $plan_validity = "2050-12-30 23:23:59";
                                $message = trans("Plan activated successfully!");
                            } else {
                                $plan_validity = Carbon::now();
                                $plan_validity->addDays($term_days);
                                $message = trans("Plan activated successfully!");
                            }
                        }

                        // Making all cards inactive, For Plan change
                        BusinessCard::where('user_id', $transaction_details->user_id)->update([
                            'card_status' => 'inactive',
                        ]);
                    } else {
                        // Making all cards inactive, For Plan change
                        BusinessCard::where('user_id', $transaction_details->user_id)->update([
                            'card_status' => 'inactive',
                        ]);

                        // Add days
                        if ($term_days == "9999") {
                            $plan_validity = "2050-12-30 23:23:59";
                            $message = trans("Plan activated successfully!");
                        } else {
                            $plan_validity = Carbon::now();
                            $plan_validity->addDays($term_days);
                            $message = trans("Plan activated successfully!");
                        }
                    }

                    // Transactions count
                    $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
                    $invoice_number = $invoice_count + 1;

                    // Update transaction details
                    Transaction::where('transaction_id', $transactionId)->update([
                        'invoice_prefix' => $config[15]->config_value,
                        'invoice_number' => $invoice_number,
                        'payment_status' => 'SUCCESS',
                    ]);

                    // Update customer plan details
                    if ($user_details) {
                        $user_details->plan_id              = $transaction_details->plan_id;
                        $user_details->term                 = $term_days;
                        $user_details->plan_validity        = $plan_validity;
                        $user_details->plan_activation_date = now();
                        $user_details->plan_details         = $plan_data;
        
                        $user_details->save();
                    }

                    // Save applied coupon
                    AppliedCoupon::where('transaction_id', $transactionId)->update([
                        'status' => 1
                    ]);

                    // Generate JSON
                    $encode = json_decode($transaction_details['invoice_details'], true);
                    $details = [
                        'from_billing_name' => $encode['from_billing_name'],
                        'from_billing_email' => $encode['from_billing_email'],
                        'from_billing_address' => $encode['from_billing_address'],
                        'from_billing_city' => $encode['from_billing_city'],
                        'from_billing_state' => $encode['from_billing_state'],
                        'from_billing_country' => $encode['from_billing_country'],
                        'from_billing_zipcode' => $encode['from_billing_zipcode'],
                        'transaction_id' => $paymentId,
                        'to_billing_name' => $encode['to_billing_name'],
                        'invoice_currency' => $transaction_details->transaction_currency,
                        'subtotal' => $encode['subtotal'],
                        'tax_amount' => (float)($plan_data->plan_price) * (float)($config[25]->config_value) / 100,
                        'applied_coupon' => $encode['applied_coupon'],
                        'discounted_price' => $encode['discounted_price'],
                        'invoice_amount' => $encode['invoice_amount'],
                        'invoice_id' => $config[15]->config_value . $invoice_number,
                        'invoice_date' => $transaction_details->created_at,
                        'description' => $transaction_details->desciption,
                        'email_heading' => $config[27]->config_value,
                        'email_footer' => $config[28]->config_value,
                    ];

                    // Send email to user email
                    try {
                        Mail::to($encode['to_billing_email'])->send(new \App\Mail\SendEmailInvoice($details));
                    } catch (\Exception $e) {
                    }

                    // Page redirect
                    return redirect()->route('user.plans')->with('success', trans($message));
                }
            } else {

                // Update tranaction details
                Transaction::where('transaction_id', $transactionId)->update([
                    'payment_status' => 'FAILED',
                ]);

                // Page redirect
                return redirect()->route('user.plans')->with('failed', trans('Payment failed'));
            }

            // Payment failed
            // Handle the failed payment scenario
            // Page redirect
            return redirect()->route('user.plans')->with('failed', trans('Payment failed'));
        }
    }

    // Failed Payment
    public function paytrPaymentFailure(Request $request)
    {
        $paymentDetails = $request->all();

        if (!$paymentDetails) {
            // Page redirect
            return redirect()->route('user.plans')->with('failed', trans('Payment status update failed'));
        } else {
            // Update tranaction details
            Transaction::where('transaction_id', $paymentDetails['merchant_oid'])->update([
                'payment_status' => 'FAILED',
            ]);

            // Page redirect
            return redirect()->route('user.plans')->with('failed', trans('Payment failed'));
        }
    }

    public function paytrPaymentWebhook(Request $request)
    {
        $paymentDetails = $request->all();

        // Validate required fields
        $request->validate([
            'merchant_oid' => 'required|string',
            'status' => 'required|string',
            'total_amount' => 'required|numeric',
            'hash' => 'required|string',
        ]);

        $transactionId = $paymentDetails['merchant_oid'];

        // Verify PayTR's notification
        $hash = base64_encode(hash_hmac(
            'sha256',
            $paymentDetails['merchant_oid'] . $this->merchant_salt . $paymentDetails['status'] . $paymentDetails['total_amount'],
            $this->merchant_key,
            true
        ));

        if (!hash_equals($hash, $paymentDetails['hash'])) {
            return response('Invalid hash', 400);
        }

        // Retrieve transaction details
        $transaction_details = Transaction::where('transaction_id', $transactionId)->where('status', 1)->first();
        if (!$transaction_details) {
            return response('Transaction not found', 404);
        }

        if ($paymentDetails['status'] !== 'success') {
            // Mark transaction as failed
            Transaction::where('transaction_id', $transactionId)->update(['payment_status' => 'FAILED']);
            return response('Payment failed', 200);
        }

        try {
            // Activate the plan
            $this->activatePlan($transaction_details, $paymentDetails);

            // Respond to PayTR
            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('PayTR Webhook Error: ' . $e->getMessage());
            return response('Internal Server Error', 500);
        }
    }

    protected function activatePlan($transaction_details, $paymentDetails)
    {
        // Retrieve user and plan details
        $user_details = User::find($transaction_details->user_id);
        if (!$user_details) {
            throw new \Exception('User not found');
        }

        $plan_data = Plan::where('plan_id', $transaction_details->plan_id)->first();
        if (!$plan_data) {
            throw new \Exception('Plan not found');
        }

        $term_days = (int) $plan_data->validity;
        $config = DB::table('config')->get();

        // Compute new plan validity
        $plan_validity = $user_details->plan_validity ?: now();
        if ($term_days === '9999') {
            $plan_validity = Carbon::createFromFormat('Y-m-d H:i:s', '2050-12-30 23:23:59');
        } else {
            $plan_validity = Carbon::parse($plan_validity)->addDays($term_days);
        }

        // Update user details
        if ($user_details) {
            $user_details->plan_id              = $transaction_details->plan_id;
            $user_details->term                 = $term_days;
            $user_details->plan_validity        = $plan_validity;
            $user_details->plan_activation_date = now();
            $user_details->plan_details         = $plan_data;

            $user_details->save();
        }

        // Generate invoice number
        $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
        $invoice_number = $invoice_count + 1;

        // Update transaction
        Transaction::where('transaction_id', $transaction_details->transaction_id)->update([
            'invoice_prefix' => $config[15]->config_value,
            'invoice_number' => $invoice_number,
            'payment_status' => 'SUCCESS',
        ]);

        // Update applied coupon
        AppliedCoupon::where('transaction_id', $transaction_details->transaction_id)->update(['status' => 1]);

        // Handle business cards if the plan changes
        if ($user_details->plan_id !== $transaction_details->plan_id) {
            BusinessCard::where('user_id', $transaction_details->user_id)->update(['card_status' => 'inactive']);
        }

        // Generate invoice details
        $encode = json_decode($transaction_details['invoice_details'], true);
        $details = [
            'from_billing_name' => $encode['from_billing_name'],
            'from_billing_email' => $encode['from_billing_email'],
            'from_billing_address' => $encode['from_billing_address'],
            'from_billing_city' => $encode['from_billing_city'],
            'from_billing_state' => $encode['from_billing_state'],
            'from_billing_country' => $encode['from_billing_country'],
            'from_billing_zipcode' => $encode['from_billing_zipcode'],
            'transaction_id' => $transaction_details->transaction_id,
            'to_billing_name' => $encode['to_billing_name'],
            'invoice_currency' => $transaction_details->transaction_currency,
            'subtotal' => $encode['subtotal'],
            'tax_amount' => (float)($plan_data->plan_price) * (float)($config[25]->config_value) / 100,
            'applied_coupon' => $encode['applied_coupon'],
            'discounted_price' => $encode['discounted_price'],
            'invoice_amount' => $encode['invoice_amount'],
            'invoice_id' => $config[15]->config_value . $invoice_number,
            'invoice_date' => $transaction_details->created_at,
            'description' => $transaction_details->desciption,
            'email_heading' => $config[27]->config_value,
            'email_footer' => $config[28]->config_value,
        ];

        // Send invoice email
        try {
            Mail::to($encode['to_billing_email'])->send(new \App\Mail\SendEmailInvoice($details));
        } catch (\Exception $e) {
            Log::error('Invoice email sending failed: ' . $e->getMessage());
        }
    }
}
