@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

{{-- Custom CSS --}}
@section('css')
<script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
@endsection

@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <!-- Page title -->
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                            {{ __('Overview') }}
                        </div>
                        <h2 class="page-title">
                            {{ __('Referral System Configurations') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page body -->
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
                        <div class="card">
                            <form action="{{ route('admin.update.referral.system.configuration') }}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Referral Commission Type --}}
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-group">
                                                <div class="form-label required">{{ __('Type') }}</div>
                                                <div class="input-group mb-3">
                                                    <select class="form-control" name="referral_commission_type" id="referral_commission_type" required>
                                                        <option value="" selected disabled>{{ __('Select Type') }}</option>
                                                        <option value="0" {{ old('referral_commission_type', $config[81]->config_value) == '0' ? 'selected' : '' }}>{{ __('Percentage') }}</option>
                                                        <option value="1" {{ old('referral_commission_type', $config[81]->config_value) == '1' ? 'selected' : '' }}>{{ __('Fixed Amount') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Referral Commission --}}
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-group">
                                                <div class="form-label required">{{ __('Commission') }}</div>
                                                <div class="input-group mb-3">
                                                    <input type="number" class="form-control"
                                                        name="referral_commission_amount"
                                                        value="{{ old('referral_commission_amount', $config[82]->config_value) }}" min="0" step="0.01" required>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Minimum Withdrawal Amount --}}
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-group">
                                                <div class="form-label required">{{ __('Minimum Withdrawal') }}</div>
                                                <div class="input-group mb-3">
                                                    <input type="number" class="form-control"
                                                        name="minimum_withdrawal_amount"
                                                        value="{{ old('minimum_withdrawal_amount', $config[83]->config_value) }}" min="0" step="0.01" required>
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
        </div>

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>

    {{-- Custom scripts --}}
    @section('scripts')
    <script>
        // Array of element IDs
        var elementSelectors = ['referral_commission_type'];
        
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
