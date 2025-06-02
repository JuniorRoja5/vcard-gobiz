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
                            {{ __('Mailgun Configuration') }}
                        </h2>
                    </div>
                    <span class="mt-3">{{ __('How to configure Mailgun SMTP from the Mailgun documentation?') }} {!! __('<a href="https://docs.nativecode.in/gobiz/where-can-I-find-my-API-keys-and-SMTP-credentials" target="_blank">Click here</a>') !!}</span>
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
                        <form action="{{ route('admin.marketing.mailgun.update') }}" method="post" class="card">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Configuration') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Mailgun mailer --}}
                                    <div class="col-md-6 col-xl-6">
                                        <div class="mb-3">
                                            <div class="form-label required">{{ __('Mailgun mailer') }}</div>
                                            <select class="form-control" id="mailgun_mailer" name="mailgun_mailer" disabled>
                                                <option value="smtp" selected>{{ __('SMTP') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Mailgun Host --}}
                                    <div class="col-md-6 col-xl-6">
                                        <div class="mb-3">
                                            <div class="form-label required">{{ __('Mailgun Host') }}</div>
                                            <input type="text" class="form-control" id="mailgun_host" name="mailgun_host" value="smtp.mailgun.org" placeholder="{{ __('Mailgun Host') }}" disabled>
                                        </div>
                                    </div>

                                    {{-- Mailgun Port --}}
                                    <div class="col-md-6 col-xl-6">
                                        <div class="mb-3">
                                            <div class="form-label required">{{ __('Mailgun Port') }}</div>
                                            <input type="text" class="form-control" id="mailgun_port" name="mailgun_port" value="587" placeholder="{{ __('Mailgun Port') }}" disabled>
                                        </div>
                                    </div>

                                    {{-- Mailgun Username --}}
                                    <div class="col-md-6 col-xl-6">
                                        <div class="mb-3">
                                            <div class="form-label required">{{ __('Mailgun Username') }}</div>
                                            <input type="text" class="form-control" id="mailgun_username" name="mailgun_username" value="{{ $config[57]->config_value }}" placeholder="{{ __('Mailgun Username') }}" required>
                                        </div>
                                    </div>

                                    {{-- Mailgun Password --}}
                                    <div class="col-md-6 col-xl-6">
                                        <div class="mb-3">
                                            <div class="form-label required">{{ __('Mailgun Password') }}</div>
                                            <input type="text" class="form-control" id="mailgun_password" name="mailgun_password" value="{{ $config[58]->config_value }}" placeholder="{{ __('Mailgun Password') }}" required>
                                        </div>
                                    </div>

                                    {{-- Mailgun From Email --}}
                                    <div class="col-md-6 col-xl-6">
                                        <div class="mb-3">
                                            <div class="form-label required">{{ __('Mailgun From Email') }}</div>
                                            <input type="text" class="form-control" id="mailgun_from_email" name="mailgun_from_email" value="{{ $config[59]->config_value }}" placeholder="{{ __('Mailgun From Email') }}" required>
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
        <!-- Footer -->
        @include('admin.includes.footer')
    </div>
@endsection