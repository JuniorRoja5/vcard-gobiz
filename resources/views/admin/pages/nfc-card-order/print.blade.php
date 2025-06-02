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
                        <h2 class="page-title">
                            {{ __('Write in NFC Card') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page body -->
        <div class="page-body">
            <div class="container-fluid">
                {{-- NFC Card Order Details --}}
                <div class="row mt-3">
                    <div class="col-md-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="bg-light p-2 rounded d-flex justify-content-between align-items-center">
                                    <span
                                        class="text-truncate fw-bold">{{ route('read.nfc.card', $orderDetails['unique_key'] ?? '-') }}</span>
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="copyToClipboard('{{ route('read.nfc.card', $orderDetails['unique_key'] ?? '-') }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
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
                            {{-- Write Button --}}
                            {{-- <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-outline-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                            <path
                                                d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                        </svg>
                                        {{ __('Write') }}
                                    </button>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                    {{-- NFC Card Logo --}}
                    @if (!empty($nfcCardLogo))
                        <div class="col-md-4">
                            <div class="text-left">
                                <a href="{{ asset($nfcCardLogo) }}" class="d-block mb-2" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="{{ __('Download NFC Card Logo') }}" download>
                                    <img src="{{ asset($nfcCardLogo) }}" class="img-fluid rounded shadow-sm"
                                        style="max-width: 200px;" alt="NFC Card Logo">
                                </a>

                                {{-- Download Button --}}
                                <a href="{{ asset($nfcCardLogo) }}" class="btn btn-outline-primary"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="{{ __('Download NFC Card Logo') }}" download>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                        <polyline points="7 11 12 16 17 11" />
                                        <line x1="12" y1="4" x2="12" y2="16" />
                                    </svg>
                                    {{ __('Download') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>

    {{-- Copy clipboard --}}
    <div class="modal modal-blur fade" id="copy-clipboard" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-status bg-success"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon mb-2 text-green icon-lg">
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                        <path d="M9 12l2 2l4 -4"></path>
                    </svg>
                    <h3 id="status_id"></h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Custom JS --}}
@section('scripts')
    <script type="text/javascript">
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text);
            // Show modal
            $('#copy-clipboard').modal('show');
            // Update status
            $('#status_id').text('{{ __('Copied to clipboard!') }}');
            setTimeout(function() {
                $('#copy-clipboard').modal('hide');
            }, 2000);
        }
    </script>
@endsection
@endsection
