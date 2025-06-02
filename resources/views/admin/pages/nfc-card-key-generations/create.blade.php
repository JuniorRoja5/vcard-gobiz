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
                            {{ __('Create NFC Card Key Generation') }}
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
                            <form action="{{ route('admin.save.key.generation') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Number of Keys --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="nfc_card_key_generations" class="form-label required">{{ __('Number of Keys') }}</label>
                                                <input type="number" min="1" max="999" step="1" value="1" name="nfc_card_key_generations" id="nfc_card_key_generations" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <div class="d-flex">
                                        <a href="{{ route('admin.key.generations') }}" class="btn btn-danger">{{ __('Cancel') }}</a>
                                        <button type="submit" class="btn btn-primary ms-auto">{{ __('Generate') }}</button>
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