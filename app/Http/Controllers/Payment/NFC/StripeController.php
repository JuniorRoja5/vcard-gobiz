<?php

namespace App\Http\Controllers\Payment\NFC;

use App\User;
use App\Coupon;
use App\Setting;
use App\NfcCardOrder;
use App\AppliedCoupon;
use App\NfcCardDesign;
use App\Classes\OrderNFC;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\NfcCardOrderTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{ 
    public function nfcStripeCheckout(Request $request, $nfcId, $couponId)
    {
        if (Auth::user()) {
            $config = DB::table('config')->get();
            $userData = User::where('id', Auth::user()->id)->first();

            $settings = Setting::where('status', 1)->first();
            $nfcDetails = NfcCardDesign::where('nfc_card_id', $nfcId)->where('status', 1)->first();

            if ($nfcDetails == null) {
                return view('errors.404');
            } else {

                // Check applied coupon
                $couponDetails = Coupon::where('used_for', 'nfc')->where('coupon_code', $couponId)->first();

                // Applied tax in total
                $appliedTaxInTotal = 0;

                // Discount price
                $discountPrice = 0;

                // Applied coupon
                $appliedCoupon = null;

                // NFC Card Order ID
                $nfcCardOrderId = "OD" . preg_replace('/\D/', '', Str::uuid());
                $nfcTransactionId = "TX" . preg_replace('/\D/', '', Str::uuid());

                // Check coupon type
                if ($couponDetails != null) {
                    if ($couponDetails->coupon_type == 'fixed') {
                        // Applied tax in total
                        $appliedTaxInTotal = ((float)($nfcDetails->nfc_card_price) * (float)($config[25]->config_value) / 100);

                        // Get discount in plan price
                        $discountPrice = $couponDetails->coupon_amount;

                        // Total
                        $amountToBePaid = ($nfcDetails->nfc_card_price + $appliedTaxInTotal) - $discountPrice;
                        $amountToBePaid = (float)number_format($amountToBePaid, 2, '.', '');

                        // Coupon is applied
                        $appliedCoupon = $couponDetails->coupon_code;
                    } else {
                        // Applied tax in total
                        $appliedTaxInTotal = ((float)($nfcDetails->nfc_card_price) * (float)($config[25]->config_value) / 100);

                        // Get discount in plan price
                        $discountPrice = $nfcDetails->nfc_card_price * $couponDetails->coupon_amount / 100;

                        // Total
                        $amountToBePaid = ($nfcDetails->nfc_card_price + $appliedTaxInTotal) - $discountPrice;
                        $amountToBePaid = (float)number_format($amountToBePaid, 2, '.', '');

                        // Coupon is applied
                        $appliedCoupon = $couponDetails->coupon_code;
                    }
                } else {
                    // Applied tax in total
                    $appliedTaxInTotal = ((float)($nfcDetails->nfc_card_price) * (float)($config[25]->config_value) / 100);

                    // Total
                    $amountToBePaid = ($nfcDetails->nfc_card_price + $appliedTaxInTotal);
                }

                $amountToBePaidPaise = $amountToBePaid * 100;

                \Stripe\Stripe::setApiKey($config[10]->config_value);

                $currency = Str::lower($config[1]->config_value); // 'usd' or 'eur'
                $paymentMethods = $currency === 'eur' ? ['bancontact', 'ideal', 'sepa_debit', 'sofort', 'card'] : ['card'];

                $payment_intent = \Stripe\PaymentIntent::create([
                    'description' => $nfcDetails->nfc_card_name . " NFC Card",
                    'shipping' => [
                        'name' => Auth::user()->name,
                        'address' => [
                            'line1' => Auth::user()->billing_address,
                            'postal_code' => Auth::user()->billing_zipcode,
                            'city' => Auth::user()->billing_city,
                            'state' => Auth::user()->billing_state,
                            'country' => Auth::user()->billing_country,
                        ],
                    ],
                    'amount' => $amountToBePaidPaise,
                    'currency' => $config[1]->config_value,
                    'payment_method_types' => $paymentMethods, // Default methods
                ]);

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
                $invoice_details['subtotal'] = $nfcDetails->nfc_card_price;
                $invoice_details['tax_name'] = $config[24]->config_value;
                $invoice_details['tax_type'] = $config[14]->config_value;
                $invoice_details['tax_value'] = $config[25]->config_value;
                $invoice_details['tax_amount'] = $appliedTaxInTotal;
                $invoice_details['applied_coupon'] = $appliedCoupon;
                $invoice_details['discounted_price'] = $discountPrice;
                $invoice_details['invoice_amount'] = $amountToBePaid;

                $intent = $payment_intent->client_secret;
                $paymentId = $payment_intent->id;

                // If order is created from stripe
                if (isset($intent)) {

                    // Store transaction details in nfc_card_order_id table before redirecting to Stripe
                    $nfcCardOrder = new NfcCardOrder();
                    $nfcCardOrder->nfc_card_order_id = $nfcCardOrderId;
                    $nfcCardOrder->user_id = Auth::id();
                    $nfcCardOrder->nfc_card_id = $nfcId;
                    $nfcCardOrder->nfc_card_order_transaction_id = $nfcTransactionId;
                    $nfcCardOrder->order_details = json_encode($this->prepareOrderDetails($config, $amountToBePaid, $nfcDetails, $appliedCoupon, $discountPrice));
                    $nfcCardOrder->delivery_address = json_encode($this->prepareDeliveryAddress($userData));
                    $nfcCardOrder->delivery_note = "-";
                    $nfcCardOrder->order_status = 'pending';
                    $nfcCardOrder->status = 1;
                    $nfcCardOrder->save();

                    // Store transaction details in nfc_card_order_transactions table before redirecting to Stripe
                    $transaction = new NfcCardOrderTransaction();
                    $transaction->nfc_card_order_transaction_id = $nfcTransactionId;
                    $transaction->nfc_card_order_id = $nfcCardOrderId;
                    $transaction->payment_transaction_id = $paymentId;
                    $transaction->payment_method = "Stripe";
                    $transaction->currency = $config[1]->config_value;
                    $transaction->amount = $amountToBePaid;
                    $transaction->invoice_details = json_encode($this->prepareInvoiceDetails($config, $userData, $amountToBePaid, $nfcDetails, $appliedCoupon, $discountPrice));
                    $transaction->payment_status = "pending";
                    $transaction->save();

                    // Coupon is not applied
                    if ($couponId != " ") {
                        // Save applied coupon
                        $appliedCoupon = new AppliedCoupon;
                        $appliedCoupon->applied_coupon_id = uniqid();
                        $appliedCoupon->transaction_id = $paymentId;
                        $appliedCoupon->user_id = Auth::user()->id;
                        $appliedCoupon->coupon_id = $couponId;
                        $appliedCoupon->status = 0;
                        $appliedCoupon->save();
                    }
                    
                    return view('user.pages.order.nfc-card.checkout.pay-with-stripe', compact('settings', 'intent', 'nfcDetails', 'nfcTransactionId', 'config', 'paymentId'));
                }
            }
        } else {
            return redirect()->route('login');
        }
    }

    // Update payment status
    public function nfcStripePaymentStatus(Request $request, $paymentId)
    {
        if (!$paymentId) {
            return view('errors.404');
        } else {
            $orderId = $paymentId;
            $config = DB::table('config')->get();
            $stripe = new \Stripe\StripeClient($config[10]->config_value);

            try {
                $payment = $stripe->paymentIntents->retrieve($paymentId, []);
            } catch (\Exception $e) {
                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
            }

            if ($payment->status == "succeeded") {
                // Place order
                $order = new OrderNFC();
                $order->order($orderId, $payment);

                return redirect()->route('user.order.nfc.cards')->with('success', trans('Order has been successfully placed!. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.'));
            } else {
                // Payment failed
                $order = new OrderNFC();
                $order->paymentFailed($orderId);

                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
            }
        }
    }

    // Cancel payment
    public function nfcStripePaymentCancel(Request $request, $paymentId)
    {
        if (!$paymentId) {
            return view('errors.404');
        } else {
            $config = DB::table('config')->get();
            $stripe = new \Stripe\StripeClient($config[10]->config_value);

            try {
                $payment = $stripe->paymentIntents->cancel($paymentId, []);
            } catch (\Exception $e) {
                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Payment cancelled!'));
            }

            // Payment failed
            $order = new OrderNFC();
            $order->paymentFailed($paymentId);

            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Payment cancelled!'));
        }
    }

    private function prepareInvoiceDetails($config, $userData, $amountToBePaid, $nfcDetails, $appliedCoupon, $discountPrice)
    {
        // Prepare invoice details
        $invoiceDetails = [
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
            'tax_name' => $config[24]->config_value,
            'tax_type' => $config[14]->config_value,
            'tax_value' => $config[25]->config_value,
            'applied_coupon' => $appliedCoupon,
            'discounted_price' => $discountPrice,
            'invoice_amount' => $amountToBePaid,
            'subtotal' => $nfcDetails->nfc_card_price,
            'tax_amount' => (float)($nfcDetails->nfc_card_price) * (float)($config[25]->config_value) / 100
        ];

        return $invoiceDetails;
    }


    // Prepare oder details
    private function prepareOrderDetails($config, $amountToBePaid, $nfcDetails, $appliedCoupon, $discountPrice)
    {
        // Prepare invoice details
        $invoiceDetails = [
            'nfc_card_id' => $nfcDetails->nfc_card_id,
            'order_item' => $nfcDetails->nfc_card_name,
            'order_description' => $nfcDetails->nfc_card_description,
            'order_quantity' => 1,
            'price' => $nfcDetails->nfc_card_price,
            'invoice_amount' => $amountToBePaid,
            'tax_name' => $config[24]->config_value,
            'tax_type' => $config[14]->config_value,
            'tax_value' => $config[25]->config_value,
            'tax_amount' => (float)($nfcDetails->nfc_card_price) * (float)($config[25]->config_value) / 100,
            'applied_coupon' => $appliedCoupon,
            'discounted_price' => $discountPrice,
            'subtotal' => $nfcDetails->nfc_card_price
        ];

        return $invoiceDetails;
    }

    // Prepare delivery address
    private function prepareDeliveryAddress($userData)
    {
        // Prepare delivery address
        $deliveryAddress = [
            'billing_name' => $userData->billing_name,
            'billing_address' => $userData->billing_address,
            'billing_city' => $userData->billing_city,
            'billing_state' => $userData->billing_state,
            'billing_zipcode' => $userData->billing_zipcode,
            'billing_country' => $userData->billing_country,
            'billing_phone' => $userData->billing_phone,
            'billing_email' => $userData->billing_email,
            'shipping_name' => $userData->billing_name,
            'shipping_address' => $userData->billing_address,
            'shipping_city' => $userData->billing_city,
            'shipping_state' => $userData->billing_state,
            'shipping_zipcode' => $userData->billing_zipcode,
            'shipping_country' => $userData->billing_country,
            'shipping_phone' => $userData->billing_phone,
            'shipping_email' => $userData->billing_email,
            'type' => $userData->type,
            'vat_number' => $userData->vat_number
        ];

        return $deliveryAddress;
    }
}
