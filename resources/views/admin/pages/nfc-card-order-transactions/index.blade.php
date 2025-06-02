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
                        {{ __('NFC Card Order Transactions') }}
                    </h2> 
                </div>
                <div class="col-auto ms-auto">
                    {{-- Tutorials --}}
                    <a href="https://www.youtube.com/watch?v=GlGgUGe8o9o" target="_blank" class="btn btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-video">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" />
                            <path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" />
                        </svg>
                        {{ __('Tutorial') }}
                    </a>
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
                    <div class="card">
                        <div class="table-responsive px-2 py-2">
                            <table class="table table-vcenter card-table" id="nfc-card-order-transactions-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('#') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Unique Transaction ID')}}</th>
                                        <th>{{ __('Payment ID') }}</th>
                                        <th>{{ __('Order ID') }}</th>
                                        <th>{{ __('Customer') }}</th>
                                        <th>{{ __('Payment Method') }}</th>
                                        <th>{{ __('Total') }}</th>
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

    {{-- Status modal --}}
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
    @include('admin.includes.footer')
</div>

{{-- Custom scripts --}}
@section('scripts')
    {{-- Get transactions --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#nfc-card-order-transactions-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: "{{ route('admin.transactions') }}",
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
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'nfc_card_order_transaction_id', name: 'nfc_card_order_transaction_id' },
                    { data: 'payment_transaction_id', name: 'payment_transaction_id' },
                    { data: 'nfc_card_order_id', name: 'nfc_card_order_id' },
                    { data: 'user_id', name: 'user_id' },
                    { data: 'payment_method', name: 'payment_method' },
                    { data: 'amount', name: 'amount' },
                    { data: 'payment_status', name: 'payment_status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                preDrawCallback: function(settings) {
                    // Add placeholder-glow class to the table before rendering
                    $('#nfc-card-order-transactions-table_wrapper').addClass('placeholder-glow');
                    
                    // Check if there are rows in the tbody after draw
                    if ($('#nfc-card-order-transactions-table tbody tr').length === 0) {
                        // If there are no rows, add 5 placeholder rows with 7 columns each
                        var placeholderRows = '';
                        for (var i = 0; i < 5; i++) {
                            placeholderRows += '<tr>' +
                                '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'
                                .repeat(10) + '</tr>';
                        }
                        $('#nfc-card-order-transactions-table tbody').html(placeholderRows);
                    }
                },
                drawCallback: function(settings) {
                    // Remove the placeholder-glow class once the table is fully rendered
                    $('nfc-card-order-transactions-table_wrapper').removeClass('placeholder-glow');
                    
                    // clear any existing placeholder rows
                    $('#nfc-card-order-transactions-table tbody tr').each(function() {
                        var actionCell = $(this).find('td').eq(
                        9); // Targeting the 9th column (index 7)
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
            
            // Show modal
            $("#status-modal").modal("show");

            // Modal message
            var modalMessage = document.getElementById("action_status");
            let messageStatus = status; // Status
            let message = `{{ __('If you proceed, this transaction will be :status.', ['status' => '${messageStatus}']) }}`;
            modalMessage.innerHTML = message.replace(':status', status);

            // Status ID
            var link = document.getElementById("status_id");
            link.getAttribute("href");
            link.setAttribute("href", "{{ route('admin.action.transaction') }}?id=" + pageId + "&status=" + status);
        }
    </script>
@endsection
@endsection
