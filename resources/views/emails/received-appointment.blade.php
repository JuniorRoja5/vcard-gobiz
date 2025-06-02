<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('New Appointment Request') }}</title>
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
        <h2 class="title">{{ __('New Appointment Request') }}</h2>
        <p class="text">{{ __('Hello,') }}</p>
        <p class="text">
            {{ __('You have received a new appointment request from') }}
            <span class="highlight">{{ __($ownerdDetails['customerName']) }}</span>.
            {{ __('Here are the details:') }}
        </p>

        <!-- Appointment Details -->
        <div class="appointment-details">
            <p>{{ __('Appointment Details:') }}</p>
            <ul class="list-none">
                <li class="list-item"><span>{{ __('Date:') }}</span> {{ $ownerdDetails['appointmentDate'] }}</li>
                <li class="list-item"><span>{{ __('Time:') }}</span> {{ $ownerdDetails['appointmentTime'] }}</li>
            </ul>
        </div>

        <!-- View Appointment Button -->
        <a href="{{ route('user.appointments', $ownerdDetails['cardId']) }}" class="button">
            {{ __('View Appointment') }}
        </a>

        <p class="text">{{ __('Thank you for using our service!') }}</p>
        <p class="text">{{ __('Best regards,') }}</p>
        <p class="highlight">{{ env('APP_NAME') }}</p>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; {{ env('APP_NAME') }}. {{ __('All rights reserved.') }}</p>
    </footer>
</body>

</html>
