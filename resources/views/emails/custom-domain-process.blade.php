<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Domain Processed Notification') }}</title>
    <style>
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
        <h2 class="title">{{ __('Domain Processed Notification') }}</h2>
        <p class="text">{{ __('Hello ' . $customDomainRequestDetails['cardName'] . ',') }}</p>
        <p class="text">
            {{ __('We are pleased to inform you that your domain request has been successfully processed.') }}
        </p>
        <p class="text">
            {{ __('Here are the details of the processed domain:') }}
        </p>

        <!-- Domain Details -->
        <div class="domain-details">
            <p>{{ __('Domain Details:') }}</p>
            <ul class="list-none">
                <li class="list-item"><span>{{ __('Previous Domain:') }}</span> <a href="https://{{ $customDomainRequestDetails['previousDomain'] }}" target="_blank">{{ $customDomainRequestDetails['previousDomain'] }}</a></li>
                <li class="list-item"><span>{{ __('Current Domain:') }}</span> <a href="https://{{ $customDomainRequestDetails['currentDomain'] }}" target="_blank">{{ $customDomainRequestDetails['currentDomain'] }}</a></li>
            </ul>
        </div>

        <p class="text">{{ __('Thank you for choosing our service!') }}</p>
        <p class="text">{{ __('Best regards,') }}</p>
        <p class="highlight">{{ env('APP_NAME') }}</p>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; {{ env('APP_NAME') }}. {{ __('All rights reserved.') }}</p>
    </footer>
</body>

</html>
