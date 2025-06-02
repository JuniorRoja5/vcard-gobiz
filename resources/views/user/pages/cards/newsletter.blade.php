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
                            {{ __('Subscribed Newsletter') }}
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
                        <div class="card card-table">
                            <div class="table-responsive">
                                <table class="table table-vcenter" id="emailSubscribed">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Email') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subscripedEmails as $subscripedEmail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $subscripedEmail->created_at->diffForHumans() }}</td>
                                                <td><a
                                                        href="mailto:{{ $subscripedEmail->email }}">{{ $subscripedEmail->email }}</a>
                                                </td>
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
        @include('user.includes.footer')
    </div>

    {{-- Custom CSS --}}
    @push('custom-js')
        <!-- Buttons for DataTables -->
        <script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('js/jszip.min.js') }}"></script>
        <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#emailSubscribed').DataTable({
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
                    dom: 'Bfrtip', // Enables buttons at the top of the table
                    buttons: [{
                        extend: 'excelHtml5', // Excel export functionality
                        text: 'Download Excel', // Button text
                        className: 'btn btn-primary btn-sm mx-4 mt-4', // Button styling
                        title: '{{ __('Subscribed Newsletter Emails') }}', // Button title
                        exportOptions: {
                            columns: [0, 2], // Exports only the "Email" column (zero-based index)
                        }
                    }]
                });
            });
        </script>
    @endpush
@endsection
