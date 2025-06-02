@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

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
                            {{ __('Rejected Custom Domains') }}
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
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table" id="custom-domain-requests-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Card/Store Name') }}</th>
                                            <th>{{ __('Current Domain') }}</th>
                                            <th>{{ __('New Domain') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th class="w-1">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.includes.footer')
    </div>

    {{-- Processed domain request --}}
    <div class="modal modal-blur fade" id="process-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9v2m0 4v.01" />
                        <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                    </svg>
                    <h3>{{ __('Are you sure?') }}</h3>
                    <div id="processed_status" class="text-secondary">
                        {{ __('If you proceed, you will process this domain.')}}
                    </div>
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
                                <a class="btn btn-danger w-100" id="process_request_id">
                                    {{ __('Yes, proceed') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Approve domain request --}}
    <div class="modal modal-blur fade" id="approve-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9v2m0 4v.01" />
                        <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                    </svg>
                    <h3>{{ __('Are you sure?') }}</h3>
                    <div id="approved_status" class="text-secondary">
                        {{ __('If you proceed, you will approve this domain.')}}
                    </div>
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
                                <a class="btn btn-danger w-100" id="approve_request_id">
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
        <script type="text/javascript">
            $(document).ready(function() {
                $('#custom-domain-requests-table').DataTable({
                    processing: false,
                    serverSide: true,
                    ajax: "{{ route('admin.rejected.custom.domain') }}",
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
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'vcard_id',
                            name: 'vcard_id'
                        },
                        {
                            data: 'previous_domain',
                            name: 'previous_domain'
                        },
                        {
                            data: 'current_domain',
                            name: 'current_domain'
                        },
                        {
                            data: 'transfer_status',
                            name: 'transfer_status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    preDrawCallback: function(settings) {
                        // Add placeholder-glow class to the table before rendering
                        $('#custom-domain-requests-table_wrapper').addClass('placeholder-glow');

                        // Check if there are rows in the tbody after draw
                        if ($('#custom-domain-requests-table tbody tr').length === 0) {
                            // If there are no rows, add 5 placeholder rows with 7 columns each
                            var placeholderRows = '';
                            for (var i = 0; i < 5; i++) {
                                placeholderRows += '<tr>' +
                                    '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'
                                    .repeat(7) + '</tr>';
                            }
                            $('#custom-domain-requests-table tbody').html(placeholderRows);
                        }
                    },
                    drawCallback: function(settings) {
                        // Remove the placeholder-glow class once the table is fully rendered
                        $('#custom-domain-requests-table_wrapper').removeClass('placeholder-glow');

                        // clear any existing placeholder rows
                        $('#custom-domain-requests-table tbody tr').each(function() {
                            var actionCell = $(this).find('td').eq(
                                6); // Targeting the 9th column (index 7)
                            if (actionCell.find('span.placeholder').length > 0) {
                                actionCell.empty(); // Clear the placeholder once data is available
                            }
                        });
                    }
                });
            });

            // Processed domain
            function processDomain(requestId) {
                "use strict";

                $("#process-modal").modal("show");
                var link = document.getElementById("process_request_id");
                link.getAttribute("href");
                link.setAttribute("href", "/admin/process-custom-domain-requests?id=" + requestId);
            }
            
            // Approve domain
            function approveDomain(requestId) {
                "use strict";

                $("#approve-modal").modal("show");
                var link = document.getElementById("approve_request_id");
                link.getAttribute("href");
                link.setAttribute("href", "/admin/approved-custom-domain-requests?id=" + requestId);
            }
        </script>
    @endsection
@endsection
