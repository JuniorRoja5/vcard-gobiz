@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true, 'settings' => $settings])

{{-- Custom CSS --}}
@section('css')
    <style>
        .btn-update {
            margin-top: -12px;
        }
    </style>
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
                        <h2 class="page-title mb-2">
                            {{ __('Support') }}
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

                <div class="row row-cards">
                    <div class="col-lg-8 col-md-12">
                        <form action="{{ route('admin.upgrade.support') }}" method="post" class="card">
                            @csrf
                            <div class="card-body mb-4">
                                <div class="row align-items-center gx-2">
                                    <!-- License Section -->
                                    <div class="col-lg-8 col-md-8">
                                        <div class="mt-2">
                                            <label class="form-label required">{{ __('Support License') }}</label>
                                            <input type="text" class="form-control" name="support_code"
                                                placeholder="{{ __('Enter Your Support License') }}"
                                                value="{{ $config[73]->config_value != '' ? $config[73]->config_value : env('PURCHASE_CODE') }}"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div
                                        class="col-lg-4 col-md-4 d-flex justify-content-lg-end justify-content-md-start mt-3 mt-lg-5">
                                        <button type="submit" class="btn btn-primary btn-md w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="currentColor"
                                                class="icon icon-tabler icons-tabler-filled icon-tabler-rosette-discount-check">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M12.01 2.011a3.2 3.2 0 0 1 2.113 .797l.154 .145l.698 .698a1.2 1.2 0 0 0 .71 .341l.135 .008h1a3.2 3.2 0 0 1 3.195 3.018l.005 .182v1c0 .27 .092 .533 .258 .743l.09 .1l.697 .698a3.2 3.2 0 0 1 .147 4.382l-.145 .154l-.698 .698a1.2 1.2 0 0 0 -.341 .71l-.008 .135v1a3.2 3.2 0 0 1 -3.018 3.195l-.182 .005h-1a1.2 1.2 0 0 0 -.743 .258l-.1 .09l-.698 .697a3.2 3.2 0 0 1 -4.382 .147l-.154 -.145l-.698 -.698a1.2 1.2 0 0 0 -.71 -.341l-.135 -.008h-1a3.2 3.2 0 0 1 -3.195 -3.018l-.005 -.182v-1a1.2 1.2 0 0 0 -.258 -.743l-.09 -.1l-.697 -.698a3.2 3.2 0 0 1 -.147 -4.382l.145 -.154l.698 -.698a1.2 1.2 0 0 0 .341 -.71l.008 -.135v-1l.005 -.182a3.2 3.2 0 0 1 3.013 -3.013l.182 -.005h1a1.2 1.2 0 0 0 .743 -.258l.1 -.09l.698 -.697a3.2 3.2 0 0 1 2.269 -.944zm3.697 7.282a1 1 0 0 0 -1.414 0l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.32 1.497l2 2l.094 .083a1 1 0 0 0 1.32 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" />
                                            </svg>
                                            {{ __('Activate') }}
                                        </button>
                                    </div>

                                    {{-- Success --}}
                                    @if (Session::has('success'))
                                        <div class="col-sm-12 col-lg-8 mt-5">
                                            <div class="card alert alert-important alert-success">
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <h2 class="mb-1">{{ __('Thanks for your support!') }}</h2>
                                                        <p>{{ __('Your support is important to us. We will use it to keep the project alive and to continue to add new features and support to it.') }}
                                                        </p>
                                                        <div class="btn-list">
                                                            <a href="https://support.nativecode.in?ref={{ urlencode(config('app.url')) }}&size=source"
                                                                target="_blank" class="btn btn-white border border-success text-primary">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-message-chatbot">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                    <path
                                                                        d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                                                    <path d="M9.5 9h.01" />
                                                                    <path d="M14.5 9h.01" />
                                                                    <path d="M9.5 13a3.5 3.5 0 0 0 5 0" />
                                                                </svg>
                                                                {{ __('Raise Support Ticket') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Failed --}}
                                    @if (Session::has('failed'))
                                        <div class="col-sm-12 col-lg-8 mt-5">
                                            <div class="card alert alert-important alert-danger">
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <h2 class="mb-1">{{ __('Your support plan has ended!') }}</h2>
                                                        <p>{{ __('Renew now to continue enjoying priority support, updates, and uninterrupted access to exclusive features.') }}
                                                        </p>
                                                        <div class="btn-list">
                                                            <a href="https://store.nativecode.in?ref={{ urlencode(config('app.url')) }}&size=source"
                                                                target="_blank" class="btn btn-white border border-danger text-black">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-message-chatbot">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                    <path
                                                                        d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                                                    <path d="M9.5 9h.01" />
                                                                    <path d="M14.5 9h.01" />
                                                                    <path d="M9.5 13a3.5 3.5 0 0 0 5 0" />
                                                                </svg>
                                                                {{ __('Renew Now') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Support Renewal -->
                    <div class="col-sm-12 col-lg-4 d-none d-lg-block">
                        <div class="card">
                            <a href="https://store.nativecode.in?ref={{ urlencode(config('app.url')) }}&size=source"
                                target="_blank" class="card">
                                <img src="{{ asset('img/in-extended-license.png') }}" alt="Get Support" class="img-fluid">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>
@endsection
