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
                            {{ __('Groups') }}
                        </h2>
                    </div>
                    {{-- Create Group --}}
                    <div class="col-auto">
                        <a href="{{ route('admin.marketing.groups.create') }}" class="btn btn-primary btn-sm">
                            {{ __('Create Group') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

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
                                <table class="table table-vcenter card-table" id="groups-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Emails') }}</th>
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

        <!-- Footer -->
        @include('admin.includes.footer')
    </div>

    {{-- Delete Confirmation --}}
    <div class="modal modal-blur fade" id="delete-confirmation" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <div id="delete-confirmation-text" class="text-muted">{{ __('If you proceed, you will delete this group.') }}</div>
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
                                <a class="btn btn-danger w-100" id="delete-confirmation-button">
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
            $('#groups-table').DataTable({
                processing: false, // Disable processing indicator
                serverSide: true,
                ajax: "{{ route('admin.marketing.groups') }}",
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
                    { data: 'group_name', name: 'group_name' },
                    { data: 'email_count', name: 'email_count' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' },
                ],
                preDrawCallback: function(settings) {
                    // Add placeholder-glow class to the table before rendering
                    $('#groups-table_wrapper').addClass('placeholder-glow');

                    // Check if there are rows in the tbody after draw
                    if ($('#groups-table tbody tr').length === 0) {
                        // If there are no rows, add 10 placeholder rows with 7 columns each
                        var placeholderRows = '';
                        for (var i = 0; i < 10; i++) {
                            placeholderRows += '<tr>' + '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>' .repeat(5) + '</tr>';
                        }
                        $('#groups-table tbody').html(placeholderRows);
                    }
                },
                drawCallback: function(settings) {
                    // Remove the placeholder-glow class once the table is fully rendered
                    $('#groups-table_wrapper').removeClass('placeholder-glow');

                    // clear any existing placeholder rows
                    $('#groups-table tbody tr').each(function() {
                        var actionCell = $(this).find('td').eq(4); // Targeting the 4th column (index 4)
                        if (actionCell.find('span.placeholder').length > 0) {
                            actionCell.empty(); // Clear the placeholder once data is available
                        }
                    });
                }
            });
        });
    </script>
    {{-- Delete Confirmation --}}
    <script>
        function onDelete(id) {
            $('#delete-confirmation').modal('show');
            $('#delete-confirmation-button').attr('href', '{{ route('admin.marketing.groups.delete') }}' + '?id=' + id);
        }
    </script>
@endsection