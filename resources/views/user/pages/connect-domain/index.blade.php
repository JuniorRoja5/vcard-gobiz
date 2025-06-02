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
                            {{ __('Connect with Custom Domain') }}
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

                {{-- Note --}}
                <div class="alert alert-important alert-primary alert-dismissible mb-2" role="alert">
                    <div class="d-flex">
                        <div>
                            {{ __('DNS record changes can take 24 to 48 hours to propagate to your connected domain. During this time, your vCard or store may not be visible on the connected domain.') }}
                        </div>
                    </div>
                </div>

                <div class="row row-deck row-cards">
                    <div class="col-sm-12 col-lg-12">
                        {{-- Form of request --}}
                        <form class="card" action="{{ route('user.new.domain.request') }}" method="post">
                            @csrf
                            <div class="card-body">
                                <input type="hidden" name="card_id" value="{{ Request::segment(3) }}">
                                <div class="mb-3 row">
                                    <label class="col-3 col-form-label required">{{ __('Custom Domain') }}</label>
                                    <div class="col">
                                        {{-- Without http:// or https:// or www. --}}
                                        <input type="text" class="form-control" id="domain" name="domain"
                                            placeholder="example.com" required>
                                        <small class="form-hint">{{ __('Don\'t use http:// or https:// or www.') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                            </div>
                        </form>
                    </div>
                    {{-- Previous Domain list --}}
                    <div class="col-md-6 col-xl-6">
                        <div class="card card-table">
                            <div class="table-responsive">
                                {{-- In table --}}
                                <h3 class="card-title px-3 pt-3">{{ __('Connected Domain') }}</h3>
                                <table class="table table-vcenter" id="previousDomain">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Domain') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($previous_domains as $domain)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $domain->current_domain }}</strong>
                                                    <a href="http://{{ $domain->current_domain }}" target="_blank"
                                                        class="ms-1" aria-label="Open website">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="icon">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M9 15l6 -6"></path>
                                                            <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464">
                                                            </path>
                                                            <path
                                                                d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($domain->transfer_status == 0)
                                                        <span
                                                            class="badge bg-warning text-white text-white">{{ __('Processing') }}</span>
                                                    @elseif ($domain->transfer_status == 1)
                                                        <span
                                                            class="badge bg-green text-white text-white">{{ __('Connected') }}</span>
                                                    @elseif ($domain->transfer_status == 2)
                                                        <span
                                                            class="badge bg-red text-white text-white">{{ __('Disconnected') }}</span>
                                                    @else
                                                        <span
                                                            class="badge bg-red text-white text-white">{{ __('Rejected') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{-- Unlink domain --}}
                                                    <span class="dropdown">
                                                        <button class="btn small-btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">{{ __('Action') }}</button>
                                                        <div class="actions dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" onclick="UnlinkDomain(`{{ $domain->custom_domain_request_id }}`, `{{ $domain->card_id }}`)">{{ __('Unlink') }}</a>
                                                        </div>
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- Steps to Add a Custom Domain to Your vCard or Store --}}
                    <div class="col-md-6 col-xl-6">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="card-title">{{ __('Steps to Add a Custom Domain to Your vCard or Store') }}</h1>

                                <h2>1. {{ __('Access Your DNS Management') }}</h2>
                                <ul>
                                    <li>{{ __('Log in to your domain registrar’s account (e.g., GoDaddy, Namecheap, Bluehost, etc.).') }}
                                    </li>
                                    <li>{{ __('Navigate to the DNS management section for the domain you wish to connect.') }}
                                    </li>
                                </ul>

                                <h2>2. {{ __('Add a CNAME Record') }}</h2>
                                <ul>
                                    <li>{{ __('Look for an option to add a new DNS record.') }}</li>
                                    <li>{!! __('Choose <strong>CNAME</strong> as the record type.') !!}</li>
                                    <li>{{ __('Fill in the details as follows:') }}</li>
                                    <ul>
                                        <li>{!! __('<strong>Host</strong>: <code>@</code> (This represents the root domain.)') !!}</li>
                                        <li>{!! __(
                                            '<strong>Value</strong>: <code>' . str_replace(['http://', 'https://', 'www.'], '', config('app.url')) . '</code>',
                                        ) !!}</li>
                                        <li>{!! __('<strong>Proxy Status</strong>: DNS only') !!}</li>
                                        <li>{!! __('<strong>TTL</strong>: Leave it as the auto or set to your desired value.') !!}</li>
                                    </ul>
                                </ul>

                                <div class="table-responsive">
                                    <table class="mb-3">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Type') }}</th>
                                                <th>{{ __('Host') }}</th>
                                                <th>{{ __('Value') }}</th>
                                                <th>{{ __('Proxy Status') }}</th>
                                                <th>{{ __('TTL') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>CNAME</td>
                                                <td>@</td>
                                                <td>{{ str_replace(['http://', 'https://', 'www.'], '', config('app.url')) }}
                                                </td>
                                                <td>DNS only</td>
                                                <td>auto</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <p>{{ __('If you are using Cloudflare,') }} {{ __('Please refer to the following link to complete your custom domain.') }}</p>
                                <a href="{{ route('user.custom.domain.cloudflare.rules') }}" target="_blank">{{ __('Cloudflare DNS Settings') }}</a><br><br>

                                <h2>3. {{ __('Save Changes') }}</h2>
                                <ul>
                                    <li>{{ __('After adding the CNAME record, ensure to save your changes in the DNS management panel.') }}
                                    </li>
                                </ul>

                                <h2>4. {{ __('Configure Your vCard or Store') }}</h2>
                                <ul>
                                    <li>{{ __('After adding the custom domain to DNS, enter the custom domain you set up') }} {!! __('(e.g., <code>yourdomain.com</code>).',) !!}</li>
                                </ul>

                                <h2>5. {{ __('Verification') }}</h2>
                                <ul>
                                    <li>{{ __('After requesting, the custom domain will go to the website admin. They will update you by adding the custom domain name to your vCard or store. Until then, your request will be under processing. It may take some time for the website admin to handle this.') }}
                                    </li>
                                </ul>

                                <h2>{{ __('Important Note') }}</h2>
                                <p class="note">
                                    {{ __('Methods may vary by provider: The steps for adding a CNAME record may differ based on your domain provider. Some registrars might have a different interface or terminology. If you encounter any difficulties, refer to your provider’s documentation or customer support for assistance.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('user.includes.footer')
    </div>

    {{-- Unlink domain modal --}}
    <div class="modal modal-blur fade" id="unlinkDomainModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9v2m0 4v.01" />
                        <path
                            d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                    </svg>
                    <h3>{{ __('Are you sure?') }}</h3>
                    <div id="unlink_status" class="text-muted"></div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                            <div class="col">
                                <a class="btn btn-danger w-100" id="request_id">
                                    {{ __('Yes, proceed') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Custom JS --}}
@section('scripts')
    <!-- Validate domain -->
    <script>
        $('#previousDomain').DataTable({
            language: {
                "sProcessing": `{{ __("Processing...") }}`,
                "sLengthMenu": `{{ __("Show _MENU_ entries") }}`,
                "sSearch": `{{ __("Search:") }}`,
                "oPaginate": {
                    "sNext": `{{ __("Next") }}`,
                    "sPrevious": `{{ __("Previous") }}`
                },
                "sInfo": `{{ __("Showing _START_ to _END_ of _TOTAL_ entries") }}`,
                "sInfoEmpty": `{{ __("Showing 0 to 0 of 0 entries") }}`,
                "sInfoFiltered": `{{ __("(filtered from _MAX_ total entries)") }}`,
                "sInfoPostFix": "",
                "sUrl": "",
                "oAria": {
                    "sSortAscending": `{{ __(": activate to sort column in ascending order") }}`,
                    "sSortDescending": `{{ __(": activate to sort column in descending order") }}`
                },
                loadingRecords: `{{ __("Please wait - loading...") }}`,
                emptyTable: `{{ __("No data available in the table") }}` // Message for an empty table
            },
        });

        $(document).ready(function() {
            $('#domain').on('input', function() {
                var domain = $('#domain').val();
                if (domain.indexOf('http://') == 0 || domain.indexOf('https://') == 0 || domain.indexOf(
                        'www.') == 0) {
                    $('#domain').val(domain.replace('http://', '').replace('https://', '').replace('www.',
                        ''));
                }
            });
        });

        // Validate domain
        function validateDomain(domain) {
            "use strict";
            
            var pattern = /^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/;
            if (pattern.test(domain)) {
                return true;
            } else {
                return false;
            }
        }

        // Unlink domain
        function UnlinkDomain(id, card_id) {
            "use strict";

            $("#unlinkDomainModal").modal("show");
            var unlink_status = document.getElementById("unlink_status");

            let messageStatus = "unlinked";
            let message = `{{ __('If you proceed, this domain will be :status.', ['status' => '${messageStatus}']) }}`;
            unlink_status.innerHTML = message.replace(':status', status);

            var unlink_link = document.getElementById("request_id");
            unlink_link.getAttribute("href");
            unlink_link.setAttribute("href", "{{ route('user.unlink.domain') }}?id=" + id + '&card_id=' + card_id);
        }
    </script>
@endsection
@endsection
