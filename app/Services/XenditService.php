<?php

namespace App\Services;

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Illuminate\Support\Facades\DB;

class XenditService
{
    protected $invoiceApi;

    public function __construct()
    {
        // Get secrect key from config table
        $config = DB::table('config')->get();
        $secretKey = $config[72]->config_value;

        Configuration::setXenditKey($secretKey);
        $this->invoiceApi = new InvoiceApi();
    }

    public function createInvoice($externalId, $amount, $payerEmail, $description, $successRedirectUrl)
    {
        $params = [
            'external_id' => $externalId,
            'amount' => $amount,
            'payer_email' => $payerEmail,
            'description' => $description,
            'success_redirect_url' => $successRedirectUrl
        ];

        return $this->invoiceApi->createInvoice($params);
    }

    public function getInvoiceById($paymentId)
    {
        try {
            // Assuming the $invoiceApi->getInvoiceById is calling the external Xendit API to retrieve the invoice details
            $response = $this->invoiceApi->getInvoiceById($paymentId);

            // Check if the response is valid (you may want to validate it based on the actual API response)
            if (isset($response['status'])) {
                return $response; // Return the payment status data if available
            } else {
                // Handle the case where the status is not available (you can log or throw an exception)
                throw new \Exception('Invalid response from Xendit API.');
            }
        } catch (\Exception $e) {
            // Handle any errors from the API call (e.g., network error, invalid payment ID)
            // You can log the error for debugging or handle it as needed
            return [
                'status' => 'ERROR',
                'message' => $e->getMessage(),
            ];
        }
    }
}
