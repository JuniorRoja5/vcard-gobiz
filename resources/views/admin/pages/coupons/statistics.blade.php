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
                            {{ __('Coupon') }}
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
                        <div class="card">
                            {{-- Transactions --}}
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table" id="transactions-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Transaction ID') }}</th>
                                            <th>{{ __('Customer Name') }}</th>
                                            <th>{{ __('Before Discount') }}</th>
                                            <th>{{ __('After Discount') }}</th>
                                            <th>{{ __('Saved') }}</th>
                                        </tr>
                                    </thead>
                                    {{-- Transactions --}}
                                    <tbody>
                                        @foreach ($couponUsage as $transaction)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if ($transaction->used_for == 'plan')
                                                        <a
                                                            href="{{ route('admin.view.invoice', $transaction->transaction_id) }}" target="_blank" rel="noopener noreferrer">
                                                            {{ $transaction->transaction_id }}
                                                        </a>
                                                    @else
                                                        <a href="#" target="_blank" rel="noopener noreferrer">
                                                            {{ $transaction->transaction_id }}
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($transaction->used_for == 'plan')
                                                        <a href="{{ route('admin.view.user', json_decode($transaction->user)->user_id) }}" target="_blank" rel="noopener noreferrer">
                                                            {{ json_decode($transaction->user)->name }}
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.view.user', json_decode($transaction->user)->user_id) }}" target="_blank" rel="noopener noreferrer">
                                                            {{ json_decode($transaction->user)->name }}
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="fw-bold">
                                                    @if ($transaction->transactions->isNotEmpty())
                                                        @php
                                                            $invoiceDetails = json_decode($transaction->transactions->first()->invoice_details);
                                                        @endphp
                                                        @if ($transaction->used_for == 'plan')
                                                            {{ $symbol }}{{ $invoiceDetails->subtotal ?? 'N/A' }}
                                                        @else
                                                            {{ $symbol }}{{ $invoiceDetails->subtotal ?? 'N/A' }}
                                                        @endif
                                                    @else
                                                        <span>{{ __('0') }}</span>
                                                    @endif
                                                </td>
                                                <td class="fw-bold">
                                                    @if ($transaction->transactions->isNotEmpty())
                                                        @php
                                                            $invoiceDetails = json_decode($transaction->transactions->first()->invoice_details);
                                                        @endphp
                                                        {{ $symbol }}{{ $invoiceDetails->invoice_amount ?? 'N/A' }}
                                                    @else
                                                        <span>{{ __('0') }}</span>
                                                    @endif
                                                </td>
                                                <th>
                                                    <span class="badge bg-success text-white">
                                                        @if ($transaction->transactions->isNotEmpty())
                                                            @php
                                                                $transactionDetails = json_decode($transaction->transactions->first()->invoice_details);
                                                                $subtotal = $transactionDetails->subtotal ?? 0;
                                                                $invoiceAmount = $transactionDetails->invoice_amount ?? 0;
                                                            @endphp
                                                            {{ $symbol }}{{ $subtotal - $invoiceAmount }}
                                                        @else
                                                            <span>{{ __('0') }}</span>
                                                        @endif
                                                    </span>
                                                </th>
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

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>

    {{-- Custom scripts --}}
    @section('scripts')
        <script type="text/javascript">
            $(document).ready(function() {
                $('#transactions-table').DataTable();
            });
        </script>
    @endsection
@endsection
