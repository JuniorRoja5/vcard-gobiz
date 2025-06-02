<?php

namespace App\Http\Controllers\Payment\NFC;

use App\Plan;
use App\User;
use App\Coupon;
use App\Gateway;
use Carbon\Carbon;
use App\AppliedCoupon;
use App\NfcCardDesign;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Classes\ZeroOrderNFC;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    // Payment Gateway
    public function placeOrder(Request $request, $nfcId)
    {
        // Queries
        $config = DB::table('config')->get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }

        // Check available stock
        $nfcCard = NfcCardDesign::where('nfc_card_id', $nfcId)->first();
        if ($nfcCard->available_stocks <= 0) {
            return redirect()->route('user.order.nfc.cards')->with('failed', __('This NFC card is out of stock.'));
        }

        // Check if the user has a plan
        $active_plan = Plan::where('plan_id', Auth::user()->plan_id)->first();
        $plan = User::where('user_id', Auth::user()->user_id)->first();
        $active_plan = json_decode($plan->plan_details);

        // Check "nfc_card" is available in the plan
        if ($active_plan->nfc_card == 1) {

            // Get payment gateway details
            $payment_mode = Gateway::where('payment_gateway_id', $request->payment_gateway_id)->first();

            // Check payment mode
            if ($payment_mode == null) {
                return redirect()->route('user.order.nfc.card.checkout')->with('failed', trans('Select a payment method not available. Please choose another payment method.'));
            } else {
                // Validate request
                $validator = Validator::make($request->all(), [
                    'shipping_name' => 'required',
                    'shipping_email' => 'required',
                    'shipping_address' => 'required',
                    'shipping_city' => 'required',
                    'billing_country' => 'required',
                    'shipping_phone' => 'required',
                    'shipping_state' => 'required',
                    'type' => 'required',
                ]);

                if ($validator->fails()) {
                    return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', $validator->messages()->all()[0])->withInput();
                }

                User::where('user_id', Auth::user()->user_id)->update([
                    'billing_name' => $request->shipping_name,
                    'billing_address' => $request->shipping_address,
                    'billing_city' => $request->shipping_city,
                    'billing_state' => $request->shipping_state,
                    'billing_zipcode' => $request->shipping_zipcode ?? "",
                    'billing_country' => $request->billing_country,
                    'billing_phone' => $request->shipping_phone ?? "",
                    'billing_email' => $request->shipping_email,
                    'type' => $request->type ?? "",
                    'vat_number' => $request->vat_number ?? ""
                ]);

                // Insert plugins provider in config file
                $configPath = config_path('app.php');

                // Read the current config file
                $configContent = file_get_contents($configPath);

                // Check if the provider already exists to avoid duplication
                if (strpos($configContent, 'App\Providers\PluginServiceProvider::class,') === false) {
                    // Find the last occurrence of the providers array and insert before the closing bracket ]
                    $updatedConfig = preg_replace(
                        "/('providers' => \[)(.*?)(\n\s*\],)/s",
                        "$1$2\n        App\Providers\PluginServiceProvider::class,$3",
                        $configContent
                    );

                    // Write back the updated config file
                    file_put_contents($configPath, $updatedConfig);

                    // Clear config cache to apply changes
                    Artisan::call('config:clear');
                }

                // Coupon ID
                $couponId = $request->applied_coupon;

                if ($couponId == "") {
                    $couponId = " ";
                }

                // Check NFC card price is 0 or not
                if ((float)$request->payment_gateway_amount <= 0) {

                    // Zero Place order
                    $zeroOrder = new ZeroOrderNFC();
                    $zeroOrder->zero($couponId, $nfcCard, $nfcId, $plan, $payment_mode);

                    return redirect()->route('user.order.nfc.cards')->with('success', trans('Order has been successfully placed!. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.'));
                } else {

                    if ($payment_mode->payment_gateway_id == "60964401751ab") {
                        // Check key and secret
                        if ($config[4]->config_value != "YOUR_PAYPAL_CLIENT_ID" || $config[5]->config_value != "YOUR_PAYPAL_SECRET") {
                            return redirect()->route('nfcpaywithpaypal', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "60964410731d9") {
                        // Check key and secret
                        if ($config[6]->config_value != "YOUR_RAZORPAY_KEY" || $config[7]->config_value != "YOUR_RAZORPAY_SECRET") {
                            return redirect()->route('nfcpaywithrazorpay', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "60964410732t9") {
                        // Check key and secret
                        if ($config[9]->config_value != "YOUR_STRIPE_PUB_KEY" || $config[10]->config_value != "YOUR_STRIPE_SECRET") {
                            return redirect()->route('nfcpaywithstripe', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "60964410736592") {
                        // Check key and secret
                        if ($config[33]->config_value != "PAYSTACK_PUBLIC_KEY" || $config[34]->config_value != "PAYSTACK_SECRET_KEY") {
                            return redirect()->route('nfcpaywithpaystack', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "6096441071589632") {
                        // Check key and secret
                        if ($config[37]->config_value != "mollie_key") {
                            return redirect()->route('nfcpaywithmollie', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "659644107y2g5") {
                        // Check key and secret
                        if ($config[31]->config_value != "") {
                            return redirect()->route('nfcpaywithoffline', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "19065566166715") {
                        // Check key and secret
                        if ($config[77]->config_value != "YOUR_PHONEPE_CLIENT_ID" || $config[78]->config_value != "YOUR_PHONEPE_CLIENT_VERSION" || $config[79]->config_value != "YOUR_PHONEPE_CLIENT_SECRET") {
                            return redirect()->route('nfcpaywithphonepe', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "776111730465") {
                        // Check key and secret
                        if ($config[47]->config_value != "YOUR_MERCADO_PAGO_PUBLIC_KEY" || $config[48]->config_value != "YOUR_MERCADO_PAGO_ACCESS_TOKEN") {
                            return redirect()->route('nfcpaywithmercadopago', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "767510608137") {
                        // Check key and secret
                        if ($config[49]->config_value != "YOUR_TOYYIBPAY_API_KEY" || $config[50]->config_value != "YOUR_TOYYIBPAY_CATEGORY_CODE") {
                            return redirect()->route('nfc.prepare.toyyibpay', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "754201940107") {
                        // Check key, secret and encryption key
                        if ($config[51]->config_value != "YOUR_FLW_PUBLIC_KEY" || $config[52]->config_value != "YOUR_FLW_SECRET_KEY" || $config[53]->config_value != "YOUR_FLW_ENCRYPTION_KEY") {
                            return redirect()->route('nfc.prepare.flutterwave', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "5992737427969") {
                        // Check key, secret and encryption key
                        if ($config[65]->config_value != "YOUR_PADDLE_SELLER_ID" || $config[66]->config_value != "YOUR_PADDLE_API_KEY" || $config[67]->config_value != "YOUR_PADDLE_CLIENT_SIDE_TOKEN") {
                            return redirect()->route('nfc.prepare.paddle', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "5992737427970") {
                        // Check key, secret and encryption key
                        if ($config[68]->config_value != "YOUR_PAYTR_MERCHANT_ID" || $config[69]->config_value != "YOUR_PAYTR_MERCHANT_KEY" || $config[70]->config_value != "YOUR_PAYTR_MERCHANT_SALT") {
                            return redirect()->route('nfc.prepare.paytr', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "278523098674") {
                        // Check key, secret and encryption key
                        if ($config[72]->config_value != "YOUR_XENDIT_SECRET_KEY") {
                            return redirect()->route('nfc.prepare.xendit', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else if ($payment_mode->payment_gateway_id == "278523098675") {
                        // Check key, secret and encryption key
                        if ($config[85]->config_value != "YOUR_CASHFREE_APP_ID" || $config[86]->config_value != "YOUR_CASHFREE_SECRET_KEY") {
                            return redirect()->route('nfc.prepare.cashfree', compact('nfcId', 'couponId'));
                        } else {
                            return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                        }
                    } else {
                        return redirect()->route('user.order.nfc.card.checkout', $nfcId)->with('failed', trans('Something went wrong!'));
                    }
                }
            }
        } else {
            return redirect()->route('user.order.nfc.cards')->with('failed', __('This feature is not available on your current plan. Upgrade to a plan that includes this feature.'));
        }
    }

    // Checkout coupon
    public function coupon(Request $request, $designId)
    {
        // Queries
        $config = DB::table('config')->get();
        $tax = (float) $config[25]->config_value;
        $total = 0;
        $applied = false;

        // Coupon code
        $coupon_code = Str::upper($request->coupon_code);
 
        // Get NFC Card details
        $nfcCardDetails = NfcCardDesign::where('nfc_card_id', $designId)->first();

        // Check NFC Card exists
        if (!$nfcCardDetails) {
            return response()->json(['success' => false, 'message' => trans('NFC Card not found!')]);
        }

        // Get coupon details
        $couponDetails = Coupon::where('used_for', 'nfc')->where('coupon_code', $coupon_code)->where('status', 1)->first();

        // Check coupon exists
        if (!$couponDetails) {
            return response()->json(['success' => false, 'message' => trans('Coupon not vaild!')]);
        }

        // Check coupon validity
        if ($couponDetails->coupon_expired_on < Carbon::now()) {
            return response()->json(['success' => false, 'message' => trans('Coupon not vaild!')]);
        }

        // Check user already has this coupon
        $userCouponCount = AppliedCoupon::where('user_id', Auth::user()->id)->where('coupon_id', $couponDetails->coupon_code)->where('status', 1)->count();
        if ($userCouponCount >= $couponDetails->coupon_user_usage_limit) {
            return response()->json(['success' => false, 'message' => trans('Coupon already used.')]);
        }

        // Check total already has this coupon
        $totalCouponCount = AppliedCoupon::where('coupon_id', $couponDetails->coupon_code)->where('status', 1)->count();
        if ($totalCouponCount >= $couponDetails->coupon_total_usage_limit) { 
            return response()->json(['success' => false, 'message' => trans('Total number of users already reached.')]);
        }

        // Check coupon type 
        if ($couponDetails->coupon_type == 'fixed') {
            $appliedTaxInTotal = ($nfcCardDetails->nfc_card_price * $tax) / 100;
            $discountPrice = $couponDetails->coupon_amount;
            $total = ($nfcCardDetails->nfc_card_price + $appliedTaxInTotal) - $discountPrice;
        } else {
            // Applied tax in total
            $appliedTaxInTotal = ($nfcCardDetails->nfc_card_price * $tax) / 100;
            // Get discount in plan price
            $discountPrice = ($nfcCardDetails->nfc_card_price * $couponDetails->coupon_amount) / 100;

            // Total
            $total = ($nfcCardDetails->nfc_card_price + $appliedTaxInTotal) - $discountPrice;
        }

        // dd($total);

        // Change applied status
        $applied = true;

        return response()->json(['success' => true, 'applied' => $applied, 'coupon_code' => $coupon_code, 'coupon_id' => $coupon_code, 'discountPrice' => $discountPrice, 'total' => $total]);
    }
}
