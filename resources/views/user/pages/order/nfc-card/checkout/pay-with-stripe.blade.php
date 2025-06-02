@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true, 'settings' => $settings])

@section('content')
    <div class="page-wrapper">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="container-fluid">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                            {{ __('Overview') }}
                        </div>
                        <h2 class="page-title">
                            {{ __('Checkout') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-fluid">
                {{-- Failed --}}
                @if (Session::has('failed'))
                    <div class="alert alert-important alert-danger alert-dismissible mb-2" role="alert">
                        <div class="d-flex">
                            <div>
                                {{ Session::get('failed') }}
                            </div>
                        </div>
                        <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- Success --}}
                @if (Session::has('success'))
                    <div class="alert alert-important alert-success alert-dismissible mb-2" role="alert">
                        <div class="d-flex">
                            <div>
                                {{ Session::get('success') }}
                            </div>
                        </div>
                        <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">
                                {{ __('Choosed NFC Card') }}: {{ $nfcDetails->nfc_card_name }}
                            </h3>
                
                            <div class="card col-12">
                                <form action="{{ route('nfc.stripe.payment.status', $paymentId) }}" method="post" id="payment-form">
                                    @csrf
                                    <div id="payment-element">
                                        <!-- Stripe Payment Element will be inserted here -->
                                    </div>
                                    <div id="card-errors" class="text-danger mt-3" role="alert"></div>
                                    <div class="mt-3">
                                        <button id="submit-button" class="btn btn-dark" type="submit">
                                            {{ __('Pay Now') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                
                            <br>
                
                            <a class="mt-2 text-muted text-underline" href="{{ route('nfc.stripe.payment.cancel', $paymentId) }}">
                                {{ __('Cancel payment and back to home') }}
                            </a>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
        @include('user.includes.footer')
    </div>
    
    @push('custom-js')
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            (async function() {
                "use strict";
        
                const stripe = Stripe('{{ $config[9]->config_value }}'); // Replace with your Stripe publishable key
                const clientSecret = '{{ $intent }}'; // Replace with the PaymentIntent client secret from your server
        
                const elements = stripe.elements({
                    clientSecret,
                    appearance: {
                        theme: 'stripe',
                    },
                });
        
                // Create the Payment Element
                const paymentElement = elements.create('payment');
                paymentElement.mount('#payment-element');
        
                // Form submission handling
                const form = document.getElementById('payment-form');
                const submitButton = document.getElementById('submit-button');
                const errorContainer = document.getElementById('card-errors');
        
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();
        
                    // Disable the submit button to prevent multiple submissions
                    submitButton.disabled = true;
        
                    const { error } = await stripe.confirmPayment({
                        elements,
                        confirmParams: {
                            return_url: '{{ route('nfc.stripe.payment.status', $paymentId) }}', // Optional: Redirect URL on success
                        },
                    });
        
                    if (error) {
                        // Show error to the customer
                        errorContainer.textContent = error.message;
                        submitButton.disabled = false;
                    }
                });
            })();
        </script>        
    @endpush
@endsection
