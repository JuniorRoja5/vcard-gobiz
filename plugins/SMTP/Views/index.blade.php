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
                            {{ __('SMTP Settings') }}
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
                        <form action="{{ route('admin.smtp_settings.update') }}" method="post" class="card">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('SMTP Credentials') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    {{-- Sender Name --}}
                                    <div class=" col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Sender Name') }}</label>
                                            <input type="text" class="form-control" name="mail_sender"
                                                value="{{ $email_configuration['name'] }}" maxlength="50"
                                                placeholder="{{ __('Sender Name') }}">
                                        </div>
                                    </div>

                                    {{-- Sender Email Address --}}
                                    <div class=" col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Sender Email Address') }}</label>
                                            <input type="text" class="form-control" name="mail_address"
                                                value="{{ $email_configuration['address'] }}"
                                                placeholder="{{ __('Sender Email Address') }}">
                                        </div>
                                    </div>

                                    {{-- Mailer Driver --}}
                                    <div class=" col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Mailer Driver') }}</label>
                                            <input type="text" class="form-control" name="mail_driver"
                                                value="{{ $email_configuration['driver'] }}"
                                                placeholder="{{ __('Mailer Driver') }}">
                                        </div>
                                    </div>

                                    {{-- Mailer Host --}}
                                    <div class=" col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Mailer Host') }}</label>
                                            <input type="text" class="form-control" name="mail_host"
                                                value="{{ $email_configuration['host'] }}"
                                                placeholder="{{ __('Mailer Host') }}">
                                        </div>
                                    </div>

                                    {{-- Mailer Port --}}
                                    <div class=" col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Mailer Port') }}</label>
                                            <input type="number" class="form-control" name="mail_port"
                                                oninput="validatePort(this)" maxlength="4"
                                                value="{{ $email_configuration['port'] }}"
                                                placeholder="{{ __('Mailer Port') }}">
                                        </div>
                                    </div>

                                    {{-- Mailer Encryption --}}
                                    <div class=" col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="mail_encryption">{{ __('Mailer Encryption') }}</label>
                                            <select name="mail_encryption" id="mail_encryption" class="form-select">
                                                <option value="tls"
                                                    {{ $email_configuration['encryption'] == 'tls' ? 'selected' : '' }}>
                                                    {{ __('TLS/STARTTLS') }}</option>
                                                <option value="ssl"
                                                    {{ $email_configuration['encryption'] == 'ssl' ? 'selected' : '' }}>
                                                    {{ __('SSL') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Mailer Username --}}
                                    <div class=" col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Mailer Username') }}</label>
                                            <input type="text" class="form-control" name="mail_username"
                                                value="{{ $email_configuration['username'] }}"
                                                placeholder="{{ __('Mailer Username') }}">
                                        </div>
                                    </div>

                                    {{-- Mailer Password --}}
                                    <div class=" col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Mailer Password') }}</label>
                                            <input type="password" class="form-control" name="mail_password"
                                                value="{{ $email_configuration['password'] }}" maxlength="30"
                                                placeholder="{{ __('Mailer Password') }}">
                                        </div>
                                    </div>

                                    {{-- Test Mail --}}
                                    <div class=" col-xl-4 mt-3">
                                        <div class="mb-3">
                                            <label class="form-label"></label>
                                            <a href="{{ route('admin.plugin.test.email') }}" class="btn btn-primary">
                                                {{ __('Test Mail') }}
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Customer Email Verification System --}}
                                    <div class="row">
                                        <h2 class="page-title my-3">
                                            {{ __('Customer Email Verification System') }}
                                        </h2>
                                        <div class="col-xl-4 col-12">
                                            <div class="mb-3">
                                                <label class="form-label required"
                                                    for="disable_user_email_verification">{{ __('Require customer email verification?') }}</label>
                                                <select name="disable_user_email_verification"
                                                    id="disable_user_email_verification" class="form-select" required>
                                                    <option value="1"
                                                        {{ $config[43]->config_value == '1' ? 'selected' : '' }}>
                                                        {{ __('Yes') }}</option>
                                                    <option value="0"
                                                        {{ $config[43]->config_value == '0' ? 'selected' : '' }}>
                                                        {{ __('No') }}</option>
                                                </select>
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

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>
@endsection
