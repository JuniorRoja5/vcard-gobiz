@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

{{-- Custom CSS --}}
@section('css')
    {{-- Tiny MCE --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.0.1/tinymce.min.js" integrity="sha512-KGtsnWohFUg0oksKq7p7eDgA1Rw2nBfqhGJn463/rGhtUY825dBqGexj8eP04LwfnsSW6dNAHAlOqKJKquHsnw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- QrCode --}}
    <script src="{{ url('js/qrious.min.js') }}"></script>

    <style>
        .activation-code {
            font-size: 16px;
            font-weight: bold;
            color: #d9534f;
            margin: 10px 0;
        }

        .qr-code {
            margin-top: 20px;
        }

        .logo {
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 150px;
        }
    </style>
@endsection

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
                            {{ __('Greeting Letter') }}
                        </h2>
                    </div>
                    {{-- Print --}}
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" onclick="window.print();">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-1">
                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2">
                                </path>
                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z">
                                </path>
                            </svg>
                            {{ __('Print') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page body -->
        <div class="page-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-5 col-xl-5">
                        <div class="card m-3">
                            <div class="card-body m-2">
                                <div class="card-text">
                                    @php
                                        // Replacements
                                        $orderDetailsDecoded = json_decode($orderDetails['order_details']);
                                        $invoiceDetails = json_decode($orderDetails['invoice_details']);

                                        $replacements = [
                                            ':logo' => asset($settings->site_logo),
                                            '%3Alogo' => asset($settings->site_logo),
                                            ':websitename' => config('app.name'),
                                            ':customername' => $orderDetails['name'],
                                            ':activationcode' => $orderDetailsDecoded->unique_key,
                                            ':supportemail' => $invoiceDetails->from_billing_email,
                                            ':supportphone' => empty($invoiceDetails->from_billing_phone) ? 'N/A' : $invoiceDetails->from_billing_phone,
                                        ];

                                        $greetingLetter = str_replace(array_keys($replacements), array_values($replacements), $config[74]->config_value);
                                    @endphp

                                    {{-- Render --}}
                                    {!! $greetingLetter !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Update greeting letter --}}
                    <div class="col-12 col-md-7 col-xl-7 d-print-none">
                        <div class="card m-3">
                            <form action="{{ route('admin.update.greeting.letter', $orderDetails['nfc_card_order_id']) }}"
                                method="post">
                                @csrf
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('Update Greeting Letter') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <textarea name="greeting_letter" id="greeting_letter" class="form-control" rows="5">{{ $config[74]->config_value ?? $template }}</textarea>
                                    </div>
                                    {{-- Available Short Codes --}}
                                    <div class="table-responsive">
                                        <h2 class="page-title my-3"> {{ __('Available Short Codes') }} </h2>
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Short Code') }}</th>
                                                    <th>{{ __('Value') }}</th>
                                                    <th>{{ __('Short Code') }}</th>
                                                    <th>{{ __('Value') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="py-3">:logo</td>
                                                    <td class="py-3 fw-bold">{{ __('Logo') }}</td>
                                                    <td class="py-3">:websitename</td>
                                                    <td class="py-3 fw-bold">{{ __('Website Name') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="py-3">:customername</td>
                                                    <td class="py-3 fw-bold">{{ __('Customer Name') }}</td>
                                                    <td class="py-3">:activationcode</td>
                                                    <td class="py-3 fw-bold">{{ __('Activation Code') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="py-3">:supportemail</td>
                                                    <td class="py-3 fw-bold">{{ __('Support Email') }}</td>
                                                    <td class="py-3">:supportphone</td>
                                                    <td class="py-3 fw-bold">{{ __('Support Phone') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>

    {{-- Custom JS --}}
@section('scripts')
    <script>
        // HTML Editor
        tinymce.init({
            selector: '#greeting_letter',
            plugins: 'code preview importcss searchreplace autolink autosave save directionality visualblocks visualchars link charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap quickbars emoticons',
            menubar: 'file edit view insert format tools',
            toolbar: 'code undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | preview save print | insertfile link anchor | ltr rtl',
            content_style: 'body { font-family:"Inter Var",Inter,-apple-system,BlinkMacSystemFont,San Francisco,Segoe UI,Roboto,Helvetica Neue,sans-serif; font-size:16px }',
            menubar: false,
            statusbar: false,
            height: 450
        });

        // Activate QR Code
        document.addEventListener("DOMContentLoaded", function() {
            "use strict";
            var activateQrCodeElement = document.getElementById('activateQrCode');

            if (activateQrCodeElement) {
                var activationUrl =
                    "{{ route('user.activate.nfc.card') }}?id={{ json_decode($orderDetails->order_details)->unique_key }}";

                new QRious({
                    element: activateQrCodeElement,
                    value: activationUrl, // Laravel route with unique key
                    size: 150,
                    background: 'white', // Background color
                    foreground: 'black', // Foreground (QR code) color
                    level: 'H' // Error correction level
                });
            }
        });
    </script>
@endsection
@endsection
