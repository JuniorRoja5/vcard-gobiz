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
                            {{ __('Business Cards') }}
                        </h2>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="d-flex">
                            <a href="{{ route('user.choose.card.type') }}" class="btn btn-icon btn-primary">
                                <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon d-lg-none d-inline" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                <span class="d-lg-inline d-none">{{ __('Create new vcard') }}</span>
                            </a>
                        </div>
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
                            {{-- Business Cards --}}
                            <div class="table-responsive">
                                <table class="table table-vcenter display" id="businessCardsTable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th class="">{{ __('Date') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Views') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th class="w-1"></th>
                                        </tr>
                                    </thead>
                                </table>
                                <div id="cardView" class="mobile-cards p-3"></div>
                                <div class="d-flex justify-content-center">
                                    <div id="cardPagination" class="pagination-container"></div>
                                </div>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9v2m0 4v.01" />
                        <path
                            d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                    </svg>
                    <h3>{{ __('Are you sure?') }}</h3>
                    <div id="status_message" class="text-muted">
                        {{ __('If you proceed, you will enabled/disabled this card.') }}</div>
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
                    <h3 class="mb-5">{{ __('Scan QR') }}</h3>
                    <div id="status_message" class="qr-code"></div>
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

    {{-- Duplicate Card Modal --}}
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

    {{-- Custom JS --}}
@section('scripts')
    <script>
        // Business cards
        $(document).ready(function() {
            const table = $('#businessCardsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('user.cards') }}',
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
                loadingRecords: `{{ __('Please wait - loading...') }}`,
                emptyTable: `{{ __('No data available in the table') }}`
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
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'views',
                    name: 'views'
                },
                {
                    data: 'card_status',
                    name: 'card_status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            preDrawCallback: function(settings) {
                // Placeholder handling for loading state            
                const windowWidth = $(window).width();

                if (windowWidth < 768) {
                $('#businessCardsTable_wrapper').hide();
                    $('#cardView').empty().addClass('placeholder-glow');

                    // Generate placeholder cards
                    const placeholderCards = Array(10).fill().map(() => `
                        <div class="mb-3 p-3 border rounded-4 position-relative shadow-sm placeholder placeholder-xs col-12">
                            <div class="placeholder placeholder-xs col-3 position-absolute top-0 end-0"></div>
                            <div class="placeholder placeholder-xs col-2 position-absolute top-0 start-0 mt-2"></div>
                            <div class="mt-4 placeholder col-8 mb-2"></div>
                            <div class="d-flex justify-content-between">
                                <div class="placeholder col-4"></div>
                                <div class="placeholder col-3"></div>
                            </div>
                            <div class="placeholder placeholder-xs col-3 position-absolute bottom-0 start-0"></div>
                        </div>
                    `).join('');

                    $('#cardView').html(placeholderCards);
                } else {
                $('#businessCardsTable_wrapper').addClass('placeholder-glow');
                // Check if there are rows in the tbody after draw
                if ($('#businessCardsTable tbody tr').length === 0) {
                    // If there are no rows, add 10 placeholder rows with 6 columns each
                    var placeholderRows = '';
                    for (var i = 0; i < 10; i++) {
                        placeholderRows += '<tr>' +
                            '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'
                            .repeat(7) + '</tr>';
                    }
                    $('#businessCardsTable tbody').html(placeholderRows);
                }
                }
            },
            drawCallback: function(settings) {
                $('#businessCardsTable_wrapper').removeClass('placeholder-glow');
                $('#cardView').removeClass('placeholder-glow');

                // Clear any existing placeholder rows
                $('#businessCardsTable tbody tr').each(function() {
                    var actionCell = $(this).find('td').eq(6); // Targeting the 7th column (index 6)
                    if (actionCell.find('span.placeholder').length > 0) {
                        actionCell.empty(); // Clear the placeholder once data is available
                    }
                });

                // Always clear the card view first
                $('#cardView').empty();
                
                // Then call toggleCardView to populate with fresh data
                toggleCardView();
            }
            });

            // Function to toggle between Table and Card view and update pagination
            function toggleCardView() {
                const windowWidth = $(window).width();

                if (windowWidth < 768) {
                    // Switch to card view for mobile
                    $('#businessCardsTable_wrapper').hide();
                    $('#cardView').show();
                    
                    // Important: Make sure cardView is empty before adding new cards
                    $('#cardView').empty();

                    table.rows({
                        page: 'current'
                    }).every(function() {
                        const data = this.data();
                        let cardStatus = data.text_card_status;
                        let statusClass = cardStatus === 'activated' ? 'bg-success text-white' : 'bg-danger text-white';
                        let status = cardStatus === 'activated' ? `{{ __('Active') }}` : `{{ __('Inactive') }}`;
                        $('#cardView').append(`
                            <div class="mb-3 p-3 border rounded-4 position-relative shadow-sm" id="card-${data.DT_RowIndex}">
                                <p class="position-absolute top-0 end-0 bg-primary text-white px-3 py-1 fw-bold fs-4 text-capitalize" style="border-top-right-radius: var(--tblr-border-radius-xl) !important;;border-bottom-left-radius: var(--tblr-border-radius-xl) !important;;">
                                    ${$(data.type).text()}
                                </p>
                                <p class="position-absolute top-0 start-0 px-3 py-2 d-flex align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chart-spline text-muted "><path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="M7 16c.5-2 1.5-7 4-7 2 0 2 3 4 3 2.5 0 4.5-5 5-7"/></svg> <span class="mx-1">${data.views}</span></p>
                                <p class="mt-4">${data.title}</p>
                                <div class="d-flex justify-content-between align-items-center mb-3"><p class="text-muted fs-5">${$(data.created_at).text()}</p><div>${data.action}</div></div>
                                <p class="position-absolute bottom-0 start-0 px-3 py-1 fw-bold fs-4 ${statusClass}" 
                                    style="border-top-right-radius: var(--tblr-border-radius-xl) !important; 
                                            border-bottom-left-radius: var(--tblr-border-radius-xl) !important;">
                                        ${status}
                                </p>
                            </div>
                        `);
                    });

                    // Move pagination buttons to the card view container
                    $('.dataTables_paginate').appendTo('#cardPagination');
                } else {
                    // Switch back to table view on larger screens
                    $('#businessCardsTable_wrapper').show();
                    $('#cardView').hide(); // Hide card view
                }
            }

            // Run toggleCardView on page load and window resize
            $(window).resize(toggleCardView);
        });

        // Duplicate card
        function duplicateCard(id, type) {
            "use strict";

            $("#duplicateModal").modal("show");
            var duplicate_status = document.getElementById("duplicate_status");
            duplicate_status.innerHTML = "<?php echo __('If you proceed, you will duplicate this card.'); ?>"
            var duplicate_link = document.getElementById("duplicate_id");
            duplicate_link.getAttribute("href");
            duplicate_link.setAttribute("href", "{{ route('user.duplicate') }}?id=" + id + "&type=" + type);
        }

        // Delete card
        function deleteCard(id, action) {
            "use strict";

            $("#forceDeleteModal").modal("show");
            var delete_status = document.getElementById("delete_status");

            let messageStatus = action; // Status
            let message = `{{ __('If you proceed, this card will be :status.', ['status' => '${messageStatus}']) }}`;
            delete_status.innerHTML = message.replace(':status', status);

            var delete_link = document.getElementById("delete_id");
            delete_link.getAttribute("href");
            delete_link.setAttribute("href", "{{ route('user.delete.card') }}?id=" + id);
        }
    </script>
@endsection
@endsection
