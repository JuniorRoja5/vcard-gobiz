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
        <div class="container-fluid">
            <!-- Page title -->
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                            {{ __('Overview') }}
                        </div>
                        <h2 class="page-title">
                            {{ __('Update Plan') }}
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
                        <form action="{{ route('admin.update.plan') }}" method="post" class="card">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Plan Details') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <input type="hidden" class="form-control" name="plan_id"
                                                placeholder="{{ __('Plan ID') }}" value="{{ $plan_details->plan_id }}"
                                                readonly>
                                            {{-- Recommended --}}
                                            <div class="col-md-4 col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Recommended Plan') }}</div>
                                                    <select class="form-select recommended" name="recommended">
                                                        <option value="on"
                                                            {{ $plan_details->recommended == 1 ? 'selected' : '' }}>
                                                            {{ __('Yes') }}</option>
                                                        <option value="off"
                                                            {{ $plan_details->recommended == 0 ? 'selected' : '' }}>
                                                            {{ __('No') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            {{-- Private Plan --}}
                                            <div class="col-md-4 col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Private Plan') }}</div>
                                                    <select class="form-select is_private" name="is_private">
                                                        <option value="on"
                                                            {{ $plan_details->is_private == 1 ? 'selected' : '' }}>
                                                            {{ __('Yes') }}</option>
                                                        <option value="off"
                                                            {{ $plan_details->is_private == 0 ? 'selected' : '' }}>
                                                            {{ __('No') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            {{-- Plan Type --}}
                                            <input type="hidden" name="plan_type" value="{{ $plan_details->plan_type }}">

                                            {{-- Plan Name --}}
                                            <div class="col-md-4 col-xl-4">
                                                <div class="mb-3">
                                                    <label class="form-label required">{{ __('Plan Name') }}</label>
                                                    <input type="text" class="form-control" name="plan_name"
                                                        placeholder="{{ __('Plan Name') }}"
                                                        value="{{ $plan_details->plan_name }}" required>
                                                </div>
                                            </div>
                                            {{-- Description --}}
                                            <div class="col-md-4 col-xl-4">
                                                <div class="mb-3">
                                                    <label class="form-label required">{{ __('Description') }}</label>
                                                    <input type="text" class="form-control" name="plan_description"
                                                        placeholder="{{ __('Description') }}"
                                                        value="{{ $plan_details->plan_description }}" required>

                                                </div>
                                            </div>
                                            <h2 class="page-title my-3">
                                                {{ __('Plan Prices') }}
                                            </h2>
                                            <div class="col-md-4 col-xl-3">
                                                <div class="mb-3">
                                                    <label class="form-label required">{{ __('Price') }}</label>
                                                    <input type="number" class="form-control" name="plan_price"
                                                        min="0" step="0.01" placeholder="{{ __('Price') }}"
                                                        value="{{ $plan_details->plan_price }}" required>
                                                    <small class="text-muted">{{ __('For free, enter 0') }} </small>
                                                </div>
                                            </div>

                                            <div class="col-md-4 col-xl-3">
                                                <div class="mb-3">
                                                    <label class="form-label required">{{ __('Validity') }}</label>
                                                    <input type="number" class="form-control" name="validity"
                                                        min="0" max="9999" placeholder="{{ __('Validity') }}"
                                                        value="{{ $plan_details->validity }}" required>
                                                    <small class="text-muted">{{ __('For forever, enter 9999') }} </small>
                                                </div>
                                            </div>

                                            {{-- vCard Features --}}
                                            @if ($plan_details->plan_type == 'BOTH' || $plan_details->plan_type == 'VCARD')
                                                <div class="row" id="vcard-features">
                                                    <h2 class="page-title my-3">
                                                        {{ __('vCard Features') }}
                                                    </h2>

                                                    {{-- No. of vCards --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label required">{{ __('No. of vCards') }}</label>
                                                            <input type="number" class="form-control"
                                                                name="no_of_vcards" min="0" max="999"
                                                                placeholder="{{ __('No. of vCards') }}"
                                                                value="{{ $plan_details->no_of_vcards }}" required>
                                                            <small class="text-muted">{{ __('For unlimited, enter 999') }}
                                                            </small>
                                                        </div>
                                                    </div>

                                                    {{-- No. of Services --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label required">{{ __('No. of Services') }}</label>
                                                            <input type="number" class="form-control"
                                                                name="no_of_services" min="0" max="999"
                                                                placeholder="{{ __('No. of Services') }}"
                                                                value="{{ $plan_details->no_of_services }}" required>
                                                            <small class="text-muted">{{ __('For unlimited, enter 999') }}
                                                            </small>
                                                        </div>
                                                    </div>

                                                    {{-- No. of Products --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label required">{{ __('No. of Products') }}</label>
                                                            <input type="number" class="form-control"
                                                                name="no_of_vcard_products" min="0" max="999"
                                                                placeholder="{{ __('No. of Products') }}"
                                                                value="{{ $plan_details->no_of_vcard_products }}"
                                                                required>
                                                            <small class="text-muted">{{ __('For unlimited, enter 999') }}
                                                            </small>
                                                        </div>
                                                    </div>

                                                    {{-- No. of Links --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label required">{{ __('No. of Links') }}</label>
                                                            <input type="number" class="form-control" name="no_of_links"
                                                                min="0" max="999"
                                                                placeholder="{{ __('No. of Links') }}"
                                                                value="{{ $plan_details->no_of_links }}" required>
                                                            <small class="text-muted">{{ __('For unlimited, enter 999') }}
                                                            </small>
                                                        </div>
                                                    </div>

                                                    {{-- No. of Payment Listed --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label required">{{ __('No. of Payment Listed') }}</label>
                                                            <input type="number" class="form-control"
                                                                name="no_of_payments" min="0" max="999"
                                                                placeholder="{{ __('No. of Payment Listed') }}"
                                                                value="{{ $plan_details->no_of_payments }}" required>
                                                            <small class="text-muted">{{ __('For unlimited, enter 999') }}
                                                            </small>
                                                        </div>
                                                    </div>

                                                    {{-- No. of Testimonials --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label required">{{ __('No. of Testimonials') }}</label>
                                                            <input type="number" class="form-control"
                                                                name="no_testimonials" min="0" max="999"
                                                                placeholder="{{ __('No. of Testimonials') }}"
                                                                value="{{ $plan_details->no_testimonials }}" required>
                                                            <small class="text-muted">{{ __('For unlimited, enter 999') }}
                                                            </small>
                                                        </div>
                                                    </div>

                                                    {{-- No. of Galleries --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label required">{{ __('No. of Galleries') }}</label>
                                                            <input type="number" class="form-control"
                                                                name="no_of_galleries" min="0" max="999"
                                                                placeholder="{{ __('No. of Galleries') }}"
                                                                value="{{ $plan_details->no_of_galleries }}" required>
                                                            <small class="text-muted">{{ __('For unlimited, enter 999') }}
                                                            </small>
                                                        </div>
                                                    </div>

                                                    {{-- Business Hours --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <div class="form-label required">{{ __('Business Hours') }}
                                                            </div>
                                                            <select class="form-select business_hours"
                                                                name="business_hours" required>
                                                                <option value="on"
                                                                    {{ $plan_details->business_hours == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}</option>
                                                                <option value="off"
                                                                    {{ $plan_details->business_hours == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    {{-- Appointment --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <div class="form-label required">{{ __('Appointment') }}</div>
                                                            <select class="form-select appointment" name="appointment"
                                                                required>
                                                                <option value="on"
                                                                    {{ $plan_details->appointment == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}</option>
                                                                <option value="off"
                                                                    {{ $plan_details->appointment == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    {{-- Contact Form --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <div class="form-label required">{{ __('Contact Form') }}
                                                            </div>
                                                            <select class="form-select contact_form" name="contact_form"
                                                                required>
                                                                <option value="on"
                                                                    {{ $plan_details->contact_form == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}</option>
                                                                <option value="off"
                                                                    {{ $plan_details->contact_form == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    {{-- Password Protected --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <div class="form-label required">
                                                                {{ __('Password Protected') }}</div>
                                                            <select class="form-select password_protected"
                                                                name="password_protected" required>
                                                                <option value="on"
                                                                    {{ $plan_details->password_protected == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}</option>
                                                                <option value="off"
                                                                    {{ $plan_details->password_protected == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    {{-- No. of Enquiries --}}
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <div class="form-label required">{{ __('No. of Enquiries') }}
                                                            </div>
                                                            <input type="number" class="form-control"
                                                                name="no_of_enquires" min="0" max="999"
                                                                placeholder="{{ __('No. of Enquiries') }}"
                                                                value="{{ $plan_details->no_of_enquires }}" required>
                                                            <small class="text-muted">{{ __('For unlimited, enter 999') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Store Features --}}
                                            @if ($plan_details->plan_type == 'BOTH' || $plan_details->plan_type == 'STORE')
                                                <div class="row" id="store-features">
                                                    <h2 class="page-title mb-3">
                                                        {{ __('Store Features') }}
                                                    </h2>
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label required">{{ __('No. of Stores') }}</label>
                                                            <input type="number" class="form-control"
                                                                name="no_of_stores" min="0" max="999"
                                                                placeholder="{{ __('No. of Stores') }}"
                                                                value="{{ $plan_details->no_of_stores }}" required>
                                                            <small class="text-muted">{{ __('For unlimited, enter 999') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label required">{{ __('No. of Categories') }}</label>
                                                            <input type="number" class="form-control"
                                                                name="no_of_categories" min="0" max="999"
                                                                placeholder="{{ __('No. of Categories') }}"
                                                                value="{{ $plan_details->no_of_categories }}" required>
                                                            <small class="text-muted">{{ __('For unlimited, enter 999') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label required">{{ __('No. of Products') }}</label>
                                                            <input type="number" class="form-control"
                                                                name="no_of_store_products" min="0" max="999"
                                                                placeholder="{{ __('No. of Products') }}"
                                                                value="{{ $plan_details->no_of_store_products }}"
                                                                required>
                                                            <small class="text-muted">{{ __('For unlimited, enter 999') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <h2 class="page-title my-3">
                                                {{ __('Additional features') }}
                                            </h2>

                                            {{-- Custom domain --}}
                                            <div class="col-md-3 col-sm-6">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Custom Domain') }}</div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="custom_domain" value="on"
                                                            {{ $plan_details->custom_domain == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="custom_domain">{{ __('Yes') }}</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- NFC Card --}}
                                            <div class="col-md-3 col-sm-6">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('NFC Card') }}
                                                        <span id="popover-element" class="form-help bg-primary text-white"
                                                            data-bs-toggle="popover" data-bs-placement="top"
                                                            data-bs-html="true"
                                                            data-bs-content="<p>{{ __('NFC stands for Near Field Communication. NFC is a wireless technology that allows two devices to communicate with each other directly, without the need for a wired connection.') }}</p>">?</span>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="nfc_card" value="on"
                                                            {{ $plan_details->nfc_card == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="nfc_card">{{ __('Yes') }}</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- PWA --}}
                                            <div class="col-md-3 col-sm-6">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('PWA') }}
                                                        <span id="popover-element" class="form-help bg-primary text-white"
                                                            data-bs-toggle="popover" data-bs-placement="top"
                                                            data-bs-html="true"
                                                            data-bs-content="<p>{{ __('PWA stands for Progressive Web Application. PWAs are web applications that are built using common web technologies like HTML, CSS, JavaScript, and WebAssembly. They are designed to work on any platform with a standards-compliant browser, including desktop and mobile devices.') }}</p>">?</span>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="pwa"
                                                            value="on"
                                                            {{ $plan_details->pwa == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="pwa">{{ __('Yes') }}</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Advanced Settings --}}
                                            <div class="col-md-3 col-sm-6">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Advanced Settings') }}</div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="advanced_settings" value="on"
                                                            {{ $plan_details->advanced_settings == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="advanced_settings">{{ __('Yes') }}</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Personalized Link --}}
                                            <div class="col-md-3 col-sm-6">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Personalized Link') }}</div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="personalized_link" value="on"
                                                            {{ $plan_details->personalized_link == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="personalized_link">{{ __('Yes') }}</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Hide Branding --}}
                                            <div class="col-md-3 col-sm-6">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Hide Branding') }}</div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="hide_branding" value="on"
                                                            {{ $plan_details->hide_branding == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="hide_branding">{{ __('Yes') }}</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Free Setup --}}
                                            <div class="col-md-3 col-sm-6">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Free Setup') }}</div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="free_setup"
                                                            value="on"
                                                            {{ $plan_details->free_setup == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="free_setup">{{ __('Yes') }}</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Free Support --}}
                                            <div class="col-md-3 col-sm-6">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Free Support') }}</div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="free_support" value="on"
                                                            {{ $plan_details->free_support == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="free_support">{{ __('Yes') }}</label>
                                                    </div>
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
        document.addEventListener('DOMContentLoaded', () => {
            const popoverElement = document.getElementById('popover-element');
            const popover = new bootstrap.Popover(popoverElement, {
                trigger: 'manual'
            });

            popoverElement.addEventListener('click', (e) => {
                e.stopPropagation();
                popover.toggle();
            });

            document.addEventListener('click', (e) => {
                if (!popoverElement.contains(e.target)) {
                    popover.hide();
                }
            });
        });
    </script>
    <script>
        function checkPlanType(type) {
            "use strict";
            var planType = type.value;
            switch (planType) {
                case 'VCARD':

                    // Remove store inputs "required" attributes
                    $("input[name='no_of_stores']").removeAttr('required');
                    $("input[name='no_of_categories']").removeAttr('required');
                    $("input[name='no_of_store_products']").removeAttr('required');

                    // Hidden store
                    $("#store-features").attr("class", "row d-none");

                    // Add vcard inputs "required" attributes
                    $("input[name='no_of_vcards']").attr('required', 'required');
                    $("input[name='no_of_services']").attr('required', 'required');
                    $("input[name='no_of_vcard_products']").attr('required', 'required');
                    $("input[name='no_of_links']").attr('required', 'required');
                    $("input[name='no_of_payments']").attr('required', 'required');
                    $("input[name='no_of_galleries']").attr('required', 'required');
                    $("input[name='no_testimonials']").attr('required');
                    $("input[name='business_hours']").attr('required', 'required');
                    $("input[name='contact_form']").attr('required', 'required');
                    $("input[name='no_of_enquires']").attr('required', 'required');

                    // Enable vcard
                    $("#vcard-features").attr("class", "row");

                    break;
                case 'STORE':

                    // Remove vcard inputs "required" attributes
                    $("input[name='no_of_vcards']").removeAttr('required');
                    $("input[name='no_of_services']").removeAttr('required');
                    $("input[name='no_of_vcard_products']").removeAttr('required');
                    $("input[name='no_of_links']").removeAttr('required');
                    $("input[name='no_of_payments']").removeAttr('required');
                    $("input[name='no_of_galleries']").removeAttr('required');
                    $("input[name='business_hours']").removeAttr('required');
                    $("input[name='contact_form']").removeAttr('required');
                    $("input[name='no_testimonials']").removeAttr('required');
                    $("input[name='no_of_enquires']").removeAttr('required');

                    // Hidden vcard
                    $("#vcard-features").attr("class", "row d-none");

                    // Add store inputs "required" attributes
                    $("input[name='no_of_stores']").attr('required', 'required');
                    $("input[name='no_of_categories']").attr('required', 'required');
                    $("input[name='no_of_store_products']").attr('required', 'required');

                    // Enable Store
                    $("#store-features").attr("class", "row");

                    break;
                default:
                    // Add vcard/store inputs "required" attributes
                    $("input[name='no_of_vcards']").attr('required', 'required');
                    $("input[name='no_of_services']").attr('required', 'required');
                    $("input[name='no_of_vcard_products']").attr('required', 'required');
                    $("input[name='no_of_links']").attr('required', 'required');
                    $("input[name='no_of_payments']").attr('required', 'required');
                    $("input[name='no_of_galleries']").attr('required', 'required');
                    $("input[name='business_hours']").attr('required', 'required');
                    $("input[name='no_testimonials']").attr('required');
                    $("input[name='contact_form']").attr('required', 'required');
                    $("input[name='no_of_enquires']").attr('required', 'required');

                    $("input[name='no_of_stores']").attr('required', 'required');
                    $("input[name='no_of_categories']").attr('required', 'required');
                    $("input[name='no_of_store_products']").attr('required', 'required');

                    // Enable vcard
                    $("#vcard-features").attr("class", "row");
                    // Enable Store
                    $("#store-features").attr("class", "row");
            }
        }
    </script>

    <script>
        // Array of element selectors
        var elementSelectors = ['.plan_type', '.business_hours', '.contact_form', '.pwa', '.password_protected',
            '.appointment', '.custom_domain', '.advanced_settings', '.additional_tools', '.personalized_link',
            '.hide_branding', '.free_setup', '.free_support', '.recommended', '.is_private'
        ];

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
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    </script>
@endsection
@endsection
