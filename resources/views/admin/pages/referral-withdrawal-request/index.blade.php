@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <!-- Page title -->
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                            {{ __('Overview') }}
                        </div>
                        <h2 class="page-title">
                            {{ __('Withdraw Requests') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page body -->
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
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table" id="requests-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Customer') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Transfer To') }}</th>
                                            <th>{{ __('Payment Status') }}</th>
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


        {{-- Withdrawal Request Modal --}}
        <div class="modal modal-blur fade" id="withdrawal-request-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                        <div id="withdrawal-request-status" class="text-secondary"></div>
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
                                    <a class="btn btn-danger w-100" id="withdrawal-request-id">
                                        {{ __('Yes, proceed') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transfer modal --}}
        <div class="modal modal-blur fade" id="transfer-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Transfer Details') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.transfer.withdrawal.request') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12 col-lg-12">
                                    {{-- Request ID --}}
                                    <input type="hidden" name="request_id" id="request_id" value>

                                    {{-- Transaction ID --}}
                                    <div class="form-group mb-3">
                                        <label class="form-label required">{{ __('Transaction ID') }}</label>
                                        <input type="text" class="form-control" id="transfer-transaction-id" name="transfer_transaction_id"
                                            placeholder="Enter the payement transaction ID" required>
                                    </div>

                                    {{-- Notes --}}
                                    <div class="form-group">
                                        <label class="form-label required">{{ __('Notes') }}</label>
                                        <textarea class="form-control" id="transfer-notes" name="transfer_notes" rows="3" placeholder="Enter the notes"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="btn btn-danger">
                                {{ __('Transfer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>

    {{-- Custom scripts --}}
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#requests-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: "{{ route('admin.referral.withdrawal.request') }}",
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
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'bank_details',
                        name: 'bank_details'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ],
                preDrawCallback: function(settings) {
                    // Add placeholder-glow class to the table before rendering
                    $('#referrals-table_wrapper').addClass('placeholder-glow');

                    // Check if there are rows in the tbody after draw
                    if ($('#referrals-table tbody tr').length === 0) {
                        // If there are no rows, add 5 placeholder rows with 7 columns each
                        var placeholderRows = '';
                        for (var i = 0; i < 5; i++) {
                            placeholderRows += '<tr>' +
                                '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'
                                .repeat(7) + '</tr>';
                        }
                        $('#referrals-table tbody').html(placeholderRows);
                    }
                },
                drawCallback: function(settings) {
                    // Remove the placeholder-glow class once the table is fully rendered
                    $('#referrals-table_wrapper').removeClass('placeholder-glow');

                    // clear any existing placeholder rows                
                    $('#referrals-table tbody tr').each(function() {
                        var actionCell = $(this).find('td').eq(
                            6); // Targeting the 9th column (index 7)
                        if (actionCell.find('span.placeholder').length > 0) {
                            actionCell.empty(); // Clear the placeholder once data is available
                        }
                    });
                }
            });
        });
    </script>
    {{-- Withdrawal Request Modal --}}
    <script>
        function updateWithdrawalRequest(requestId, status) {
            "use strict";

            // Modal message
            var modalMessage = document.getElementById("withdrawal-request-status");
            let messageStatus = '{{ __('Accepted') }}'; // Status
            if (status == 2) {
                messageStatus = '{{ __('Transfer') }}';
            } else if (status == -1) {
                messageStatus = '{{ __('Rejected') }}';
            }
            let message =
                `{{ __('If you proceed, this withdrawal request will be :status.', ['status' => '${messageStatus}']) }}`;
            modalMessage.innerHTML = message.replace(':status', status);

            // Status ID
            var link = document.getElementById("withdrawal-request-id");
            link.getAttribute("href");

            // Check status
            if (status == 1) {
                // Show modal
                $("#withdrawal-request-modal").modal("show");

                link.setAttribute("href", "/admin/update-withdrawal-request-status?requestId=" + requestId + "&status=accepted");
            } else if (status == 2) {
                // Open transfer modal
                $("#transfer-modal").modal("show");

                // Set request ID
                document.getElementById("request_id").value = requestId;
            } else {
                // Show modal
                $("#withdrawal-request-modal").modal("show");

                link.setAttribute("href", "/admin/update-withdrawal-request-status?requestId=" + requestId + "&status=rejected");
            }
        }
    </script>
@endsection
@endsection
