@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <!-- Page title -->
            <div class="page-header d-print-none">
                <div class="container-fluid">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <div class="page-pretitle">
                                {{ __('Overview') }}
                            </div>
                            <h2 class="page-title">
                                {{ __('Customer Details') }}
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

                    <div class="row row-deck row-cards">
                        <div class="col-md-12 col-xl-12">
                            <div class="card">
                                <div class="card-body p-4 text-center">
                                    <span class="avatar avatar-xl mb-3"
                                        style="background-image: url({{ asset($user_details->profile_image == '' ? 'profile.png' : $user_details->profile_image) }})"></span>
                                    <h3 class="m-0 mb-1 text-center">{{ $user_details->name }}</h3>
                                    <div>
                                        {{ $user_details->email == '' ? __('Not Available') : $user_details->email }}
                                    </div>
                                    <div class="mt-3">
                                        <span
                                            class="badge bg-green text-white">{{ $user_details->role_id == 2 ? __('Customer') : '' }}</span>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <a href="mailto:{{ $user_details->email == '' ? __('Not Available') : $user_details->email }}"
                                        class="card-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <rect x="3" y="5" width="18" height="14" rx="2" />
                                            <polyline points="3 7 12 13 21 7" />
                                        </svg>
                                        {{ __('Email') }}</a>
                                    <a href="#" class="card-btn"
                                        onclick="loginUser('{{ $user_details->user_id }}'); return false;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                            <path d="M20 12h-13l3 -3m0 6l-3 -3" />
                                        </svg>
                                        {{ __('Login via Admin') }}</a>
                                </div>
                            </div>
                        </div>

                        {{-- Business Cards --}}
                        <div class="col-md-6 col-xl-6 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('Business Cards') }}</h3>
                                </div>
                                <div class="table-responsive">
                                    <table class="table card-table table-vcenter text-nowrap datatable" id="cardsTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('#') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="w-1">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($user_cards as $user_card)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ formatDateForUser($user_card->created_at) }}</td>
                                                    <td>
                                                        <a href="{{ route('profile', $user_card->card_url) }}"
                                                            target="_blank">
                                                            {{ $user_card->title }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if ($user_card->card_status == 'inactive')
                                                            <span
                                                                class="badge bg-red text-white">{{ __('Deactive') }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-green text-white">{{ __('Active') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="dropdown text-end">
                                                            <button class="btn small-btn dropdown-toggle align-text-top"
                                                                id="dropdownMenuButton" data-bs-boundary="viewport"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                {{ __('Actions') }}
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a class="dropdown-item" onclick="assignCard('{{ $user_card->card_id }}', 'vcard')">{{ __('Copy to Another Customer') }}</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Stores --}}
                        <div class="col-md-6 col-xl-6 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('Stores') }}</h3>
                                </div>
                                <div class="table-responsive">
                                    <table class="table card-table table-vcenter text-nowrap datatable" id="storesTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('#') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th class="fw-bold">{{ __('Title') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="w-1">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($user_stores as $user_store)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ formatDateForUser($user_store->created_at) }}</td>
                                                    <td>
                                                        <a href="{{ route('profile', $user_store->card_url) }}"
                                                            target="_blank">
                                                            {{ $user_store->title }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if ($user_store->card_status == 'inactive')
                                                            <span
                                                                class="badge bg-red text-white">{{ __('Deactive') }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-green text-white">{{ __('Active') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="dropdown text-end">
                                                            <button class="btn small-btn dropdown-toggle align-text-top"
                                                                id="dropdownMenuButton" data-bs-boundary="viewport"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                {{ __('Actions') }}
                                                            </button>
                                                            <div class="dropdown-menu"
                                                                aria-labelledby="dropdownMenuButton">
                                                                <a class="dropdown-item" onclick="assignCard('{{ $user_store->card_id }}', 'store')">{{ __('Copy to Another Customer') }}</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- NFC Card Orders --}}
                        <div class="col-md-12 col-xl-12 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('NFC Card Orders') }}</h3>
                                </div>
                                <div class="table-responsive">
                                    <table class="table card-table table-vcenter text-nowrap datatable" id="ordersTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('#') }}</th>
                                                <th>{{ __('Order Date') }}</th>
                                                <th>{{ __('Order ID') }}</th>
                                                <th>{{ __('Item') }}</th>
                                                <th>{{ __('Total') }}</th>
                                                <th>{{ __('Delivery Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ formatDateForUser($order->created_at) }}</td>
                                                    <td class="fw-bold">
                                                        <a href="{{ route('admin.order.show', $order->nfc_card_order_id) }}"
                                                            target="_blank">
                                                            {{ $order->nfc_card_order_id ?? '-' }}
                                                        </a>
                                                    </td>
                                                    <td class="fw-bold">
                                                        <a href="{{ route('admin.order.show', $order->nfc_card_order_id) }}"
                                                            target="_blank">
                                                            {{ json_decode($order->order_details)->order_item }}
                                                        </a>
                                                    </td>
                                                    <td class="fw-bold">
                                                        {{ $symbol }}{{ json_decode($order->order_details)->invoice_amount }}
                                                    </td>
                                                    <td class="fw-bold">
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
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Transactions (Plan) --}}
                        <div class="col-md-6 col-xl-6 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('Subscription Plan Transactions') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover"
                                            id="transactions-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('#') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Transaction') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Transactions --}}
                                                @foreach ($transactions as $transaction)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ formatDateForUser($transaction->created_at) }}</td>
                                                        <td class="fw-bold">
                                                            @if ($transaction->payment_status == 'SUCCESS')
                                                                <a href="{{ route('admin.view.invoice', $transaction->transaction_id) }}"
                                                                    target="_blank">
                                                                    {{ $transaction->transaction_id ?? '-' }}
                                                                </a>
                                                            @else
                                                                {{ $transaction->transaction_id ?? '-' }}
                                                            @endif
                                                        </td>
                                                        <td class="fw-bold">
                                                            {{ $symbol }}{{ $transaction->transaction_amount }}
                                                        </td>
                                                        <td>
                                                            @if ($transaction->payment_status == 'SUCCESS')
                                                                <span class="badge bg-success text-white text-uppercase">
                                                                    {{ __('Success') }}
                                                                </span>
                                                            @elseif($transaction->payment_status == 'FAILED')
                                                                <span class="badge bg-danger text-white text-uppercase">
                                                                    {{ __('Failed') }}
                                                                </span>
                                                            @elseif($transaction->payment_status == 'PENDING')
                                                                <span class="badge bg-warning text-white text-uppercase">
                                                                    {{ __('Pending') }}
                                                                </span>
                                                            @elseif($transaction->payment_status == 'CANCELLED')
                                                                <span class="badge bg-danger text-white text-uppercase">
                                                                    {{ __('Cancelled') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Transactions (NFC Card Orders) --}}
                        <div class="col-md-6 col-xl-6 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('NFC Card Order Transactions') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover"
                                            id="nfc-transactions-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('#') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Transaction') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Transactions --}}
                                                @foreach ($nfc_transactions as $transaction)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ formatDateForUser($transaction->created_at) }}</td>
                                                        <td class="fw-bold">
                                                            @if ($transaction->payment_status == 'success')
                                                                <a href="{{ route('admin.transaction.show', $transaction->nfc_card_order_transaction_id) }}"
                                                                    target="_blank">
                                                                    {{ $transaction->nfc_card_order_transaction_id ?? '-' }}
                                                                </a>
                                                            @else
                                                                {{ $transaction->nfc_card_order_transaction_id ?? '-' }}
                                                            @endif

                                                        </td>
                                                        <td class="fw-bold">
                                                            {{ $symbol }}{{ $transaction->amount }}
                                                        </td>
                                                        <td>
                                                            @if ($transaction->payment_status == 'success')
                                                                <span class="badge bg-success text-white text-uppercase">
                                                                    {{ __('Success') }}
                                                                </span>
                                                            @elseif($transaction->payment_status == 'failed')
                                                                <span class="badge bg-danger text-white text-uppercase">
                                                                    {{ __('Failed') }}
                                                                </span>
                                                            @elseif($transaction->payment_status == 'pending')
                                                                <span class="badge bg-warning text-white text-uppercase">
                                                                    {{ __('Pending') }}
                                                                </span>
                                                            @elseif($transaction->payment_status == 'cancelled')
                                                                <span class="badge bg-danger text-white text-uppercase">
                                                                    {{ __('Cancelled') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            @include('admin.includes.footer')
        </div>

        {{-- Login Modal --}}
        <div class="modal modal-blur fade" id="login-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                <div class="modal-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-user-pentagon mb-2 text-danger icon-lg">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M13.163 2.168l8.021 5.828c.694 .504 .984 1.397 .719 2.212l-3.064 9.43a1.978 1.978 0 0 1 -1.881 1.367h-9.916a1.978 1.978 0 0 1 -1.881 -1.367l-3.064 -9.43a1.978 1.978 0 0 1 .719 -2.212l8.021 -5.828a1.978 1.978 0 0 1 2.326 0z" />
                            <path d="M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z" />
                            <path d="M6 20.703v-.703a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.707" />
                        </svg>
                        <h3>{{ __('Are you sure login into the user?') }}</h3>
                        <div class="text-muted">{{ __('Note : If you proceed, you will lose your admin session.') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                        {{ __('Cancel') }}
                                    </button>
                                </div>
                                <div class="col">
                                    <a href="{{ route('admin.login-as.customer', $user_details->user_id) }}"
                                        target="_blank" class="btn btn-danger w-100">{{ __('Yes, proceed') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Copy card to another customer modal --}}
        <div class="modal modal-blur fade" id="assign-card-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">{{ __('Copy') }}</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.assign.card') }}" method="post">
                            @csrf
                            {{-- Card ID --}}
                            <input type="hidden" name="card_id" id="card_id" value="">

                            {{-- Type --}}
                            <input type="hidden" name="type" id="type" value="">

                            <div class="col-md-12 col-xl-12">
                                <div class="mb-3">
                                    {{-- Customers --}}
                                    <label class="form-label required">{{ __('Customers') }}</label>
                                    <select name="customer_id" id="customer_id" class="form-control" required>
                                        <option value='' disabled selected>{{ __('Choose a customer') }}</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->user_id }}">
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Copy') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Custom JS scripts --}}
    @section('scripts')
        <script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                "use strict";

                // DataTable configuration
                let tableConfig = {
                    language: {
                        "sProcessing": `{{ __("Processing...") }}`,
                        "sLengthMenu": `{{ __("Show _MENU_ entries") }}`,
                        "sSearch": `{{ __("Search:") }}`,
                        "oPaginate": {
                            "sNext": `{{ __("Next") }}`,
                            "sPrevious": `{{ __("Previous") }}`
                        },
                        "sInfo": `{{ __("Showing _START_ to _END_ of _TOTAL_ entries") }}`,
                        "sInfoEmpty": `{{ __("Showing 0 to 0 of 0 entries") }}`,
                        "sInfoFiltered": `{{ __("(filtered from _MAX_ total entries)") }}`,
                        "sInfoPostFix": "",
                        "sUrl": "",
                        "oAria": {
                            "sSortAscending": `{{ __(": activate to sort column in ascending order") }}`,
                            "sSortDescending": `{{ __(": activate to sort column in descending order") }}`
                        },
                        loadingRecords: `{{ __("Please wait - loading...") }}`,
                        emptyTable: `{{ __("No data available in the table") }}` // Message for an empty table
                    },
                    "pageLength": 10,
                    "lengthMenu": [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    "columnDefs": [{
                        "orderable": false,
                        "targets": 0
                    }]
                };

                // Initialize DataTables
                $('#cardsTable, #storesTable, #ordersTable, #transactions-table, #nfc-transactions-table').DataTable(tableConfig);
            });

            // Copy card to another customer
            function assignCard(cardId, type) {
                "use strict";
                // Show modal
                $("#assign-card-modal").modal("show");

                // Copy card ID in text field
                var cardIdInput = document.getElementById("card_id");
                cardIdInput.value = cardId;

                // Copy type in hidden field
                var typeInput = document.getElementById("type");
                typeInput.value = type;
            }

            // Array of element IDs
            var elementSelectors = ['customer_id'];
            
            // Function to initialize TomSelect and enforce the "required" attribute
            function initializeTomSelectWithRequired(el) {
                new TomSelect(el, {
                    copyClassesToDropdown: false,
                    dropdownClass: 'dropdown-menu ts-dropdown',
                    optionClass: 'dropdown-item',
                    controlInput: '<input>',
                    maxOptions: null,
                    render: {
                        item: function(data, escape) {
                            if (data.customProperties) {
                                return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                            }
                            return '<div>' + escape(data.text) + '</div>';
                        },
                        option: function(data, escape) {
                            if (data.customProperties) {
                                return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                            }
                            return '<div>' + escape(data.text) + '</div>';
                        },
                    },
                });
            
                // Ensure the "required" attribute is enforced
                el.addEventListener('change', function() {
                    if (el.value) {
                        el.setCustomValidity('');
                    } else {
                        el.setCustomValidity('This field is required');
                    }
                });
            
                // Trigger validation on load
                el.dispatchEvent(new Event('change'));
            }
            
            // Loop through each element ID
            elementSelectors.forEach(function(id) {
                // Check if the element exists
                var el = document.getElementById(id);
                if (el) {
                    // Apply TomSelect and enforce the "required" attribute
                    initializeTomSelectWithRequired(el);
                }
            });
        </script> 
    @endsection
@endsection
