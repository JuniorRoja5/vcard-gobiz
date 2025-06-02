<?php

namespace App\Classes;

use App\User;
use App\Currency;
use App\NfcCardKey;
use App\NfcCardOrder;
use App\AppliedCoupon;
use App\EmailTemplate;
use App\NfcCardDesign;
use Illuminate\Support\Str;
use App\NfcCardOrderTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderNFC
{
    public function order($paymentId, $res)
    {
        // Queries
        $config = DB::table('config')->get();

        // Currency symbol
        $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
        $symbol = $currencies[$config[1]->config_value] ?? '';

        $transactionDetails = NfcCardOrderTransaction::where('payment_transaction_id', $paymentId)->where('status', 1)->first();

        $invoice_count = NfcCardOrderTransaction::where("invoice_prefix", $config[15]->config_value)->count();
        $invoice_number = $invoice_count + 1;

        // Update nfc card order transaction details
        if ($transactionDetails) {
            $transactionDetails->payment_transaction_id = $paymentId;
            $transactionDetails->invoice_prefix = $config[15]->config_value;
            $transactionDetails->invoice_number = $invoice_number;
            $transactionDetails->payment_status = 'success';
            $transactionDetails->save();
        }

        // Update nfc card order details
        NfcCardOrder::where('nfc_card_order_id', $transactionDetails->nfc_card_order_id)->update([
            'order_status' => 'processing',
        ]);

        // Save applied coupon
        AppliedCoupon::where('transaction_id', $paymentId)->update([
            'status' => 1
        ]);

        // Get NFC card order details
        $nfcCardOrderDetails = NfcCardOrder::where('nfc_card_order_id', $transactionDetails->nfc_card_order_id)->first();

        // Reduce NFC card stock
        $nfcCardDesign = NfcCardDesign::where('nfc_card_id', $nfcCardOrderDetails->nfc_card_id)->first();
        $nfcCardDesign->available_stocks = (int) $nfcCardDesign->available_stocks - 1;
        $nfcCardDesign->save();

        // Generate NFC card key
        $unqiueKey = Str::random(25);

        // Generate NFC card key
        $nfcCardKey = new NfcCardKey();
        $nfcCardKey->nfc_card_key_id = uniqid();
        $nfcCardKey->key_type = 'online';
        $nfcCardKey->unqiue_key = $unqiueKey;
        $nfcCardKey->save();

        // Update nfc card order details
        $orderDetails = json_decode($nfcCardOrderDetails->order_details, true) ?? []; // Ensure it's an array
        $orderDetails['unique_key'] = $unqiueKey;

        $orderDetails = json_encode($orderDetails);

        NfcCardOrder::where('nfc_card_order_id', $transactionDetails->nfc_card_order_id)->update([
            'order_details' => $orderDetails,
            'updated_at' => now(),
        ]);

        // Get customer details
        $customerDetails = User::where('id', $nfcCardOrderDetails->user_id)->first();


        // Email Message details
        $encode = json_decode($transactionDetails['invoice_details'], true);
        $itemDetails = json_decode($nfcCardOrderDetails['order_details'], true);

        $MailTemplateDetails = EmailTemplate::where('email_template_id', '584922675209')->first();
        $ownerMailTemplateDetails = EmailTemplate::where('email_template_id', '584922675211')->first();

        $details = [
            'emailSubject' => $MailTemplateDetails->email_template_subject,
            'emailContent' => $MailTemplateDetails->email_template_content,
            'orderid' => $transactionDetails->nfc_card_order_id,
            'cardname' => $itemDetails['order_item'],
            'cardprice' => $itemDetails['price'],
            'paymentstatus' => $transactionDetails->payment_status,
            'deliverystatus' => 'processing',
            'quantity' => 1,
            'trackingnumber' => $itemDetails['tracking_number'] ?? '-',
            'courierpartner' => $itemDetails['courier_partner'] ?? '-',
            'orderpageurl' => route('user.order.nfc.card.view', $transactionDetails->nfc_card_order_id),
            'totalprice' => $symbol . $encode['invoice_amount'],
            'supportemail' => $encode['from_billing_email'],
            'supportphone' => $encode['from_billing_phone'],
        ];

        $ownerDetails = [
            'emailSubject' => $ownerMailTemplateDetails->email_template_subject,
            'emailContent' => $ownerMailTemplateDetails->email_template_content,
            'orderid' => $nfcCardOrderDetails->nfc_card_order_id,
            'cardname' => $itemDetails['order_item'],
            'cardprice' => $itemDetails['price'],
            'paymentstatus' => $nfcCardOrderDetails->payment_status,
            'deliverystatus' => 'processing',
            'quantity' => 1,
            'trackingnumber' => $itemDetails['tracking_number'] ?? '-',
            'courierpartner' => $itemDetails['courier_partner'] ?? '-',
            'orderpageurl' => route('admin.order.show', $nfcCardOrderDetails->nfc_card_order_id),
            'totalprice' => $symbol . $encode['invoice_amount'],
            'supportemail' => $encode['from_billing_email'],
            'supportphone' => $encode['from_billing_phone'],
            'customerName' => $customerDetails->name,
            'customeremail' => $customerDetails->email,
        ];

        try {
            Mail::to($encode['to_billing_email'])->send(new \App\Mail\AppointmentMail($details));
            Mail::to(env('MAIL_FROM_ADDRESS'))->send(new \App\Mail\AppointmentMail($ownerDetails));
        } catch (\Exception $e) {
            Log::info($e);
        }
    }

    // Failed
    public function paymentFailed($paymentId)
    {
        NfcCardOrderTransaction::where('payment_transaction_id', $paymentId)->update([
            'payment_status' => 'failed',
        ]);
    }
}
