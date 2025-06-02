@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true, 'settings' => $settings])

@section('content')
    <div class="page-wrapper">
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

                <div class="card">
                    <div class="row g-0">
                        <div class="col-12 col-md-3 border-end">
                            <div class="card-body">
                                <h4 class="subheader">{{ __('Update Business Card') }}</h4>
                                <div class="list-group list-group-transparent">
                                    {{-- Nav links --}}
                                    @include('user.pages.edit-cards.includes.nav-link', [
                                        'link' => 'hours',
                                    ])
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-9 d-flex flex-column">
                            <form action="{{ route('user.update.business.hours', Request::segment(3)) }}" method="post"
                                enctype="multipart/form-data" id="myForm">
                                @csrf
                                {{-- Business hours --}}
                                <div class="card-body">
                                    <h3 class="card-title mb-4">{{ __('Business Hours') }}</h3>

                                    <div class="row">
                                        <h2 class="page-title my-3">{{ __('Always Open') }}</h2>

                                        <div class="row">
                                            <div class="col-md-3 col-xl-3">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('24 Hours') }}</div>
                                                    <label class="form-check form-switch">
                                                        <input id="always-open" class="form-check-input" type="checkbox"
                                                            name="always_open"
                                                            {{ $business_hrs['alwaysOpen'] == 'Opening' ? 'checked' : '' }}>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-xl-3">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Hide Business Hours') }}</div>
                                                    <label class="form-check form-switch">
                                                        <input id="display-hrs" class="form-check-input" type="checkbox"
                                                            onchange="toggleDisplay('is_display', '#business-hrs')"
                                                            name="is_display"
                                                            {{ $business_hrs['isDisplay'] == 0 ? 'checked' : '' }}>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Business Hours --}}
                                        <div id="business-hrs">
                                            <div class="row">
                                                @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                                    <div class="col-md-3 col-xl-3">
                                                        <div class="mb-3">
                                                            <h2 class="page-title my-3">{{ __(ucfirst($day)) }}</h2>
                                                            <div class="mb-3">
                                                                <div class="form-label">{{ __('Closed') }}</div>
                                                                <label class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        onload="toggleDisplay('{{ $day }}_closed', '#{{ $day }}-business')"
                                                                        onchange="toggleDisplay('{{ $day }}_closed', '#{{ $day }}-business')"
                                                                        name="{{ $day }}_closed"
                                                                        {{ $business_hrs[$day . '_status'] == 'Closed' ? 'checked' : '' }}>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div id="{{ $day }}-business">
                                                            <div class="mb-3">
                                                                <input type="time" class="form-control"
                                                                    name="{{ $day }}_open"
                                                                    placeholder="{{ __('Opening Time') }}"
                                                                    value="{{ $business_hrs[$day . '_open'] }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <input type="time" class="form-control"
                                                                    name="{{ $day }}_closing"
                                                                    placeholder="{{ __('Closing Time') }}"
                                                                    value="{{ $business_hrs[$day . '_close'] }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer text-end">
                                    <div class="d-flex">
                                        <a href="{{ route('user.cards') }}"
                                            class="btn btn-outline-primary ms-2">{{ __('Cancel') }}</a>
                                        {{-- Next link --}}
                                        @php
                                            $route = route('user.cards');

                                            // Check business hours is "ENABLED"
                                            if ($plan_details->appointment == 1) {
                                                $route = route('user.edit.appointment', Request::segment(3));
                                            } elseif ($plan_details->contact_form == 1) {
                                                $route = route('user.edit.contact.form', Request::segment(3));
                                            } elseif (
                                                $plan_details->password_protected == 1 ||
                                                $plan_details->advanced_settings == 1
                                            ) {
                                                $route = route('user.edit.advanced.setting', Request::segment(3));
                                            }
                                        @endphp

                                        <a href="{{ $route }}" class="btn btn-outline-primary ms-2">
                                            {{ __('Skip') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary ms-auto">{{ __('Submit') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('user.includes.footer')
    </div>

    {{-- Custom JavaScript --}}
    @push('custom-js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                "use strict";

                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                const businessHours = document.getElementById('business-hrs');
                const alwaysOpenCheckbox = document.getElementById('always-open');
                const displayHrsCheckbox = document.getElementById('display-hrs');

                // Initialize toggles for each day on page load
                days.forEach(function(day) {
                    toggleDisplay(day + '_closed', '#' + day + '-business');
                });

                // Check if business hours should be hidden initially
                @if ($business_hrs['isDisplay'] === 0 || $business_hrs['alwaysOpen'] === 'Opening')
                    if (businessHours) {
                        businessHours.style.display = 'none';
                    }
                @endif

                // Handle "Always Open" checkbox changes
                if (alwaysOpenCheckbox) {
                    alwaysOpenCheckbox.addEventListener('change', function() {
                        if (alwaysOpenCheckbox.checked) {
                            displayHrsCheckbox.checked = false;
                            if (businessHours) {
                                businessHours.style.display = 'none';
                            }
                        } else {
                            if (businessHours) {
                                businessHours.style.display = 'flex';
                            }
                        }
                    });
                }

                // Handle "Display Hours" checkbox changes
                if (displayHrsCheckbox) {
                    displayHrsCheckbox.addEventListener('change', function() {
                        if (displayHrsCheckbox.checked) {
                            alwaysOpenCheckbox.checked = false;
                        }
                    });
                }
            });

            /**
             * Toggles the display of an element based on the state of an input checkbox.
             *
             * @param {string} inputName - The name of the input checkbox.
             * @param {string} elementId - The ID of the element to toggle.
             */
            function toggleDisplay(inputName, elementId) {
                "use strict";
                const isChecked = document.querySelector(`input[name="${inputName}"]:checked`) !== null;
                const element = document.querySelector(elementId);

                if (element) {
                    element.style.display = isChecked ? 'none' : 'block';
                }
            }
        </script>
    @endpush
@endsection
