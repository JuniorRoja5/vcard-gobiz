@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

{{-- Custom CSS --}}
@section('css')
    <style>
        .ts-control>input {
            display: contents !important;
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
                            {{ __('Customers') }}
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
                            {{-- Customers --}}
                            <div class="table-responsive px-2 py-2">
                                <table class="table table-vcenter card-table" id="customers-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Email Verified') }}</th>
                                            <th>{{ __('Subscription Plan') }}</th>
                                            <th>{{ __('Valid Until') }}</th>
                                            <th>{{ __('Subscription Status') }}</th>
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
@endsection

{{-- Custom JS --}}
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#customers-table').DataTable({
                processing: false, // Disable processing indicator
                serverSide: true,
                ajax: "{{ route('admin.marketing.customers') }}",
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
                    emptyTable: `{{ __("No data available in the table") }}`
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'email_verified_at', name: 'email_verified_at' },
                    { data: 'subscriped_plan', name: 'subscriped_plan' },
                    { data: 'valid_until', name: 'valid_until' },
                    { data: 'subscriped_badge', name: 'subscriped_badge' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                preDrawCallback: function(settings) {
                    // Add placeholder-glow class to the table before rendering
                    $('#customers-table_wrapper').addClass('placeholder-glow');

                    // Check if there are rows in the tbody after draw
                    if ($('#customers-table tbody tr').length === 0) {
                        // If there are no rows, add 10 placeholder rows with 9 columns each
                        var placeholderRows = '';
                        for (var i = 0; i < 10; i++) {
                            placeholderRows += '<tr>' + '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>' .repeat(9) + '</tr>';
                        }
                        $('#customers-table tbody').html(placeholderRows);
                    }
                },
                drawCallback: function(settings) {
                    // Remove the placeholder-glow class once the table is fully rendered
                    $('#customers-table_wrapper').removeClass('placeholder-glow');

                    // clear any existing placeholder rows
                    $('#customers-table tbody tr').each(function() {
                        var actionCell = $(this).find('td').eq(8); // Targeting the 8th column (index 8)
                        if (actionCell.find('span.placeholder').length > 0) {
                            actionCell.empty(); // Clear the placeholder once data is available
                        }
                    });
                }
            });
        });
    </script>
@endsection
