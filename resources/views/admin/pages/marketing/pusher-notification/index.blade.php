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
                            {{ __('Pusher Notifications') }}
                        </h2>
                    </div>
                    <span class="mt-3">{{ __('How to configure Pusher notifications?') }} {!! __('<a href="https://docs.nativecode.in/gobiz/how-to-get-pusher-beams-instance-id-and-secret-key" target="_blank">Click here</a>') !!}</span>
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
                        <form action="{{ route('admin.marketing.pusher.notification.send') }}" method="post" class="card" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header">
                                <h4 class="page-title">{{ __('Notification Details') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            {{-- Title --}}
                                            <div class="col-md-6 col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Title') }}</div>
                                                    <input type="text" class="form-control" name="title" value="{{ old('title') }}"
                                                        placeholder="{{ __('Title') }}" required>
                                                </div>
                                            </div>

                                            {{-- Message --}}
                                            <div class="col-md-6 col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Message') }}</div>
                                                    <input type="text" class="form-control" name="message" value="{{ old('message') }}"
                                                        placeholder="{{ __('Message') }}" required>
                                                </div>
                                            </div>

                                            {{-- Upload Image --}}
                                            <div class="col-md-6 col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Upload Image') }}</div>
                                                    <input type="file" class="form-control" name="image" accept="image/png, image/jpeg, image/jpg" required>
                                                </div>
                                            </div>

                                            {{-- Target URL --}}
                                            <div class="col-md-6 col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label required">{{ __('Target URL') }}</div>
                                                    <input type="text" class="form-control" name="target_url" value="{{ old('target_url') }}"
                                                        placeholder="{{ __('Target URL') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.includes.footer')
    </div>
@endsection