@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

@section('title', __('Marketing Group'))

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
                            {{ __('View Marketing Group') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-fluid">
                <div class="row row-cards">
                    <div class="col-sm-12 col-lg-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <!-- Group Name -->
                                <h3 class="m-0 mb-1">{{ __($group->group_name) }}</h3>
                                <div class="text-secondary">{{ __($group->group_desc) }}</div>
                                <div class="mt-3">
                                    <span class="badge bg-purple-lt">{{ __('Group') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    {{ __('Emails') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table" id="emails-table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('#') }}</th>
                                                <th>{{ __('Email') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $emails = json_decode($group->emails, true);
                                            @endphp

                                            @foreach ($emails as $email)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $email }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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

    {{-- Custom JS --}}
@section('scripts')
    <!-- Emails Table -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#emails-table').DataTable({
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
            });
        });
    </script>
@endsection
@endsection
