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
                                        'link' => 'contact',
                                    ])
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-9 d-flex flex-column">
                            <form action="{{ route('user.update.contact.form', Request::segment(3)) }}" method="post"
                                enctype="multipart/form-data" id="myForm">
                                @csrf
                                <div class="card-body">
                                    <h3 class="card-title mb-4">{{ __('Contact & Inquiry Form') }}</h3>
                                
                                    <div class="row g-4">
                                        <!-- Hide Contact / Inquiry Form Toggle -->
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Hide Contact / Inquiry Form') }}</label>
                                                <label class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" onchange="displayContactForm()" name="contact_form"
                                                        id="contact-form" {{ $business_card->enquiry_email == null ? 'checked' : '' }}>
                                                </label>
                                            </div>
                                        </div>
                                
                                        <!-- Contact Form Email Configuration -->
                                        <div class="col-md-6" id="contactForm">
                                            <h4>{{ __('Contact & Inquiry Form Email Configuration') }}</h4>
                                
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Want to receive email') }}</label>
                                                <input type="email" class="form-control" name="receive_email" id="receive_email"
                                                    value="{{ $business_card->enquiry_email }}" placeholder="{{ __('Email Address') }}">
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
                                            if (
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

    {{-- Custom JS --}}
    @push('custom-js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Check the initial state of the checkbox
                displayContactForm();
            });

            function displayContactForm() {
                "use strict";

                const contactForm = document.getElementById('contactForm');
                const contactCheckbox = document.getElementById('contact-form');
                
                if (contactCheckbox.checked) {
                    contactForm.classList.add('d-none');

                    // Remove required attribute from email input
                    const emailInput = document.getElementById('receive_email');
                    emailInput.required = false;
                } else {
                    contactForm.classList.remove('d-none');

                    // Add required attribute to email input
                    const emailInput = document.getElementById('receive_email');
                    emailInput.required = true;
                }
            }
        </script>
    @endpush
@endsection
