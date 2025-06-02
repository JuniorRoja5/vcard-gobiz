@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

@section('css')
<script src="{{ asset('js/html2pdf.bundle.min.js')}}"></script>
<script>
    function generatePDF() {
        const element = document.getElementById('invoice');
        html2pdf()
		.set({ filename: `{{ $transaction_details->invoice_prefix ? $transaction_details->invoice_prefix : 'TR' }}{{ $transaction_details->invoice_number ? $transaction_details->invoice_number : $transaction_details->nfc_card_order_transaction_id }}`+'.pdf', html2canvas: { scale: 4 } })
		.from(element)
		.save();
    }
</script>
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
                        {{ __('Invoice') }}
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="dropdown">
                        <button type="button" class="btn btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                            </svg>
                            {{ __('Actions') }}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" onclick="generatePDF()" onclick="javascript:window.print();">
                                {{ __('Download') }}
                            </a>
                            <a class="dropdown-item" onclick="javascript:window.print();">
                                {{ __('Print') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            {{-- Failed --}}
            @if(Session::has("failed"))
            <div class="alert alert-important alert-danger alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('failed')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            {{-- Success --}}
            @if(Session::has("success"))
            <div class="alert alert-important alert-success alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('success')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif
            
            <div class="card card-lg">
                <div class="p-3" id="invoice">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-6">

                                {{-- Logo --}}
                                <img src="{{ asset($settings->site_logo) }}" class="img-fluid" alt="{{ config('app.name') }}"><br>

                                {{-- Name --}}
                                <span class="h4">{{ json_decode($transaction_details->invoice_details)->from_billing_name }}</span><br>

                                {{-- Address --}}
                                <span>
                                    {{ json_decode($transaction_details->invoice_details)->from_billing_address }},
                                    {{ json_decode($transaction_details->invoice_details)->from_billing_city }}, {{ json_decode($transaction_details->invoice_details)->from_billing_state }}
                                    {{ json_decode($transaction_details->invoice_details)->from_billing_country }} <br>
                                </span>

                                {{-- Email --}}
                                <span><strong>{{ __('Email') }}</strong>: {{ json_decode($transaction_details->invoice_details)->from_billing_email }}</span><br>

                                {{-- Phone --}}
                                @if (json_decode($transaction_details->invoice_details)->from_billing_phone != null)
                                <span><strong>{{ __('Phone') }}</strong>: {{ json_decode($transaction_details->invoice_details)->from_billing_phone }}</span><br>
                                @endif

                                {{-- Tax Number --}}
                                @if (json_decode($transaction_details->invoice_details)->from_vat_number != null)
                                <span><strong>{{ __('Tax Number') }}</strong>: {{ json_decode($transaction_details->invoice_details)->from_vat_number }}</span><br>
                                @endif
                            </div>
                            <div class="col-6 text-end">
                                <h1>{{ __('INVOICE') }}</h1>
                                <h4>#{{ $transaction_details->invoice_prefix }}{{$transaction_details->invoice_number }}</h4>
                            </div>
                        </div>
            
                        <!-- Billing Information -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <h4 class="text-muted">{{ __('Bill To') }}</h4>
                                <span class="h4">{{ json_decode($transaction_details->invoice_details)->to_billing_name }}</span><br>
                                <span>
                                    {{ json_decode($transaction_details->invoice_details)->to_billing_address }},
                                    {{ json_decode($transaction_details->invoice_details)->to_billing_city }}, {{ json_decode($transaction_details->invoice_details)->to_billing_state }}
                                    {{ json_decode($transaction_details->invoice_details)->to_billing_country }} <br>
                                </span>

                                {{-- Email --}}
                                <span><strong>{{ __('Email') }}</strong>: {{ json_decode($transaction_details->invoice_details)->to_billing_email }}</span><br>

                                {{-- Phone --}}
                                @if (json_decode($transaction_details->invoice_details)->to_billing_phone != null)
                                <span><strong>{{ __('Phone') }}</strong>: {{ json_decode($transaction_details->invoice_details)->to_billing_phone }}</span><br>
                                @endif

                                {{-- Tax Number --}}
                                @if (json_decode($transaction_details->invoice_details)->to_vat_number != null)
                                <span><strong>{{ __('Tax Number') }}</strong>: {{ json_decode($transaction_details->invoice_details)->to_vat_number }}</span><br>
                                @endif
                            </div>
                            <div class="col-6 text-end">
                                <p><strong>{{ __('Date') }}</strong>: {{ formatDateForUser($transaction_details->created_at) }}</p>
                                <p><strong>{{ __('Payment Terms') }}</strong>: {{ __("One time") }}</p>
                                <h5><strong>{{ __('Balance Due') }}</strong>: {{ $currencies->firstWhere('iso_code', $transaction_details->currency)->symbol ?? '' }}0.00</h5>
                            </div>
                        </div>
            
                        <!-- Items Table -->
                        <table class="table table-borderless">
                            <thead class="border-bottom">
                                <tr>
                                    <th>{{ __('Item') }}</th>
                                    <th class="text-end">{{ __('Quantity') }}</th>
                                    <th class="text-end">{{ __('Rate') }}</th>
                                    <th class="text-end">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="fw-bold">
                                    <td>{{ __($transaction_details->nfc_card_name) }} - {{ $currencies->firstWhere('iso_code', $transaction_details->currency)->symbol ?? '' }}{{ json_decode($transaction_details->invoice_details)->subtotal }}/{{ __('One time') }}</td>
                                    <td class="text-end">1</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction_details->currency)->symbol ?? '' }}{{ json_decode($transaction_details->invoice_details)->subtotal }}</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction_details->currency)->symbol ?? '' }}{{ json_decode($transaction_details->invoice_details)->subtotal }}</td>
                                </tr>
                            </tbody>
                            <tfoot class="border-top fw-bold">
                                {{-- Subtotal --}}
                                <tr>
                                    <td colspan="3" class="text-end">{{ __('Subtotal') }}</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction_details->currency)->symbol ?? '' }}{{ json_decode($transaction_details->invoice_details)->subtotal }}</td>
                                </tr>

                                {{-- Tax --}}
                                @if (json_decode($transaction_details->invoice_details)->tax_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-end">{{ __(json_decode($transaction_details->invoice_details)->tax_name) }} ({{ json_decode($transaction_details->invoice_details)->tax_value }}%)</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction_details->currency)->symbol ?? '' }}{{ json_decode($transaction_details->invoice_details)->tax_amount }}</td>
                                </tr>
                                @endif

                                {{-- Applied Coupon --}}
                                @if (isset(json_decode($transaction_details->invoice_details)->applied_coupon) != null)
                                {{-- Before Discount --}}
                                <tr>
                                    <td colspan="3" class="text-end">{{ __('Before Discount') }}</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction_details->currency)->symbol ?? '' }}{{ json_decode($transaction_details->invoice_details)->subtotal + json_decode($transaction_details->invoice_details)->tax_amount }}</td>
                                </tr>

                                {{-- Discount --}}
                                <tr>
                                    <td colspan="3" class="font-weight-bold text-end"><strong>{{ __('Applied Coupon') }} : {{ json_decode($transaction_details->invoice_details)->applied_coupon }}</strong></td>
                                    <td class="font-weight-bold text-end">
                                        - {{ $currencies->firstWhere('iso_code', $transaction_details->currency)->symbol ?? '' }}{{ json_decode($transaction_details->invoice_details)->discounted_price }}
                                    </td>
                                </tr>
                                @endif

                                {{-- Total --}}
                                <tr>
                                    <td colspan="3" class="text-end"><strong>{{ __('Total') }}</strong></td>
                                    <td class="text-end"><strong>{{ $currencies->firstWhere('iso_code', $transaction_details->currency)->symbol ?? '' }}{{ json_decode($transaction_details->invoice_details)->invoice_amount }}</strong></td>
                                </tr>

                                {{-- Amount Paid --}}
                                <tr>
                                    <td colspan="3" class="text-end">{{ __('Amount Paid') }}</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction_details->currency)->symbol ?? '' }}{{ json_decode($transaction_details->invoice_details)->invoice_amount }}</td>
                                </tr>
                            </tfoot>
                        </table>
            
                        <!-- Notes Section -->
                        <p class="mt-5"><strong>{{ __('Notes') }}</strong>:<br><span class="text-muted">{{ __('Payment from '. $transaction_details->payment_method) }}<br>{{ __('Transaction ID: ') }} {{ $transaction_details->payment_transaction_id }}</span></p>

                        {{-- Footer --}}
                        <p class="text-center text-muted mt-5">
                            {{ __($config[29]->config_value ?? 'Thank you for your business!') }}
                        </p>
                    </div>
                </div>
            </div>                         
        </div>
    </div>

    {{-- Footer --}}
    @include('admin.includes.footer')
</div>
@endsection