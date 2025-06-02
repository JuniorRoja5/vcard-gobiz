<?php

namespace App\Http\Controllers\Payment;

use App\Plan;
use App\User;
use Redirect;
use App\Coupon;
use App\Gateway;
use Carbon\Carbon;
use App\Transaction;
use App\BusinessCard;
use App\AppliedCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function preparePaymentGateway(Request $request, $planId)
    {
        $config = DB::table('config')->get();
        $payment_mode = Gateway::where('payment_gateway_id', $request->payment_gateway_id)->first();

        if ($payment_mode == null) {
            return redirect()->route('user.plans')->with('failed', trans('Please choose valid payment method!'));
        } else {
            $validator = Validator::make($request->all(), [
                'billing_name' => 'required',
                'billing_email' => 'required',
                'billing_address' => 'required',
                'billing_city' => 'required',
                'billing_state' => 'required',
                'billing_country' => 'required',
                'type' => 'required',
            ]);

            if ($validator->fails()) {
                return back()->with('failed', $validator->messages()->all()[0])->withInput();
            }

            User::where('user_id', Auth::user()->user_id)->update([
                'mobile_number' => $request->billing_phone ?? "",
                'billing_name' => $request->billing_name,
                'billing_email' => $request->billing_email,
                'billing_phone' => $request->billing_phone ?? "",
                'whatsapp_number' => $request->billing_whatsapp ?? "",
                'billing_address' => $request->billing_address,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_zipcode' => $request->billing_zipcode ?? "",
                'billing_country' => $request->billing_country,
                'type' => $request->type ?? "",
                'vat_number' => $request->vat_number ?? ""
            ]);

            // Coupon ID
            $couponId = $request->applied_coupon;

            if ($couponId == "") {
                $couponId = " ";
            }

            // Check payment_gateway_amount is 0
            if ((float)$request->payment_gateway_amount <= 0) {
                // Set zero price plan
                $this->zeroPricePlan($request, $planId, $payment_mode->payment_gateway_name, $request->payment_gateway_amount, $request->applied_coupon);

                return redirect()->route('user.plans')->with('success', trans('Your plan has been activated.'));
            } else {
                if ($payment_mode->payment_gateway_id == "60964401751ab") {
                    // Check key and secret
                    if ($config[4]->config_value != "YOUR_PAYPAL_CLIENT_ID" || $config[5]->config_value != "YOUR_PAYPAL_SECRET") {
                        return redirect()->route('paywithpaypal', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use PayPal payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "60964410731d9") {
                    // Check key and secret
                    if ($config[6]->config_value != "YOUR_RAZORPAY_KEY" || $config[7]->config_value != "YOUR_RAZORPAY_SECRET") {
                        return redirect()->route('paywithrazorpay', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use Razorpay payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "60964410732t9") {
                    // Check key and secret
                    if ($config[9]->config_value != "YOUR_STRIPE_PUB_KEY" || $config[10]->config_value != "YOUR_STRIPE_SECRET") {
                        return redirect()->route('paywithstripe', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use Stripe payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "60964410736592") {
                    // Check key and secret
                    if ($config[33]->config_value != "PAYSTACK_PUBLIC_KEY" || $config[34]->config_value != "PAYSTACK_SECRET_KEY") {
                        return redirect()->route('paywithpaystack', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use Paystack payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "6096441071589632") {
                    // Check key and secret
                    if ($config[37]->config_value != "YOUR_MOLLIE_KEY") {
                        return redirect()->route('paywithmollie', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use Mollie payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "659644107y2g5") {
                    // Check key and secret
                    if ($config[31]->config_value != "") {
                        return redirect()->route('paywithoffline', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use Offline payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "19065566166715") {
                    // Check key and secret
                    if ($config[77]->config_value != "YOUR_PHONEPE_CLIENT_ID" || $config[78]->config_value != "YOUR_PHONEPE_CLIENT_VERSION" || $config[79]->config_value != "YOUR_PHONEPE_CLIENT_SECRET") {
                        return redirect()->route('paywithphonepe', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use PhonePe payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "776111730465") {
                    // Check key and secret
                    if ($config[47]->config_value != "YOUR_MERCADO_PAGO_PUBLIC_KEY" || $config[48]->config_value != "YOUR_MERCADO_PAGO_ACCESS_TOKEN") {
                        return redirect()->route('paywithmercadopago', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use Mercado Pago payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "767510608137") {
                    // Check key and secret
                    if ($config[49]->config_value != "YOUR_TOYYIBPAY_API_KEY" || $config[50]->config_value != "YOUR_TOYYIBPAY_CATEGORY_CODE") {
                        return redirect()->route('prepare.toyyibpay', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use Toyyibpay payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "754201940107") {
                    // Check key, secret and encryption key
                    if ($config[51]->config_value != "YOUR_FLW_PUBLIC_KEY" || $config[52]->config_value != "YOUR_FLW_SECRET_KEY" || $config[53]->config_value != "YOUR_FLW_ENCRYPTION_KEY") {
                        return redirect()->route('prepare.flutterwave', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use Flutterwave payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "5992737427969") {
                    // Check key, secret and encryption key
                    if ($config[65]->config_value != "YOUR_PADDLE_SELLER_ID" || $config[66]->config_value != "YOUR_PADDLE_API_KEY" || $config[67]->config_value != "YOUR_PADDLE_CLIENT_SIDE_TOKEN") {
                        return redirect()->route('prepare.paddle', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use Paddle payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "5992737427970") {
                    // Check key, secret and encryption key
                    if ($config[68]->config_value != "YOUR_PAYTR_MERCHANT_ID" || $config[69]->config_value != "YOUR_PAYTR_MERCHANT_KEY" || $config[70]->config_value != "YOUR_PAYTR_MERCHANT_SALT") {
                        return redirect()->route('prepare.paytr', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use PayTR payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "278523098674") {
                    // Check key, secret and encryption key
                    if ($config[72]->config_value != "YOUR_XENDIT_SECRET_KEY") {
                        return redirect()->route('prepare.xendit', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use Xendit payment gateway!. For more information, please contact us.'));
                    }
                } else if ($payment_mode->payment_gateway_id == "278523098675") {
                    // Check key, secret and encryption key
                    if ($config[85]->config_value != "YOUR_CASHFREE_APP_ID" || $config[86]->config_value != "YOUR_CASHFREE_SECRET_KEY") {
                        return redirect()->route('prepare.cashfree', compact('planId', 'couponId'));
                    } else {
                        return redirect()->route('user.plans')->with('failed', trans('You can not use Cashfree payment gateway!. For more information, please contact us.'));
                    }
                } else {
                    return redirect()->route('user.plans')->with('failed', trans('You can not use this payment gateway!. For more information, please contact us.'));
                }
            }
        }
    } 

    // Set zero price plan
    public function zeroPricePlan($request, $planId, $gatewayName, $amount, $couponId)
    {
        // Queries
        $config = DB::table('config')->get();

        // Selected user
        $user_details = User::find(Auth::user()->id);

        // Selected plan
        $selected_plan = Plan::where('plan_id', $planId)->where('status', 1)->first();
        $term_days = $selected_plan->validity;

        // Check applied coupon
        $couponDetails = Coupon::where('used_for', 'plan')->where('coupon_id', $couponId)->first();

        // Check billing details is filled
        if (Auth::user()->billing_name == "") {
            return redirect()->route('user.checkout', $planId);
        } else {

            // Generate transaction id
            $transaction_id = uniqid();

            // Applied tax in total
            $appliedTaxInTotal = ((float)($selected_plan->plan_price) * (float)($config[25]->config_value) / 100);

            if ($couponDetails->coupon_type == 'fixed') {
                // Applied tax in total
                $appliedTaxInTotal = ((float)($selected_plan->plan_price) * (float)($config[25]->config_value) / 100);

                // Get discount in plan price
                $discountPrice = $couponDetails->coupon_amount;

                // Total
                $amountToBePaid = 0;

                // Coupon is applied
                $appliedCoupon = $couponDetails->coupon_code;
            } else {
                // Applied tax in total
                $appliedTaxInTotal = ((float)($selected_plan->plan_price) * (float)($config[25]->config_value) / 100);

                // Get discount in plan price
                $discountPrice = $selected_plan->plan_price * $couponDetails->coupon_amount / 100;

                // Total
                $amountToBePaid = 0;

                // Coupon is applied
                $appliedCoupon = $couponDetails->coupon_code;
            }

            // Generate invoice details
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
            $invoice_details['to_billing_name'] = $request->billing_name;
            $invoice_details['to_billing_address'] = $request->billing_address;
            $invoice_details['to_billing_city'] = $request->billing_city;
            $invoice_details['to_billing_state'] = $request->billing_state;
            $invoice_details['to_billing_zipcode'] = $request->billing_zipcode;
            $invoice_details['to_billing_country'] = $request->billing_country;
            $invoice_details['to_billing_phone'] = $request->billing_phone;
            $invoice_details['to_billing_email'] = $request->billing_email;
            $invoice_details['to_vat_number'] = $request->vat_number;
            $invoice_details['subtotal'] = $selected_plan->plan_price;
            $invoice_details['tax_name'] = $config[24]->config_value;
            $invoice_details['tax_type'] = $config[14]->config_value;
            $invoice_details['tax_value'] = $config[25]->config_value;
            $invoice_details['tax_amount'] = $appliedTaxInTotal;
            $invoice_details['applied_coupon'] = $couponDetails->coupon_code;
            $invoice_details['discounted_price'] = $discountPrice;
            $invoice_details['invoice_amount'] = $amountToBePaid;

            // Save applied coupon
            $appliedCoupon = new AppliedCoupon;
            $appliedCoupon->applied_coupon_id = uniqid();
            $appliedCoupon->transaction_id = $transaction_id;
            $appliedCoupon->user_id = Auth::user()->id;
            $appliedCoupon->coupon_id = $couponId;
            $appliedCoupon->status = 1;
            $appliedCoupon->save();

            // Save new transaction
            $transaction = new Transaction();
            $transaction->gobiz_transaction_id = uniqid();
            $transaction->transaction_date = now();
            $transaction->transaction_id = $transaction_id;
            $transaction->user_id = Auth::user()->id;
            $transaction->plan_id = $selected_plan->plan_id;
            $transaction->desciption = $selected_plan->plan_name . " Plan";
            $transaction->payment_gateway_name = $gatewayName;
            $transaction->transaction_amount = $amount;
            $transaction->transaction_currency = $config[1]->config_value;
            $transaction->invoice_details = json_encode($invoice_details);
            $transaction->payment_status = "SUCCESS";
            $transaction->save();

            // Get transaction details
            $transaction_details = Transaction::where('transaction_id', $transaction_id)->where('status', 1)->first();

            // Add new plan validity
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
                    } else {
                        $plan_validity = Carbon::parse($user_details->plan_validity);
                        $plan_validity->addDays($term_days);
                    }
                } else {
                    // Add days
                    if ($term_days == "9999") {
                        $plan_validity = "2050-12-30 23:23:59";
                    } else {
                        $plan_validity = Carbon::now();
                        $plan_validity->addDays($term_days);
                    }
                }

                // Making all cards inactive, For Plan change
                BusinessCard::where('user_id', Auth::user()->user_id)->update([
                    'card_status' => 'inactive',
                ]);
            } else {

                // Making all cards inactive, For Plan change
                BusinessCard::where('user_id', Auth::user()->user_id)->update([
                    'card_status' => 'inactive',
                ]);

                // Add days
                if ($term_days == "9999") {
                    $plan_validity = "2050-12-30 23:23:59";
                } else {
                    $plan_validity = Carbon::now();
                    $plan_validity->addDays($term_days);
                }
            }

            // Update validity in user
            User::where('user_id', Auth::user()->user_id)->update([
                'plan_id' => $planId,
                'term' => $selected_plan->validity,
                'plan_validity' => $plan_validity,
                'plan_activation_date' => now(),
                'plan_details' => $selected_plan,
            ]);

            // Invoice number
            $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
            $invoice_number = $invoice_count + 1;

            $encode = json_decode($transaction_details['invoice_details'], true);
            $details = [
                'from_billing_name' => $encode['from_billing_name'],
                'from_billing_email' => $encode['from_billing_email'],
                'from_billing_address' => $encode['from_billing_address'],
                'from_billing_city' => $encode['from_billing_city'],
                'from_billing_state' => $encode['from_billing_state'],
                'from_billing_country' => $encode['from_billing_country'],
                'from_billing_zipcode' => $encode['from_billing_zipcode'],
                'gobiz_transaction_id' => $transaction_details->gobiz_transaction_id,
                'to_billing_name' => $encode['to_billing_name'],
                'invoice_currency' => $transaction_details->transaction_currency,
                'subtotal' => $encode['subtotal'],
                'tax_amount' => (float)($amount) * (float)($config[25]->config_value) / 100,
                'applied_coupon' => $encode['applied_coupon'],
                'discounted_price' => $encode['discounted_price'],
                'invoice_amount' => $encode['invoice_amount'],
                'invoice_id' => $config[15]->config_value . $invoice_number,
                'invoice_date' => $transaction_details->created_at,
                'description' => $transaction_details->desciption,
                'email_heading' => $config[27]->config_value,
                'email_footer' => $config[28]->config_value,
            ];

            try {
                Mail::to($encode['to_billing_email'])->send(new \App\Mail\SendEmailInvoice($details));
            } catch (\Exception $e) {
            }
        }
    }
}
