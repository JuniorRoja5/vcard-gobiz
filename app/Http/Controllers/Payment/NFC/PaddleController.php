<?php

namespace App\Http\Controllers\Payment\NFC;

use App\User;
use App\Coupon;
use Carbon\Carbon;
use App\NfcCardOrder;
use App\AppliedCoupon;
use App\NfcCardDesign;
use GuzzleHttp\Client;
use App\Classes\OrderNFC;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\NfcCardOrderTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaddleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function nfcGeneratePaymentLink(Request $request, $nfcId, $couponId)
    {
        if (Auth::user()) {
            // Queries
            $config = DB::table('config')->get();
            $userData = User::where('id', Auth::user()->id)->first();

            // NFC Card details
            $nfcDetails = NfcCardDesign::where('nfc_card_id', $nfcId)->where('status', 1)->first();

            // Check nfc card details
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

                // Store transaction details in nfc_card_order_id table before redirecting to Paddle
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

                // Store transaction details in nfc_card_order_transactions table before redirecting to Paddle
                $transaction = new NfcCardOrderTransaction();
                $transaction->nfc_card_order_transaction_id = $nfcTransactionId;
                $transaction->nfc_card_order_id = $nfcCardOrderId;
                $transaction->payment_transaction_id = $nfcTransactionId;
                $transaction->payment_method = "Paddle";
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

                // Call
                $client = new Client();

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
                            "title" => trans("NFC card purchase for ") . $nfcDetails->nfc_card_name,
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
                            'return_url' => route('nfc.paddle.payment.status') . '?' . http_build_query([
                                'checkout' => '{checkout_hash}',
                                'passthrough' => '{"user_id": "' . Auth::user()->id . '", "transaction_id": "' . $nfcTransactionId . '"}'
                            ]),
                            'webhook_url' => route('nfc.paddle.payment.webhook'),
                            // 'webhook_url' => "https://nativecode.in/payment/paddle/webhook",
                            'passthrough' => '{"user_id": "' . Auth::user()->id . '", "transaction_id": "' . $nfcTransactionId . '"}'
                        ]
                    ]);

                    if ($response->getStatusCode() == 200) {
                        $data = json_decode($response->getBody(), true);

                        return redirect()->to($data['response']['url']);
                    } else {
                        return redirect()->route('user.order.nfc.cards')->with('failed', trans('Something went wrong!'));
                    }
                } catch (\Exception $e) {
                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Something went wrong!'));
                }
            }
        } else {
            return redirect()->route('login');
        }
    }

    // Successful Payment
    public function nfPaddlePaymentStatus(Request $request)
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
                    // Place order
                    $order = new OrderNFC();
                    $order->order($transaction_id, $data);

                    return redirect()->route('user.order.nfc.cards')->with('success', trans('Order has been successfully placed!. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.'));
                } else {
                    // Update the transaction status to FAILED
                    $order = new OrderNFC();
                    $order->paymentFailed($transaction_id);

                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
                }
            } catch (\Exception $e) {
                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Something went wrong!'));
            }
        }

        return redirect()->route('user.order.nfc.cards')->with('failed', trans('Payment failed!'));
    }

    // Paddle webhook
    public function nfPaddlePaymentWebhook(Request $request)
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
            // Order place
            $order = new OrderNFC();
            $order->order($transaction_id, $webhookData);
        }

        // If payment status is pending
        if ($paymentStatus == 'pending') {
            // Update the transaction status to FAILED
            $order = new OrderNFC();
            $order->paymentFailed($transaction_id);
        }

        // If payment status is failure
        if ($paymentStatus == 'failure') {
            // Update the transaction status to FAILED
            $order = new OrderNFC();
            $order->paymentFailed($transaction_id);
        }

        // If payment status is error
        if ($paymentStatus == 'error') {
            // Update the transaction status to FAILED
            $order = new OrderNFC();
            $order->paymentFailed($transaction_id);
        }

        // If payment status is canceled
        if ($paymentStatus == 'canceled') {
            // Update the transaction status to FAILED
            $order = new OrderNFC();
            $order->paymentFailed($transaction_id);
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
