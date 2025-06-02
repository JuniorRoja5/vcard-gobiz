@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

{{-- Custom CSS --}}
@section('css')
<style>
    .ts-control>input {
        display: contents !important;
    }
    .discount {
        border-radius: 8px 0 0 8px !important;
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
                        {{ __('Update coupon') }}
                    </h2>
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
            
            <div class="row row-deck row-cards">
                <div class="col-sm-12 col-lg-12">
                    <form action="{{ route('admin.update.coupon', $couponDetails->coupon_id) }}" method="post" class="card">
                        @csrf
                        <div class="card-header">
                            <h4 class="page-title">{{ __('Coupon Details') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="row">
                                        {{-- Used for --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Used for') }}</div>
                                                <select class="form-control used_for" name="used_for" required>
                                                    <option value="plan" {{ $couponDetails->used_for == 'plan' ? 'selected' : '' }}>{{ __('Plan') }}</option>
                                                    <option value="nfc" {{ $couponDetails->used_for == 'nfc' ? 'selected' : '' }}>{{ __('NFC Card') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Coupon Code --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Coupon Code') }}</div>
                                                <input type="text" class="form-control text-uppercase" name="code" value="{{ old('code') ?? $couponDetails->coupon_code }}" placeholder="{{ __('Coupon Code') }}" autocomplete="code" autofocus required>
                                            </div>
                                        </div>

                                        {{-- Coupon description --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label">{{ __('Description') }}</div>
                                                <input type="text" class="form-control text-capitalize" name="description" value="{{ old('description') ?? $couponDetails->coupon_desc }}" placeholder="{{ __('Description') }}" autocomplete="description" autofocus>
                                            </div>
                                        </div>

                                        {{-- Coupon type --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Type') }}</div>
                                                <select class="form-select type" name="type" onchange="couponType(this)" required>
                                                    <option value="fixed" {{ $couponDetails->coupon_type == 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
                                                    <option value="percentage" {{ $couponDetails->coupon_type == 'percentage' ? 'selected' : '' }}>{{ __('Percentage') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Coupon discount --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Discount') }}</div>
                                                <div class="input-group">
                                                    <input type="number" class="form-control discount" name="discount" id="discount" value="{{ old('discount') ?? $couponDetails->coupon_amount }}" min="0" step="0.001" max="{{ $couponDetails->coupon_type == 'fixed' ? '' : '99' }}" placeholder="{{ __('Discount') }}" autocomplete="discount" autofocus required>
                                                    <span class="input-group-text fw-bold" id="discount-addon">{{ $config[1]->config_value }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Coupon validity --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Validity') }}</div>
                                                <input type="date" class="form-control" name="validity" value="{{ old('validity') ?? \Carbon\Carbon::parse($couponDetails->coupon_expired_on)->format('Y-m-d') }}" placeholder="{{ __('Validity') }}" autocomplete="validity" autofocus required>
                                            </div>
                                        </div>

                                        {{-- Coupon user limit --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('User Limit') }}</div>
                                                <input type="number" class="form-control" name="user_limit" value="{{ old('user_limit') ?? $couponDetails->coupon_user_usage_limit }}" min="1" step="1" placeholder="{{ __('User Limit') }}" autocomplete="user_limit" autofocus required>
                                            </div>
                                        </div>

                                        {{-- Coupon total limit --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Total Limit') }}</div>
                                                <input type="number" class="form-control" name="total_limit" value="{{ old('total_limit') ?? $couponDetails->coupon_total_usage_limit }}" min="1" step="1" placeholder="{{ __('Total Limit') }}" autocomplete="total_limit" autofocus required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
    @include('admin.includes.footer')
</div>

{{-- Custom JS --}}
@section('scripts')
<script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
<script>
    // Array of element selectors
    var elementSelectors = ['.type', '.used_for'];

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
<script>
    // Get coupon type
    function couponType(type) {
        "use strict";
        var discount = type.value;
        switch (discount) { 
            case 'fixed': 
                // Remove discount inputs "required" attributes
                $("#discount-addon").html(`{{ $config[1]->config_value }}`);
                // Remove max value for discount
                $("#discount").attr("max", "");
                break;
            case 'percentage': 
                // Add discount inputs "required" attributes
                $("#discount-addon").html("%");
                // Set max value for discount
                $("#discount").attr("max", 100);
                break;
        }
    }
</script>
@endsection
@endsection