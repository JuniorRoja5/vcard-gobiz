<?php

namespace App\Http\Controllers\Payment;

use App\Plan;
use App\User;
use App\Coupon;
use App\Transaction;
use App\AppliedCoupon;
use App\Classes\UpgradePlan;
use Illuminate\Http\Request;
use App\Services\XenditService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class XenditController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService) 
    {
        $this->xenditService = $xenditService;
    }

    public function generatePaymentLink(Request $request, $planId, $couponId)
    {
        if (Auth::user()) {
            // Queries
            $config = DB::table('config')->get();

            // Get user details
            $userData = User::where('id', Auth::user()->id)->first();

            // Get plan details
            $plan_details = Plan::where('plan_id', $planId)->where('status', 1)->first();
            if (!$plan_details) {
                return redirect()->route('user.plans')->with('failed', trans('Invalid plan!'));
            }

            // Validate Xendit access token
            if ($config[72]->config_value == null) {
                return redirect()->route('user.plans')->with('failed', trans('Something went wrong!'));
            }

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

                $amountToBePaidPaise = $amountToBePaid;

                // Define your return and cancel URLs
                $successRedirectUrl = route('xendit.payment.status', ['transactionId' => $gobiz_transaction_id]);  // URL to redirect user after successful payment
                $failureRedirectUrl = route('xendit.payment.failure', ['transactionId' => $gobiz_transaction_id]);   // URL to redirect user if payment is canceled

                // Create an invoice with return and cancel URLs
                $response = $this->xenditService->createInvoice(
                    $gobiz_transaction_id,
                    $amountToBePaidPaise,
                    $userData->email,
                    'Payment for Order',
                    $successRedirectUrl
                );

                // Check for success and retrieve the invoice details
                if ($response['status'] == 'PENDING') {

                    // Invoice ID
                    $transaction_id = $response['id'];

                    // Prepare invoice details
                    $invoice_details = [
                        'from_billing_name' => $config[16]->config_value,
                        'from_billing_address' => $config[19]->config_value,
                        'from_billing_city' => $config[20]->config_value,
                        'from_billing_state' => $config[21]->config_value,
                        'from_billing_zipcode' => $config[22]->config_value,
                        'from_billing_country' => $config[23]->config_value,
                        'from_vat_number' => $config[26]->config_value,
                        'from_billing_phone' => $config[18]->config_value,
                        'from_billing_email' => $config[17]->config_value,
                        'to_billing_name' => $userData->billing_name,
                        'to_billing_address' => $userData->billing_address,
                        'to_billing_city' => $userData->billing_city,
                        'to_billing_state' => $userData->billing_state,
                        'to_billing_zipcode' => $userData->billing_zipcode,
                        'to_billing_country' => $userData->billing_country,
                        'to_billing_phone' => $userData->billing_phone,
                        'to_billing_email' => $userData->billing_email,
                        'to_vat_number' => $userData->vat_number,
                        'subtotal' => $plan_details->plan_price,
                        'tax_name' => $config[24]->config_value,
                        'tax_type' => $config[14]->config_value,
                        'tax_value' => $config[25]->config_value,
                        'tax_amount' => $appliedTaxInTotal,
                        'applied_coupon' => $appliedCoupon,
                        'discounted_price' => $discountPrice,
                        'invoice_amount' => $amountToBePaid,
                    ];

                    // Create a new transaction entry in the database
                    $transaction = new Transaction();
                    $transaction->gobiz_transaction_id = $gobiz_transaction_id;
                    $transaction->transaction_date = now();
                    $transaction->transaction_id = $transaction_id;
                    $transaction->user_id = Auth::user()->id;
                    $transaction->plan_id = $plan_details->plan_id;
                    $transaction->desciption = $plan_details->plan_name . " Plan";
                    $transaction->payment_gateway_name = "Xendit";
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
                        $appliedCoupon->transaction_id = $transaction_id;
                        $appliedCoupon->user_id = Auth::user()->id;
                        $appliedCoupon->coupon_id = $couponId;
                        $appliedCoupon->status = 0;
                        $appliedCoupon->save();
                    }

                    // Redirect to Xendit payment page
                    return redirect($response['invoice_url']);
                } else {
                    // Handle API error
                    return redirect()->route('user.plans')->with('failed', trans('Unable to create payment'));
                }
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function xenditPaymentStatus(Request $request, $transactionId)
    {
        // Get transaction details based on the transactionId
        $transaction_details = Transaction::where('gobiz_transaction_id', $transactionId)->first();

        if (!$transaction_details) {
            return redirect()->route('user.plans')->with('failed', trans('Transaction not found or already processed.'));
        }

        // Get Xendit payment status by passing the correct invoice ID (transactionId in this case)
        $paymentStatus = $this->xenditService->getInvoiceById($transaction_details->transaction_id);

        // Check if the payment status exists to avoid errors
        if (!isset($paymentStatus['status'])) {
            return redirect()->route('user.plans')->with('failed', trans('Unable to retrieve payment status.'));
        }

        // Check payment status
        switch ($paymentStatus['status']) {
            case 'PAID':
                // Plan upgrade
                $upgradePlan = new UpgradePlan;
                $upgradePlan->upgrade($transaction_details->transaction_id, "PAID");
                
                return redirect()->route('user.plans')->with('success', trans('Your payment has been successful.'));
            case 'SETTLED':
                // Plan upgrade
                $upgradePlan = new UpgradePlan;
                $upgradePlan->upgrade($transaction_details->transaction_id, "SETTLED");
                
                return redirect()->route('user.plans')->with('success', trans('Your payment has been successful.'));
            case 'FAILED':
                // Payment has failed
                return redirect()->route('user.plans')->with('failed', trans('Your payment has failed.'));
            case 'PENDING':
                // Payment is pending
                return redirect()->route('user.plans')->with('pending', trans('Your payment is pending.'));
            case 'CANCELED':
                // Payment has failed
                return redirect()->route('user.plans')->with('failed', trans('Your payment has failed.'));
            case 'EXPIRED':
                // Payment has failed
                return redirect()->route('user.plans')->with('failed', trans('Your payment has failed.'));
            default:
                // Unknown payment status
                return redirect()->route('user.plans')->with('failed', trans('Unable to determine payment status.'));
        }
    }

    public function xenditPaymentWebhook(Request $request)
    {
        // Get transaction details based on the transactionId
        $transaction_details = Transaction::where('gobiz_transaction_id', $request->transactionId)->first();

        if (!$transaction_details) {
            return redirect()->route('user.plans')->with('failed', trans('Transaction not found or already processed.'));
        }
        
        // Get Xendit payment status by passing the correct invoice ID (transactionId in this case)
        $paymentStatus = $this->xenditService->getInvoiceById($transaction_details->transaction_id);

        // Check if the payment status exists to avoid errors
        if (!isset($paymentStatus['status'])) {
            return redirect()->route('user.plans')->with('failed', trans('Unable to retrieve payment status.'));
        }

        // Check payment status
        switch ($paymentStatus['status']) {
            case 'PAID':
                // Plan upgrade
                $upgradePlan = new UpgradePlan;
                $upgradePlan->upgrade($transaction_details->transaction_id, "PAID");
                
                return redirect()->route('user.plans')->with('success', trans('Your payment has been successful.'));
            case 'SETTLED':
                // Plan upgrade
                $upgradePlan = new UpgradePlan;
                $upgradePlan->upgrade($transaction_details->transaction_id, "SETTLED");
                
                return redirect()->route('user.plans')->with('success', trans('Your payment has been successful.'));
            case 'FAILED':
                // Payment has failed
                Transaction::where('transaction_id', $transaction_details->transaction_id)->update([
                    'payment_status' => 'FAILED',
                ]);

                return redirect()->route('user.plans')->with('failed', trans('Your payment has failed.'));
            case 'PENDING':
                // Payment has failed
                Transaction::where('transaction_id', $transaction_details->transaction_id)->update([
                    'payment_status' => 'PENDING',
                ]);

                // Payment is pending
                return redirect()->route('user.plans')->with('pending', trans('Your payment is pending.'));
            case 'CANCELED':
                // Payment has failed
                Transaction::where('transaction_id', $transaction_details->transaction_id)->update([
                    'payment_status' => 'FAILED',
                ]);

                return redirect()->route('user.plans')->with('failed', trans('Your payment has failed.'));
            case 'EXPIRED':
                // Payment has failed
                Transaction::where('transaction_id', $transaction_details->transaction_id)->update([
                    'payment_status' => 'FAILED',
                ]);

                return redirect()->route('user.plans')->with('failed', trans('Your payment has failed.'));
            default:
                // Payment has failed
                Transaction::where('transaction_id', $transaction_details->transaction_id)->update([
                    'payment_status' => 'FAILED',
                ]);
                
                // Unknown payment status
                return redirect()->route('user.plans')->with('failed', trans('Unable to determine payment status.'));
        }
    }
}
