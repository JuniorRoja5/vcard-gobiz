<?php

namespace App\Classes;

use App\Coupon;
use App\NfcCardOrder;
use App\AppliedCoupon;
use Illuminate\Support\Str;
use App\NfcCardOrderTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ZeroOrderNFC
{
    public function zero($couponId, $nfcCard, $nfcId, $plan, $payment_mode)
    {
        // Queries
        $config = DB::table('config')->get();

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
                $appliedTaxInTotal = ((float)($nfcCard->nfc_card_price) * (float)($config[25]->config_value) / 100);

                // Get discount in plan price
                $discountPrice = $couponDetails->coupon_amount;

                // Total
                $amountToBePaid = ($nfcCard->nfc_card_price + $appliedTaxInTotal) - $discountPrice;
                $amountToBePaid = (float)number_format($amountToBePaid, 2, '.', '');

                // Coupon is applied
                $appliedCoupon = $couponDetails->coupon_code;
            } else {
                // Applied tax in total
                $appliedTaxInTotal = ((float)($nfcCard->nfc_card_price) * (float)($config[25]->config_value) / 100);

                // Get discount in plan price
                $discountPrice = $nfcCard->nfc_card_price * $couponDetails->coupon_amount / 100;

                // Total
                $amountToBePaid = ($nfcCard->nfc_card_price + $appliedTaxInTotal) - $discountPrice;
                $amountToBePaid = (float)number_format($amountToBePaid, 2, '.', '');

                // Coupon is applied
                $appliedCoupon = $couponDetails->coupon_code;
            }
        } else {
            // Applied tax in total
            $appliedTaxInTotal = ((float)($nfcCard->nfc_card_price) * (float)($config[25]->config_value) / 100);

            // Total
            $amountToBePaid = ($nfcCard->nfc_card_price + $appliedTaxInTotal);
        }

        $amountToBePaidPaise = $amountToBePaid;

        // Store transaction details in nfc_card_order_id table before redirecting to PayPal
        $nfcCardOrder = new NfcCardOrder();
        $nfcCardOrder->nfc_card_order_id = $nfcCardOrderId;
        $nfcCardOrder->user_id = Auth::id();
        $nfcCardOrder->nfc_card_id = $nfcId;
        $nfcCardOrder->nfc_card_order_transaction_id = $nfcTransactionId;
        $nfcCardOrder->order_details = json_encode($this->prepareOrderDetails($config, $amountToBePaid, $nfcCard, $appliedCoupon, $discountPrice));
        $nfcCardOrder->delivery_address = json_encode($this->prepareDeliveryAddress($plan));
        $nfcCardOrder->delivery_note = "-";
        $nfcCardOrder->order_status = 'pending';
        $nfcCardOrder->status = 1;
        $nfcCardOrder->save();

        // Store transaction details in nfc_card_order_transactions table before redirecting to PayPal
        $transaction = new NfcCardOrderTransaction();
        $transaction->nfc_card_order_transaction_id = $nfcTransactionId; 
        $transaction->nfc_card_order_id = $nfcCardOrderId;
        $transaction->payment_transaction_id = $nfcTransactionId;
        $transaction->payment_method = $payment_mode->payment_gateway_name ?? "-";
        $transaction->currency = $config[1]->config_value;
        $transaction->amount = $amountToBePaid;
        $transaction->invoice_details = json_encode($this->prepareInvoiceDetails($config, $plan, $amountToBePaid, $nfcCard, $appliedCoupon, $discountPrice));
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

        // Place order
        $order = new OrderNFC();
        $order->order($nfcTransactionId, $nfcCardOrderId);
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
