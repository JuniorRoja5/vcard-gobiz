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
                            {{ __('Link Card to NFC Card Key') }}
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

                <div class="row row-deck row-cards">
                    <div class="col-sm-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Link Card to NFC Card Key') }}</h3>
                            </div>
                            <form action="{{ route('admin.update.link.key') }}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="row">
                                                {{-- NFC Card Key ID --}}
                                                <input type="hidden" class="form-control" name="key_id" id="key_id"
                                                    value="{{ $nfcCardKey->nfc_card_key_id }}">

                                                {{-- Customers --}}
                                                <div class="col-md-6 col-xl-6">
                                                    <div class="mb-3">
                                                        <div class="form-label required">{{ __('Select a customer') }}</div>
                                                        <select class="form-control customer" name="customer" id="customer" onchange="getCustomerCards(this.value)" required>
                                                            <option value="" selected disabled>{{ __('Select a customer') }}</option>
                                                            @foreach ($customers as $customer)
                                                                <option value="{{ $customer->user_id }}">{{ $customer->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Cards --}}
                                                <div class="col-md-6 col-xl-6">
                                                    <div class="mb-3">
                                                        <div class="form-label required">{{ __('Select a card') }}</div>
                                                        <select class="form-control cards" name="card_id" id="card_id" required>
                                                            <option value="" selected disabled>{{ __('Select a card') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary" type="submit">
                                        {{ __('Link') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('admin.includes.footer')
    </div>

    {{-- Custom JS --}}
@section('scripts')
    <script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
    <script>
        // Array of element selectors
        var elementSelectors = ['customer'];

        // Function to initialize TomSelect and enforce the "required" attribute
        function initializeTomSelectWithRequired(el) {
            "use strict";

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
            "use strict";

            // Check if the element exists
            var el = document.getElementById(id);
            if (el) {
                // Apply TomSelect and enforce the "required" attribute
                initializeTomSelectWithRequired(el);
            }
        });

        // Get customer cards
        function getCustomerCards(customerId) {
            "use strict";
            
            // Get the customer ID
            var customerId = document.getElementById("customer").value;

            // CSRF token
            var token = document.querySelector('meta[name="csrf-token"]').content;

            // Ajax request to get the customer cards
            $.ajax({
                url: "{{ route('admin.get.customer.cards') }}",
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: "POST",
                data: {
                    customerId: customerId
                },
                success: function(response) {
                    // Update the cards dropdown
                    $(".cards").empty();
                    // Check response count
                    if (response.length == 0) {
                        // No cards found
                        $(".cards").append('<option value="" selected disabled>No cards found</option>');
                    } else {
                        // Cards found
                        $(".cards").append('<option value="" selected disabled>Select a card</option>');
                        // Loop through each card
                        response.forEach(function(card) {
                            $(".cards").append('<option value="' + card.card_id + '">' + card.title + '</option>');
                        });

                        // Set tom select options
                        initializeTomSelectWithRequired(document.getElementById("card_id"));
                    }
                }
            });
        }
    </script>
@endsection
@endsection
