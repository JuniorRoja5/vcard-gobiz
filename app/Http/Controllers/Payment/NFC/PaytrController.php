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
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

    public function nfcGeneratePaymentLink(Request $request, $nfcId, $couponId)
    {
        if (Auth::user()) {
            // Queries
            $config = DB::table('config')->get();
            $settings = Setting::where('status', 1)->first();

            $userData = User::where('id', Auth::user()->id)->first();

            // Get nfc card details
            $nfcDetails = NfcCardDesign::where('nfc_card_id', $nfcId)->where('status', 1)->first();

            // Check nfc card details
            if ($nfcDetails == null) {
                // Page redirect
                return redirect()->route('paytr.payment.failure')->with('failed', trans('Payment failed!'));
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

                        // Get discount in nfc card price
                        $discountPrice = $couponDetails->coupon_amount;

                        // Total
                        $amountToBePaid = ($nfcDetails->nfc_card_price + $appliedTaxInTotal) - $discountPrice;
                        $amountToBePaid = (float)number_format($amountToBePaid, 2, '.', '');

                        // Coupon is applied
                        $appliedCoupon = $couponDetails->coupon_code;
                    } else {
                        // Applied tax in total
                        $appliedTaxInTotal = ((float)($nfcDetails->nfc_card_price) * (float)($config[25]->config_value) / 100);

                        // Get discount in nfc card price
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

                // Store transaction details in nfc_card_order_id table before redirecting to Paytr
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

                // Store transaction details in nfc_card_order_transactions table before redirecting to Paytr
                $transaction = new NfcCardOrderTransaction();
                $transaction->nfc_card_order_transaction_id = $nfcTransactionId;
                $transaction->nfc_card_order_id = $nfcCardOrderId;
                $transaction->payment_transaction_id = $nfcTransactionId;
                $transaction->payment_method = "Paytr";
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
                    $appliedCoupon->transaction_id = $nfcTransactionId;
                    $appliedCoupon->user_id = Auth::user()->id;
                    $appliedCoupon->coupon_id = $couponId;
                    $appliedCoupon->status = 0;
                    $appliedCoupon->save();
                }

                $paymentData = [
                    'merchant_id' => $this->merchant_id,
                    'user_ip' => $request->ip(),
                    'merchant_oid' => $nfcTransactionId, // Unique order ID
                    'email' => $userData->billing_email,
                    'payment_amount' => $amountToBePaidPaise, // Amount in cents
                    'user_basket' => base64_encode(json_encode([
                        ['NFC Card Purchase for' . $nfcDetails->nfc_card_name, $nfcDetails->nfc_card_price, 1],
                    ])),
                    'debug_on' => 1,
                    'no_installment' => 0,
                    'max_installment' => 0,
                    'currency' => 'TL',
                    'test_mode' => $this->mode,
                    'user_name' => Auth::user()->billing_name,
                    'user_address' => Auth::user()->billing_address,
                    'user_phone' => Auth::user()->billing_phone,
                    'merchant_ok_url' => route('nfc.paytr.payment.status'),
                    'merchant_fail_url' => route('nfc.paytr.payment.failure')
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
                        return view('user.pages.order.nfc-card.checkout.pay-with-paytr', [
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

    // PayTR Payment Status
    public function nfcPaytrPaymentStatus(Request $request)
    {
        $paymentDetails = $request->all();

        // Check payment id
        if (!$paymentDetails) {
            // Page redirect
            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Payment failed'));
        } else {

            // Security: Verify PayTR's notification
            $hash = base64_encode(hash_hmac(
                'sha256',
                $paymentDetails['merchant_oid'] . $this->merchant_salt . $paymentDetails['status'] . $paymentDetails['total_amount'],
                $this->merchant_key,
                true
            ));

            if ($hash != $paymentDetails['hash']) {
                return redirect()->route('user.order.nfc.cards')->with(['failed' => trans('Payment failed')]);
            }

            // Queries
            $transactionId = $paymentDetails['merchant_oid'];
            $paymentId = $paymentDetails['merchant_oid'];
            $config = DB::table('config')->get();

            // Check payment status
            if ($paymentDetails['status'] == "success") {
                // Place order
                $order = new OrderNFC();
                $order->order($transactionId, $paymentId);

                return redirect()->route('user.order.nfc.cards')->with('success', trans('Order has been successfully placed!. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.'));
            } else {
                // Payment failed
                $order = new OrderNFC();
                $order->paymentFailed($transactionId);

                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
            }

            // Payment failed
            // Handle the failed payment scenario
            $order = new OrderNFC();
            $order->paymentFailed($transactionId);

            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
        }
    }

    // PayTR Payment Failure
    public function nfcPaytrPaymentFailure(Request $request)
    {
        $paymentDetails = $request->all();

        if (!$paymentDetails) {
            // Page redirect
            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Payment status update failed'));
        } else {
            // Payment failed
            $order = new OrderNFC();
            $order->paymentFailed($paymentDetails['merchant_oid']);

            // Page redirect
            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Payment failed'));
        }
    }

    // PayTR Payment Webhook
    public function nfcPaytrPaymentWebhook(Request $request)
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

        // Retrieve transaction details (NFCCardOrderTransaction)
        $transaction_details = NfcCardOrderTransaction::where('payment_transaction_id', $transactionId)->first();
        if (!$transaction_details) {
            return response('Transaction not found', 404);
        }

        if ($paymentDetails['status'] !== 'success') {
            // Mark transaction as failed
            $order = new OrderNFC();
            $order->paymentFailed($transactionId);
            return response('Payment failed', 200);
        }

        try {
            // Place order
            $order = new OrderNFC();
            $order->order($transactionId, $paymentDetails);

            // Respond to PayTR
            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('PayTR Webhook Error: ' . $e->getMessage());
            return response('Internal Server Error', 500);
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
