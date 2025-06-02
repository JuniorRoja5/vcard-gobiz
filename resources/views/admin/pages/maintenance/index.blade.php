@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

@section('content')
<div class="page-wrapper">
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        {{ __('Maintenance Mode') }}
                    </div>
                    <h2 class="page-title mb-2">
                        {{ __('Site Maintenance') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            {{-- Failed --}}
            @if(Session::has("failed"))
            <div class="alert alert-important alert-danger alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('failed')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            {{-- Success --}}
            @if(Session::has("success"))
            <div class="alert alert-important alert-success alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('success')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            <div class="row row-deck row-cards">                
                <div class="col-sm-12 col-lg-6">
                    <form action="{{ route('admin.maintenance.toggle') }}" method="post" class="card">
                        @csrf
                        <div class="card-header">
                            <h4 class="page-title">{{ __('Site Maintenance') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="row">
                                        <div class="col-xl-12 text-uppercase">
                                            @if (app()->isDownForMaintenance())
                                                <div class="mb-3">
                                                    <h4 class="text-danger">{{ __('Site is currently in maintenance mode') }}</h4>
                                                </div>
                                            @else
                                                <div class="mb-3">
                                                    <h4 class="text-success">{{ __('Site is currently not in maintenance mode') }}</h4>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Update button --}}
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary btn-md ms-auto">
                                @if (app()->isDownForMaintenance())
                                    {{ __('Disable') }}
                                @else
                                    {{ __('Enable') }}
                                @endif
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