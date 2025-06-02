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
                            {{ __('Plans') }}
                        </h2>
                    </div>
                    <!-- Page title actions -->
                    <div class="col-auto ms-auto d-print-none">
                        <a type="button" href="{{ route('admin.add.plan') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            {{ __('Add Plan') }}
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
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table" id="plans-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Plan Name') }}</th>
                                            <th>{{ __('Plan Price') }}</th>
                                            <th>{{ __('Plan Validity') }}</th>
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

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>

    {{-- Enable / disable plan --}}
    <div class="modal modal-blur fade" id="status-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <div id="modal-message" class="text-muted"></div>
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
                                <a class="btn btn-danger w-100" id="status_id">
                                    {{ __('Yes, proceed') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete plan --}}
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
                    <div id="modal-message" class="text-muted">{{ __('If you proceed, you will delete this plan data.') }}</div>
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
                                <a class="btn btn-danger w-100" id="delete_id">
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
            $('#plans-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: "{{ route('admin.plans') }}",
                language: {
                    "sProcessing": `{{ __('Processing...') }}`,
                    "sLengthMenu": `{{ __('Show _MENU_ entries') }}`,
                    "sSearch": `{{ __('Search:') }}`,
                    "oPaginate": {
                        "sNext": `{{ __('Next') }}`,
                        "sPrevious": `{{ __('Previous') }}`
                    },
                    "sInfo": `{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}`,
                    "sInfoEmpty": `{{ __('Showing 0 to 0 of 0 entries') }}`,
                    "sInfoFiltered": `{{ __('(filtered from _MAX_ total entries)') }}`,
                    "sInfoPostFix": "",
                    "sUrl": "",
                    "oAria": {
                        "sSortAscending": `{{ __(': activate to sort column in ascending order') }}`,
                        "sSortDescending": `{{ __(': activate to sort column in descending order') }}`
                    },
                    loadingRecords: `{{ __('Please wait - loading...') }}`,
                    emptyTable: `{{ __('No data available in the table') }}` // Message for an empty table
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'plan_type',
                        name: 'plan_type'
                    },
                    {
                        data: 'plan_name',
                        name: 'plan_name'
                    },
                    {
                        data: 'plan_price',
                        name: 'plan_price'
                    },
                    {
                        data: 'validity',
                        name: 'validity'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
                    $('#plans-table_wrapper').addClass('placeholder-glow');

                    // Check if there are rows in the tbody after draw
                    if ($('#plans-table tbody tr').length === 0) {
                        // If there are no rows, add 5 placeholder rows with 7 columns each
                        var placeholderRows = '';
                        for (var i = 0; i < 5; i++) {
                            placeholderRows += '<tr>' +
                                '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'
                                .repeat(7) + '</tr>';
                        }
                        $('#plans-table tbody').html(placeholderRows);
                    }
                },
                drawCallback: function(settings) {
                    // Remove the placeholder-glow class once the table is fully rendered
                    $('#plans-table_wrapper').removeClass('placeholder-glow');

                    // clear any existing placeholder rows
                    $('#plans-table tbody tr').each(function() {
                        var actionCell = $(this).find('td').eq(
                        6); // Targeting the 9th column (index 7)
                        if (actionCell.find('span.placeholder').length > 0) {
                            actionCell.empty(); // Clear the placeholder once data is available
                        }
                    });
                }
            });
        });

        // Get plan
        function getPlan(planId, status) {
            "use strict";

            // Show modal
            $("#status-modal").modal("show");

            // Modal message
            var modalMessage = document.getElementById("modal-message");
            let messageStatus = status; // Status
            let message = `{{ __('If you proceed, this plan will be :status.', ['status' => '${messageStatus}']) }}`;
            modalMessage.innerHTML = message.replace(':status', status);

            // Status ID
            var link = document.getElementById("status_id");
            link.getAttribute("href");
            link.setAttribute("href", "/admin/status-plan?id=" + planId);
        }

        // Delete plan
        function deletePlan(planId) {
            "use strict";

            // Show modal
            $("#delete-modal").modal("show");

            // Modal link
            var link = document.getElementById("delete_id");
            link.getAttribute("href");
            link.setAttribute("href", "/admin/delete-plan?id=" + planId);
        }
    </script>
@endsection
@endsection
