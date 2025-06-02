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
                        {{ __('Offline Transactions') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid mt-3">
            {{-- Failed --}}
            @if(Session::has("failed"))
            <div class="alert alert-important alert-danger alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('failed')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            {{-- Success --}}
            @if(Session::has("success"))
            <div class="alert alert-important alert-success alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('success')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif
            
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter text-nowrap datatable" id="transactions-table">
                            <thead>
                                <tr>
                                    <th>{{ __('#') }}</th>
                                    <th>{{ __('Transaction Date') }}</th>
                                    <th class="w-1">{{ __('Trans ID') }}</th>
                                    <th>{{ __('Payment Trans ID') }}</th>
                                    <th>{{ __('Customer Name') }}</th>
                                    <th>{{ __('Gateway Name') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.includes.footer')
</div>

<div class="modal modal-blur fade" id="delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">{{ __('Are you sure?')}}</div>
                <div>{{ __('If you proceed with this transaction, you will have payment status success, and user chosen plan will be activated.')}}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary btn-sm me-auto" data-bs-dismiss="modal">{{
                    __('Cancel')}}</button>
                <a class="btn btn-danger btn-sm" id="transaction_id">{{ __('Yes, proceed')}}</a>
            </div>
        </div>
    </div>
</div>

{{-- Custom JS --}}
@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#transactions-table').DataTable({
            processing: false, // Disable processing indicator
            serverSide: true,
            ajax: "{{ route('admin.offline.transactions') }}",
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
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'gobiz_transaction_id', name: 'gobiz_transaction_id' },
                { data: 'transaction_id', name: 'transaction_id' },
                { data: 'user', name: 'user' },
                { data: 'payment_gateway_name', name: 'payment_gateway_name' },
                { data: 'transaction_amount', name: 'transaction_amount' },
                { data: 'payment_status', name: 'payment_status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            preDrawCallback: function(settings) {
                // Add placeholder-glow class to the table before rendering
                $('#transactions-table_wrapper').addClass('placeholder-glow');

                // Check if there are rows in the tbody after draw
                if ($('#transactions-table tbody tr').length === 0) {
                    // If there are no rows, add 10 placeholder rows with 9 columns each
                    var placeholderRows = '';
                    for (var i = 0; i < 10; i++) {
                        placeholderRows += '<tr>' + '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'.repeat(9) + '</tr>';
                    }
                    $('#transactions-table tbody').html(placeholderRows);
                }
            },
            drawCallback: function(settings) {
                // Remove the placeholder-glow class once the table is fully rendered
                $('#transactions-table_wrapper').removeClass('placeholder-glow');

                // clear any existing placeholder rows
                $('#transactions-table tbody tr').each(function() {
                    var actionCell = $(this).find('td').eq(8); // Targeting the 8th column (index 8)
                    if (actionCell.find('span.placeholder').length > 0) {
                        actionCell.empty(); // Clear the placeholder once data is available
                    }
                });
            }
        });
    });
    
    function getOfflineTransaction(transactionId) {
        "use strict";
        $("#delete-modal").modal("show");
        var link = document.getElementById("transaction_id");
        link.getAttribute("href");
        link.setAttribute("href", "/admin/offline-transaction-status/" + transactionId + "/SUCCESS");
    }
</script>
@endsection
@endsection