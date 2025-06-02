@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

{{-- Custom CSS --}}
@section('css')
<style>
    .ts-control>input {
        display: contents !important;
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
                        {{ __('Update User') }}
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
                    <form action="{{ route('admin.update.user') }}" method="post" class="card">
                        @csrf
                        <div class="card-header">
                            <h4 class="page-title">{{ __('User Details') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="row">
                                        <input type="hidden" class="form-control" name="user_id" value="{{ $user_details->user_id }}">
                                        {{-- Role --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Role') }}</div>
                                                <select class="form-select role" name="role" required>
                                                    <option value="">{{ __('Choose a role') }}</option>
                                                    <option value="3" {{ $user_details->role_id == 3 ? "selected" : "" }}>{{ __('Administrator') }}</option>
                                                    <option value="4" {{ $user_details->role_id == 4 ? "selected" : "" }}>{{ __('Manager') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        {{-- Name --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Name') }}</label>
                                                <input type="text" class="form-control" name="name" value="{{ $user_details->name }}"
                                                    placeholder="{{ __('Name') }}" required>
                                            </div>
                                        </div>
                                        {{-- Email --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Email') }}</label>
                                                <input type="email" class="form-control" name="email"
                                                    placeholder="{{ __('Email') }}" value="{{ $user_details->email }}" required>

                                            </div>
                                        </div>
                                        {{-- Password --}}
                                        <div class="col-md-6 col-xl-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Password') }}</label>
                                                <input type="password" class="form-control" name="password" placeholder="{{ __('Password') }}">
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            {{-- Permissions --}}
                                            <h2 class="page-title my-3">{{ __('Permissions') }}</h2>
                                        
                                            {{-- Permission Options --}}
                                            <div class="row g-2">
                                                {{-- Add additional permissions dynamically --}}
                                                @php
                                                    $permissions = [
                                                        'themes' => __('Themes'),
                                                        'plans' => __('Plans'),
                                                        'customers' => __('Customers'),
                                                        'payment_methods' => __('Payment Methods'),
                                                        'coupons' => __('Coupons'),
                                                        'transactions' => __('Transactions'),
                                                        'pages' => __('Pages'),
                                                        'blogs' => __('Blogs'),
                                                        'users' => __('Users'),
                                                        'custom_domain' => __('Custom Domains'),
                                                        'backup' => __('Backup'),
                                                        'general_settings' => __('General Settings'),
                                                        'translations' => __('Translations'),
                                                        'marketing' => __('Marketing'),
                                                        'sitemap' => __('Generate Sitemap'),
                                                        'invoice_tax' => __('Invoice & Tax'),
                                                        'maintenance_mode' => __('Maintenance Mode'),
                                                        'demo_mode' => __('Demo Mode'),
                                                        'software_update' => __('Software Update'),
                                                        'nfc_card_design' => __('NFC Card Design'),
                                                        'nfc_card_orders' => __('NFC Card Orders'),
                                                        'nfc_card_order_transactions' => __('NFC Card Order Transactions'),
                                                        'nfc_card_key_generations' => __('NFC Card Key Generations'),
                                                        'referral_system' => __('Referral System'),
                                                        'email_templates' => __('Email Templates'),
                                                        'plugins' => __('Plugins'),
                                                    ];
                                                
                                                    // Decode user permissions safely
                                                    $userPermissions = json_decode($user_details->permissions, true) ?? [];
                                                @endphp
                                                
                                                @foreach ($permissions as $key => $label)
                                                    <div class="col-md-6 col-xl-2">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="{{ $key }}" name="{{ $key }}" 
                                                                {{ isset($userPermissions[$key]) && $userPermissions[$key] === 1 ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach    
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
    var elementSelectors = ['.role', '.themes', '.plans', '.customers', '.payment_methods', '.coupons', '.transactions', '.pages', '.blogs', '.users', '.custom_domain', '.general_settings', '.translations', '.marketing', '.sitemap', '.invoice_tax', '.software_update', '.maintenance_mode', '.demo_mode'];

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