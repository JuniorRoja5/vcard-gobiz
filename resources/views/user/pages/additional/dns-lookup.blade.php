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
                        {{ __('DNS Lookup') }}
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
                {{-- Failed --}}
                @if (Session::has("failed"))
                <div class="alert alert-important alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="alert-icon icon icon-tabler icon-tabler-alert-circle" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <circle cx="12" cy="12" r="9"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div>
                            {{Session::get('failed')}}
                        </div>
                    </div>
                    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
                @endif

                {{-- Success --}}
                @if(Session::has("success"))
                <div class="alert alert-important alert-success alert-dismissible" role="alert">
                    <div class="d-flex">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="alert-icon icon icon-tabler icon-tabler-check" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <div>
                            {{Session::get('success')}}
                        </div>
                    </div>
                    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
                @endif

                {{-- Search DNS Lookup --}}
                <div class="col-sm-6 col-lg-6">
                    <form action="{{ route('user.result.dns-lookup') }}" method="post" class="card">
                        @csrf
                        <div class="card border-0 shadow-sm">
                            <div class="card-header align-items-center">
                                <div class="row">
                                    <div class="col">
                                        <div class="card-title">{{ __('Domain') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    {{-- Domain --}}
                                    <div class="col-md-12 col-xl-12">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="domain"
                                                value="{{ $domain ?? (old('domain') ?? '') }}"
                                                placeholder="{{ __('Eg: https://domain.com') }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-search" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <circle cx="10" cy="10" r="7"></circle>
                                        <line x1="21" y1="21" x2="15" y2="15"></line>
                                    </svg>
                                    {{ __('Search') }}
                                </button>
                                <a href="{{ route('user.dns-lookup') }}" class="btn btn-dark">
                                    {{ __('Reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Result --}}
                <div class="col-sm-6 col-lg-6">
                    @if(isset($results))
                        <div class="card border-0 shadow-sm">
                            <div class="card-header align-items-center">
                                <div class="row">
                                    <div class="col">
                                        <div class="card-title">{{ __('Result') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(empty($results))
                                {{ __('No results found.') }}
                                @else
                                <ul class="nav nav-tabs d-flex flex-fill flex-column flex-md-row mb-3" data-bs-toggle="tabs"
                                    id="tabs-tab" role="tablist">
                                    <li class="nav-item flex-grow-1 text-center" role="presentation">
                                        <a href="#tabs-lookup-a" class="nav-link active" data-bs-toggle="tab"
                                            aria-selected="true" role="tab">{{ __('A Record') }}</a>
                                    </li>
                                    <li class="nav-item flex-grow-1 text-center" role="presentation">
                                        <a href="#tabs-lookup-aaaa" class="nav-link" data-bs-toggle="tab"
                                            aria-selected="false" role="tab" tabindex="-1">{{ __('AAAA Record') }}</a>
                                    </li>
                                    <li class="nav-item flex-grow-1 text-center">
                                        <a href="#tabs-lookup-cname" class="nav-link" data-bs-toggle="tab"
                                            aria-selected="false" role="tab" tabindex="-1">{{ __('CNAME Record') }}</a>
                                    </li>
                                    <li class="nav-item flex-grow-1 text-center">
                                        <a href="#tabs-lookup-mx" class="nav-link" data-bs-toggle="tab"
                                            aria-selected="false" role="tab" tabindex="-1">{{ __('MX Record') }}</a>
                                    </li>
                                    <li class="nav-item flex-grow-1 text-center">
                                        <a href="#tabs-lookup-txt" class="nav-link" data-bs-toggle="tab"
                                            aria-selected="false" role="tab" tabindex="-1">{{ __('TXT Record') }}</a>
                                    </li>
                                    <li class="nav-item flex-grow-1 text-center">
                                        <a href="#tabs-lookup-ns" class="nav-link" data-bs-toggle="tab"
                                            aria-selected="false" role="tab" tabindex="-1">{{ __('NS Record') }}</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="tabs-lookup-a" role="tabpanel"
                                        aria-labelledby="tabs-lookup-a">
                                        <div class="list-group list-group-flush mb-n3">
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('Type') }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-truncate"><strong>{{ __('Hostname') }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-truncate"><strong>{{ __('IP') }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('TTL') }}</strong></div>
                                                </div>
                                            </div>

                                            @foreach($results as $result)
                                            @if(strtolower($result['type']) == 'a')
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['type'] }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-break"><strong>{{ $result['host'] }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-break"><strong>{{ $result['ip'] }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['ttl'] }}</strong></div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tabs-lookup-aaaa" role="tabpanel"
                                        aria-labelledby="tabs-lookup-aaaa">
                                        <div class="list-group list-group-flush mb-n3">
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('Type') }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-truncate"><strong>{{ __('Hostname') }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-truncate"><strong>{{ __('IPv6') }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('TTL') }}</strong></div>
                                                </div>
                                            </div>

                                            @foreach($results as $result)
                                            @if(strtolower($result['type']) == 'aaaa')
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['type'] }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-break"><strong>{{ $result['host'] }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-break"><strong>{{ $result['ipv6'] }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['ttl'] }}</strong></div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tabs-lookup-cname" role="tabpanel"
                                        aria-labelledby="tabs-lookup-cname">
                                        <div class="list-group list-group-flush mb-n3">
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('Type') }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-truncate"><strong>{{ __('Hostname') }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-truncate"><strong>{{ __('Target') }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('TTL') }}</strong></div>
                                                </div>
                                            </div>

                                            @foreach($results as $result)
                                            @if(strtolower($result['type']) == 'cname')
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['type'] }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-break"><strong>{{ $result['host'] }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-break"><strong>{{ $result['target'] }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['ttl'] }}</strong></div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tabs-lookup-mx" role="tabpanel"
                                        aria-labelledby="tabs-lookup-mx">
                                        <div class="list-group list-group-flush mb-n3">
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('Type') }}</strong></div>
                                                    <div class="col-12 col-lg-3 text-truncate"><strong>{{ __('Hostname') }}</strong></div>
                                                    <div class="col-12 col-lg-3 text-truncate"><strong>{{ __('Target') }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('Priority') }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('TTL') }}</strong></div>
                                                </div>
                                            </div>

                                            @foreach($results as $result)
                                            @if(strtolower($result['type']) == 'mx')
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['type'] }}</strong></div>
                                                    <div class="col-12 col-lg-3 text-break"><strong>{{ $result['host'] }}</strong></div>
                                                    <div class="col-12 col-lg-3 text-break"><strong>{{ $result['target'] }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['pri'] }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['ttl'] }}</strong></div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tabs-lookup-txt" role="tabpanel"
                                        aria-labelledby="tabs-lookup-txt">
                                        <div class="list-group list-group-flush mb-n3">
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('Type') }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-truncate"><strong>{{ __('Hostname') }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-truncate"><strong>{{ __('Entries') }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('TTL') }}</strong></div>
                                                </div>
                                            </div>

                                            @foreach($results as $result)
                                            @if(strtolower($result['type']) == 'txt')
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['type'] }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-break"><strong>{{ $result['host'] }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-break">
                                                        @foreach($result['entries'] as $entry)
                                                        <div class="text-break {{ !$loop->first ? 'mt-1' : '' }}"><strong>{{ $entry}}</strong></div>
                                                        @endforeach
                                                    </div>
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['ttl'] }}</strong></div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tabs-lookup-ns" role="tabpanel"
                                        aria-labelledby="tabs-lookup-ns">
                                        <div class="list-group list-group-flush mb-n3">
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('Type') }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-truncate"><strong>{{ __('Hostname') }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-truncate"><strong>{{ __('Target') }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-truncate"><strong>{{ __('TTL') }}</strong></div>
                                                </div>
                                            </div>

                                            @foreach($results as $result)
                                            @if(strtolower($result['type']) == 'ns')
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center text-muted">
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['type'] }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-break"><strong>{{ $result['host'] }}</strong></div>
                                                    <div class="col-12 col-lg-4 text-break"><strong>{{ $result['target'] }}</strong></div>
                                                    <div class="col-12 col-lg-2 text-break"><strong>{{ $result['ttl'] }}</strong></div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Footer --}}
    @include('user.includes.footer')
</div>
@endsection