<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ __('New Appointment') }}</title>
    <style>
        /* Basic Tailwind-inspired styles for email */
        /* Tailwind CSS for email styles */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7fafc;
        }

        .container {
            max-width: 100%;
            margin: 20px auto;
            padding: 24px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 16px;
        }

        .text {
            font-size: 1rem;
            color: #4a5568;
            margin-bottom: 16px;
        }

        .highlight {
            font-weight: 600;
            color: #1a202c;
        }

        .domain-details {
            background-color: #f7fafc;
            padding: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-top: 16px;
        }

        .domain-details p {
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 8px;
        }

        .appointment-details {
            background-color: #f7fafc;
            padding: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-top: 16px;
        }

        .appointment-details p {
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 8px;
        }

        .list-item {
            margin-bottom: 8px;
        }

        .list-item span {
            font-weight: 600;
            color: #2d3748;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3182ce;
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            margin-top: 24px;
        }

        .button:hover {
            background-color: #2b6cb0;
        }

        .footer {
            text-align: center;
            font-size: 0.875rem;
            color: #718096;
            margin-top: 32px;
        }

        .footer p {
            margin-bottom: 8px;
        }

        .email-container {
            width: 100%;
            padding: 20px;
            background-color: #f3f4f6;
        }

        .email-content {
            width: 600px;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            margin: auto;
        }

        .email-header h1 {
            color: #1f2937;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .email-body p {
            color: #4b5563;
            font-size: 16px;
            text-align: center;
            margin-bottom: 20px;
        }

        .email-details {
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 5px;
        }

        .email-details p {
            color: #1f2937;
            font-size: 16px;
        }

        .email-details ul {
            color: #4b5563;
            font-size: 14px;
            list-style-type: none;
            padding: 0;
        }

        .track-button,
        .view-order-button {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            text-align: center;
            margin: 20px auto;
        }

        .email-footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer p {
            color: #6b7280;
            font-size: 12px;
        }

        .email-footer a {
            color: #3b82f6;
            text-decoration: none;
        }

        @media (max-width: 640px) {
            .container {
                margin: 20px auto;
                padding: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        @php
            $hyperlink = '';
            if (isset($details['vcardUrl'])) {
                $hyperlink = '<a href="' . $details['vcardUrl'] . '">' . $details['vcardName'] . '</a>';
            }
        @endphp

        <!-- Header Section -->
        {!! strtr($details['emailContent'], [
            ':appname' => env('APP_NAME'),
            ':hyperlink' => $hyperlink ?? '',
            ':vcardname' => $details['vcardName'] ?? '',
            ':vcardurl' => $details['vcardUrl'] ?? '',
            ':appointmentdate' => $details['appointmentDate'] ?? '',
            ':appointmenttime' => $details['appointmentTime'] ?? '',
            ':googlecalendarurl' => $details['googleCalendarUrl'] ?? '',
            '%3Agooglecalendarurl' => $details['googleCalendarUrl'] ?? '',
            ':status' => $details['status'] ?? '',
            ':appointmentpageurl' => isset($details['cardId']) ? route('user.appointments', $details['cardId']) : '',
            '%3Aappointmentpageurl' => isset($details['cardId']) ? route('user.appointments', $details['cardId']) : '',
            ':customername' => $details['customerName'] ?? '',
            ':previousdomain' => $details['previousDomain'] ?? '',
            ':currentdomain' => $details['currentDomain'] ?? '',
            ':receivername' => $details['receiverName'] ?? '',
            ':receiveremail' => $details['receiverEmail'] ?? '',
            ':receiverphone' => $details['receiverPhone'] ?? '',
            ':receivermessage' => $details['receiverMessage'] ?? '',
            ':planname' => $details['planName'] ?? '',
            ':plancode' => $details['planCode'] ?? '',
            ':planprice' => $details['planPrice'] ?? '',
            ':expirydate' => $details['expiryDate'] ?? '',
            ':registeredname' => $details['registeredName'] ?? '',
            ':registeredemail' => $details['registeredEmail'] ?? '',
            ':orderid' => $details['orderid'] ?? '',
            ':cardname' => $details['cardname'] ?? '',
            ':cardprice' => $details['cardprice'] ?? '',
            ':paymentstatus' => $details['paymentstatus'] ?? '',
            ':deliverystatus' => $details['deliverystatus'] ?? '',
            ':quantity' => $details['quantity'] ?? '',
            ':trackingnumber' => $details['trackingnumber'] ?? '',
            ':courierpartner' => $details['courierpartner'] ?? '',
            ':orderpageurl' => $details['orderpageurl'] ?? '',
            '%3Aorderpageurl' => $details['orderpageurl'] ?? '',
            ':totalprice' => $details['totalprice'] ?? '',
            ':supportemail' => $details['supportemail'] ?? '',
            ':supportphone' => $details['supportphone'] ?? '',
            ':customeremail' => $details['customeremail'] ?? '',
            ':actionlink' => $details['actionlink'] ?? '',
            '%3Aactionlink' => $details['actionlink'] ?? '',
        ]) !!}

        <!-- Footer Section -->
        <div class="footer">
            &copy; {{ env('APP_NAME') }}. {{ __('All rights reserved.') }}
        </div>
    </div>
</body>

</html>
