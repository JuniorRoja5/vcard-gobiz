@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

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
                            {{ __('Update Status & Tracking Details') }}
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

                <div class="row mt-3">
                    <div class="col-sm-12 col-md-12">
                        <div class="card">
                            <form action="{{ route('admin.updated.order') }}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="row">
                                                {{-- Order ID --}}
                                                <input type="hidden" class="form-control" name="order_id" id="order_id" value="{{ $order->nfc_card_order_id }}">

                                                {{-- Delivery Status --}}
                                                <div class="col-md-6 col-xl-4">
                                                    <div class="mb-3">
                                                        <div class="form-label required">{{ __('Delivery Status') }}</div>
                                                        <select class="form-control status" name="status" id="status" required>
                                                            <option selected disabled value="">{{ __('Choose a delivery status') }}</option>
                                                            <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                            <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                                                            <option value="hold" {{ $order->order_status == 'hold' ? 'selected' : '' }}>{{ __('Hold') }}</option>
                                                            <option value="printing process begun" {{ $order->order_status == 'printing process begun' ? 'selected' : '' }}>{{ __('Printing Process Begun') }}</option>
                                                            <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                                                            <option value="out for delivery" {{ $order->order_status == 'out for delivery' ? 'selected' : '' }}>{{ __('Out for delivery') }}</option>
                                                            <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                                            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Tracking Number --}}
                                                <div class="col-md-6 col-xl-4">
                                                    <div class="mb-3">
                                                        <div class="form-label">{{ __('Tracking Number') }}</div>
                                                        <input type="text" class="form-control" name="tracking_number" id="tracking_number" value="{{ isset(json_decode($order->order_details)->tracking_number) ? json_decode($order->order_details)->tracking_number : '' }}">
                                                    </div>
                                                </div>

                                                {{-- Courier Partner --}}
                                                <div class="col-md-6 col-xl-4">
                                                    <div class="mb-3">
                                                        <div class="form-label">{{ __('Courier Partner') }}</div>
                                                        <input type="text" class="form-control" name="courier_partner" id="courier_partner" value="{{ isset(json_decode($order->order_details)->courier_partner) ? json_decode($order->order_details)->courier_partner : '' }}">
                                                    </div>
                                                </div>

                                                {{-- Delivery Message --}}
                                                <div class="col-md-6 col-xl-4">
                                                    <div class="mb-3">
                                                        <div class="form-label">{{ __('Delivery Message') }}</div>
                                                        <textarea class="form-control" name="delivery_message" id="delivery_message" rows="3">{{ isset(json_decode($order->order_details)->delivery_message) ? json_decode($order->order_details)->delivery_message : '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary" type="submit">
                                        {{ __('Update') }}
                                    </button>
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
    <script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
    <script>
        "use strict";

        // Array of element selectors
        var elementSelectors = ['.status'];
    
        // Function to initialize TomSelect on an element
        function initializeTomSelect(el) {
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
        }
    
        // Initialize TomSelect on existing elements
        elementSelectors.forEach(function(selector) {
            var elements = document.querySelectorAll(selector);
            elements.forEach(function(el) {
                initializeTomSelect(el);
            });
        });
    
        // Observe the document for dynamically added elements
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Ensure it's an element node
                        elementSelectors.forEach(function(selector) {
                            if (node.matches(selector)) {
                                initializeTomSelect(node);
                            }
                            // Also check if new nodes have children that match
                            var childElements = node.querySelectorAll(selector);
                            childElements.forEach(function(childEl) {
                                initializeTomSelect(childEl);
                            });
                        });
                    }
                });
            });
        });
    
        // Configure the observer
        observer.observe(document.body, { childList: true, subtree: true });
    </script>
    @endsection
@endsection
