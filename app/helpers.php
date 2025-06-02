<?php

use App\Currency;
use Illuminate\Support\Facades\DB;

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount)
    {
        // Fetch settings from the database
        $config = DB::table('config')->get();

        // Set decimal value
        $formatType = $config[55]->config_value ?? '1.234.567,89';
        $setDecimalsPlaces = (int)($config[56]->config_value ?? 2);

        // Define formatting styles based on user preference
        switch ($formatType) {
            case '1,234,567.89':
                return number_format($amount, $setDecimalsPlaces, '.', ',');
            case '12,34,567.89':
                return formatIndianNumber($amount, $setDecimalsPlaces);
            case '1.234.567,89':
                return number_format($amount, $setDecimalsPlaces, ',', '.');
            case '1 234 567,89':
                return number_format($amount, $setDecimalsPlaces, ',', ' ');
            case "1'234'567.89":
                return number_format($amount, $setDecimalsPlaces, '.', "'");
            default:
                return number_format($amount, $setDecimalsPlaces, '.', ','); // default format
        }
    }

    // Custom function for Indian numbering system
    function formatIndianNumber($amount, $setDecimalsPlaces = 2)
    {
        // Round to the specified decimal places
        $amount = number_format($amount, $setDecimalsPlaces, '.', '');

        // Split the amount into integer and decimal parts
        $amountParts = explode('.', $amount);
        $integerPart = $amountParts[0];
        $decimalPart = isset($amountParts[1]) ? '.' . $amountParts[1] : '';

        // Format the integer part for the Indian numbering system
        $lastThreeDigits = substr($integerPart, -3);
        $otherDigits = substr($integerPart, 0, -3);

        if ($otherDigits !== '') {
            $otherDigits = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $otherDigits);
            $formattedInteger = $otherDigits . ',' . $lastThreeDigits;
        } else {
            $formattedInteger = $lastThreeDigits;
        }

        return $formattedInteger . $decimalPart;
    }
}