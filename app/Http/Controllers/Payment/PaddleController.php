<?php

namespace App\Http\Controllers\Payment;

use App\Plan;
use App\User;
use App\Coupon;
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

class PaddleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generatePaymentLink(Request $request, $planId, $couponId)
    {
        if (Auth::user()) {
            // Queries
            $config = DB::table('config')->get();
            $userData = User::where('id', Auth::user()->id)->first();
            $plan_details = Plan::where('plan_id', $planId)->where('status', 1)->first();

            // Check plan details
            if ($plan_details == null) {
                return view('errors.404');
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
                $transaction->payment_gateway_name = "Paddle";
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

                // Call
                $client = new \GuzzleHttp\Client();

                // Check sandbox or live
                $sandbox = $config[64]->config_value;

                if ($sandbox == "true") {
                    $url = 'https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link';
                } else {
                    $url = 'https://vendors.paddle.com/api/2.0/product/generate_pay_link';
                }

                try {
                    $response = $client->request('POST', $url, [
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/x-www-form-urlencoded',
                        ],
                        'form_params' => [
                            'vendor_id' => $config[65]->config_value,
                            'vendor_auth_code' => $config[66]->config_value,
                            "title" => trans("Plan purchase for ") . $plan_details->plan_name,
                            "image_url" => url('img/favicon.png'),
                            "prices" => [
                                $config[1]->config_value . ':' . $amountToBePaid
                            ],
                            'quantity' => 1, 
                            'quantity_variable' => 0,
                            'discountable' => 0,
                            'expires' => Carbon::now()->addMinutes(15),
                            'customer_email' => Auth::user()->email,
                            'customer_postcode' => $userData->billing_pincode,
                            'return_url' => route('paddle.payment.status') . '?' . http_build_query([
                                'checkout' => '{checkout_hash}',
                                'passthrough' => '{"user_id": "' . Auth::user()->id . '", "transaction_id": "' . $transactionId . '"}'
                            ]),
                            'webhook_url' => route('paddle.payment.webhook'),
                            // 'webhook_url' => "https://nativecode.in/payment/paddle/webhook",
                            'passthrough' => '{"user_id": "' . Auth::user()->id . '", "transaction_id": "' . $transactionId . '"}'
                        ]
                    ]);

                    if ($response->getStatusCode() == 200) {
                        $data = json_decode($response->getBody(), true);

                        return redirect()->to($data['response']['url']);
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('Something went wrong!'));
                    }
                } catch (\Exception $e) {
                    return redirect()->route('user.plans')->with('failed', trans('Something went wrong!'));
                }
            }
        } else {
            return redirect()->route('login');
        }
    }

    // Successful Payment
    public function paddlePaymentStatus(Request $request)
    {
        // Queries
        $config = DB::table('config')->get();

        if ($request->has('checkout')) {
            $checkoutId = $request->get('checkout');
            $passthrough = json_decode($request->get('passthrough'), true);

            // Fetch transaction details via Paddle API
            $client = new \GuzzleHttp\Client();

            // Check sandbox or live
            $sandbox = $config[64]->config_value;

            if ($sandbox == "true") {
                $url = 'https://sandbox-vendors.paddle.com/api/2.0/subscription/payments';
            } else {
                $url = 'https://vendors.paddle.com/api/2.0/subscription/payments';
            }

            try {
                $response = $client->request('POST', $url, [
                    'form_params' => [
                        'vendor_id' => $config[65]->config_value,
                        'vendor_auth_code' => $config[66]->config_value,
                        'checkout_id' => $checkoutId,
                    ],
                ]);

                $data = json_decode($response->getBody(), true);

                // Get user id and transaction id
                $user_id = $passthrough['user_id'];
                $transaction_id = $passthrough['transaction_id'];

                // dd($passthrough);

                if ($data['success']) {
                    // Get transaction details based on the transaction_id
                    $transaction_details = Transaction::where('transaction_id', $transaction_id)->first();

                    if (!$transaction_details) {
                        return redirect()->route('user.plans')->with('failed', trans('Transaction not found or already processed.'));
                    }

                    // Get user details
                    $user_details = User::find($user_id);

                    // Get plan data
                    $plan_data = Plan::where('plan_id', $transaction_details->plan_id)->first();
                    $term_days = (int) $plan_data->validity;

                    if ($user_details->plan_validity == "") {
                        // Add days
                        if ($term_days == "9999") {
                            $plan_validity = "2050-12-30 23:23:59";
                        } else {
                            $plan_validity = Carbon::now();
                            $plan_validity->addDays($term_days);
                        }

                        // Generate invoice number
                        $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
                        $invoice_number = $invoice_count + 1;

                        // Update transaction details
                        Transaction::where('transaction_id', $transaction_id)->update([
                            'transaction_id' => $checkoutId,
                            'invoice_prefix' => $config[15]->config_value,
                            'invoice_number' => $invoice_number,
                            'payment_status' => 'SUCCESS',
                            'updated_at' => now(),
                        ]);

                        // Update user details
                        if ($user_details) {
                            $user_details->plan_id              = $transaction_details->plan_id;
                            $user_details->term                 = $term_days;
                            $user_details->plan_validity        = $plan_validity;
                            $user_details->plan_activation_date = now();
                            $user_details->plan_details         = $plan_data;
            
                            $user_details->save();
                        }

                        // Check enable referral system
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
                        AppliedCoupon::where('transaction_id', $transaction_id)->update([
                            'status' => 1
                        ]);

                        $message = trans('Plan activation success!');
                    } else {
                        // Renew existing plan
                        $plan_validity = Carbon::createFromFormat('Y-m-d H:i:s', $user_details->plan_validity);
                        $current_date = Carbon::now();
                        $remaining_days = $current_date->diffInDays($plan_validity, false);

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

                            $message = trans('Plan renewed successfully!');
                        } else {
                            // Add days
                            if ($term_days == "9999") {
                                $plan_validity = "2050-12-30 23:23:59";
                                $message = trans("Plan activated successfully!");
                            } else {
                                $plan_validity = Carbon::parse($user_details->plan_validity);
                                $plan_validity->addDays($term_days);
                                $message = trans("Plan activated successfully!");
                            }

                            $message = trans("Plan activated successfully!");
                        }

                        // Generate invoice number
                        $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
                        $invoice_number = $invoice_count + 1;

                        // Update transaction details
                        Transaction::where('transaction_id', $transaction_id)->update([
                            'transaction_id' => $checkoutId,
                            'invoice_prefix' => $config[15]->config_value,
                            'invoice_number' => $invoice_number,
                            'payment_status' => 'SUCCESS',
                            'updated_at' => now(),
                        ]);

                        // Update user details
                        if ($user_details) {
                            $user_details->plan_id              = $transaction_details->plan_id;
                            $user_details->term                 = $term_days;
                            $user_details->plan_validity        = $plan_validity;
                            $user_details->plan_activation_date = now();
                            $user_details->plan_details         = $plan_data;
            
                            $user_details->save();
                        }

                        // Save applied coupon
                        AppliedCoupon::where('transaction_id', $transaction_id)->update([
                            'status' => 1
                        ]);
                    }

                    // Making all cards inactive, For Plan change
                    BusinessCard::where('user_id', Auth::user()->user_id)->update([
                        'card_status' => 'inactive',
                    ]);

                    // Generate and send invoice details
                    $encode = json_decode($transaction_details->invoice_details, true);

                    $details = [
                        'from_billing_name' => $encode['from_billing_name'],
                        'from_billing_email' => $encode['from_billing_email'],
                        'from_billing_address' => $encode['from_billing_address'],
                        'from_billing_city' => $encode['from_billing_city'],
                        'from_billing_state' => $encode['from_billing_state'],
                        'from_billing_country' => $encode['from_billing_country'],
                        'from_billing_zipcode' => $encode['from_billing_zipcode'],
                        'gobiz_transaction_id' => $checkoutId,
                        'to_billing_name' => $encode['to_billing_name'],
                        'to_vat_number' => $encode['to_vat_number'],
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

                    // Send invoice via email
                    try {
                        Mail::to($encode['to_billing_email'])->send(new \App\Mail\SendEmailInvoice($details));
                    } catch (\Exception $e) {
                        // Handle email sending failure if needed
                    }

                    // Redirect to the user's plans page with success message
                    return redirect()->route('user.plans')->with('success', $message);
                } else {
                    // Update the transaction status to FAILED
                    Transaction::where('transaction_id', $transaction_id)->update(['payment_status' => 'FAILED', 'updated_at' => now()]);

                    return redirect()->route('user.plans')->with('failed', trans('Payment failed!'));
                }
            } catch (\Exception $e) {
                return redirect()->route('user.plans')->with('failed', trans('Something went wrong!'));
            }
        }

        return redirect()->route('user.plans')->with('failed', trans('Payment failed!'));
    }

    // Paddle webhook
    public function paddleWebhook(Request $request)
    {
        // Get the webhook data
        $webhookData = $request->getContent();

        // Decode the webhook data
        $webhookData = json_decode($webhookData, true);

        // Get payment status
        $paymentStatus = $webhookData['status'];

        // Get the payment id
        $paymentId = $webhookData['payment_id'];

        // Get the passthrough
        $passthrough = $webhookData['passthrough'];

        // Get the user id
        $user_id = $passthrough['user_id'];

        // Get the transaction id
        $transaction_id = $passthrough['transaction_id'];

        // If payment status is success
        if ($paymentStatus == 'success') {
            // Get config
            $config = DB::table('config')->get();

            // Get transaction details based on the transaction_id
            $transaction_details = Transaction::where('transaction_id', $transaction_id)->first();

            // Get user details
            $user_details = User::find($user_id);

            // Get plan data
            $plan_data = Plan::where('plan_id', $transaction_details->plan_id)->first();
            $term_days = (int) $plan_data->validity;

            if ($user_details->plan_validity == "") {
                // Add days
                if ($term_days == "9999") {
                    $plan_validity = "2050-12-30 23:23:59";
                } else {
                    $plan_validity = Carbon::now();
                    $plan_validity->addDays($term_days);
                }

                // Generate invoice number
                $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
                $invoice_number = $invoice_count + 1;

                // Update transaction details
                Transaction::where('transaction_id', $transaction_id)->update([
                    'transaction_id' => $paymentId,
                    'invoice_prefix' => $config[15]->config_value,
                    'invoice_number' => $invoice_number,
                    'payment_status' => 'SUCCESS',
                    'updated_at' => now(),
                ]);

                // Update user details
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
                AppliedCoupon::where('transaction_id', $transaction_id)->update([
                    'status' => 1
                ]);

                $message = trans('Plan activation success!');
            } else {
                // Renew existing plan
                $plan_validity = Carbon::createFromFormat('Y-m-d H:i:s', $user_details->plan_validity);
                $current_date = Carbon::now();
                $remaining_days = $current_date->diffInDays($plan_validity, false);

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

                    $message = trans('Plan renewed successfully!');
                } else {
                    // Add days
                    if ($term_days == "9999") {
                        $plan_validity = "2050-12-30 23:23:59";
                        $message = trans("Plan activated successfully!");
                    } else {
                        $plan_validity = Carbon::parse($user_details->plan_validity);
                        $plan_validity->addDays($term_days);
                        $message = trans("Plan activated successfully!");
                    }

                    $message = trans("Plan activated successfully!");
                }

                // Generate invoice number
                $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
                $invoice_number = $invoice_count + 1;

                // Update transaction details
                Transaction::where('transaction_id', $transaction_id)->update([
                    'transaction_id' => $paymentId,
                    'invoice_prefix' => $config[15]->config_value,
                    'invoice_number' => $invoice_number,
                    'payment_status' => 'SUCCESS',
                    'updated_at' => now(),
                ]);

                // Update user details
                if ($user_details) {
                    $user_details->plan_id              = $transaction_details->plan_id;
                    $user_details->term                 = $term_days;
                    $user_details->plan_validity        = $plan_validity;
                    $user_details->plan_activation_date = now();
                    $user_details->plan_details         = $plan_data;
    
                    $user_details->save();
                }

                // Save applied coupon
                AppliedCoupon::where('transaction_id', $transaction_id)->update([
                    'status' => 1
                ]);
            }

            // Making all cards inactive, For Plan change
            BusinessCard::where('user_id', Auth::user()->user_id)->update([
                'card_status' => 'inactive',
            ]);

            // Generate and send invoice details
            $encode = json_decode($transaction_details->invoice_details, true);

            $details = [
                'from_billing_name' => $encode['from_billing_name'],
                'from_billing_email' => $encode['from_billing_email'],
                'from_billing_address' => $encode['from_billing_address'],
                'from_billing_city' => $encode['from_billing_city'],
                'from_billing_state' => $encode['from_billing_state'],
                'from_billing_country' => $encode['from_billing_country'],
                'from_billing_zipcode' => $encode['from_billing_zipcode'],
                'gobiz_transaction_id' => $paymentId,
                'to_billing_name' => $encode['to_billing_name'],
                'to_vat_number' => $encode['to_vat_number'],
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

            // Send invoice via email
            try {
                Mail::to($encode['to_billing_email'])->send(new \App\Mail\SendEmailInvoice($details));
            } catch (\Exception $e) {
                // Handle email sending failure if needed
            }
        }

        // If payment status is pending
        if ($paymentStatus == 'pending') {
            // Update the transaction status to FAILED
            Transaction::where('transaction_id', $transaction_id)->update(['payment_status' => 'PENDING', 'updated_at' => now()]);
        }

        // If payment status is failure
        if ($paymentStatus == 'failure') {
            // Update the transaction status to FAILED
            Transaction::where('transaction_id', $transaction_id)->update(['payment_status' => 'FAILED', 'updated_at' => now()]);
        }

        // If payment status is error
        if ($paymentStatus == 'error') {
            // Update the transaction status to FAILED
            Transaction::where('transaction_id', $transaction_id)->update(['payment_status' => 'FAILED', 'updated_at' => now()]);
        }

        // If payment status is canceled
        if ($paymentStatus == 'canceled') {
            // Update the transaction status to FAILED
            Transaction::where('transaction_id', $transaction_id)->update(['payment_status' => 'CANCELED', 'updated_at' => now()]);
        }
    }
}
