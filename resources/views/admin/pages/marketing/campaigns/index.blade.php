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
                            {{ __('Campaigns') }}
                        </h2>
                    </div>
                    {{-- Create Campaign --}}
                    <div class="col-auto ms-auto d-print-none">
                        <a href="{{ route('admin.marketing.campaigns.create') }}" class="btn btn-primary">
                            {{ __('Create Campaign') }}
                        </a>
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
                            <div class="table-responsive px-2 py-2">
                                <table class="table table-vcenter card-table" id="campaigns-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th class="text-end">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>

    {{-- Activate Campaign modal --}}
    <div class="modal modal-blur fade" id="action-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <div id="action-modal-text" class="text-muted"></div>
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
                                <a class="btn btn-danger w-100" id="campaignId">
                                    {{ __('Yes, proceed') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Deactivate Campaign modal --}}
    <div class="modal modal-blur fade" id="deactivate-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <div id="deactivate-modal-text" class="text-muted"></div>
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
                                <a class="btn btn-danger w-100" id="deactivate-campaign">
                                    {{ __('Yes, proceed') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Campaign modal --}}
    <div class="modal modal-blur fade" id="delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <div id="delete-modal-text" class="text-muted"></div>
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
                                <a class="btn btn-danger w-100" id="delete-campaign">
                                    {{ __('Yes, proceed') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Custom JS --}}
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#campaigns-table').DataTable({
                processing: false, // Disable processing indicator
                serverSide: true,
                ajax: "{{ route('admin.marketing.campaigns') }}",
                language: {
                    "sProcessing": "{{ __('Processing...') }}",
                    "sLengthMenu": "{{ __('Show _MENU_ entries') }}",
                    "sSearch": "{{ __('Search:') }}",
                    "oPaginate": {
                        "sNext": "{{ __('Next') }}",
                        "sPrevious": "{{ __('Previous') }}"
                    },
                    "sInfo": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
                    "sInfoEmpty": "{{ __('Showing 0 to 0 of 0 entries') }}",
                    "sInfoFiltered": "{{ __('(filtered from _MAX_ total entries)') }}",
                    "sInfoPostFix": "",
                    "sUrl": "",
                    "oAria": {
                        "sSortAscending": "{{ __(': activate to sort column in ascending order') }}",
                        "sSortDescending": "{{ __(': activate to sort column in descending order') }}"
                    },
                    loadingRecords: "{{ __('Please wait - loading...') }}",
                    emptyTable: `{{ __('No data available in the table') }}` // Message for an empty table
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'campaign_name',
                        name: 'campaign_name'
                    },
                    {
                        data: 'campaign_desc',
                        name: 'campaign_desc'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-end'
                    },
                ],
                preDrawCallback: function(settings) {
                    // Add placeholder-glow class to the table before rendering
                    $('#campaigns-table_wrapper').addClass('placeholder-glow');

                    // Check if there are rows in the tbody after draw
                    if ($('#campaigns-table tbody tr').length === 0) {
                        // If there are no rows, add 10 placeholder rows with 4 columns each
                        var placeholderRows = '';
                        for (var i = 0; i < 10; i++) {
                            placeholderRows += '<tr>' +
                                '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'
                                .repeat(5) + '</tr>';
                        }
                        $('#campaigns-table tbody').html(placeholderRows);
                    }
                },
                drawCallback: function(settings) {
                    // Remove the placeholder-glow class once the table is fully rendered
                    $('#campaigns-table_wrapper').removeClass('placeholder-glow');

                    // clear any existing placeholder rows
                    $('#campaigns-table tbody tr').each(function() {
                        var actionCell = $(this).find('td').eq(4); // Targeting the 4th column (index 4)
                        if (actionCell.find('span.placeholder').length > 0) {
                            actionCell.empty(); // Clear the placeholder once data is available
                        }
                    });
                }
            });
        });

        // Activate Campaign
        function activateCampaign(campaignId) {
            "use strict";

            $("#deactivate-modal").modal("show");
            var deactivate_status = document.getElementById("deactivate-modal-text");
            deactivate_status.innerHTML = "{{ __('If you proceed, you will') }} " + "{{ __('activate') }}" + " {{ __('this campaign.') }}";
            var deactivateLink = document.getElementById("deactivate-campaign");
            deactivateLink.getAttribute("href");
            deactivateLink.setAttribute("href", "{{ route('admin.marketing.campaigns.status') }}?id=" + campaignId + "&mode=activate");
        }

        // Deactivate Campaign
        function deactivateCampaign(campaignId) {
            "use strict";

            $("#deactivate-modal").modal("show");
            var deactivate_status = document.getElementById("deactivate-modal-text");
            deactivate_status.innerHTML = "{{ __('If you proceed, you will') }} " + "{{ __('deactivate') }}" + " {{ __('this campaign.') }}";
            var deactivateLink = document.getElementById("deactivate-campaign");
            deactivateLink.getAttribute("href");
            deactivateLink.setAttribute("href", "{{ route('admin.marketing.campaigns.status') }}?id=" + campaignId + "&mode=deactivate");
        }

        // Delete Campaign
        function deleteCampaign(campaignId) {
            "use strict";

            $("#delete-modal").modal("show");
            var delete_status = document.getElementById("delete-modal-text");
            delete_status.innerHTML = `{{ __('If you proceed, you will') }} ` + `{{ __('delete') }}` +
                ` {{ __('this campaign.') }}`;
            var deleteLink = document.getElementById("delete-campaign");
            deleteLink.getAttribute("href");
            deleteLink.setAttribute("href", "{{ route('admin.marketing.campaigns.delete') }}?id=" + campaignId);
        }
    </script>
@endsection
