@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

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
                            {{ __('NFC Card Order') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page body -->
        <div class="page-body">
            <div class="container-fluid">
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

                {{-- Order Details --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Order & Delivery Details') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="datagrid">
                                    <div class="datagrid-item mx-3 my-1 d-print-none">
                                        <div class="datagrid-title">{{ __('Payment ID') }}</div>
                                        <div class="datagrid-content fw-bold">
                                            {{ $order->nfc_card_order_transaction_id ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="datagrid-item mx-3 my-1">
                                        <div class="datagrid-title">{{ __('Order ID') }}</div>
                                        <div class="datagrid-content fw-bold">{{ $order->nfc_card_order_id ?? '-' }}</div>
                                    </div>
                                    <div class="datagrid-item mx-3 my-1 d-print-none">
                                        <div class="datagrid-title">{{ __('Order Date') }}</div>
                                        <div class="datagrid-content fw-bold">
                                            {{ formatDateForUser($order->created_at) }}
                                        </div>
                                    </div>
                                    <div class="datagrid-item mx-3 my-1 d-print-none">
                                        <div class="datagrid-title">{{ __('Delivery Status') }}</div>
                                        <div class="datagrid-content fw-bold">
                                            @if ($order->order_status == 'pending')
                                                <span class="badge bg-warning text-white text-uppercase">
                                                    {{ __('Pending') }}
                                                </span>
                                            @elseif($order->order_status == 'processing')
                                                <span class="badge bg-primary text-white text-uppercase">
                                                    {{ __('Processing') }}
                                                </span>
                                            @elseif($order->order_status == 'out for delivery')
                                                <span class="badge bg-dark text-white text-uppercase">
                                                    {{ __('Out for delivery') }}
                                                </span>
                                            @elseif($order->order_status == 'delivered')
                                                <span class="badge bg-success text-white text-uppercase">
                                                    {{ __('Delivered') }}
                                                </span>
                                            @elseif($order->order_status == 'cancelled')
                                                <span class="badge bg-danger text-white text-uppercase">
                                                    {{ __('Cancelled') }}
                                                </span>
                                            @elseif($order->order_status == 'hold')
                                                <span class="badge bg-warning text-white text-uppercase">
                                                    {{ __('Hold') }}
                                                </span>
                                            @elseif($order->order_status == 'shipped')
                                                <span class="badge bg-success text-white text-uppercase">
                                                    {{ __('Shipped') }}
                                                </span>
                                            @elseif($order->order_status == 'printing process begun')
                                                <span class="badge bg-dark text-white text-uppercase">
                                                    {{ __('Printing Process Begun') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="datagrid mt-4">
                                    <div class="datagrid-item mx-3 my-1">
                                        <div class="datagrid-title">{{ __('Payment Mode') }}</div>
                                        <div class="datagrid-content fw-bold">
                                            <span class="badge bg-dark text-white text-uppercase">
                                                {{ $order->payment_method ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="datagrid-item mx-3 my-1">
                                        <div class="datagrid-title">{{ __('Delivery Partner') }}</div>
                                        <div class="datagrid-content fw-bold">
                                            {{ json_decode($order->order_details)->courier_partner ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="datagrid-item mx-3 my-1">
                                        <div class="datagrid-title">{{ __('Tracking Number') }}</div>
                                        <div class="datagrid-content fw-bold">
                                            {{ json_decode($order->order_details)->tracking_number ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="datagrid-item mx-3 my-1 d-print-none">
                                        <div class="datagrid-title">{{ __('Delivery Note') }}</div>
                                        <div class="datagrid-content fw-bold">
                                            {{ json_decode($order->order_details)->delivery_message ?? '-' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="datagrid mt-4">
                                    <div class="datagrid-item mx-3 my-1">
                                        <div class="datagrid-title">{{ __('Billing Address') }}</div>
                                        <div class="datagrid-content">
                                            @php
                                                $address = $order->delivery_address
                                                    ? json_decode($order->delivery_address)
                                                    : new stdClass();
                                                $billingName = $address->billing_name ?? '';
                                                $billingAddress = $address->billing_address ?? '';
                                                $billingCity = $address->billing_city ?? '';
                                                $billingPostcode = $address->billing_zipcode ?? '';
                                                $billingCountry = $address->billing_country ?? '';
                                                $billingPhone = $address->billing_phone ?? '';
                                                $billingEmail = $address->billing_email ?? '';
                                                $vatNumber = $address->vat_number ?? '';
                                                $type = $address->type ?? '';
                                            @endphp
                                            <span>
                                                <span class="fw-bold">{{ $billingName }}</span>
                                                <br>
                                                <a class="fw-bold"
                                                    href="http://maps.google.com/?q={{ $billingAddress }},{{ $billingCity }},{{ $billingPostcode }},{{ $billingCountry }}"
                                                    target="_blank">{{ $billingAddress }}, {{ $billingCity }},
                                                    {{ $billingPostcode }}, {{ $billingCountry }}</a>
                                                <br>
                                                {{ __('Phone Number') }} : <span class="fw-bold"><a
                                                        href="tel:0{{ $billingPhone }}">{{ $billingPhone }}</a></span>
                                                <br>
                                                {{ __('Email') }} : <span class="fw-bold"><a
                                                        href="mailto:{{ $billingEmail }}">{{ $billingEmail }}</a></span>
                                                <br>
                                                {{ __('VAT Number') }} : <span class="fw-bold">{{ $vatNumber }}
                                                </span>
                                        </div>
                                    </div>

                                    <div class="datagrid-item mx-3 my-1">
                                        <div class="datagrid-title">{{ __('Shipping Address') }}</div>
                                        <div class="datagrid-content">
                                            @php
                                                $address = $order->delivery_address
                                                    ? json_decode($order->delivery_address)
                                                    : new stdClass();
                                                $billingName = $address->billing_name ?? '';
                                                $billingAddress = $address->billing_address ?? '';
                                                $billingCity = $address->billing_city ?? '';
                                                $billingPostcode = $address->billing_zipcode ?? '';
                                                $billingCountry = $address->billing_country ?? '';
                                                $billingPhone = $address->billing_phone ?? '';
                                                $billingEmail = $address->billing_email ?? '';
                                                $vatNumber = $address->vat_number ?? '';
                                                $type = $address->type ?? '';
                                            @endphp
                                            <span>
                                                <span class="fw-bold">{{ $billingName }}</span>
                                                <br>
                                                <a class="fw-bold"
                                                    href="http://maps.google.com/?q={{ $billingAddress }},{{ $billingCity }},{{ $billingPostcode }},{{ $billingCountry }}"
                                                    target="_blank">{{ $billingAddress }}, {{ $billingCity }},
                                                    {{ $billingPostcode }}, {{ $billingCountry }}</a>
                                                <br>
                                                {{ __('Phone Number') }} : <span class="fw-bold"><a
                                                        href="tel:0{{ $billingPhone }}">{{ $billingPhone }}</a></span>
                                                <br>
                                                {{ __('Email') }} : <span class="fw-bold"><a
                                                        href="mailto:{{ $billingEmail }}">{{ $billingEmail }}</a></span>
                                                <br>
                                                {{ __('VAT Number') }} : <span class="fw-bold">{{ $vatNumber }}
                                                </span>
                                        </div>
                                    </div>

                                    <div class="datagrid-item mx-3 my-1 d-print-none">
                                        <div class="datagrid-title">{{ __('Delivery Note') }}</div>
                                        <div class="datagrid-content fw-bold">
                                            {{ $order->delivery_note ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="datagrid-item mx-3 my-1">
                                        <div class="datagrid-title">{{ __('Total') }}</div>
                                        <div class="datagrid-content fw-bold">
                                            {{ $symbol }}{{ json_decode($order->order_details)->invoice_amount }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card card-table">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Order Items') }}</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Item') }}</th>
                                            <th class="text-end">{{ __('Price') }}</th>
                                            <th class="text-end">{{ __('Quantity') }}</th>
                                            <th class="text-end">{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-bold">
                                        <tr>
                                            <td>
                                                <p class="strong mb-1">
                                                    {{ json_decode($order->order_details)->order_item }}
                                                </p>
                                                <div class="text-secondary d-none d-sm-block">{!! nl2br(e(wordwrap(json_decode($order->order_details)->order_description, 100, "\n"))) !!}</div>
                                            </td>
                                            <td class="text-end">
                                                {{ $symbol }}{{ json_decode($order->order_details)->price }}</td>
                                            <td class="text-end">{{ json_decode($order->order_details)->order_quantity }}
                                            </td>
                                            <td class="text-end">
                                                {{ $symbol }}{{ json_decode($order->order_details)->price }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="strong text-end">{{ __('Subtotal') }}</td>
                                            <td class="text-end">
                                                {{ $symbol }}{{ json_decode($order->order_details)->subtotal }}</td>
                                        </tr>
                                        @if (json_decode($order->order_details)->tax_value != null)
                                            <tr>
                                                <td colspan="3" class="strong text-end">{{ __('Tax') }} <br><small
                                                        class="text-secondary">{{ __(json_decode($order->order_details)->tax_name) }}
                                                        : {{ json_decode($order->order_details)->tax_value }}%</small></td>
                                                <td class="text-end">
                                                    {{ $symbol }}{{ json_decode($order->order_details)->tax_amount }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if (json_decode($order->order_details)->discounted_price != null)
                                            <tr>
                                                <td colspan="3" class="strong text-end">
                                                    {{ __('Before Applied Coupon') }}
                                                </td>
                                                <td class="text-end">
                                                    {{ $symbol }}{{ json_decode($order->order_details)->subtotal + json_decode($order->order_details)->tax_amount }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="strong text-end">{{ __('Coupon') }} <br><small
                                                        class="text-secondary">{{ __('Applied Coupon') }} :
                                                        {{ json_decode($order->order_details)->applied_coupon }}</small>
                                                </td>
                                                <td class="text-end">
                                                    -{{ $symbol }}{{ json_decode($order->order_details)->discounted_price }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="3" class="strong text-end">{{ __('Total') }}</td>
                                            <td class="text-end">
                                                {{ $symbol }}{{ json_decode($order->order_details)->invoice_amount }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        @include('user.includes.footer')
    </div>
@endsection
