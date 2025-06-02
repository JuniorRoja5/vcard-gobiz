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
                            {{ __('Activate NFC Card') }}
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
                            <form action="{{ route('user.activated.nfc.card') }}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Get activation code from the URL (?id=GpNbiXTKjQqJ2K3PhiV21nWdD) --}}
                                        @php
                                            $activationCode = request()->get('id', ''); // Default to empty string if not set
                                        @endphp

                                        {{-- NFC Card Activation Code --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('NFC Card Activation Code') }}</label>
                                                <input type="text" class="form-control" id="activationCode"
                                                    name="activationCode" placeholder="{{ __('NFC Card Activation Code') }}"
                                                    value="{{ $activationCode }}"
                                                    required>
                                            </div>
                                        </div>

                                        {{-- Business cards and stores in select option --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Business Cards & Stores') }}</div>
                                                <select class="form-control businessCard" id="businessCard" name="businessCard" required>
                                                @if (count($businessCards) > 0)
                                                    <option value="" selected disabled>{{ __('Select a business card or store') }}</option>
                                                    @foreach ($businessCards as $businessCard)
                                                        <option value="{{ $businessCard->card_id }}">
                                                            {{ $businessCard->title }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="" selected disabled>
                                                        {{ __('No business cards found') }}</option>
                                                @endif
                                            </select>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Activate') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('user.includes.footer')
    </div>

    {{-- Custom scripts --}}
    @section('scripts')
    <script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
    <script>
        // Array of element selectors
        var elementSelectors = ['.businessCard'];

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
