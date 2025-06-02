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
                            {{ __('Create NFC Card Design') }}
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
    
                <div class="row row-deck row-cards">
                    <div class="col-sm-12 col-lg-12">
                        <div class="card">
                            <form action="{{ route('admin.save.design') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Front Image --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="nfc_card_front_image" class="form-label required">{{ __('Front Image') }}</label>
                                                <input type="file" name="nfc_card_front_image" id="nfc_card_front_image" class="form-control" required>
                                            </div>
                                        </div>

                                        {{-- Back Image --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="nfc_card_back_image" class="form-label required">{{ __('Back Image') }}</label>
                                                <input type="file" name="nfc_card_back_image" id="nfc_card_back_image" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        {{-- Name --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="nfc_card_name" class="form-label required">{{ __('Name') }}</label>
                                                <input type="text" name="nfc_card_name" id="nfc_card_name" class="form-control text-capitalize" minlength="3" maxlength="999" required>
                                            </div>
                                        </div>

                                        {{-- Price --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="nfc_card_price" class="form-label required">{{ __('Price') }}</label>
                                                <input type="number" min="0" max="9999999999" step="0.01" name="nfc_card_price" id="nfc_card_price" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        {{-- Available stocks --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="nfc_card_available_stocks" class="form-label required">{{ __('Available Stocks') }}</label>
                                                <input type="number" min="0" max="9999999999" step="1" value="1" name="nfc_card_available_stocks" id="nfc_card_available_stocks" class="form-control" required>
                                            </div>
                                        </div>
                                        
                                        {{-- Description --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="nfc_card_description" class="form-label required">{{ __('Description') }}</label>
                                                <textarea name="nfc_card_description" id="nfc_card_description" class="form-control text-capitalize" minlength="5" maxlength="4999" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <div class="d-flex">
                                        <a href="{{ route('admin.designs') }}" class="btn btn-danger">{{ __('Cancel') }}</a>
                                        <button type="submit" class="btn btn-primary ms-auto">{{ __('Create') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('admin.includes.footer')
    </div>    
@endsection