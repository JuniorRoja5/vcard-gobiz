@extends('user.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

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
                            {{ __('Referrals') }}
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

                {{-- Referral Details --}}
                <div class="row row-deck row-cards">
                    {{-- Referral Amount --}}
                    <div class="col-sm-3 col-lg-3">
                        <div class="card mb-4">
                            <div class="card-stamp">
                                <div class="card-stamp-icon bg-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-coin">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path
                                            d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" />
                                        <path d="M12 7v10" />
                                    </svg>
                                </div>
                            </div>
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Overall Earnings') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12 col-lg-12">
                                        <h2 class="card-title">{{ $symbol }}{{ $overAllEarning }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Referral List --}}
                    <div class="col-sm-3 col-lg-3">
                        <div class="card mb-4">
                            <div class="card-stamp">
                                <div class="card-stamp-icon bg-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-coin">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path
                                            d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" />
                                        <path d="M12 7v10" />
                                    </svg>
                                </div>
                            </div>
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Current Balance') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12 col-lg-12">
                                        <h2 class="card-title">{{ $symbol }}{{ $currentBalance }}</h2>
                                        <p class="text-secondary">{{ __('Minimum withdrawal amount is ') }} {{ $symbol }}{{ $minWithdrawalAmount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Referral URL --}}
                    <div class="col-sm-6 col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Referral URL') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Referral URL --}}
                                    <div class="col-sm-12 col-lg-12">
                                        <div class="form-group">
                                            <div class="row g-2">
                                                <div class="col">
                                                    <input type="text" class="form-control" id="referral-url"
                                                        value="{{ $referralUrl }}" readonly>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-2 btn-icon"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Copy to Clipboard') }}"
                                                        data-clipboard-text="{{ $referralUrl }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-clipboard"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z">
                                                            </path>
                                                            <path
                                                                d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Social Media Share Buttons --}}
                                    @php
                                        $referralMessage =
                                            __(
                                                'Hey! I found something awesome that I think you’ll love. Check it out using my referral link: ',
                                            ) .
                                            $referralUrl .
                                            ' 🚀';
                                    @endphp

                                    <div class="col-sm-12 col-lg-12 mt-3">
                                        <div class="d-flex gap-2">
                                            {{-- Facebook --}}
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($referralUrl) }}&quote={{ urlencode($referralMessage) }}"
                                                class="btn btn-facebook btn-icon" target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-brand-facebook">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
                                                </svg>
                                            </a>
                                            {{-- Twitter --}}
                                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($referralMessage) }}"
                                                class="btn btn-x btn-icon" target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-brand-x">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 4l11.733 16h4.267l-11.733 -16z" />
                                                    <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" />
                                                </svg>
                                            </a>
                                            {{-- LinkedIn --}}
                                            <a href="https://www.linkedin.com/shareArticle?url={{ urlencode($referralUrl) }}&summary={{ urlencode($referralMessage) }}"
                                                class="btn btn-linkedin btn-icon" target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-brand-linkedin">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M8 11v5" />
                                                    <path d="M8 8v.01" />
                                                    <path d="M12 16v-5" />
                                                    <path d="M16 16v-3a2 2 0 1 0 -4 0" />
                                                    <path
                                                        d="M3 7a4 4 0 0 1 4 -4h10a4 4 0 0 1 4 4v10a4 4 0 0 1 -4 4h-10a4 4 0 0 1 -4 -4z" />
                                                </svg>
                                            </a>
                                            {{-- Instagram --}}
                                            <a href="https://www.instagram.com/share/photo?u={{ urlencode($referralMessage) }}"
                                                class="btn btn-instagram btn-icon" target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-brand-instagram">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M4 8a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z" />
                                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                    <path d="M16.5 7.5v.01" />
                                                </svg>
                                            </a>
                                            {{-- WhatsApp --}}
                                            <a href="https://wa.me/?text={{ urlencode($referralMessage) }}"
                                                class="btn btn-success btn-icon" target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-brand-whatsapp">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
                                                    <path
                                                        d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
                                                </svg>
                                            </a>
                                            {{-- Email --}}
                                            <a href="mailto:?subject=Check this out&body={{ urlencode($referralMessage) }}"
                                                class="btn btn-dark btn-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                                    <path d="M3 7l9 6l9 -6" />
                                                </svg>
                                            </a>
                                            {{-- Copy --}}
                                            <button type="button" class="btn btn-copy btn-icon" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="{{ __('Copy to Clipboard') }}"
                                                data-clipboard-text="{{ $referralMessage }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-copy">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                                                    <path
                                                        d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Referral List --}}
                <div class="row row-deck row-cards">
                    <div class="col-sm-12 col-lg-12">
                        <div class="card card-table">                            
                            <div class="table-responsive">
                                <table class="table table-vcenter" id="referral-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Registed User') }}</th>
                                            <th>{{ __('Is Registered') }}</th>
                                            <th>{{ __('Is Subscribed') }}</th>
                                            <th>{{ __('Referral Amount') }} </th>
                                            <th>{{ __('Status') }}</th>
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
        @include('user.includes.footer')
    </div>

    {{-- Custom scripts --}}
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#referral-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: "{{ route('user.referrals') }}",
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
                        data: 'user_id',
                        name: 'referral_id',
                        orderable: false
                    },
                    {
                        data: 'is_registered',
                        name: 'is_registered'
                    },
                    {
                        data: 'is_subscribed',
                        name: 'is_subscribed'
                    },
                    {
                        data: 'referral_amount',
                        name: 'referral_amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                ],
                preDrawCallback: function(settings) {
                    // Add placeholder-glow class to the table before rendering
                    $('#referral-table_wrapper').addClass('placeholder-glow');

                    // Check if there are rows in the tbody after draw
                    if ($('#referral-table tbody tr').length === 0) {
                        // If there are no rows, add 5 placeholder rows with 6 columns each
                        var placeholderRows = '';
                        for (var i = 0; i < 5; i++) {
                            placeholderRows += '<tr>' +
                                '<td class="text-center"><div class="placeholder placeholder-xs col-12"></div></td>'
                                .repeat(6) + '</tr>';
                        }
                        $('#referral-table tbody').html(placeholderRows);
                    }
                },
                drawCallback: function(settings) {
                    // Remove the placeholder-glow class once the table is fully rendered
                    $('referral-table_wrapper').removeClass('placeholder-glow');

                    // clear any existing placeholder rows
                    $('#referral-table tbody tr').each(function() {
                        var actionCell = $(this).find('td').eq(
                            5); // Targeting the 9th column (index 7)
                        if (actionCell.find('span.placeholder').length > 0) {
                            actionCell.empty(); // Clear the placeholder once data is available
                        }
                    });
                }
            });
        });
    </script>
    {{-- Copy to clipboard --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            "use strict";

            const clipboardButtons = document.querySelectorAll('[data-clipboard-text]');

            clipboardButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const text = button.getAttribute('data-clipboard-text');
                    navigator.clipboard.writeText(text).then(() => {
                        // Optional: Show a tooltip or some feedback after copying
                        const tooltip = bootstrap.Tooltip.getInstance(button) ||
                            new bootstrap.Tooltip(button);
                        tooltip.setContent({
                            '.tooltip-inner': '{{ __('Copied!') }}'
                        });
                        tooltip.show();
                        setTimeout(() => tooltip.hide(), 1000);
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                    });
                });
            });
        });
    </script>
@endsection
@endsection
