@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

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
                            {{ __('Manage NFC Cards') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page body -->
        <div class="page-body">
            <div class="container-fluid">
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

                <div class="row row-deck row-cards">
                    <div class="col-sm-12 col-lg-12">
                        <div class="card card-table">
                            <div class="table-responsive">
                                <table class="table table-vcenter" id="nfc-card-order-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('NFC Card Name') }}</th>
                                            <th>{{ __('Linked Card')}}</th>
                                            <th>{{ __('Linked Status') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Link / Unlink status modal --}}
        <div class="modal modal-blur fade" id="status-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                        <div id="action_status" class="text-secondary"></div>
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

        <!-- Footer -->
        @include('user.includes.footer')
    </div>

    {{-- Custom scripts --}}
    @section('scripts')
        {{-- Get orders --}}
        <script type="text/javascript">
            $(document).ready(function() {
                $('#nfc-card-order-table').DataTable({
                    processing: false,
                    serverSide: true,
                    ajax: "{{ route('user.manage.nfc.cards') }}",
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
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },   
                        { data: 'created_at', name: 'created_at' },
                        { data: 'nfc_card_name', name: 'nfc_card_name', orderable: false },
                        { data: 'card_id', name: 'nfc_card_name', orderable: false },
                        { data: 'link_status', name: 'linked_status' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    preDrawCallback: function(settings) {
                        // Add placeholder-glow class to the table before rendering
                        $('#nfc-card-order-table_wrapper').addClass('placeholder-glow');

                        // Check if there are rows in the tbody after draw
                        if ($('#nfc-card-order-table tbody tr').length === 0) {
                            // If there are no rows, add 5 placeholder rows with 6 columns each
                            var placeholderRows = '';
                            for (var i = 0; i < 5; i++) {
                                placeholderRows += '<tr>' +
                                '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'
                                .repeat(6) + '</tr>';
                                }
                                $('#nfc-card-order-table tbody').html(placeholderRows);
                            }
                        },
                        drawCallback: function(settings) {
                            // Remove the placeholder-glow class once the table is fully rendered
                            $('nfc-card-order-table_wrapper').removeClass('placeholder-glow');
                            
                            // clear any existing placeholder rows
                            $('#nfc-card-order-table tbody tr').each(function() {
                                var actionCell = $(this).find('td').eq(
                                5); // Targeting the 9th column (index 7)
                                if (actionCell.find('span.placeholder').length > 0) {
                                    actionCell.empty(); // Clear the placeholder once data is available
                                }
                            });
                        }
                    });
                });
            </script>

            {{-- Update status --}}
            <script type="text/javascript">
                function updateStatus(pageId, status) {
                    "use strict";

                    // Get card ID
                    
                    // Show modal
                    $("#status-modal").modal("show");

                    // Modal message
                    var modalMessage = document.getElementById("action_status");
                    let messageStatus = status; // Status
                    let message = `{{ __('If you continue, the vcard/store link will be :status', ['status' => '${messageStatus}']) }}`;
                    modalMessage.innerHTML = message.replace(':status', status);

                    // Status ID
                    var link = document.getElementById("status_id");
                    link.getAttribute("href");
                    link.setAttribute("href", "{{ route('user.action.key.generation') }}?id=" + pageId + "&status=" + status);
                }
            </script>
    @endsection
@endsection
