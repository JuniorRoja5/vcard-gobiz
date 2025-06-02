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
                        {{ __('Transactions') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
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
                                    <th class="w-1">{{ __('Payment ID') }}</th>
                                    <th>{{ __('Trans ID') }}</th>
                                    <th>{{ __('Payment Mode') }}</th>
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
    @include('user.includes.footer')
</div>

{{-- Custom JS --}}
@section('scripts')
<script>
    $(document).ready(function() {
    var table = $('#transactions-table').DataTable({
        processing: false, // Keep it enabled for server-side loading
        serverSide: true,
        ajax: {
            url: "{{ route('user.transactions') }}",
            beforeSend: function() {
                // Show a custom loader only on first load
                if ($('#transactions-table tbody').is(':empty')) {
                }
            }
        },
        language: {
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
            emptyTable: `{{ __("No data available in the table") }}`
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'gobiz_transaction_id', name: 'gobiz_transaction_id' },
            { data: 'transaction_id', name: 'transaction_id' },
            { data: 'payment_gateway_name', name: 'payment_gateway_name' },
            { data: 'transaction_amount', name: 'transaction_amount' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' },
        ],
        preDrawCallback: function(settings) {
            // Add placeholder-glow before making an AJAX call
            $('#transactions-table_wrapper').addClass('placeholder-glow');

            // Generate 10 placeholder rows with 8 columns each
            var placeholderRows = '';
            for (var i = 0; i < 10; i++) {
                placeholderRows += '<tr>' + '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'.repeat(8) + '</tr>';
            }

            // Insert placeholders before the new data loads
            $('#transactions-table tbody').html(placeholderRows);
        },
        drawCallback: function(settings) {
            // Remove placeholder-glow once the table is fully rendered
            $('#transactions-table_wrapper').removeClass('placeholder-glow');

            // Clear placeholders once real data is available
            $('#transactions-table tbody tr').each(function() {
                var actionCell = $(this).find('td').eq(7); // Targeting the 8th column (index 7)
                if (actionCell.find('.placeholder').length > 0) {
                    actionCell.empty(); // Remove placeholder from action cell
                }
            });
        }
    });
});
</script>
@endsection
@endsection