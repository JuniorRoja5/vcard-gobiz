@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">📊 System Health Check</h1>

        <div class="row">
            @forelse ($checks->checkResults ?? [] as $check)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">{{ $check->label }}</h3>

                            @if ($check->status === 'ok')
                                <span class="badge bg-success">✅ Healthy</span>
                            @elseif ($check->status === 'warning')
                                <span class="badge bg-warning">⚠️ Warning</span>
                            @else
                                <span class="badge bg-danger">❌ Critical</span>
                            @endif

                            <p class="mt-2"><strong>Message:</strong> {{ $check->notificationMessage ?? 'No issues detected' }}</p>

                            @if (!empty($check->meta))
                                <ul class="list-group mt-2">
                                    @foreach ($check->meta as $key => $value)
                                        <li class="list-group-item">
                                            <strong>{{ ucfirst($key) }}:</strong> {{ $value }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p>No health checks found.</p>
            @endforelse
        </div>
    </div>
@endsection