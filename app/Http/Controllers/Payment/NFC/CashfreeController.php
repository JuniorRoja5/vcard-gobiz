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
use App\Classes\UpgradePlan;
use Illuminate\Http\Request;
use App\NfcCardOrderTransaction;
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
    public function nfcGeneratePaymentLink(Request $request, $nfcId, $couponId)
    {
        // Check if user is logged in
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

                $amountToBePaidPaise = $amountToBePaid;

                $data = [
                    'order_id' =>  $nfcTransactionId,
                    'order_amount' => $amountToBePaidPaise,
                    "order_currency" => $config[1]->config_value,
                    "customer_details" => [
                        "customer_id" => Auth::user()->user_id,
                        "customer_name" => Auth::user()->name,
                        "customer_email" => Auth::user()->email,
                        "customer_phone" => Auth::user()->billing_phone,
                    ],
                    "order_meta" => [
                        "return_url" => route('nfc.cashfree.payment.status') . '?order_id={order_id}',
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

                        // Order ID
                        $orderId = $responseBody['cf_order_id'];

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
                        $transaction->payment_method = "Cashfree";
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
                            $appliedCoupon->transaction_id = $orderId;
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

                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
                } catch (\Exception $e) {
                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
                }
            }
        } else {
            return redirect()->route('login');
        }
    }

    // Get payment status
    public function nfcCashfreePaymentStatus(Request $request)
    {
        // Order ID
        $order_id = $request->query('order_id');

        // Get last transaction id for phonepe and user id
        $nfcTransactionDetails = NfcCardOrderTransaction::where('payment_transaction_id', $order_id)->orderBy('id', 'desc')->first();

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
                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Invalid payment response.'));
            }
        
            $paymentDetails = (object) $paymentDetailsArray[0];
        
            // Check payment status
            if (isset($paymentDetails->payment_status) && $paymentDetails->payment_status == "SUCCESS") {
                // Get transactionId
                $orderId = $paymentDetails->order_id ?? null;
                $transactionId = $paymentDetails->cf_payment_id ?? null;
        
                if (!$orderId || !$transactionId) {
                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Missing order or transaction ID.'));
                }
        
                // Place order
                $order = new OrderNFC();
                $order->order($orderId, $paymentDetails);
        
                // Update transaction id
                NfcCardOrderTransaction::where('payment_transaction_id', $nfcTransactionDetails->payment_transaction_id)
                    ->update(['payment_transaction_id' => $transactionId]);
        
                // Redirect
                return redirect()->route('user.order.nfc.cards')->with('success', trans('Order has been successfully placed! If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.'));
            } else {
                // Payment failed
                $order = new OrderNFC();
                $order->paymentFailed($nfcTransactionDetails->payment_transaction_id);
        
                // Redirect
                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
            }
        } else {
            // Payment failed
            $order = new OrderNFC();
            $order->paymentFailed($nfcTransactionDetails->payment_transaction_id);
        
            // Redirect
            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
        }        

        return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed.'));
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
