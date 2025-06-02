@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

{{-- Custom CSS --}}
@section('css')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.0.1/tinymce.min.js"
        integrity="sha512-KGtsnWohFUg0oksKq7p7eDgA1Rw2nBfqhGJn463/rGhtUY825dBqGexj8eP04LwfnsSW6dNAHAlOqKJKquHsnw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
                            {{ __('Withdrawal Request') }}
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

                {{-- Update Bank Details --}}
                <div class="row row-deck row-cards mb-4 d-print-none">
                    <div class="col-sm-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Update Bank Details') }}</h3>
                            </div>
                            <form action="{{ route('user.update.bank.details') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 col-lg-12">
                                            <div class="row">
                                                {{-- Bank Details --}}
                                                <div class="col-sm-12 col-lg-12">
                                                    <div class="form-group">
                                                        <label
                                                            class="form-label">{{ __('Bank Details / Paypal Email Address / UPI') }}</label>
                                                        <textarea name="bank_details" class="form-control" id="bank_details" rows="5">{{ $bankDetails }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row row-deck row-cards">
                    {{-- Withdrawal Request --}}
                    <div class="col-sm-12 col-lg-12">
                        <div class="card card-table">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Withdrawal Requests') }}</h3>
                                {{-- Raise new request --}}
                                <div class="col-auto ms-auto">
                                    <a href="{{ route('user.new.withdrawal.request') }}"
                                        class="btn btn-primary">{{ __('New Request') }}</a>
                                </div>
                            </div>

                            <!-- Withdrawal Request -->
                            <div class="table-responsive">
                                <table class="table table-vcenter" id="withdrawal-request-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Request ID') }} </th>
                                            <th>{{ __('Transaction ID') }}</th>
                                            <th>{{ __('Requested Amount') }} </th>
                                            <th>{{ __('Transfer To') }}</th>
                                            <th>{{ __('Notes') }}</th>
                                            <th>{{ __('Transfer Status') }}</th>
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
    <script>
        tinymce.init({
            selector: 'textarea#bank_details',
            plugins: 'code preview importcss searchreplace autolink autosave save directionality visualblocks visualchars link table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | pagebreak | link',
            toolbar_sticky: true,
            height: 200,
            menubar: false,
            statusbar: false,
            autosave_interval: '30s',
            autosave_prefix: '{path}{query}-{id}-',
            autosave_restore_when_empty: false,
            autosave_retention: '2m',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#withdrawal-request-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: "{{ route('user.referrals.withdrawal.request') }}",
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
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',                        
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'request_id',
                        name: 'request_id',
                        orderable: false
                    },
                    {
                        data: 'transfer_id',
                        name: 'transfer_id',
                        orderable: false
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'bank_details',
                        name: 'bank_details',
                        orderable: false
                    },
                    {
                        data: 'notes',
                        name: 'notes',
                        orderable: false
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                ],
                preDrawCallback: function(settings) {
                    // Add placeholder-glow class to the table before rendering
                    $('#withdrawal-request-table_wrapper').addClass('placeholder-glow');

                    // Check if there are rows in the tbody after draw
                    if ($('#withdrawal-request-table tbody tr').length === 0) {
                        // If there are no rows, add 5 placeholder rows with 6 columns each
                        var placeholderRows = '';
                        for (var i = 0; i < 5; i++) {
                            placeholderRows += '<tr>' +
                                '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'
                                .repeat(8) + '</tr>';
                        }
                        $('#withdrawal-request-table tbody').html(placeholderRows);
                    }
                },
                drawCallback: function(settings) {
                    // Remove the placeholder-glow class once the table is fully rendered
                    $('withdrawal-request-table_wrapper').removeClass('placeholder-glow');

                    // clear any existing placeholder rows                    
                    $('#withdrawal-request-table tbody tr').each(function() {
                        var actionCell = $(this).find('td').eq(
                            7); // Targeting the 9th column (index 7)
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
