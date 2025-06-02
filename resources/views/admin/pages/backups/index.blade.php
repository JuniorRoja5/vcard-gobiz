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
                            {{ __('Backups') }}
                        </h2>
                    </div>
                    {{-- Notes --}}
                    <span class="fw-bold"><span class="text-primary">{{ __('Note') }}</span>:
                        {{ __('Backups are stored in the') }} <code>storage/app/backups</code> {{ __('folder.') }}
                    </span>
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
                    {{-- Files Backups --}}
                    <div class="col-sm-12 col-lg-12">
                        {{-- All Backups --}}
                        <div class="card">
                            <div class="card-header">
                                <h4 class="page-title">{{ __('File Backups') }}</h4>
                                <div class="card-actions">
                                    <a href="{{ route('admin.create.file.backup') }}" class="btn btn-primary btn-sm ms-2">
                                        {{ __('Create Backup') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-striped table-bordered" id="files-backups-table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('ID') }}</th>
                                                            <th>{{ __('Version') }}</th>
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
                        </div>
                    </div>

                    {{-- Database Backups --}}
                    <div class="col-sm-12 col-lg-12">
                        {{-- All Backups --}}
                        <div class="card">
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Database Backups') }}</h4>
                                <div class="card-actions">
                                    <a href="{{ route('admin.create.database.backup') }}" class="btn btn-primary btn-sm ms-2">
                                        {{ __('Create Backup') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-striped table-bordered" id="database-backups-table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('ID') }}</th>
                                                            <th>{{ __('Version') }}</th>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>

    {{-- Delete modal --}}
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
                    <div id="delete-modal-text" class="text-secondary"></div>
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
                                <a class="btn btn-danger w-100" id="delete-backup">
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
            // Files Backups
            $('#files-backups-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: "{{ route('admin.backups') }}",
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
                    { data: 'version', name: 'version' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                preDrawCallback: function(settings) {
                    // Add placeholder-glow class to the table before rendering
                    $('#files-backups-table_wrapper').addClass('placeholder-glow');

                    // Check if there are rows in the tbody after draw
                    if ($('#files-backups-table tbody tr').length === 0) {
                        // If there are no rows, add 10 placeholder rows with 4 columns each
                        var placeholderRows = '';
                        for (var i = 0; i < 10; i++) {
                            placeholderRows += '<tr>' +
                                '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'
                                .repeat(4) + '</tr>';
                        }
                        $('#files-backups-table tbody').html(placeholderRows);
                    }
                },
                drawCallback: function(settings) {
                    // Remove the placeholder-glow class once the table is fully rendered
                    $('#files-backups-table_wrapper').removeClass('placeholder-glow');

                    // clear any existing placeholder rows
                    $('#files-backups-table tbody tr').each(function() {
                        var actionCell = $(this).find('td').eq(
                            3); // Targeting the 9th column (index 4)
                        if (actionCell.find('span.placeholder').length > 0) {
                            actionCell.empty(); // Clear the placeholder once data is available
                        }
                    });
                }
            });

            // Database backups
            $('#database-backups-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: "{{ route('admin.get.database.backup') }}",
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
                    { data: 'version', name: 'version' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                preDrawCallback: function(settings) {
                    // Add placeholder-glow class to the table before rendering
                    $('#database-backups-table_wrapper').addClass('placeholder-glow');

                    // Check if there are rows in the tbody after draw
                    if ($('#database-backups-table tbody tr').length === 0) {
                        // If there are no rows, add 10 placeholder rows with 4 columns each
                        var placeholderRows = '';
                        for (var i = 0; i < 10; i++) {
                            placeholderRows += '<tr>' +
                                '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'
                                .repeat(4) + '</tr>';
                        }
                        $('#database-backups-table tbody').html(placeholderRows);
                    }
                },
                drawCallback: function(settings) {
                    // Remove the placeholder-glow class once the table is fully rendered
                    $('#database-backups-table_wrapper').removeClass('placeholder-glow');

                    // clear any existing placeholder rows
                    $('#database-backups-table tbody tr').each(function() {
                        var actionCell = $(this).find('td').eq(
                            3); // Targeting the 9th column (index 4)
                        if (actionCell.find('span.placeholder').length > 0) {
                            actionCell.empty(); // Clear the placeholder once data is available
                        }
                    });
                }
            });
        });

        // Delete Backup
        function deleteBackup(backupId) {
            "use strict";

            $("#delete-modal").modal("show");
            var delete_status = document.getElementById("delete-modal-text");
            delete_status.innerHTML = `{{ __('If you proceed, you will') }} ` + `{{ __('delete') }}` +
                ` {{ __('this backup.') }}`;
            var deleteLink = document.getElementById("delete-backup");
            deleteLink.getAttribute("href");
            deleteLink.setAttribute("href", "{{ route('admin.backup.delete') }}?id=" + backupId);
            deleteLink.setAttribute("onclick", "");
            deleteLink.setAttribute("class", "btn btn-danger btn-4 w-100");
            deleteLink.innerHTML = "<?php echo __('Delete'); ?>";
            deleteLink.classList.remove("btn-primary");
        }
    </script>
@endsection
@endsection
