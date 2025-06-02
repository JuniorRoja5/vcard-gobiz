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
                            {{ __('Manage Orders') }}
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
                                            <th>{{ __('Order Date') }}</th>
                                            <th>{{ __('Activation Code') }}</th>
                                            <th>{{ __('Order ID') }}</th>
                                            <th>{{ __('Attachments') }}</th>
                                            <th>{{ __('Item') }}</th>
                                            <th>{{ __('Total') }}</th>
                                            <th>{{ __('Payment Status') }}</th>
                                            <th>{{ __('Delivery Status') }}</th>
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
                    ajax: "{{ route('user.manage.nfc.orders') }}",
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
                        { data: 'order_details', name: 'order_details' },
                        { data: 'nfc_card_order_id', name: 'nfc_card_order_id' },
                        { data: 'nfc_card_logo', name: 'nfc_card_logo' },
                        { data: 'nfc_card_name', name: 'nfc_card_name' },
                        { data: 'nfc_card_price', name: 'nfc_card_price' },
                        { data: 'payment_status', name: 'payment_status' },
                        { data: 'delivery_status', name: 'delivery_status' },
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
                                .repeat(10) + '</tr>';
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
                                9); // Targeting the 9th column (index 7)
                                if (actionCell.find('span.placeholder').length > 0) {
                                    actionCell.empty(); // Clear the placeholder once data is available
                                }
                            });
                        }
                    });
                });
            </script>
    @endsection
@endsection
