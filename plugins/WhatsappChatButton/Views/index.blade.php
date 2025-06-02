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
                        <h2 class="page-title mb-2">
                            {{ __('Whatsapp Chat Button Settings') }}
                        </h2>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="{{ route('admin.plugins') }}"
                                class="btn btn-primary text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l14 0" />
                                    <path d="M5 12l6 6" />
                                    <path d="M5 12l6 -6" />
                                </svg>
                                {{ __('Back') }}
                            </a>
                        </div>
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
                    <div class="col-sm-12 col-lg-12">
                        <form action="{{ route('admin.whatsapp_chat_button_settings.update') }}" method="post" class="card">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Whatsapp Chat Button Credentials') }}</h4>
                            </div>
                            <div class="card-body">
                                {{-- Whatapp Chatbot --}}
                                <div class="row">
                                    <div class="col-xl-4 col-12">
                                        <div class="mb-3">
                                            <label class="form-label required"
                                                for="show_whatsapp_chatbot">{{ __('Want to display whatsapp chat button on website?') }}</label>
                                            <select name="show_whatsapp_chatbot" id="show_whatsapp_chatbot"
                                                class="form-select show_whatsapp_chatbot" required>
                                                <option value="1"
                                                    {{ $whatsapp_settings[40]->config_value == '1' ? 'selected' : '' }}>
                                                    {{ __('Yes') }}</option>
                                                <option value="0"
                                                    {{ $whatsapp_settings[40]->config_value == '0' ? 'selected' : '' }}>
                                                    {{ __('No') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- WhatsApp Number --}}
                                    <div class="col-xl-4 col-12">
                                        <div class="mb-3">
                                            <label class="form-label required">{{ __('WhatsApp Number') }}</label>
                                            <input type="tel" class="form-control reduce-control"
                                                name="whatsapp_chatbot_mobile_number"
                                                value="{{ $whatsapp_settings[41]->config_value }}"
                                                placeholder="{{ __('WhatsApp Number') }}"
                                                oninput="javascript: if (this.value.length > 20) this.value = this.value.slice(0, 20); this.value = this.value.replace(/[^0-9]/g, '');">
                                            <small>{{ __('With Country code (without +)') }}</small>
                                        </div>
                                    </div>

                                    {{-- Initial Chat Message --}}
                                    <div class="col-xl-4 col-12">
                                        <div class="mb-3">
                                            <label class="form-label required">{{ __('Initial Chat Message') }}</label>
                                            <textarea class="form-control" name="whatsapp_chatbot_message" id="whatsapp_chatbot_message" cols="30"
                                                rows="2" placeholder="{{ __('Initial Chat Message') }}" required>{{ $whatsapp_settings[42]->config_value }}</textarea>
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

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>
@section('scripts')
<script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
<script>
    // Array of element selectors
    var elementSelectors = ['.show_whatsapp_chatbot'];

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
