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
use App\Services\XenditService;
use App\NfcCardOrderTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class XenditController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    public function nfcGeneratePaymentLink(Request $request, $nfcId, $couponId)
    {
        if (Auth::user()) {
            // Queries
            $config = DB::table('config')->get();

            // Get user details
            $userData = User::where('id', Auth::user()->id)->first();

            // Get mfc card details
            $nfcDetails = NfcCardDesign::where('nfc_card_id', $nfcId)->where('status', 1)->first();

            if (!$nfcDetails) {
                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Invalid NFC card!'));
            }

            // Validate Xendit access token
            if ($config[72]->config_value == null) {
                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Something went wrong!'));
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

                // Define your return and cancel URLs
                $successRedirectUrl = route('nfc.xendit.payment.status', ['transactionId' => $nfcTransactionId]);  // URL to redirect user after successful payment
                $failureRedirectUrl = route('nfc.xendit.payment.failure', ['transactionId' => $nfcTransactionId]);   // URL to redirect user if payment is canceled

                // Create an invoice with return and cancel URLs
                $response = $this->xenditService->createInvoice(
                    $nfcTransactionId,
                    $amountToBePaidPaise,
                    $userData->email,
                    'Payment for Order',
                    $successRedirectUrl
                );

                // Check for success and retrieve the invoice details
                if ($response['status'] == 'PENDING') {

                    // Invoice ID
                    $transaction_id = $response['id'];

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
                    $transaction->payment_transaction_id = $transaction_id;
                    $transaction->payment_method = "Xendit";
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
                    return redirect()->route('user.order.nfc.cards')->with('failed', trans('Unable to create payment'));
                }
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function nfcXenditPaymentStatus(Request $request, $transactionId)
    {
        // Get transaction details based on the transactionId
        $transaction_details = NfcCardOrderTransaction::where('nfc_card_order_transaction_id', $transactionId)->first();

        if (!$transaction_details) {
            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Transaction not found or already processed.'));
        }

        // Get Xendit payment status by passing the correct invoice ID (transactionId in this case)
        $paymentStatus = $this->xenditService->getInvoiceById($transaction_details->payment_transaction_id);

        // Check if the payment status exists to avoid errors
        if (!isset($paymentStatus['status'])) {
            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Unable to retrieve payment status.'));
        }

        // Check payment status
        switch ($paymentStatus['status']) {
            case 'PAID':
                // Order place
                $order = new OrderNFC();
                $order->order($transaction_details->payment_transaction_id, "PAID");

                return redirect()->route('user.order.nfc.cards')->with('success', trans('Order has been successfully placed!. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.'));
            case 'SETTLED':
                // Order place
                $order = new OrderNFC();
                $order->order($transaction_details->payment_transaction_id, "SETTLED");

                return redirect()->route('user.order.nfc.cards')->with('success', trans('Order has been successfully placed!. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.'));
            case 'FAILED':
                // Payment has failed
                $order = new OrderNFC();
                $order->paymentFailed($transaction_details->payment_transaction_id);

                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
            case 'PENDING':
                // Payment is pending
                $order = new OrderNFC();
                $order->paymentFailed($transaction_details->payment_transaction_id);

                return redirect()->route('user.order.nfc.cards')->with('pending', trans('Order pending!'));
            case 'CANCELED':
                // Payment has failed
                $order = new OrderNFC();
                $order->paymentFailed($transaction_details->payment_transaction_id);

                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order canceled!'));
            case 'EXPIRED':
                // Payment has failed
                $order = new OrderNFC();
                $order->paymentFailed($transaction_details->payment_transaction_id);

                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
            default:
                // Unknown payment status
                $order = new OrderNFC();
                $order->paymentFailed($transaction_details->payment_transaction_id);

                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Unable to determine payment status.'));
        }
    }

    public function nfcXenditPaymentWebhook(Request $request)
    {
        // Get transaction details based on the transactionId
        $transaction_details = NfcCardOrderTransaction::where('nfc_card_order_transaction_id', $request->transactionId)->first();

        if (!$transaction_details) {
            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Transaction not found or already processed.'));
        }

        // Get Xendit payment status by passing the correct invoice ID (transactionId in this case)
        $paymentStatus = $this->xenditService->getInvoiceById($transaction_details->payment_transaction_id);

        // Check if the payment status exists to avoid errors
        if (!isset($paymentStatus['status'])) {
            return redirect()->route('user.order.nfc.cards')->with('failed', trans('Unable to retrieve payment status.'));
        }

        // Check payment status
        switch ($paymentStatus['status']) {
            case 'PAID':
                // Order place
                $order = new OrderNFC();
                $order->order($transaction_details->payment_transaction_id, "PAID");

                return redirect()->route('user.order.nfc.cards')->with('success', trans('Order has been successfully placed!. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.'));
            case 'SETTLED':
                // Order place
                $order = new OrderNFC();
                $order->order($transaction_details->payment_transaction_id, "SETTLED");

                return redirect()->route('user.order.nfc.cards')->with('success', trans('Order has been successfully placed!. If you want to NFC Card Logo, please upload it from the "Manage NFC Cards" section.'));
            case 'FAILED':
                // Payment has failed
                $order = new OrderNFC();
                $order->paymentFailed($transaction_details->payment_transaction_id);

                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
            case 'PENDING':
                // Payment is pending
                $order = new OrderNFC();
                $order->paymentFailed($transaction_details->payment_transaction_id);

                return redirect()->route('user.order.nfc.cards')->with('pending', trans('Order pending!'));
            case 'CANCELED':
                // Payment has failed
                $order = new OrderNFC();
                $order->paymentFailed($transaction_details->payment_transaction_id);

                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order canceled!'));
            case 'EXPIRED':
                // Payment has failed
                $order = new OrderNFC();
                $order->paymentFailed($transaction_details->payment_transaction_id);

                return redirect()->route('user.order.nfc.cards')->with('failed', trans('Order failed!'));
            default:
                // Payment has failed
                $order = new OrderNFC();
                $order->paymentFailed($transaction_details->payment_transaction_id);

                // Unknown payment status
                return redirect()->route('user.plans')->with('failed', trans('Unable to determine payment status.'));
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
