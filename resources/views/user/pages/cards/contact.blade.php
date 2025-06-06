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
                        {{ __('Contact & Inquiry Form') }}
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
                    <form action="{{ route('user.save.contact.form', Request::segment(3)) }}" method="post"
                        class="card">
                        @csrf
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h2 class="card-title">{{ __('Hide Contact / Inquiry Form') }}</h2>
                                        </div>
                                        <div class="card-body">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" onchange="displayContactForm()" name="contact_form" id="contact-form">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                        
                                <div class="col-xl-6" id="contactForm">
                                    <div class="card">
                                        <div class="card-header">
                                            <h2 class="card-title">{{ __('Appointment, Contact & Inquiry Form Configuration') }}</h2>
                                        </div>
                                        <div class="card-body">
                                            <!-- Email -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label required">{{ __('Want to receive email') }}</label>
                                                        <input type="email" class="form-control" name="receive_email" id="receive_email" value="{{ old('receive_email') }}" placeholder="{{ __('Email Address') }}" required>
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
                              <a href="{{ route('user.advanced.setting', Request::segment(3)) }}" class="btn btn-outline-primary ms-2">{{ __('Skip') }}</a>
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


@push('custom-js')
<script>
function displayContactForm() {
    "use strict";
    var disp = $('input[name="contact_form"]:checked').length;
    console.log(disp);
    if (disp == 0) {
        $("#contactForm").show();
        $('#receive_email').attr('required', 'required');
    } else {
        $("#contactForm").hide();
        $('#receive_email').removeAttr('required', 'required');
    }
}
</script>
@endpush
@endsection