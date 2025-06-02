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
                            {{ __('Create Group') }}
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
                    <div class="col-sm-12 col-lg-12">
                        <form action="{{ route('admin.marketing.groups.save') }}" method="post" class="card">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Group Details') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Group Name --}}
                                    <div class="col-md-6 col-xl-6">
                                        <div class="mb-3">
                                            <div class="form-label required">{{ __('Group Name') }}</div>
                                            <input type="text" class="form-control" id="group_name" name="group_name"
                                                placeholder="{{ __('Group Name') }}" required>
                                        </div>
                                    </div>


                                    {{-- Group Description --}}
                                    <div class="col-md-6 col-xl-6">
                                        <div class="mb-3">
                                            <div class="form-label required">{{ __('Group Description') }}</div>
                                            <input type="text" class="form-control" id="group_desc" name="group_desc"
                                                placeholder="{{ __('Group Description') }}" required>
                                        </div>
                                    </div>

                                    {{-- Emails in tomselect --}}
                                    @if (count($marketingCustomers) > 0)
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <div class="form-label required">{{ __('Emails') }}</div>
                                                <select class="form-control" id="emails" name="emails[]" multiple>
                                                    <option value="all" id="select-all-option">
                                                        {{ __('Select all customers') }}</option>
                                                    @foreach ($marketingCustomers as $customer)
                                                        <option value="{{ $customer->email }}">{{ $customer->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small>{{ __('Select the customers who will be part of this group.') }}</small>
                                            </div>
                                        </div>
                                    @else
                                    <div class="col-md-6 col-xl-6">
                                        <div class="mb-3">
                                            {{-- Heading --}}
                                            <div class="form-label">{{ __('Emails') }}</div>
                                            <div class="form-text fw-bold">{{ __('No customers found') }}</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary {{ count($marketingCustomers) > 0 ? '' : 'disabled' }}">{{ __('Save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('admin.includes.footer')
    </div>
@endsection

{{-- Custom JS --}}
@section('scripts')
    <script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
    <script>
        // Array of element IDs
        var elementSelectors = ['emails'];

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
    {{-- Select all option --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            "use strict";
            const emailsSelect = document.getElementById("emails");
            const selectAllOption = document.getElementById("select-all-option");

            emailsSelect.addEventListener("change", function() {
                const selectedOptions = Array.from(emailsSelect.selectedOptions);
                const allSelected = selectedOptions.some(option => option.value === "all");

                if (allSelected) {
                    // If "All" is selected, select all other options
                    Array.from(emailsSelect.options).forEach(option => option.selected = true);
                } else {
                    // If "All" is unselected, deselect all options
                    const allOptionSelected = Array.from(selectedOptions).some(option => option.value !==
                        "all");
                    selectAllOption.selected = !allOptionSelected;
                }
            });
        });
    </script>
@endsection
