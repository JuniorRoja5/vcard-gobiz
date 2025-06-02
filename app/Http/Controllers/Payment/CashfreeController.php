<?php

namespace App\Http\Controllers\Payment;

use App\Plan;
use App\User;
use App\Coupon;
use App\Transaction;
use App\AppliedCoupon;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use App\Classes\UpgradePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CashfreeController extends Controller
{
    protected $appId;
    protected $secretKey;
    protected $baseUrl;
    protected $mode;

    public function __construct()
    {
        // Get API key and category code from config table
        $config = DB::table('config')->get();

        $this->appId = $config[85]->config_value;
        $this->secretKey = $config[86]->config_value;
        $this->baseUrl = $config[84]->config_value === 'test' ? 'https://sandbox.cashfree.com/pg' : 'https://api.cashfree.com/pg';
    }

    // Generate payment link
    public function generatePaymentLink(Request $request, $planId, $couponId)
    {
        // Check if user is logged in
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

            // Check plan details
            if ($plan_details == null) {
                return view('errors.404');
            } else {
                $gobiz_transaction_id = "TX" . preg_replace('/\D/', '', Str::uuid());

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

                $data = [
                    'order_id' =>  $gobiz_transaction_id,
                    'order_amount' => $amountToBePaidPaise,
                    "order_currency" => $config[1]->config_value,
                    "customer_details" => [
                        "customer_id" => Auth::user()->user_id,
                        "customer_name" => Auth::user()->name,
                        "customer_email" => Auth::user()->email,
                        "customer_phone" => Auth::user()->billing_phone,
                    ],
                    "order_meta" => [
                        "return_url" => route('cashfree.payment.status') . '?order_id={order_id}',
                    ]
                ];

                try {
                    $response = Http::withHeaders([
                        "Content-Type"     => "application/json",
                        "x-api-version"    => "2022-01-01",
                        "x-client-id"      => $this->appId,
                        "x-client-secret"  => $this->secretKey,
                    ])->post("{$this->baseUrl}/orders", $data);
                
                    $responseBody = $response->json(); // Directly parse JSON response
                
                    if (isset($responseBody['order_status']) && $responseBody['order_status'] === 'ACTIVE') {
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

                        // Order ID
                        $orderId = $responseBody['cf_order_id'];

                        // Create a new transaction entry in the database
                        $transaction = new Transaction();
                        $transaction->gobiz_transaction_id = $gobiz_transaction_id;
                        $transaction->transaction_date = now();
                        $transaction->transaction_id = $gobiz_transaction_id;
                        $transaction->user_id = Auth::user()->id;
                        $transaction->plan_id = $plan_details->plan_id;
                        $transaction->desciption = $plan_details->plan_name . " Plan";
                        $transaction->payment_gateway_name = "Cashfree";
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
                            $appliedCoupon->transaction_id = $gobiz_transaction_id;
                            $appliedCoupon->user_id = Auth::user()->id;
                            $appliedCoupon->coupon_id = $couponId;
                            $appliedCoupon->status = 0;
                            $appliedCoupon->save();
                        }

                        // Redirect to payment link if available
                        if (isset($responseBody['payment_link'])) {
                            return redirect()->to($responseBody['payment_link']);
                        }
                    }

                    return redirect()->route('user.plans')->with('failed', trans('Payment initiation failed'));
                } catch (\Exception $e) {
                    return redirect()->route('user.plans')->with('failed', trans('Failed to initiate payment.'));
                }
            }
        } else {
            return redirect()->route('login');
        }
    }

    // Get payment status
    public function cashfreePaymentStatus(Request $request)
    {
        // Order ID
        $order_id = $request->query('order_id');

        // Get last transaction id for phonepe and user id
        $transactionDetails = Transaction::where('payment_gateway_name', 'Cashfree')->where('transaction_id', $order_id)->orderBy('id', 'desc')->first();

        // Queries
        $config = DB::table('config')->get();;  // If you have a config file
        $appId = $config[85]->config_value;
        $secretKey = $config[86]->config_value;
        $mode = $config[84]->config_value;  // sandbox or production

        $baseUrl = $mode === 'test'
            ? 'https://sandbox.cashfree.com/pg/orders/' . $order_id . '/payments'
            : 'https://api.cashfree.com/pg/orders/' . $order_id . '/payments';


        $response = Http::withHeaders([
            'x-client-id'     => $appId,
            'x-client-secret' => $secretKey,
            'Content-Type'    => 'application/json',
            'x-api-version'   => '2022-09-01',
        ])->get($baseUrl);

        if ($response->successful()) {
            $paymentDetailsArray = json_decode($response->body(), true);
        
            // Check if response is valid and contains at least one element
            if (!is_array($paymentDetailsArray) || empty($paymentDetailsArray[0])) {
                return redirect()->route('user.plans')->with('failed', trans('Invalid payment response.'));
            }
        
            $paymentDetails = (object) $paymentDetailsArray[0];
        
            // Check payment status
            if (isset($paymentDetails->payment_status) && $paymentDetails->payment_status == "SUCCESS") {
                // Get transactionId
                $orderId = $paymentDetails->order_id ?? null;
                $transactionId = $paymentDetails->cf_payment_id ?? null;
        
                if (!$orderId || !$transactionId) {
                    return redirect()->route('user.plans')->with('failed', trans('Missing order or transaction ID.'));
                }
        
                $upgradePlan = new UpgradePlan;
                $upgradePlan->upgrade($orderId, $paymentDetails);
                
                // Update transaction id
                Transaction::where('gobiz_transaction_id', $transactionDetails->gobiz_transaction_id)
                    ->update(['transaction_id' => $transactionId]);
                
                return redirect()->route('user.plans')->with('success', trans('Plan activated successfully!'));
            } else {
                Transaction::where('gobiz_transaction_id', $transactionDetails->gobiz_transaction_id)
                    ->update(['payment_status' => 'FAILED']);
                
                return redirect()->route('user.plans')->with('failed', trans('Payment failed.'));
            }
        } else {
            Transaction::where('gobiz_transaction_id', $transactionDetails->gobiz_transaction_id)
                ->update(['payment_status' => 'FAILED']);
                
            return redirect()->route('user.plans')->with('failed', trans('Payment failed.'));
        }        

        return redirect()->route('user.plans')->with('failed', trans('Payment failed.'));
    }
}
