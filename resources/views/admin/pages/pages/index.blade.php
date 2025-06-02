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

            <div class="row row-deck row-cards">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('Static Pages') }}
                    </h2>
                </div>
                <span class="text-muted font-weight-bold">{{ __("Note: Static pages are doesn't have HTML editor. You can able to change the contents only.")}}</span>

                {{-- Static Pages --}}
                <div class="col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table" id="pages-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('#') }}</th>
                                        <th>{{ __('Page') }}</th>
                                        <th>{{ __('Slug') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th class="w-1">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Custom Pages --}}
                <div class="col mt-4">
                    <h2 class="page-title">
                        {{ __('Custom Pages') }}
                    </h2>
                </div>
                <!-- Add page -->
                <div class="col-auto ms-auto d-print-none mt-4">
                    <a type="button" href="{{ route('admin.add.page') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        {{ __('Add New Page') }}
                    </a>
                </div>
                <div class="col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table" id="custom-pages-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('#') }}</th>
                                        <th>{{ __('Page') }}</th>
                                        <th>{{ __('Slug') }}</th>
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

{{-- Enable/Disable Page Modal --}}
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
                <div id="disable_status" class="text-muted"></div>
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
                            <a class="btn btn-danger w-100" id="page_id">
                                {{ __('Yes, proceed') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Page Modal --}}
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
                <div id="delete_status" class="text-muted"></div>
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
                            <a class="btn btn-danger w-100" id="delete_qr_code_id">
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
{{-- Static pages --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('#pages-table').DataTable({
            processing: false,
            serverSide: true,
            ajax: "{{ route('admin.pages') }}",
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
                { data: 'page_name', name: 'page_name' },
                { data: 'url', name: 'url', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            preDrawCallback: function(settings) {
                // Add placeholder-glow class to the table before rendering
                $('#pages-table_wrapper').addClass('placeholder-glow');

                // Check if there are rows in the tbody after draw
                if ($('#pages-table tbody tr').length === 0) {
                    // If there are no rows, add 10 placeholder rows with 5 columns each
                    var placeholderRows = '';
                    for (var i = 0; i < 10; i++) {
                        placeholderRows += '<tr>' + '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'.repeat(5) + '</tr>';
                    }
                    $('#pages-table tbody').html(placeholderRows);
                }
            },
            drawCallback: function(settings) {
                // Remove the placeholder-glow class once the table is fully rendered
                $('#pages-table_wrapper').removeClass('placeholder-glow');

                // clear any existing placeholder rows
                $('#pages-table tbody tr').each(function() {
                    var actionCell = $(this).find('td').eq(4); // Targeting the 4th column (index 4)
                    if (actionCell.find('span.placeholder').length > 0) {
                        actionCell.empty(); // Clear the placeholder once data is available
                    }
                });
            }
        });
    });
    
    function getDisablePage(pageName, status) {
        "use strict";

        $("#status-modal").modal("show");

        var disable_status = document.getElementById("disable_status");
        let messageStatus = status; // Status
        let message = `{{ __('If you proceed, this page will be :status.', ['status' => '${messageStatus}']) }}`;
        disable_status.innerHTML = message.replace(':status', status);

        var link = document.getElementById("page_id");
        link.getAttribute("href");
        link.setAttribute("href", "{{ route('admin.disable.page') }}?id=" + pageName);
    }
</script>

{{-- Custom pages --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('#custom-pages-table').DataTable({
            processing: false,
            serverSide: true,
            ajax: "{{ route('admin.custom.pages') }}",
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
                { data: 'section_name', name: 'section_name' },
                { data: 'url', name: 'url', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            preDrawCallback: function(settings) {
                // Add placeholder-glow class to the table before rendering
                $('#custom-pages-table_wrapper').addClass('placeholder-glow');

                // Check if there are rows in the tbody after draw
                if ($('#custom-pages-table tbody tr').length === 0) {
                    // If there are no rows, add 5 placeholder rows with 5 columns each
                    var placeholderRows = '';
                    for (var i = 0; i < 5; i++) {
                        placeholderRows += '<tr>' + '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'.repeat(5) + '</tr>';
                    }
                    $('#custom-pages-table tbody').html(placeholderRows);
                }
            },
            drawCallback: function(settings) {
                // Remove the placeholder-glow class once the table is fully rendered
                $('#custom-pages-table_wrapper').removeClass('placeholder-glow');

                // clear any existing placeholder rows
                $('#custom-pages-table tbody tr').each(function() {
                    var actionCell = $(this).find('td').eq(4); // Targeting the 4th column (index 4)
                    if (actionCell.find('span.placeholder').length > 0) {
                        actionCell.empty(); // Clear the placeholder once data is available
                    }
                });
            }
        });
    });
    
    function getPage(pageId, status) {
        "use strict";

        $("#status-modal").modal("show");

        var disable_status = document.getElementById("disable_status");
        let messageStatus = status; // Status
        let message = `{{ __('If you proceed, this page will be :status.', ['status' => '${messageStatus}']) }}`;
        disable_status.innerHTML = message.replace(':status', status);
        
        var link = document.getElementById("page_id");
        link.getAttribute("href");
        link.setAttribute("href", "{{ route('admin.status.page') }}?id=" + pageId);
    }
    
    function deletePage(pageId, status) {
        "use strict";

        $("#delete-modal").modal("show");

        var delete_status = document.getElementById("delete_status");
        let messageStatus = status; // Status
        let message = `{{ __('If you proceed, this page will be :status.', ['status' => '${messageStatus}']) }}`;
        delete_status.innerHTML = message.replace(':status', status);

        var delete_link = document.getElementById("delete_qr_code_id");
        delete_link.getAttribute("href");
        delete_link.setAttribute("href", "{{ route('admin.delete.page') }}?id=" + pageId);
    }
</script>
@endsection
@endsection