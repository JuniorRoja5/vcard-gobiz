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
                            {{ __('Cron Jobs') }}
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
                        <form action="{{ route('admin.update.cron.jobs') }}" method="post" class="card">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Cron Jobs') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            {{-- Dates --}}
                                            <div class="col-md-6 col-xl-6">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Dates') }}</div>
                                                    <input type="text" class="form-control" name="dates_in_array"
                                                        value="{{ old('dates_in_array') ?? $config[60]->config_value }}"
                                                        placeholder="{{ __('Dates') }}" autocomplete="dates_in_array"
                                                        autofocus required>
                                                    {{-- Notes --}}
                                                    <div class="form-text">
                                                        {{ __('Dates format: -30,1,3,5,10,366') }}
                                                        <br>
                                                        {{ __('Min: -30') }}
                                                        <br>
                                                        {{ __('Max: 366') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <div class="d-flex">
                                    {{-- Test Reminder --}}
                                    <a href="{{ route('admin.test.reminder') }}"
                                        class="btn btn-secondary btn-sm">{{ __('Test Reminder') }}</a>
                                    <button type="submit"
                                        class="btn btn-primary btn-sm ms-auto">{{ __('Update') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Set Cronjob time --}}
                    <div class="col-sm-12 col-lg-12">
                        <form action="{{ route('admin.set.cronjob.time') }}" method="post" class="card">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Set Cronjob time') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            {{-- 24 hours --}}
                                            <div class="col-md-6 col-xl-6">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Hour') }}</div>
                                                    <!-- Hour -->
                                                    <select id="cron-hour" name="cron_hour" class="form-control"
                                                        onchange="generateCronCommand()" required>
                                                        @for ($i = 0; $i < 24; $i++)
                                                            <option value="{{ $i }}"
                                                                {{ $i == $config[61]->config_value ? 'selected' : '' }}>
                                                                {{ $i <= 9 ? 0 : '' }}{{ $i }}:00</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-5 col-xl-5">
                                                <div class="mb-3">
                                                    {{-- Copy to clipboard --}}
                                                    <div class="form-label required">{{ __('Your CRON Command:') }}</div>
                                                    <input type="text" id="cron-command" class="form-control" value="" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-xl-1">
                                                <div class="mt-5">
                                                    <button class="btn btn-primary btn-icon" type="button" id="copy-button" onclick="copyToClipboard()">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-copy">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                                                            <path
                                                                d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" />
                                                        </svg>
                                                    </button>
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
    {{-- Tom Select --}}
    <script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
    {{-- Clipboard --}}
    <script src="{{ asset('js/clipboard.min.js') }}"></script>
    <script>
        // Call generateCronCommand function to page onload
        generateCronCommand();
        
        // Generate cronjob command
        function generateCronCommand() {
            "use strict";

            const hour = document.getElementById('cron-hour').value;
            const projectPath = "{{ str_replace('\\', '/', base_path()) }}";
            const cronCommand = `0 ${hour} * * * php ${projectPath}/artisan schedule:run >> /dev/null 2>&1`;
            document.getElementById('cron-command').value = cronCommand;
        }

        // Copy to clipboard
        function copyToClipboard() {
            "use strict";
            var copyText = document.getElementById("cron-command");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");

            // Change the button text to "Copied!"
            document.getElementById("copy-button").innerHTML =
                `<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-clipboard-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 14l2 2l4 -4" /></svg>`;
            // Change the button color to green
            document.getElementById("copy-button").style.backgroundColor = "#4CAF50";

            // Remove the "Copied!" text after 2 seconds
            setTimeout(function() {
                document.getElementById("copy-button").innerHTML =
                    `<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>`;
                // Change the button color back to original (add class)
                document.getElementById("copy-button").style.backgroundColor = "#066fd1";
            }, 2000);
        }
    </script>
    <script>
        // Array of element IDs
        var elementSelectors = ['cron-hour'];

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
