@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true, 'settings' => $settings])

{{-- Custom CSS --}}
@section('css')
    <style>
        .section {
            margin-bottom: 20px;
        }

        .code-block {
            background-color: #f1f1f1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: monospace;
        }

        .image {
            text-align: center;
            margin: 15px 0;
        }

        .image img {
            max-width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .tip {
            background-color: #e2f0d9;
            padding: 10px;
            border-left: 5px solid #4caf50;
            border-radius: 5px;
            margin-top: 10px;
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
                        <h2 class="page-title">
                            {{ __('Setting Up Cloudflare DNS Records and Page Rules') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <!-- Section 1: Setting Up Cloudflare DNS Records -->
                                <div class="mb-4">
                                    <h2 class="text-muted">1. {{ __('Setting Up Cloudflare DNS Records') }}</h2>
                                    <h3 class="mt-3">{{ __('Log In to Cloudflare')}}</h3>
                                    <ul>
                                        <li>{{ __('Visit the')}} <a href="https://dash.cloudflare.com" target="_blank"
                                                class="text-decoration-underline">{{ __('Cloudflare dashboard')}}</a> {{ __('and log in to your account.')}}</li>
                                    </ul>

                                    <h3 class="mt-4">{{ __('Select Your Domain')}}</h3>
                                    <ul>
                                        <li>{{ __('From the dashboard, select the domain you want to configure.')}}</li>
                                    </ul>

                                    <h3 class="mt-4">{{ __('Add DNS Records')}}</h3>
                                    <ul>
                                        <li>{{ __('Go to the')}} <strong>{{ __('DNS')}}</strong> {{ __('tab and click')}} <strong>{{ __('Add Record')}}</strong> {{ __('to add the necessary DNS entries.')}}</li>
                                    </ul>

                                    {{-- Table --}}
                                    <div class="table-responsive">
                                        <table class="table table-vcenter">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Type')}}</th>
                                                    <th>{{ __('Name')}}</th>
                                                    <th>{{ __('IPv4 address / Target')}}</th>
                                                    <th>{{ __('Proxy status')}}</th>
                                                    <th>{{ __('TTL')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>A</td>
                                                    <td>@ (for root domain)</td>
                                                    <td>Enter your server’s IP address</td>
                                                    <td>Enable (orange cloud icon)</td>
                                                    <td>Auto</td>
                                                </tr>
                                                <tr>
                                                    <td>CNAME</td>
                                                    <td>www</td>
                                                    <td>{{ str_replace(['http://', 'https://', 'www.'], '', config('app.url')) }}
                                                    </td>
                                                    <td>Disabled (gray cloud icon)</td>
                                                    <td>Auto</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <ul class="mt-3">
                                        <li>{{ __('Save each record after entering the details.')}}</li>
                                    </ul>

                                    {{-- DNS Records Modal Button --}}
                                    <div>
                                        {{ __('Example DNS Records:')}}
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal">
                                            {{ __('DNS Records')}}
                                        </button>
                                    </div>

                                    {{-- Example image Modal --}}
                                    <div class="modal modal-blur fade" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-full-width modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                <div class="modal-status bg-danger"></div>
                                                <div class="modal-body text-center py-4">
                                                    <h3 class="mb-5">{{ __('DNS Records') }}</h3>
                                                    <div id="example_status" class="text-muted">
                                                        <img src="{{ asset('img/cloudflare-rules/cloudflare-dns-settings.png') }}" alt="Cloudflare DNS Settings" class="card-img-top" />
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="w-100">
                                                        <div class="row">
                                                            <div class="col">
                                                                <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                                                    {{ __('Close') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <!-- Section 2: Creating Page Rules -->
                                <div>
                                    <h2 class="text-muted">2. {{ __('Creating Page Rules')}}</h2>
                                    <h3 class="mt-3">{{ __('Navigate to Page Rules')}}</h3>
                                    <ul>
                                        <li>{{ __('Go to the')}} <strong>{{ __('Page Rules')}}</strong> {{ __('tab on your domain\'s dashboard.')}}</li>
                                    </ul>

                                    <h3 class="mt-4">{{ __('Add a New Page Rule')}}</h3>
                                    <ul>
                                        <li>{{ __('Click')}} <strong>{{ __('Create Page Rule')}}</strong> {{ __('and configure rules as needed.')}}</li>
                                    </ul>

                                    {{-- Page Rules Modal Button --}}
                                    <div class="mb-3">
                                        {{ __('Example Page Rules:')}}
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal2">
                                            {{ __('Page Rules')}}
                                        </button>
                                    </div>

                                    {{-- Example image Modal --}}
                                    <div class="modal modal-blur fade" id="exampleModal2" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-full-width modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                <div class="modal-status bg-danger"></div>
                                                <div class="modal-body text-center py-4">
                                                    <h3 class="mb-5">{{ __('Page Rules') }}</h3>
                                                    <div id="example_status" class="text-muted">
                                                        <img src="{{ asset('img/cloudflare-rules/page-rules-tab.png') }}" alt="Page Rules Tab on Cloudflare" class="card-img-top" />
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="w-100">
                                                        <div class="row">
                                                            <div class="col">
                                                                <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                                                    {{ __('Close') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <h3>{{ __('Example Page Rule Configurations:')}}</h3>
                                    {{-- Table --}}
                                    <div class="table-responsive mb-3">
                                        <table class="table table-vcenter">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('URL')}}</th>
                                                    <th>{{ __('Setting')}}</th>
                                                    <th>{{ __('Select')}}</th>
                                                    <th>{{ __('Destination URL')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ __('example.com')}}</td>
                                                    <td>{{ __('Forwarding URL')}}</td>
                                                    <td>{{ __('301 Permanent Redirect')}}</td>
                                                    <td>{{ __('https://www.example.com')}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Redirect Modal Button --}}
                                    <div>
                                        {{ __('Example Redirect:')}}
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal3">
                                            {{ __('Redirect')}}
                                        </button>
                                    </div>

                                    {{-- Example image Modal --}}
                                    <div class="modal modal-blur fade" id="exampleModal3" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-full-width modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                <div class="modal-status bg-danger"></div>
                                                <div class="modal-body text-center py-4">
                                                    <h3 class="mb-5">{{ __('Redirect') }}</h3>
                                                    <div id="example_status" class="text-muted">
                                                        <img src="{{ asset('img/cloudflare-rules/page-rules-redirect.png') }}" alt="Page Rules Redirect" class="card-img-top" />
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="w-100">
                                                        <div class="row">
                                                            <div class="col">
                                                                <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                                                    {{ __('Close') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <h3 class="mt-4">{{ __('Always Use HTTPS')}}</h3>
                                    {{-- Table --}}
                                    <div class="table-responsive">
                                        <table class="table table-vcenter">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('URL')}}</th>
                                                    <th>{{ __('Setting')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>*example.com/*</td>
                                                    <td>{{ __('Always Use HTTPS')}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="mt-3">{{ __('Click')}} <strong>{{ __('Save and Deploy')}}</strong> {{ __('to activate the rule.')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-success mt-4" role="alert">
                        <strong>{{ __('Tip:') }}</strong> {!! __('Page rules are applied in order from top to bottom. Arrange them carefully based on priority. Use wildcards (<code>*</code>) for broader rule coverage, like <code>*example.com/*</code> to cover all pages.') !!}
                    </div>
                </div>
            </div>
            @include('user.includes.footer')
        </div>
    </div>
@endsection
