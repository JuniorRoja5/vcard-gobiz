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
                        <h2 class="page-title mb-2">
                            {{ __('Google OAuth Settings') }}
                        </h2>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="{{ route('admin.plugins') }}"
                                class="btn btn-primary text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l14 0" />
                                    <path d="M5 12l6 6" />
                                    <path d="M5 12l6 -6" />
                                </svg>
                                {{ __('Back') }}
                            </a>
                        </div>
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
                        <form action="{{ route('admin.google_oauth_settings.update') }}" method="post" class="card">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Google OAuth Credentials') }}</h4>
                            </div>
                            <div class="card-body">
                                {{-- Google reCAPTCHA --}}
                                <div class="row">
                                    {{-- Google Auth Enable --}}
                                    <div class="col-xl-3">
                                        <div class="mb-3">
                                            <div class="form-label">{{ __('Google Auth Enable') }}</div>
                                            <select class="form-select"
                                                placeholder="{{ __('Select a Google Auth Enable') }}"
                                                id="google_auth_enable" name="google_auth_enable">
                                                <option value="on" {{ $google_configuration['GOOGLE_ENABLE'] == 'on' ? 'selected' : '' }}>{{ __('On') }}</option>
                                                <option value="off" {{ $google_configuration['GOOGLE_ENABLE'] == 'off' ? 'selected' : '' }}>{{ __('Off') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Google Client ID --}}
                                    <div class="col-xl-3">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Google Client ID') }}</label>
                                            <input type="text" class="form-control" name="google_client_id"
                                                value="{{ $google_configuration['GOOGLE_CLIENT_ID'] }}"
                                                placeholder="{{ __('Google CLIENT ID') }}">
                                        </div>
                                    </div>

                                    {{-- Google Client Secret --}}
                                    <div class="col-xl-3">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Google Client Secret')
                                                }}</label>
                                            <input type="text" class="form-control" name="google_client_secret"
                                                value="{{ $google_configuration['GOOGLE_CLIENT_SECRET'] }}"
                                                placeholder="{{ __('Google CLIENT Secret') }}">
                                        </div>
                                    </div>

                                    {{-- Google Redirect --}}
                                    <div class="col-xl-3">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Google Redirect') }}</label>
                                            <input type="text" class="form-control" name="google_redirect"
                                                value="{{ url('/sign-in-with-google') }}"
                                                placeholder="{{ __('Google Redirect') }}">
                                        </div>
                                    </div>
                                    <span>{{ __('If you did not get a Google OAuth Client ID & Secret Key, follow a') }} <a
                                            href="https://support.google.com/cloud/answer/6158849?hl=en#zippy=%2Cuser-consent%2Cpublic-and-internal-applications%2Cauthorized-domains/"
                                            target="_blank">{{ __(' steps') }}</a> </span>
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

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>
@endsection
