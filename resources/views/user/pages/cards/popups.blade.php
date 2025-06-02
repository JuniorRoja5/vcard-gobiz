@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true, 'settings' => $settings])

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
                        {{ __('Popups') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            {{-- Failed --}}
            @if(Session::has("failed"))
            <div class="alert alert-important alert-danger alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('failed')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            {{-- Success --}}
            @if(Session::has("success"))
            <div class="alert alert-important alert-success alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('success')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif
            
            <div class="row row-deck row-cards">
                <div class="col-sm-12 col-lg-12">
                    <form action="{{ route('user.save.popups', Request::segment(3)) }}" method="post" enctype="multipart/form-data"
                        class="card">
                        @csrf
                        <div class="card-body">
                            <div class="row g-4">
                                {{-- Newsletter Popup --}}
                                <div class="col-xl-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h2 class="card-title">{{ __('Enable Newsletter Popup') }}</h2>
                                        </div>
                                        <div class="card-body">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" onchange="newsletterPopup()" name="is_newsletter_pop_active">
                                                {{-- Hidden value --}}
                                                <input type="hidden" name="is_newsletter_pop_active" value="0">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                        
                                {{-- Information Popup --}}
                                <div class="col-xl-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h2 class="card-title">{{ __('Enable Information Popup') }}</h2>
                                        </div>
                                        <div class="card-body">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" onchange="informationPopup()" name="is_info_pop_active">
                                                {{-- Hidden value --}}
                                                <input type="hidden" name="is_info_pop_active" value="0">
                                            </label>
                        
                                            <div class="row mt-4 d-none" id="info-popup">
                                                {{-- Popup Details --}}
                                                <div class="col-12">
                                                    <h3 class="mb-3">{{ __('Information Popup Details') }}</h3>
                                                </div>

                                                {{-- Confetti Effect --}}
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('Do you want to activate the confetti effect?') }}</label>
                                                        <input type="checkbox" class="form-check-input" name="confetti_effect" id="confetti_effect" value="1">
                                                    </div>
                                                </div>

                                                {{-- Image --}}
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label required">{{ __('Image') }}</label>
                                                        <input type="file" class="form-control" name="info_pop_image" id="info_pop_image" placeholder="{{ __('Image') }}">
                                                    </div>
                                                </div>
                        
                                                {{-- Title --}}
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label required">{{ __('Title') }}</label>
                                                        <input type="text" class="form-control" name="info_pop_title" id="info_pop_title" placeholder="{{ __('Title') }}">
                                                    </div>
                                                </div>
                        
                                                {{-- Description --}}
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label required">{{ __('Description') }}</label>
                                                        <textarea class="form-control" name="info_pop_desc" id="info_pop_desc" cols="30" rows="2" placeholder="{{ __('Type something') }}"></textarea>
                                                    </div>
                                                </div>
                        
                                                {{-- Button Text --}}
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label required">{{ __('Button Text') }}</label>
                                                        <input type="text" class="form-control" name="info_pop_button_text" id="info_pop_button_text" placeholder="{{ __('Button Text') }}">
                                                    </div>
                                                </div>
                        
                                                {{-- Button URL --}}
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label required">{{ __('Button URL') }}</label>
                                                        <input type="url" class="form-control" name="info_pop_button_url" id="info_pop_button_url" placeholder="{{ __('Button URL') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>     
                        
                        <div class="card-footer text-end">
                            <div class="d-flex">
                              <a href="{{ route('user.business.hours', Request::segment(3)) }}" class="btn btn-outline-primary ms-2">{{ __('Skip') }}</a>
                              <button type="submit" class="btn btn-primary ms-auto">{{ __('Submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('user.includes.footer')
</div>

{{-- Custom JS --}}
@push('custom-js')
<script>
    // Newsletter Popup
    function newsletterPopup() {
        "use strict";

        var newsletterPopup = $('input[name="is_newsletter_pop_active"]:checked').length;

        if (newsletterPopup == 0) {
            // Set value to newsletter popup
            $('input[name="is_newsletter_pop_active"]').val(0);
        } else {
            // Set value to newsletter popup
            $('input[name="is_newsletter_pop_active"]').val(1);
        }
    }

    // Information Popup
    function informationPopup() {
        "use strict";

        var infoPopup = $('input[name="is_info_pop_active"]:checked').length;

        if (infoPopup == 0) {
            // Hide details
            $("#info-popup").attr("class", "row d-none");

            // Set value to information popup
            $('input[name="is_info_pop_active"]').val(0);

            // Disable required
            $('#info_pop_image').removeAttr('required', 'required');
            $('#info_pop_title').removeAttr('required', 'required');
            $('#info_pop_desc').removeAttr('required', 'required');
            $('#info_pop_button_url').removeAttr('required', 'required');
            $('#info_pop_button_text').removeAttr('required', 'required');
        }
        else {
            // Show details
            $("#info-popup").attr("class", "row");

            // Enable required
            $('#info_pop_image').attr('required', 'required');
            $('#info_pop_title').attr('required', 'required');
            $('#info_pop_desc').attr('required', 'required');
            $('#info_pop_button_url').attr('required', 'required');
            $('#info_pop_button_text').attr('required', 'required');

            // Set value to information popup
            $('input[name="is_info_pop_active"]').val(1);
        }
    }
</script>
@endpush
@endsection