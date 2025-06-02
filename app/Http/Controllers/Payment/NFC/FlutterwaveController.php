<?php

namespace App\Http\Controllers\Payment\NFC;

use App\User;
use App\Coupon;
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

class FlutterwaveController extends Controller
{
    protected $secretKey;
    protected $baseUrl;
    protected $client;

    public function __construct()
    {
        // Get API key and category code from config table
        $config = DB::table('config')->get();

        $this->secretKey = $config[52]->config_value;
        $this->baseUrl = "https://api.flutterwave.com/v3";
    }

    public function nfcPrepareFlutterwave(Request $request, $nfcId, $couponId)
    {
        // Check if user is logged in
        if (Auth::user()) {
            // Queries
            $config = DB::table('config')->get();

            // Get user details
            $userData = User::where('id', Auth::user()->id)->first();

            // Get nfc card details
            $nfcDetails = NfcCardDesign::where('nfc_card_id', $nfcId)->where('status', 1)->first();

            if (!$nfcDetails) {
                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Invalid NFC card!'));
            }

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

                $amountToBePaidPaise = $amountToBePaid;

                $client = new Client();

                $data = [
                    'tx_ref' => $nfcCardOrderId,
                    'amount' => $amountToBePaidPaise, // Ex: 59.0
                    'currency' => $config[1]->config_value, // Ex: USD
                    'redirect_url' => route('nfc.flutterwave.payment.status'), // Redirect URL: http://127.0.0.1:8000/nfc/flutterwave-payment-status
                    'customer' => [
                        'email' => Auth::user()->email, // Ex: test@test.com
                        'name' => Auth::user()->name, // Ex: Test User
                        'phone_number' => Auth::user()->billing_phone == null ? '9876543210' : Auth::user()->billing_phone // Ex: 9876543210
                    ],
                    'customizations' => [
                        'title' => config('app.name'), // Ex: GoBiz
                        'logo' => asset('img/favicon.png'), // Ex: http://127.0.0.1:8000/img/favicon.png
                    ]
                ];

                try {
                    $response = $client->post("{$this->baseUrl}/payments", [ // URL: https://api.flutterwave.com/v3/payments
                        'headers' => [
                            'Authorization' => 'Bearer ' . $this->secretKey,  // Correct Bearer Token
                            'Content-Type' => 'application/json',
                        ],
                        'json' => $data
                    ]);

                    $responseBody = json_decode($response->getBody(), true);

                    if ($responseBody['status'] === 'success') {

                        // Store transaction details in nfc_card_order_id table before redirecting to PayPal
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

                        // Store transaction details in nfc_card_order_transactions table before redirecting to PayPal
                        $transaction = new NfcCardOrderTransaction();
                        $transaction->nfc_card_order_transaction_id = $nfcTransactionId;
                        $transaction->nfc_card_order_id = $nfcCardOrderId;
                        $transaction->payment_transaction_id = $nfcTransactionId;
                        $transaction->payment_method = "Flutterwave";
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

                        return redirect($responseBody['data']['link']);
                    }

                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Payment initiation failed'));
                } catch (\Exception $e) {
                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Failed to initiate payment.'));
                }
            }
        } else {
            return redirect()->route('login');
        }
    }

    // Update status
    public function nfcFlutterwavePaymentStatus(Request $request)
    {
        // Get transaction id from the request
        $txRef = $request->query('tx_ref');
        $status = $request->query('status');

        // Transaction success
        if ($status == "successful") {
            // Check if the transaction is already verified
            $transactionId = $request->query('transaction_id');

            if ($transactionId) {
                $client = new Client();

                try {
                    $response = $client->get("{$this->baseUrl}/transactions/{$transactionId}/verify", [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $this->secretKey,
                            'Content-Type' => 'application/json',
                        ]
                    ]);

                    $verificationResponse = json_decode($response->getBody(), true);

                    // Get tx_ref and flw_ref
                    $tx_ref = $verificationResponse['data']['tx_ref'];
                    $flw_ref = $verificationResponse['data']['flw_ref'];

                    if ($verificationResponse['status'] === 'success') {
                        // Place order
                        $order = new OrderNFC();
                        $order->order($tx_ref, $flw_ref);

                        return redirect()->route('user.order.nfc.cards')->with('success', trans('Order has been successfully placed!. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.'));
                    }

                    // Handle failed payment
                    $order = new OrderNFC();
                    $order->paymentFailed($tx_ref);
                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Payment failed.'));
                } catch (\Exception $e) {
                    // Handle failed payment
                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Payment verification failed.'));
                }
            } else {
                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Transaction not found.'));  
            }
        } elseif ($status === 'failed') {
            // Handle failed payment
            $order = new OrderNFC();
            $order->paymentFailed($txRef);

            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Transaction failed.'));
        } elseif ($status === 'cancelled') {
            // Update transaction details
            $order = new OrderNFC();
            $order->paymentFailed($txRef);

            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Transaction cancelled.'));
        }

        return redirect()->route('user.order.nfc.cards')->with('failed', trans('Invalid transaction status.'));
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
