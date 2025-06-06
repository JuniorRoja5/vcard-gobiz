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
                            {{ __('Offline Checkout') }}
                        </h2>
                        <small class="text-muted mt-2 mb-2">{{ __('Note: Do Page Refresh or back button.') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-fluid mt-3">
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
                    <div class="col-sm-6 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('nfc.mark.payment.payment') }}" method="post">
                                    @csrf
                                    <h3 class="card-title">{{ __('NFC Card Name : ') }}{{ $nfcDetails->nfc_card_name }}</h3>
                                    <input type="hidden" value="{{ $nfcDetails->nfc_card_id }}" name="nfc_id">
                                    <input type="hidden" value="{{ $couponId }}" name="coupon_id">
                                    <div class="col-md-10 col-xl-10">
                                        <div class="mb-3">
                                            <label class="form-label required">{{ __('Payment Details') }}</label>
                                            <input type="text" class="form-control" name="transaction_id"
                                                placeholder="{{ __('Payment Details') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-6 my-3">
                                        <div class="mb-3">
                                            <button type="submit"
                                                class="btn btn-primary">{{ __('Submit') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">{{ __('Bank Details') }}</h3>
                                <pre>{!! $config[31]->config_value !!}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        @include('user.includes.footer')
    </div>
@endsection
