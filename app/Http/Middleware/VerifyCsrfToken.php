<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/phonepe-payment-status',
        '/mercadopago-callback',
        '/paytr-payment-status',
        '/paytr-payment-failure',
        '/paytr-payment-webhook',
        '/nfc-phonepe-payment-status',
        '/nfc-mercadopago-callback',
        '/nfc-paytr-payment-status',
        '/nfc-paytr-payment-failure',
        '/nfc-paytr-payment-webhook',
        '/nfc/phonepe-payment-status'
    ];
}
