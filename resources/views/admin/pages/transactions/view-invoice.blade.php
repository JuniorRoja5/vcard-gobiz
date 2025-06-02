@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

@section('css')
<script src="{{ asset('js/html2pdf.bundle.min.js')}}"></script>
<script>
    function generatePDF() {
        const element = document.getElementById('invoice');
        html2pdf()
		.set({ filename: `{{ $transaction->invoice_prefix ? $transaction->invoice_prefix : 'TR' }}{{ $transaction->invoice_number ? $transaction->invoice_number : $transaction->gobiz_transaction_id }}`+'.pdf', html2canvas: { scale: 4 } })
		.from(element)
		.save();
    }
</script>
@endsection

@php
    use App\Setting;
    use App\Plan;

    $settings = Setting::first();
    $planDetails = Plan::where('plan_id', $transaction->plan_id)->first();

    // For monthly
    if ($planDetails->validity >= 30 && $planDetails->validity <= 31) {
        $term = 'Monthly';
    } else if($planDetails->validity >= 365 && $planDetails->validity <= 366) {
        $term = 'Yearly';
    } else {
        $term = $planDetails->validity . ' days';
    }
@endphp

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
                                <span class="h4">{{ $transaction->billing_details['from_billing_name'] }}</span><br>

                                {{-- Address --}}
                                <span>
                                    {{ $transaction->billing_details['from_billing_address'] }},
                                    {{ $transaction->billing_details['from_billing_city'] }}, {{ $transaction->billing_details['from_billing_state'] }}
                                    {{ $transaction->billing_details['from_billing_country'] }} <br>
                                </span>

                                {{-- Email --}}
                                <span><strong>{{ __('Email') }}</strong>: {{ $transaction->billing_details['from_billing_email'] }}</span><br>

                                {{-- Phone --}}
                                @if ($transaction->billing_details['from_billing_phone'] != null)
                                <span><strong>{{ __('Phone') }}</strong>: {{ $transaction->billing_details['from_billing_phone'] }}</span><br>
                                @endif

                                {{-- Tax Number --}}
                                @if ($transaction->billing_details['from_vat_number'] != null)
                                <span><strong>{{ __('Tax Number') }}</strong>: {{ $transaction->billing_details['from_vat_number'] }}</span><br>
                                @endif
                            </div>
                            <div class="col-6 text-end">
                                <h1>{{ __('INVOICE') }}</h1>
                                <h4>#{{ $transaction->invoice_prefix }}{{ $transaction->invoice_number }}</h4>
                            </div>
                        </div>
            
                        <!-- Billing Information -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <h4 class="text-muted">{{ __('Bill To') }}</h4>
                                <span class="h4">{{ $transaction->billing_details['to_billing_name'] }}</span><br>
                                <span>
                                    {{ $transaction->billing_details['to_billing_address'] }},
                                    {{ $transaction->billing_details['to_billing_city'] }}, {{ $transaction->billing_details['to_billing_state'] }}
                                    {{ $transaction->billing_details['to_billing_country'] }} <br>
                                </span>

                                {{-- Email --}}
                                <span><strong>{{ __('Email') }}</strong>: {{ $transaction->billing_details['to_billing_email'] }}</span><br>

                                {{-- Phone --}}
                                @if ($transaction->billing_details['to_billing_phone'] != null)
                                <span><strong>{{ __('Phone') }}</strong>: {{ $transaction->billing_details['to_billing_phone'] }}</span><br>
                                @endif

                                {{-- Tax Number --}}
                                @if ($transaction->billing_details['to_vat_number'] != null)
                                <span><strong>{{ __('Tax Number') }}</strong>: {{ $transaction->billing_details['to_vat_number'] }}</span><br>
                                @endif
                            </div>
                            <div class="col-6 text-end">
                                <p><strong>{{ __('Date') }}</strong>: {{ date('M d, Y', strtotime($transaction->transaction_date)) }}</p>
                                <p><strong>{{ __('Payment Terms') }}</strong>: {{ __($term) }}</p>
                                <h5><strong>{{ __('Balance Due') }}</strong>: {{ $currencies->firstWhere('iso_code', $transaction->transaction_currency)->symbol ?? '' }}0.00</h5>
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
                                    <td>{{ __($planDetails->plan_name) }} - {{ $currencies->firstWhere('iso_code', $transaction->transaction_currency)->symbol ?? '' }}{{ $planDetails->plan_price }}/{{ $term }}</td>
                                    <td class="text-end">1</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction->transaction_currency)->symbol ?? '' }}{{ $planDetails->plan_price }}</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction->transaction_currency)->symbol ?? '' }}{{ $planDetails->plan_price }}</td>
                                </tr>
                            </tbody>
                            <tfoot class="border-top fw-bold">
                                {{-- Subtotal --}}
                                <tr>
                                    <td colspan="3" class="text-end">{{ __('Subtotal') }}</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction->transaction_currency)->symbol ?? '' }}{{ $transaction->billing_details['subtotal'] }}</td>
                                </tr>

                                {{-- Tax --}}
                                @if ($transaction->billing_details['tax_amount'] > 0)
                                <tr>
                                    <td colspan="3" class="text-end">{{ __($transaction->billing_details['tax_name']) }} ({{ $transaction->billing_details['tax_value'] }}%)</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction->transaction_currency)->symbol ?? '' }}{{ $transaction->billing_details['tax_amount'] }}</td>
                                </tr>
                                @endif

                                {{-- Applied Coupon --}}
                                @if (isset($transaction->billing_details['applied_coupon']) != null)

                                {{-- Before Discount --}}
                                <tr>
                                    <td colspan="3" class="text-end">{{ __('Before Discount') }}</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction->transaction_currency)->symbol ?? '' }}{{ $transaction->billing_details['subtotal'] + $transaction->billing_details['tax_amount'] }}</td>
                                </tr>

                                {{-- Discount --}}
                                <tr>
                                    <td colspan="3" class="font-weight-bold text-end"><strong>{{ __('Applied Coupon') }} : {{ $transaction->billing_details['applied_coupon'] }}</strong></td>
                                    <td class="font-weight-bold text-end">
                                        - {{ $currencies->firstWhere('iso_code', $transaction->transaction_currency)->symbol ?? '' }}{{ $transaction->billing_details['discounted_price'] }}
                                    </td>
                                </tr>
                                @else
                                {{-- After Tax --}}
                                <tr>
                                    <td colspan="3" class="text-end">{{ __('After Tax') }}</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction->transaction_currency)->symbol ?? '' }}{{ $transaction->billing_details['subtotal'] + $transaction->billing_details['tax_amount'] }}</td>
                                </tr>
                                @endif

                                {{-- Total --}}
                                <tr>
                                    <td colspan="3" class="text-end"><strong>{{ __('Total') }}</strong></td>
                                    <td class="text-end"><strong>{{ $currencies->firstWhere('iso_code', $transaction->transaction_currency)->symbol ?? '' }}{{ $transaction->billing_details['invoice_amount'] }}</strong></td>
                                </tr>

                                {{-- Amount Paid --}}
                                <tr>
                                    <td colspan="3" class="text-end">{{ __('Amount Paid') }}</td>
                                    <td class="text-end">{{ $currencies->firstWhere('iso_code', $transaction->transaction_currency)->symbol ?? '' }}{{ $transaction->billing_details['invoice_amount'] }}</td>
                                </tr>
                            </tfoot>
                        </table>
            
                        <!-- Notes Section -->
                        <p class="mt-5"><strong>{{ __('Notes') }}</strong>:<br><span class="text-muted">{{ __('Payment from '. $transaction->payment_gateway_name) }}<br>{{ __('Transaction ID: ') }} {{ $transaction->transaction_id }}</span></p>

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