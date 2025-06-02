<?php

namespace App\Http\Controllers\Payment\NFC;

use App\User;
use App\Coupon;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PhonepeController extends Controller
{
    public function nfcPreparePhonpe(Request $request, $nfcId, $couponId)
    {
        if (Auth::user()) {

            // Queries
            $config = DB::table('config')->get();
            $userData = User::where('id', Auth::user()->id)->first();
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

                $amountToBePaidPaise = $amountToBePaid * 100;

                $authToken = $this->getPhonePeAuthToken();
                if (!$authToken) {
                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Failed to fetch PhonePe authentication token.'));
                }

                try {
                    $data = [
                        'merchantOrderId' => $nfcTransactionId,  // Unique transaction ID
                        'amount' => $amountToBePaidPaise,  // Amount in paise (1000 = ₹10)
                        'paymentFlow' => [
                            'type' => 'PG_CHECKOUT',  // Correct type for PhonePe checkout
                            'merchantUrls' => [
                                'redirectUrl' => route('nfc.phonepe.payment.status') // Redirect after payment
                            ]
                        ]
                    ];

                    $response = Http::withHeaders([
                        'Content-Type'  => 'application/json',
                        'Authorization' => "O-Bearer " . $authToken
                    ])->post('https://api.phonepe.com/apis/pg/checkout/v2/pay', $data);

                    // Get JSON response
                    $rData = $response->json();

                    if (isset($rData)) {
                        if (!empty($rData['state']) && $rData['state'] == "PENDING") {
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
                            $invoice_details['subtotal'] = $nfcDetails->nfc_card_price;
                            $invoice_details['tax_name'] = $config[24]->config_value;
                            $invoice_details['tax_type'] = $config[14]->config_value;
                            $invoice_details['tax_value'] = $config[25]->config_value;
                            $invoice_details['tax_amount'] = $appliedTaxInTotal;
                            $invoice_details['applied_coupon'] = $appliedCoupon;
                            $invoice_details['discounted_price'] = $discountPrice;
                            $invoice_details['invoice_amount'] = $amountToBePaid;

                            // Get order ID
                            $phonePeTransactionId = $rData['orderId'];

                            // Store transaction details in nfc_card_order_id table before redirecting to Phonepe
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

                            // Store transaction details in nfc_card_order_transactions table before redirecting to Phonepe
                            $transaction = new NfcCardOrderTransaction();
                            $transaction->nfc_card_order_transaction_id = $nfcTransactionId;
                            $transaction->nfc_card_order_id = $nfcCardOrderId;
                            $transaction->payment_transaction_id = $phonePeTransactionId;
                            $transaction->payment_method = "PhonePe";
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
                                $appliedCoupon->transaction_id = $phonePeTransactionId;
                                $appliedCoupon->user_id = Auth::user()->id;
                                $appliedCoupon->coupon_id = $couponId;
                                $appliedCoupon->status = 0;
                                $appliedCoupon->save();
                            }

                            return redirect()->to($rData['redirectUrl']);
                        } else {
                            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
                        }
                    } else {
                        return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed.'));
                    }
                } catch (\Exception $e) {
                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed.'));
                }
            }
        } else {
            return redirect()->route('login');
        }
    }


    // Update payment status
    public function nfcPhonepePaymentStatus(Request $request)
    {
        // Get last transaction id for phonepe and user id
        $nfcTransactionDetails = NfcCardOrderTransaction::join('nfc_card_orders', 'nfc_card_order_transactions.nfc_card_order_id', '=', 'nfc_card_orders.nfc_card_order_id')
            ->where('nfc_card_order_transactions.payment_method', 'PhonePe')
            ->where('nfc_card_orders.user_id', Auth::user()->id)
            ->orderBy('nfc_card_order_transactions.id', 'desc')
            ->first();

        if (isset($nfcTransactionDetails)) {
            $authToken = $this->getPhonePeAuthToken();
            if (!$authToken) {
                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Failed to fetch PhonePe authentication token.'));
            }

            $statusUrl = "https://api.phonepe.com/apis/pg/checkout/v2/order/" . $nfcTransactionDetails->nfc_card_order_transaction_id . "/status?details=false&errorContext=true";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "O-Bearer " . $authToken
            ])->get($statusUrl);

            $res = json_decode($response->body());

            try {
                // Check status is failed
                if ($res->success == false) {
                    return redirect()->route('user.order.nfc.cards')->with('failed', trans($res->message));
                }
            } catch (\Exception $e) {
            }

            if ($res->state == "COMPLETED") {
                // Get transactionId
                $orderId = $res->orderId;
                $transactionId = $res->paymentDetails[0]->transactionId;

                // Place order
                $order = new OrderNFC();
                $order->order($orderId, $res);

                // Update transaction id
                NfcCardOrderTransaction::where('payment_transaction_id', $nfcTransactionDetails->payment_transaction_id)->update(['payment_transaction_id' => $transactionId]);

                // Redirect
                return redirect()->route('user.order.nfc.cards')->with('success', trans('Order has been successfully placed!. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.'));
            } else {
                // Payment failed
                $order = new OrderNFC();
                $order->paymentFailed($nfcTransactionDetails->payment_transaction_id);

                // Redirect
                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
            }
        } else {
            // Update status
            NfcCardOrderTransaction::where('payment_transaction_id', $nfcTransactionDetails->payment_transaction_id)->update([
                'payment_status' => 'failed',
            ]);

            // Redirect
            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
        }

        return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
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

    private function getPhonePeAuthToken()
    {
        // Check if the token is cached
        if (Cache::has('phonepe_auth_token')) {
            return Cache::get('phonepe_auth_token');
        }

        // Query the config table
        $config = DB::table('config')->get();

        // Check if the config values are empty
        if ($config[77]->config_value == 'YOUR_PHONEPE_CLIENT_ID' || $config[78]->config_value == 'YOUR_PHONEPE_CLIENT_VERSION' || $config[79]->config_value == 'YOUR_PHONEPE_CLIENT_SECRET') {
            return trans("Please fill the phonepe client id, client version and client secret in the config table.");
        }

        // Set the auth URL
        $authUrl = "https://api.phonepe.com/apis/identity-manager/v1/oauth/token";

        // Set the payload
        $payload = [
            "client_id" => $config[77]->config_value,
            "client_version" => $config[78]->config_value,
            "client_secret" => $config[79]->config_value,
            "grant_type" => "client_credentials"
        ];

        // Send the request
        $response = Http::asForm()->post($authUrl, $payload); // Ensuring correct encoding

        // Decode the response
        $responseData = $response->json();

        // Check if the response contains an access token
        if (isset($responseData['access_token'])) {
            Cache::put('phonepe_auth_token', $responseData['access_token'], now()->addMinutes(55));

            return $responseData['access_token'];
        } else {
            return trans("Failed to retrieve token");
        }

        return null;
    }
}
