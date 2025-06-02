@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

{{-- Custom CSS --}}
@section('css')
    <script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
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
                            {{ __('Pusher Notification Configuration') }}
                        </h2>
                    </div>
                    <span class="mt-3">{{ __('How to configure Pusher notifications?') }} {!! __('<a href="https://docs.nativecode.in/gobiz/how-to-get-pusher-beams-instance-id-and-secret-key" target="_blank">Click here</a>') !!}</span>
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
                        <form action="{{ route('admin.marketing.pusher.update') }}" method="post" class="card"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Pusher Configuration') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            {{-- Enable Beams --}}
                                            <div class="col-md-6 col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Enable Beams') }}</div>
                                                    <select class="form-control" name="enable_beams" id="enable_beams"
                                                        required>
                                                        <option value="1"
                                                            {{ env('PUSHER_BEAMS_ENABLED') == '1' ? 'selected' : '' }}>
                                                            {{ __('Yes') }}</option>
                                                        <option value="0"
                                                            {{ env('PUSHER_BEAMS_ENABLED') == '0' ? 'selected' : '' }}>
                                                            {{ __('No') }}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Beams Instance ID --}}
                                            <div class="col-md-6 col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Beams Instance ID') }}</div>
                                                    <input type="text" class="form-control" name="beams_instance_id"
                                                        value="{{ env('PUSHER_BEAMS_INSTANCE_ID') }}"
                                                        placeholder="{{ __('Beams Instance ID') }}" required>
                                                </div>
                                            </div>

                                            {{-- Beams Secret Key --}}
                                            <div class="col-md-6 col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Beams Secret Key') }}</div>
                                                    <input type="text" class="form-control" name="beams_secret_key"
                                                        value="{{ env('PUSHER_BEAMS_SECRET_KEY') }}"
                                                        placeholder="{{ __('Beams Secret Key') }}" required>
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
        {{-- @include('admin.includes.footer') --}}
    </div>

    {{-- Custom JS --}}
@section('scripts')
    {{-- TomSelect --}}
    <script>
        // Array of element IDs
        var elementSelectors = ['enable_beams'];

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
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties +
                                '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties +
                                '</span>' + escape(data.text) + '</div>';
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
