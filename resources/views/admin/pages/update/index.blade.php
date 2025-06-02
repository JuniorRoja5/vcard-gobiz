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
                            {{ __('Software Update') }}
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
                    <!-- Check update form -->
                    <div class="col-lg-8 col-md-8">
                        <form action="{{ route('admin.check.update') }}" method="post" class="card">
                            @csrf
                            <div class="card-body mb-2">
                                <div class="row align-items-center gx-2">
                                    <!-- License Section -->
                                    <div class="col-lg-8 col-md-8">
                                        <div class="mb-0">
                                            <label class="form-label required">
                                                {{ __('Envato Purchase Code') }}
                                            </label>
                                            <input type="text" class="form-control" name="purchase_code"
                                                placeholder="{{ __('Enter your Envato Purchase Code') }}"
                                                value="{{ $purchase_code }}" required>
                                            <small class="form-hint d-block mt-2">
                                                <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"
                                                    target="_blank" class="text-primary">
                                                    {{ __('Where is my purchase code?') }}
                                                </a>
                                            </small>
                                        </div>
                                    </div>
                
                                    <!-- Submit Button -->
                                    <div class="col-lg-4 col-md-4 d-flex justify-content-lg-end justify-content-md-start mt-3 mt-lg-0">
                                        <button type="submit" class="btn btn-primary btn-md w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" 
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                                                stroke-linejoin="round" class="icon">
                                                <path stroke="none" d="M0 0h24V0H0z" fill="none" />
                                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                                <path d="M9 12l2 2l4 -4" />
                                            </svg>
                                            {{ __('Check Update') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- Support status message --}}
                        @if (isset($response['support_status_message']) && $response['support_status_message'] != '')
                            {!! __($response['support_status_message']) !!}
                        @endif

                        <!-- Check Response -->
                        @if (isset($response))
                            <div class="mt-3">
                                <div class="alert alert-success">
                                    <h1 class="display-5">{{ $response['version'] }}</h1>
                                    <p class="mb-3 h3">{{ $response['message'] }}</p>
                                    @if ($response['update'])
                                        <p class="text-dark mb-4">{!! $response['notes'] !!}</p>
                                        <p>{{ __('IMPORTANT: Before starting this process, we recommend you to take a backup of your files.') }}</p>
                                        <form action="{{ route('admin.update.code') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="app_version" value="{{ $response['version'] }}">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Install') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                
                    <!-- Additional Info Section -->
                    <div class="col-lg-4 col-md-4">
                        <!-- Piracy Warning -->
                        <a href="https://codecanyon.net/item/gobiz-digital-business-card-in-laravel-saas-product-/33165916?ref={{ urlencode(config('app.url')) }}&size=source"
                            target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('img/piracy.png') }}" alt="Piracy" class="mb-2 img-fluid">
                        </a>
                
                        <!-- Support Renewal -->
                        <a href="https://store.nativecode.in?ref={{ urlencode(config('app.url')) }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('img/in-extended-license.png') }}" alt="Get Support" class="img-fluid">
                        </a>
                
                        <!-- Check Response -->
                        @if (isset($response))
                            @if ($response['license'] == 'Regular License')
                                <a href="https://codecanyon.net/cart/configure_before_adding/33165916?license=extended&ref={{ urlencode(config('app.url')) }}&size=source"
                                    target="_blank" rel="noopener noreferrer">
                                    <img class="mt-3 img-fluid" src="{{ asset('img/upgrade-to-extended-license.png') }}"
                                        alt="Upgrade to Extended License">
                                </a>
                            @endif
                        @endif
                    </div>
                </div>              
            </div>
        </div>

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>
@endsection
