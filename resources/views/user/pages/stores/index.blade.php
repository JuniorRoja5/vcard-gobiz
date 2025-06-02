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
                        {{ __('WhatsApp Stores') }}
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <span class="d-sm-inline">
                        <a href="{{ route('user.categories') }}" class="btn btn-icon">
                            <svg class="d-lg-none d-inline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-category">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 4h6v6h-6z" />
                                <path d="M14 4h6v6h-6z" />
                                <path d="M4 14h6v6h-6z" />
                                <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            </svg>
                            <span class="d-lg-inline d-none">{{ __('All Categories') }}</span>
                        </a>
                    </span>
                    <a href="{{ route('user.create.store') }}" class="btn btn-icon btn-primary">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon d-lg-none d-inline" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        <span class="d-lg-inline d-none">{{ __('Create new store') }}</span>
                    </a>
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
                <div class="col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table" id="storesTable">
                                <thead>
                                    <tr>
                                        <th>{{ __('#') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Views') }}</th>
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
    @include('user.includes.footer')
</div>

{{-- Delete Modal --}}
<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <div class="text-muted">{{ __('If you proceed, you will enabled/disabled this card.')}}</div>
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
                            <a class="btn btn-danger w-100" id="plan_id">
                                {{ __('Yes, proceed') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Open QR Modal --}}
<div class="modal modal-blur fade" id="openQR" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <h3 class="mb-5">{{ __('Scan QR')}}</h3>
                <div id="status_message" class="qr-code"></div>
            </div>
        </div>
    </div>
</div>

{{-- Duplicate Store Modal --}}
<div class="modal modal-blur fade" id="duplicateModal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <div id="duplicate_status" class="text-muted"></div>
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
                            <a class="btn btn-danger w-100" id="duplicate_id">
                                {{ __('Yes, proceed') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete --}}
<div class="modal modal-blur fade" id="forceDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
<script>
    $(document).ready(function() {
        $('#storesTable').DataTable({
            processing: false,
            serverSide: true,
            ajax: '{{ route('user.stores') }}',
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
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'title', name: 'title' },
                { data: 'views', name: 'views' },
                { data: 'card_status', name: 'card_status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            preDrawCallback: function(settings) {
                // Add placeholder-glow class to the table before rendering
                $('#storesTable_wrapper').addClass('placeholder-glow');

                // Check if there are rows in the tbody after draw
                if ($('#storesTable tbody tr').length === 0) {
                    // If there are no rows, add 10 placeholder rows with 6 columns each
                    var placeholderRows = '';
                    for (var i = 0; i < 10; i++) {
                        placeholderRows += '<tr>' + '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'.repeat(6) + '</tr>';
                    }
                    $('#storesTable tbody').html(placeholderRows);
                }
            },
            drawCallback: function(settings) {
                // Remove the placeholder-glow class once the table is fully rendered
                $('#storesTable_wrapper').removeClass('placeholder-glow');

                // clear any existing placeholder rows
                $('#storesTable tbody tr').each(function() {
                    var actionCell = $(this).find('td').eq(5); // Targeting the 6th column (index 5)
                    if (actionCell.find('span.placeholder').length > 0) {
                        actionCell.empty(); // Clear the placeholder once data is available
                    }
                });
            }
        });
    });

    // Duplicate store
    function duplicateStore(id, type) {
        "use strict";

        $("#duplicateModal").modal("show");
        var duplicate_status = document.getElementById("duplicate_status");
        duplicate_status.innerHTML = "<?php echo __('If you proceed, you will duplicate this store.') ?>"
        var duplicate_link = document.getElementById("duplicate_id");
        duplicate_link.getAttribute("href");
        duplicate_link.setAttribute("href", "{{ route('user.duplicate') }}?id=" + id + "&type=" + type);
    }

    // Delete store
    function deleteStore(id, action) {
        "use strict";

        $("#forceDeleteModal").modal("show");
        var delete_status = document.getElementById("delete_status");

        let messageStatus = action; // Status
        let message = `{{ __('If you proceed, this store will be :status.', ['status' => '${messageStatus}']) }}`;
        delete_status.innerHTML = message.replace(':status', status);

        var delete_link = document.getElementById("delete_id");
        delete_link.getAttribute("href");
        delete_link.setAttribute("href", "{{ route('user.delete.store') }}?id=" + id);
    }
</script>
@endsection
@endsection