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
                            {{ __('My Account') }}
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
                            <div class="row g-0">
                                <div class="col-12 col-md-3 border-end">
                                    <div class="card-body pt-3">
                                        <div class="list-group list-group-transparent">
                                            {{-- Nav links --}}
                                            @include('user.pages.account.includes.navlinks', [
                                                'link' => 'settings',
                                            ])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-9 d-flex flex-column">
                                    <form action="{{ route('user.update.settings') }}" method="post" class="card">
                                        @csrf
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    {{-- Languages --}}
                                                    @if (count(config('app.languages')) > 1)
                                                        <div class="nav-item dropdown col-md-12 mb-3">
                                                            <div class="lang">
                                                                <label for="chooseLang"
                                                                    class="form-label">{{ __('Default Language') }}</label>
                                                                <select class="form-select small-btn"
                                                                    placeholder="{{ __('Select a language') }}"
                                                                    id="chooseLang">
                                                                    @foreach (config('app.languages') as $langLocale => $langName)
                                                                        <option value="{{ $langLocale }}"
                                                                            {{ app()->getLocale() == $langLocale ? 'selected' : '' }}>
                                                                            <strong>{{ $langName }}</strong>
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </form>
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
@endsection
