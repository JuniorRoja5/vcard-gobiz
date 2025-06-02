@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

{{-- Custom CSS --}}
@section('css')
<style>
    .span-radius {
        border-radius: 8px 0 0 8px !important; 
        padding-bottom: 14px;
    }

    .input-radius {
        border-radius: 0 8px 8px 0 !important; 
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
                            {{ __('Update Payment Method') }}
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
                        <form action="{{ route('admin.update.payment.method') }}" method="post"
                            enctype="multipart/form-data" class="card">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Payment Method Details') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            {{-- Payment Gateway ID --}}
                                            <input type="hidden" class="form-control" name="payment_gateway_id" value="{{ $gateway_details->payment_gateway_id }}">

                                            {{-- Payment Gateway Image (Upload) --}}
                                            <div class="col-md-6 col-xl-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('Payment Gateway Image') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text span-radius">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-library-photo">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                    <path
                                                                        d="M7 3m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                                                                    <path
                                                                        d="M4.012 7.26a2.005 2.005 0 0 0 -1.012 1.737v10c0 1.1 .9 2 2 2h10c.75 0 1.158 -.385 1.5 -1" />
                                                                    <path d="M17 7h.01" />
                                                                    <path
                                                                        d="M7 13l3.644 -3.644a1.21 1.21 0 0 1 1.712 0l3.644 3.644" />
                                                                    <path
                                                                        d="M15 12l1.644 -1.644a1.21 1.21 0 0 1 1.712 0l2.644 2.644" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <input type="file" class="form-control input-radius" name="payment_gateway_image" accept="image/png, image/jpeg, image/jpg, image/gif, image/svg, image/webp">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Payment Gateway Name --}}
                                            <div class="col-md-6 col-xl-6">
                                                <div class="mb-3">
                                                    <label
                                                        class="form-label required">{{ __('Payment Method Name') }}</label>
                                                    <input type="text" class="form-control" name="payment_gateway_name" autocomplete="off"
                                                        placeholder="{{ __('Payment Method') }}"
                                                        value="{{ $gateway_details->payment_gateway_name }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.includes.footer')
    </div>
@endsection
